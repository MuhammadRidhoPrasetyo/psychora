<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\Kraepelin\KraepelinForm;
use App\Models\Kraepelin\KraepelinAttempt;
use App\Models\Kraepelin\KraepelinAttemptColumn;
use App\Models\Kraepelin\KraepelinAttemptNumber;
use App\Models\Kraepelin\KraepelinAnswer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KraepelinTestController extends Controller
{
    private const DEFAULT_NUMBERS_PER_COLUMN = 30;
    private const DEFAULT_COLUMNS_COUNT = 50;
    private const DEFAULT_DURATION_PER_COLUMN = 15;

    public function start(Request $request, Test $test): RedirectResponse
    {
        $user = $request->user();
        $form = KraepelinForm::where('test_id', $test->id)->firstOrFail();

        // Check for existing in-progress attempt
        $existingAttempt = TestAttempt::where('user_id', $user->id)
            ->where('test_id', $test->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            $kraepelinAttempt = KraepelinAttempt::where('test_attempt_id', $existingAttempt->id)->first();
            if ($kraepelinAttempt) {
                return redirect()->route('user.kraepelin.take', $kraepelinAttempt);
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

        $numbersPerColumn = self::DEFAULT_NUMBERS_PER_COLUMN;
        $columnsCount = self::DEFAULT_COLUMNS_COUNT;
        $durationPerColumn = self::DEFAULT_DURATION_PER_COLUMN;

        $kraepelinAttempt = KraepelinAttempt::create([
            'test_attempt_id' => $testAttempt->id,
            'kraepelin_form_id' => $form->id,
            'numbers_per_column' => $numbersPerColumn,
            'columns_count' => $columnsCount,
            'duration_per_column' => $durationPerColumn,
            'attempt_number' => $attemptNo,
            'status' => 'in_progress',
            'started_at' => now(),
            'deadline_at' => now()->addSeconds($columnsCount * $durationPerColumn),
        ]);

        // Generate random columns and numbers
        for ($col = 1; $col <= $columnsCount; $col++) {
            $column = KraepelinAttemptColumn::create([
                'kraepelin_attempt_id' => $kraepelinAttempt->id,
                'column_number' => $col,
            ]);

            for ($pos = 1; $pos <= $numbersPerColumn; $pos++) {
                KraepelinAttemptNumber::create([
                    'kraepelin_attempt_column_id' => $column->id,
                    'position' => $pos,
                    'value' => random_int(0, 9),
                ]);
            }
        }

        return redirect()->route('user.kraepelin.take', $kraepelinAttempt);
    }

    public function take(Request $request, KraepelinAttempt $attempt): Response|RedirectResponse
    {
        $testAttempt = $attempt->testAttempt;
        $this->authorize('view', $testAttempt);

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('user.kraepelin.result', $attempt);
        }

        if ($attempt->deadline_at && $attempt->deadline_at->isPast()) {
            $this->submitAttempt($attempt);
            return redirect()->route('user.kraepelin.result', $attempt);
        }

        $columns = $attempt->columns()
            ->with(['numbers' => fn ($q) => $q->orderBy('position')])
            ->orderBy('column_number')
            ->get();

        $existingAnswers = $attempt->answers()
            ->get()
            ->groupBy('kraepelin_attempt_column_id');

        return Inertia::render('User/Tests/Kraepelin/Take', compact('attempt', 'columns', 'existingAnswers'));
    }

    public function saveAnswer(Request $request, KraepelinAttempt $attempt): RedirectResponse
    {
        $testAttempt = $attempt->testAttempt;
        $this->authorize('update', $testAttempt);

        if ($attempt->status !== 'in_progress') {
            return back()->with('error', 'Test sudah selesai.');
        }

        $validated = $request->validate([
            'kraepelin_attempt_column_id' => ['required', 'exists:kraepelin_attempt_columns,id'],
            'position' => ['required', 'integer', 'min:1'],
            'top_number' => ['required', 'integer', 'min:0', 'max:9'],
            'bottom_number' => ['required', 'integer', 'min:0', 'max:9'],
            'user_answer' => ['required', 'integer', 'min:0', 'max:18'],
        ]);

        $correctAnswer = ($validated['top_number'] + $validated['bottom_number']) % 10;

        KraepelinAnswer::updateOrCreate(
            [
                'kraepelin_attempt_id' => $attempt->id,
                'kraepelin_attempt_column_id' => $validated['kraepelin_attempt_column_id'],
                'position' => $validated['position'],
            ],
            [
                'top_number' => $validated['top_number'],
                'bottom_number' => $validated['bottom_number'],
                'user_answer' => $validated['user_answer'],
                'correct_answer' => $correctAnswer,
                'is_correct' => $validated['user_answer'] === $correctAnswer,
                'answered_at' => now(),
            ]
        );

        return back();
    }

    public function saveBatchAnswers(Request $request, KraepelinAttempt $attempt): RedirectResponse
    {
        $testAttempt = $attempt->testAttempt;
        $this->authorize('update', $testAttempt);

        if ($attempt->status !== 'in_progress') {
            return back()->with('error', 'Test sudah selesai.');
        }

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*.kraepelin_attempt_column_id' => ['required', 'exists:kraepelin_attempt_columns,id'],
            'answers.*.position' => ['required', 'integer', 'min:1'],
            'answers.*.top_number' => ['required', 'integer', 'min:0', 'max:9'],
            'answers.*.bottom_number' => ['required', 'integer', 'min:0', 'max:9'],
            'answers.*.user_answer' => ['required', 'integer', 'min:0', 'max:18'],
        ]);

        foreach ($validated['answers'] as $answerData) {
            $correctAnswer = ($answerData['top_number'] + $answerData['bottom_number']) % 10;

            KraepelinAnswer::updateOrCreate(
                [
                    'kraepelin_attempt_id' => $attempt->id,
                    'kraepelin_attempt_column_id' => $answerData['kraepelin_attempt_column_id'],
                    'position' => $answerData['position'],
                ],
                [
                    'top_number' => $answerData['top_number'],
                    'bottom_number' => $answerData['bottom_number'],
                    'user_answer' => $answerData['user_answer'],
                    'correct_answer' => $correctAnswer,
                    'is_correct' => $answerData['user_answer'] === $correctAnswer,
                    'answered_at' => now(),
                ]
            );
        }

        return back();
    }

    public function submit(Request $request, KraepelinAttempt $attempt): RedirectResponse
    {
        $testAttempt = $attempt->testAttempt;
        $this->authorize('update', $testAttempt);

        if ($attempt->status !== 'in_progress') {
            return back()->with('error', 'Test sudah selesai.');
        }

        $this->submitAttempt($attempt);

        return redirect()->route('user.kraepelin.result', $attempt)
            ->with('success', 'Test Kraepelin berhasil disubmit.');
    }

    public function result(KraepelinAttempt $attempt): Response
    {
        $testAttempt = $attempt->testAttempt;
        $this->authorize('view', $testAttempt);

        $testAttempt->load(['test.program:id,name', 'test.testType:id,name']);

        return Inertia::render('User/Tests/Kraepelin/Result', compact('attempt', 'testAttempt'));
    }

    protected function submitAttempt(KraepelinAttempt $attempt): void
    {
        $answers = $attempt->answers()->get();

        $totalAnswered = $answers->count();
        $totalCorrect = $answers->where('is_correct', true)->count();
        $totalWrong = $answers->where('is_correct', false)->count();

        $expectedTotal = ($attempt->numbers_per_column - 1) * $attempt->columns_count;
        $totalSkipped = $expectedTotal - $totalAnswered;

        // Speed: answers per column
        $speedScore = $attempt->columns_count > 0
            ? round($totalAnswered / $attempt->columns_count, 2)
            : 0;

        // Accuracy: correct / answered
        $accuracyScore = $totalAnswered > 0
            ? round(($totalCorrect / $totalAnswered) * 100, 2)
            : 0;

        // Stability: standard deviation of answers per column
        $answersPerColumn = $answers->groupBy('kraepelin_attempt_column_id')
            ->map->count();

        $stabilityScore = 0;
        if ($answersPerColumn->count() > 1) {
            $mean = $answersPerColumn->avg();
            $variance = $answersPerColumn->map(fn ($c) => pow($c - $mean, 2))->avg();
            $stabilityScore = round(100 - sqrt($variance), 2);
        }

        $finalScore = round(($speedScore + $accuracyScore + max($stabilityScore, 0)) / 3, 2);

        $attempt->update([
            'total_answered' => $totalAnswered,
            'total_correct' => $totalCorrect,
            'total_wrong' => $totalWrong,
            'total_skipped' => max($totalSkipped, 0),
            'speed_score' => $speedScore,
            'accuracy_score' => $accuracyScore,
            'stability_score' => max($stabilityScore, 0),
            'final_score' => $finalScore,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $attempt->testAttempt->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'total_score' => $finalScore,
        ]);
    }
}
