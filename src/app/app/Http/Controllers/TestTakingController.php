<?php

namespace App\Http\Controllers;

use App\Models\AttemptAnswer;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestResult;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class TestTakingController extends Controller
{
    /**
     * List available published tests for the user.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $tests = Test::with(['testType', 'program'])
            ->where('status', 'published')
            ->where(function ($q) {
                $q->where('visibility', 'free')
                    ->orWhere('visibility', 'premium');
            })
            ->withCount(['attempts' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($test) {
                $test->user_attempts_count = $test->attempts_count;
                return $test;
            });

        return Inertia::render('Tests/Index', [
            'tests' => $tests,
        ]);
    }

    /**
     * Show test detail page with previous attempts.
     */
    public function show(Request $request, string $slug): Response
    {
        $user = $request->user();

        $test = Test::with(['testType', 'program', 'sections' => function ($q) {
            $q->orderBy('sort_order');
        }])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $previousAttempts = TestAttempt::with(['test.testType'])
            ->where('user_id', $user->id)
            ->where('test_id', $test->id)
            ->orderBy('attempt_no', 'desc')
            ->get();

        // Check access: free tests are always accessible
        $canStart = true;
        $accessMessage = null;

        if ($test->visibility === 'premium') {
            $subscription = $user->activeSubscription ?? null;
            if (! $subscription) {
                $canStart = false;
                $accessMessage = 'Tes ini membutuhkan langganan aktif. Silakan berlangganan terlebih dahulu.';
            }
        }

        return Inertia::render('Tests/Show', [
            'test' => $test,
            'previousAttempts' => $previousAttempts,
            'canStart' => $canStart,
            'accessMessage' => $accessMessage,
        ]);
    }

    /**
     * Start a new test attempt.
     */
    public function start(Request $request, string $slug): RedirectResponse
    {
        $user = $request->user();

        $test = Test::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Check premium access
        if ($test->visibility === 'premium') {
            $subscription = $user->activeSubscription ?? null;
            if (! $subscription) {
                return redirect()->route('tests.show', $slug)
                    ->with('error', 'Anda memerlukan langganan aktif untuk mengakses tes ini.');
            }
        }

        // Check for existing in-progress attempt
        $existingAttempt = TestAttempt::where('user_id', $user->id)
            ->where('test_id', $test->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            return redirect()->route('tests.take', $existingAttempt->id);
        }

        // Create new attempt
        $attemptNo = TestAttempt::where('user_id', $user->id)
            ->where('test_id', $test->id)
            ->max('attempt_no') + 1;

        $now = Carbon::now();
        $expiredAt = $test->duration_minutes
            ? $now->copy()->addMinutes($test->duration_minutes)
            : null;

        $attempt = TestAttempt::create([
            'user_id' => $user->id,
            'test_id' => $test->id,
            'attempt_no' => $attemptNo,
            'started_at' => $now,
            'expired_at' => $expiredAt,
            'status' => 'in_progress',
        ]);

        return redirect()->route('tests.take', $attempt->id);
    }

    /**
     * Show the test-taking interface.
     */
    public function take(Request $request, string $attemptId): Response|RedirectResponse
    {
        $user = $request->user();

        $attempt = TestAttempt::with(['test.testType', 'test.program', 'test.sections'])
            ->where('id', $attemptId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Check if attempt is still in progress
        if ($attempt->status !== 'in_progress') {
            if (in_array($attempt->status, ['submitted', 'evaluated'])) {
                return redirect()->route('tests.result', $attempt->id);
            }

            return redirect()->route('tests.show', $attempt->test->slug)
                ->with('error', 'Sesi pengerjaan tes ini sudah berakhir.');
        }

        // Check if expired
        if ($attempt->expired_at && Carbon::now()->isAfter($attempt->expired_at)) {
            $this->autoSubmit($attempt);

            return redirect()->route('tests.result', $attempt->id);
        }

        // Load questions without revealing correct answers
        $questions = Question::where('test_id', $attempt->test_id)
            ->where('is_active', true)
            ->with(['options' => function ($q) {
                $q->select('id', 'question_id', 'option_key', 'content', 'sort_order')
                    ->orderBy('sort_order');
            }, 'section'])
            ->orderBy('sort_order')
            ->get()
            ->makeHidden(['explanation', 'score']);

        // Load existing answers
        $answers = AttemptAnswer::where('test_attempt_id', $attempt->id)
            ->get()
            ->map(function ($a) {
                return [
                    'question_id' => $a->question_id,
                    'selected_option_id' => $a->selected_option_id,
                    'answer_text' => $a->answer_text,
                ];
            });

        return Inertia::render('Tests/Take', [
            'attempt' => [
                'id' => $attempt->id,
                'test_id' => $attempt->test_id,
                'attempt_no' => $attempt->attempt_no,
                'started_at' => $attempt->started_at?->toISOString(),
                'expired_at' => $attempt->expired_at?->toISOString(),
                'status' => $attempt->status,
                'test' => $attempt->test,
                'questions' => $questions,
                'answers' => $answers,
            ],
        ]);
    }

    /**
     * Save an answer for a question.
     */
    public function saveAnswer(Request $request, string $attemptId): RedirectResponse
    {
        $user = $request->user();

        $attempt = TestAttempt::where('id', $attemptId)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        // Check expiry
        if ($attempt->expired_at && Carbon::now()->isAfter($attempt->expired_at)) {
            $this->autoSubmit($attempt);

            return redirect()->route('tests.result', $attempt->id);
        }

        $validated = $request->validate([
            'question_id' => 'required|uuid|exists:questions,id',
            'selected_option_id' => 'nullable|uuid|exists:question_options,id',
            'answer_text' => 'nullable|string|max:10000',
        ]);

        // Verify question belongs to this test
        $question = Question::where('id', $validated['question_id'])
            ->where('test_id', $attempt->test_id)
            ->firstOrFail();

        // Verify option belongs to the question if provided
        if ($validated['selected_option_id']) {
            QuestionOption::where('id', $validated['selected_option_id'])
                ->where('question_id', $question->id)
                ->firstOrFail();
        }

        AttemptAnswer::updateOrCreate(
            [
                'test_attempt_id' => $attempt->id,
                'question_id' => $validated['question_id'],
            ],
            [
                'selected_option_id' => $validated['selected_option_id'],
                'answer_text' => $validated['answer_text'],
                'answered_at' => Carbon::now(),
            ]
        );

        return back();
    }

    /**
     * Submit the test attempt.
     */
    public function submit(Request $request, string $attemptId): RedirectResponse
    {
        $user = $request->user();

        $attempt = TestAttempt::where('id', $attemptId)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        $this->scoreAndSubmit($attempt);

        return redirect()->route('tests.result', $attempt->id);
    }

    /**
     * Show the test result.
     */
    public function result(Request $request, string $attemptId): Response
    {
        $user = $request->user();

        $attempt = TestAttempt::with(['test.testType', 'test.program'])
            ->where('id', $attemptId)
            ->where('user_id', $user->id)
            ->whereIn('status', ['submitted', 'evaluated', 'expired'])
            ->firstOrFail();

        $result = TestResult::where('test_attempt_id', $attempt->id)->first();

        // Load answers with question info (including correct answers for review)
        $answers = AttemptAnswer::where('test_attempt_id', $attempt->id)
            ->with(['question.options'])
            ->get();

        $questions = Question::where('test_id', $attempt->test_id)
            ->where('is_active', true)
            ->with('options')
            ->orderBy('sort_order')
            ->get()
            ->keyBy('id');

        $reviewAnswers = $questions->map(function ($question) use ($answers) {
            $userAnswer = $answers->firstWhere('question_id', $question->id);
            $correctOption = $question->options->firstWhere('is_correct', true);

            return [
                'question_id' => $question->id,
                'question_content' => $question->content,
                'question_type' => $question->question_type,
                'selected_option_id' => $userAnswer?->selected_option_id,
                'answer_text' => $userAnswer?->answer_text,
                'is_correct' => $userAnswer?->is_correct,
                'score' => $userAnswer?->score,
                'correct_option_id' => $correctOption?->id,
                'explanation' => $question->explanation,
                'options' => $question->options->map(function ($opt) {
                    return [
                        'id' => $opt->id,
                        'option_key' => $opt->option_key,
                        'content' => $opt->content,
                        'sort_order' => $opt->sort_order,
                    ];
                })->values(),
            ];
        })->values();

        $answeredCorrect = $answers->where('is_correct', true)->count();
        $answeredWrong = $answers->where('is_correct', false)->count();
        $totalQuestions = $questions->count();
        $unanswered = $totalQuestions - $answers->count();

        return Inertia::render('Tests/Result', [
            'data' => [
                'attempt' => $attempt,
                'result' => $result,
                'answers' => $reviewAnswers,
                'stats' => [
                    'total' => $totalQuestions,
                    'correct' => $answeredCorrect,
                    'wrong' => $answeredWrong,
                    'unanswered' => $unanswered,
                ],
            ],
        ]);
    }

    /**
     * Auto-submit when timer expires.
     */
    private function autoSubmit(TestAttempt $attempt): void
    {
        if ($attempt->status !== 'in_progress') {
            return;
        }

        $this->scoreAndSubmit($attempt, 'expired');
    }

    /**
     * Score the attempt and mark as submitted.
     */
    private function scoreAndSubmit(TestAttempt $attempt, string $status = 'submitted'): void
    {
        DB::transaction(function () use ($attempt, $status) {
            $questions = Question::where('test_id', $attempt->test_id)
                ->where('is_active', true)
                ->with('options')
                ->get();

            $answers = AttemptAnswer::where('test_attempt_id', $attempt->id)->get();

            $totalScore = 0;
            $maxScore = 0;

            foreach ($questions as $question) {
                $maxScore += (float) $question->score;
                $userAnswer = $answers->firstWhere('question_id', $question->id);

                if (! $userAnswer) {
                    continue;
                }

                if ($question->question_type === 'multiple_choice' || $question->question_type === 'true_false') {
                    $correctOption = $question->options->firstWhere('is_correct', true);

                    if ($correctOption && $userAnswer->selected_option_id === $correctOption->id) {
                        $score = $correctOption->score ?? $question->score;
                        $userAnswer->update([
                            'is_correct' => true,
                            'score' => $score,
                        ]);
                        $totalScore += (float) $score;
                    } else {
                        $userAnswer->update([
                            'is_correct' => false,
                            'score' => 0,
                        ]);
                    }
                }
            }

            $percentage = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;

            $attempt->update([
                'status' => $status,
                'submitted_at' => Carbon::now(),
                'total_score' => $totalScore,
                'percentage' => $percentage,
            ]);

            TestResult::updateOrCreate(
                ['test_attempt_id' => $attempt->id],
                [
                    'user_id' => $attempt->user_id,
                    'test_id' => $attempt->test_id,
                    'raw_score' => $totalScore,
                    'final_score' => $totalScore,
                    'percentage' => $percentage,
                ]
            );
        });
    }
}
