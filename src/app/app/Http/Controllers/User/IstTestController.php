<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\Ist\IstForm;
use App\Models\Ist\IstAttempt;
use App\Models\Ist\IstSubtestAttempt;
use App\Models\Ist\IstAnswer;
use App\Models\Ist\IstResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IstTestController extends Controller
{
    public function start(Request $request, Test $test): RedirectResponse
    {
        $user = $request->user();
        $istForm = IstForm::where('test_id', $test->id)->where('is_active', true)->firstOrFail();

        // Check for existing in-progress attempt
        $existingAttempt = TestAttempt::where('user_id', $user->id)
            ->where('test_id', $test->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            $istAttempt = IstAttempt::where('test_attempt_id', $existingAttempt->id)->first();
            if ($istAttempt) {
                return redirect()->route('user.ist.take', $istAttempt);
            }
        }

        $attemptNo = TestAttempt::where('user_id', $user->id)
            ->where('test_id', $test->id)
            ->count() + 1;

        $testAttempt = TestAttempt::create([
            'user_id' => $user->id,
            'test_id' => $test->id,
            'attempt_no' => $attemptNo,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        $firstSubtest = $istForm->subtests()->orderBy('sort_order')->first();

        $istAttempt = IstAttempt::create([
            'test_attempt_id' => $testAttempt->id,
            'ist_form_id' => $istForm->id,
            'current_subtest_code' => $firstSubtest?->subtest_code,
            'attempt_number' => $attemptNo,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // Create subtest attempts for all 9 subtests
        $subtests = $istForm->subtests()->orderBy('sort_order')->get();
        foreach ($subtests as $index => $subtest) {
            $formItem = $istForm->formItems()->where('ist_subtest_id', $subtest->id)->first();

            IstSubtestAttempt::create([
                'ist_attempt_id' => $istAttempt->id,
                'ist_subtest_id' => $subtest->id,
                'subtest_code' => $subtest->subtest_code,
                'status' => $index === 0 ? 'in_progress' : 'not_started',
                'started_at' => $index === 0 ? now() : null,
                'deadline_at' => $index === 0 && ($formItem?->duration_minutes ?? $subtest->duration_minutes)
                    ? now()->addMinutes($formItem?->duration_minutes ?? $subtest->duration_minutes)
                    : null,
                'random_seed' => $formItem?->is_randomized ? random_int(1, 999999) : null,
            ]);
        }

        return redirect()->route('user.ist.take', $istAttempt);
    }

    public function take(Request $request, IstAttempt $attempt): Response|RedirectResponse
    {
        $testAttempt = $attempt->testAttempt;
        $this->authorize('view', $testAttempt);

        if ($attempt->status === 'completed' || $attempt->status === 'expired') {
            return redirect()->route('user.ist.result', $attempt);
        }

        $form = $attempt->form()->with('subtests')->first();

        $currentSubtestAttempt = $attempt->subtestAttempts()
            ->where('status', 'in_progress')
            ->with('subtest')
            ->first();

        if (!$currentSubtestAttempt) {
            // All subtests completed
            $this->completeIstAttempt($attempt);
            return redirect()->route('user.ist.result', $attempt);
        }

        // Check deadline
        if ($currentSubtestAttempt->deadline_at && $currentSubtestAttempt->deadline_at->isPast()) {
            $currentSubtestAttempt->update(['status' => 'completed', 'submitted_at' => now()]);
            return redirect()->route('user.ist.next-subtest', $attempt);
        }

        $subtest = $currentSubtestAttempt->subtest;
        $formItem = $form->formItems()->where('ist_subtest_id', $subtest->id)->first();

        // Get questions for this subtest
        $questionsQuery = $subtest->questions()
            ->with('question.options')
            ->orderBy('sort_order');

        if ($formItem && $formItem->number_of_questions) {
            $questionsQuery->take($formItem->number_of_questions);
        }

        $questions = $questionsQuery->get()->map(function ($sq) {
            $q = $sq->question;
            $q->options->each(fn ($opt) => $opt->makeHidden(['is_correct', 'score']));
            $q->makeHidden('explanation');
            return $q;
        });

        // Randomize if configured
        if ($formItem?->is_randomized && $currentSubtestAttempt->random_seed) {
            srand($currentSubtestAttempt->random_seed);
            $questions = $questions->shuffle();
            srand();
        }

        // Get instructions & clues
        $instructions = $form->instructions()
            ->where(function ($q) use ($subtest) {
                $q->where('ist_subtest_id', $subtest->id)
                    ->orWhereNull('ist_subtest_id');
            })
            ->orderBy('sort_order')
            ->get();

        $clues = $form->clues()
            ->where(function ($q) use ($subtest) {
                $q->where('ist_subtest_id', $subtest->id)
                    ->orWhereNull('ist_subtest_id');
            })
            ->get();

        $answers = IstAnswer::where('ist_subtest_attempt_id', $currentSubtestAttempt->id)
            ->get()
            ->keyBy('question_id');

        $allSubtestAttempts = $attempt->subtestAttempts()
            ->with('subtest:id,subtest_code,subtest_name')
            ->orderBy('subtest_code')
            ->get(['id', 'ist_subtest_id', 'subtest_code', 'status']);

        return Inertia::render('User/Tests/Ist/Take', compact(
            'attempt', 'form', 'currentSubtestAttempt', 'subtest',
            'questions', 'answers', 'instructions', 'clues',
            'allSubtestAttempts', 'formItem'
        ));
    }

    public function saveAnswer(Request $request, IstAttempt $attempt): RedirectResponse
    {
        $testAttempt = $attempt->testAttempt;
        $this->authorize('update', $testAttempt);

        $currentSubtestAttempt = $attempt->subtestAttempts()
            ->where('status', 'in_progress')
            ->firstOrFail();

        $validated = $request->validate([
            'question_id' => ['required', 'exists:questions,id'],
            'selected_option_id' => ['nullable', 'exists:question_options,id'],
            'answer_text' => ['nullable', 'string'],
            'answer_json' => ['nullable', 'array'],
        ]);

        IstAnswer::updateOrCreate(
            [
                'ist_subtest_attempt_id' => $currentSubtestAttempt->id,
                'question_id' => $validated['question_id'],
            ],
            [
                'selected_option_id' => $validated['selected_option_id'] ?? null,
                'answer_text' => $validated['answer_text'] ?? null,
                'answer_json' => $validated['answer_json'] ?? null,
                'answered_at' => now(),
            ]
        );

        return back();
    }

    public function submitSubtest(Request $request, IstAttempt $attempt): RedirectResponse
    {
        $testAttempt = $attempt->testAttempt;
        $this->authorize('update', $testAttempt);

        $currentSubtestAttempt = $attempt->subtestAttempts()
            ->where('status', 'in_progress')
            ->firstOrFail();

        $this->scoreSubtest($currentSubtestAttempt, $attempt);

        $currentSubtestAttempt->update([
            'status' => 'completed',
            'submitted_at' => now(),
        ]);

        return redirect()->route('user.ist.next-subtest', $attempt);
    }

    public function nextSubtest(IstAttempt $attempt): RedirectResponse
    {
        $testAttempt = $attempt->testAttempt;
        $this->authorize('view', $testAttempt);

        $nextSubtestAttempt = $attempt->subtestAttempts()
            ->where('status', 'not_started')
            ->orderBy('subtest_code')
            ->first();

        if (!$nextSubtestAttempt) {
            $this->completeIstAttempt($attempt);
            return redirect()->route('user.ist.result', $attempt);
        }

        $subtest = $nextSubtestAttempt->subtest;
        $formItem = $attempt->form->formItems()->where('ist_subtest_id', $subtest->id)->first();
        $duration = $formItem?->duration_minutes ?? $subtest->duration_minutes;

        $nextSubtestAttempt->update([
            'status' => 'in_progress',
            'started_at' => now(),
            'deadline_at' => $duration ? now()->addMinutes($duration) : null,
        ]);

        $attempt->update(['current_subtest_code' => $subtest->subtest_code]);

        return redirect()->route('user.ist.take', $attempt);
    }

    public function result(IstAttempt $attempt): Response
    {
        $testAttempt = $attempt->testAttempt;
        $this->authorize('view', $testAttempt);

        $attempt->load([
            'form',
            'subtestAttempts.subtest',
            'results',
        ]);

        $testAttempt->load(['test.program:id,name', 'test.testType:id,name']);

        return Inertia::render('User/Tests/Ist/Result', compact('attempt', 'testAttempt'));
    }

    protected function scoreSubtest(IstSubtestAttempt $subtestAttempt, IstAttempt $istAttempt): void
    {
        $answers = IstAnswer::where('ist_subtest_attempt_id', $subtestAttempt->id)
            ->with('question.options')
            ->get();

        $rawScore = 0;

        foreach ($answers as $answer) {
            if ($answer->selected_option_id) {
                $option = $answer->question->options->firstWhere('id', $answer->selected_option_id);
                if ($option && $option->is_correct) {
                    $score = $option->score ?? $answer->question->score ?? 1;
                    $rawScore += $score;
                    $answer->update(['is_correct' => true, 'score' => $score]);
                } else {
                    $answer->update(['is_correct' => false, 'score' => 0]);
                }
            }
        }

        // Apply multiplier if configured
        $formItem = $istAttempt->form->formItems()
            ->where('ist_subtest_id', $subtestAttempt->ist_subtest_id)
            ->first();

        $scaledScore = $rawScore * ($formItem?->multiplier ?? 1);

        $subtestAttempt->update([
            'raw_score' => $rawScore,
            'scaled_score' => $scaledScore,
        ]);
    }

    protected function completeIstAttempt(IstAttempt $attempt): void
    {
        $subtestAttempts = $attempt->subtestAttempts()->with('subtest')->get();

        $verbalCodes = ['SE', 'WA', 'AN', 'GE'];
        $numericalCodes = ['ME', 'RA', 'ZR'];
        $figuralCodes = ['FA', 'WU'];

        $categories = [
            'verbal' => $subtestAttempts->whereIn('subtest_code', $verbalCodes),
            'numerical' => $subtestAttempts->whereIn('subtest_code', $numericalCodes),
            'figural' => $subtestAttempts->whereIn('subtest_code', $figuralCodes),
        ];

        $totalScore = 0;

        foreach ($categories as $category => $attempts) {
            $rawScore = $attempts->sum('raw_score');
            $scaledScore = $attempts->sum('scaled_score');
            $count = $attempts->count();
            $avgScaled = $count > 0 ? $scaledScore / $count : 0;

            IstResult::updateOrCreate(
                ['ist_attempt_id' => $attempt->id, 'category' => $category],
                [
                    'raw_score' => $rawScore,
                    'scaled_score' => round($avgScaled, 2),
                ]
            );

            $totalScore += $scaledScore;
        }

        // Overall result
        $totalSubtests = $subtestAttempts->count();
        $avgOverall = $totalSubtests > 0 ? $totalScore / $totalSubtests : 0;

        IstResult::updateOrCreate(
            ['ist_attempt_id' => $attempt->id, 'category' => 'overall'],
            [
                'raw_score' => $subtestAttempts->sum('raw_score'),
                'scaled_score' => round($avgOverall, 2),
            ]
        );

        $attempt->update([
            'status' => 'completed',
            'submitted_at' => now(),
            'total_score' => $totalScore,
            'current_subtest_code' => null,
        ]);

        $attempt->testAttempt->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'total_score' => $totalScore,
        ]);
    }
}
