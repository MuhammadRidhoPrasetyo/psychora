<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\AttemptAnswer;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GenericTestController extends Controller
{
    public function start(Request $request, Test $test): RedirectResponse
    {
        $user = $request->user();

        $existingAttempt = $user->testAttempts()
            ->where('test_id', $test->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            return redirect()->route('user.tests.take', $existingAttempt);
        }

        $attemptNo = $user->testAttempts()
            ->where('test_id', $test->id)
            ->count() + 1;

        $attempt = TestAttempt::create([
            'user_id' => $user->id,
            'test_id' => $test->id,
            'attempt_no' => $attemptNo,
            'started_at' => now(),
            'status' => 'in_progress',
            'expired_at' => $test->duration_minutes ? now()->addMinutes($test->duration_minutes) : null,
        ]);

        return redirect()->route('user.tests.take', $attempt);
    }

    public function take(Request $request, TestAttempt $attempt): Response|RedirectResponse
    {
        $this->authorize('view', $attempt);

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('user.tests.result', $attempt);
        }

        if ($attempt->expired_at && $attempt->expired_at->isPast()) {
            $attempt->update(['status' => 'expired', 'submitted_at' => now()]);
            return redirect()->route('user.tests.result', $attempt);
        }

        $test = $attempt->test()->with('sections')->first();

        $questions = $test->questions()
            ->where('is_active', true)
            ->with('options')
            ->orderBy('sort_order')
            ->get()
            ->map(function ($question) {
                // Hide correct answer from user
                $question->options->each(fn ($opt) => $opt->makeHidden(['is_correct', 'score']));
                $question->makeHidden('explanation');
                return $question;
            });

        $answers = $attempt->answers()
            ->get()
            ->keyBy('question_id');

        return Inertia::render('User/Tests/Generic/Take', compact('attempt', 'test', 'questions', 'answers'));
    }

    public function saveAnswer(Request $request, TestAttempt $attempt): RedirectResponse
    {
        $this->authorize('update', $attempt);

        if ($attempt->status !== 'in_progress') {
            return back()->with('error', 'Test sudah selesai.');
        }

        $validated = $request->validate([
            'question_id' => ['required', 'exists:questions,id'],
            'selected_option_id' => ['nullable', 'exists:question_options,id'],
            'answer_text' => ['nullable', 'string'],
            'answer_json' => ['nullable', 'array'],
        ]);

        $attempt->answers()->updateOrCreate(
            ['question_id' => $validated['question_id']],
            [
                'selected_option_id' => $validated['selected_option_id'] ?? null,
                'answer_text' => $validated['answer_text'] ?? null,
                'answer_json' => $validated['answer_json'] ?? null,
                'answered_at' => now(),
            ]
        );

        return back();
    }

    public function submit(Request $request, TestAttempt $attempt): RedirectResponse
    {
        $this->authorize('update', $attempt);

        if ($attempt->status !== 'in_progress') {
            return back()->with('error', 'Test sudah selesai.');
        }

        $this->scoreAttempt($attempt);

        $attempt->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('user.tests.result', $attempt)
            ->with('success', 'Test berhasil disubmit.');
    }

    public function result(TestAttempt $attempt): Response
    {
        $this->authorize('view', $attempt);

        $attempt->load([
            'test.program:id,name',
            'test.testType:id,name',
            'result',
        ]);

        $showExplanation = in_array($attempt->status, ['submitted', 'evaluated']);

        $answers = $attempt->answers()
            ->with(['question.options', 'question.essayAnswers', 'selectedOption'])
            ->get();

        if (!$showExplanation) {
            $answers->each(function ($answer) {
                $answer->question->makeHidden('explanation');
                $answer->question->options->each(fn ($opt) => $opt->makeHidden(['is_correct', 'score']));
            });
        }

        return Inertia::render('User/Tests/Generic/Result', compact('attempt', 'answers', 'showExplanation'));
    }

    protected function scoreAttempt(TestAttempt $attempt): void
    {
        $totalScore = 0;
        $totalQuestions = 0;

        $answers = $attempt->answers()->with('question.options', 'question.essayAnswers')->get();

        foreach ($answers as $answer) {
            $question = $answer->question;
            $score = 0;

            if ($question->question_type === 'multiple_choice' || $question->question_type === 'true_false') {
                if ($answer->selected_option_id) {
                    $option = $question->options->firstWhere('id', $answer->selected_option_id);
                    if ($option && $option->is_correct) {
                        $score = $option->score ?? $question->score;
                    }
                }
                $answer->update([
                    'is_correct' => $score > 0,
                    'score' => $score,
                ]);
            } elseif ($question->question_type === 'essay' && $answer->answer_text) {
                $essayAnswers = $question->essayAnswers()->orderBy('priority')->get();
                foreach ($essayAnswers as $essayAnswer) {
                    $matched = match ($essayAnswer->match_type) {
                        'exact' => strtolower(trim($answer->answer_text)) === strtolower(trim($essayAnswer->answer_text)),
                        'contains' => str_contains(strtolower($answer->answer_text), strtolower($essayAnswer->answer_text)),
                        'fuzzy' => similar_text(strtolower($answer->answer_text), strtolower($essayAnswer->answer_text), $percentage) && $percentage >= 80,
                        'regex' => (bool) preg_match('/' . $essayAnswer->answer_text . '/i', $answer->answer_text),
                        default => false,
                    };

                    if ($matched) {
                        $score = $essayAnswer->score;
                        break;
                    }
                }
                $answer->update([
                    'is_correct' => $score > 0,
                    'score' => $score,
                ]);
            }

            $totalScore += $score;
            $totalQuestions++;
        }

        $maxScore = $attempt->test->questions()->where('is_active', true)->sum('score');
        $percentage = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;

        $attempt->update([
            'total_score' => $totalScore,
            'percentage' => round($percentage, 2),
        ]);

        $attempt->result()->updateOrCreate(
            ['test_attempt_id' => $attempt->id],
            [
                'user_id' => $attempt->user_id,
                'test_id' => $attempt->test_id,
                'raw_score' => $totalScore,
                'final_score' => $totalScore,
                'percentage' => round($percentage, 2),
            ]
        );
    }
}
