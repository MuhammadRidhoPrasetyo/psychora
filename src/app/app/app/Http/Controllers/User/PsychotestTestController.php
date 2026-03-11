<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\Psychotest\PsychotestForm;
use App\Models\Psychotest\PsychotestAttempt;
use App\Models\Psychotest\PsychotestAnswer;
use App\Models\Psychotest\PsychotestResultCharacteristic;
use App\Models\Psychotest\PsychotestResultAspect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PsychotestTestController extends Controller
{
    public function start(Request $request, Test $test): RedirectResponse
    {
        $user = $request->user();
        $form = PsychotestForm::where('test_id', $test->id)->firstOrFail();

        // Check for existing in-progress attempt
        $existingAttempt = TestAttempt::where('user_id', $user->id)
            ->where('test_id', $test->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            $psychotestAttempt = PsychotestAttempt::where('test_attempt_id', $existingAttempt->id)->first();
            if ($psychotestAttempt) {
                return redirect()->route('user.psychotest.take', $psychotestAttempt);
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
            'expired_at' => $test->duration_minutes ? now()->addMinutes($test->duration_minutes) : null,
        ]);

        $psychotestAttempt = PsychotestAttempt::create([
            'test_attempt_id' => $testAttempt->id,
            'psychotest_form_id' => $form->id,
            'attempt_number' => $attemptNo,
            'status' => 'in_progress',
            'started_at' => now(),
            'deadline_at' => $test->duration_minutes ? now()->addMinutes($test->duration_minutes) : null,
        ]);

        return redirect()->route('user.psychotest.take', $psychotestAttempt);
    }

    public function take(Request $request, PsychotestAttempt $attempt): Response|RedirectResponse
    {
        $testAttempt = $attempt->testAttempt;
        $this->authorize('view', $testAttempt);

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('user.psychotest.result', $attempt);
        }

        if ($attempt->deadline_at && $attempt->deadline_at->isPast()) {
            $this->submitAttempt($attempt);
            return redirect()->route('user.psychotest.result', $attempt);
        }

        $form = $attempt->form;
        $questions = $form->questions()
            ->where('is_active', true)
            ->with('options')
            ->orderBy('number')
            ->get();

        $answers = $attempt->answers()
            ->get()
            ->keyBy('psychotest_question_id');

        return Inertia::render('User/Tests/Psychotest/Take', compact('attempt', 'form', 'questions', 'answers'));
    }

    public function saveAnswer(Request $request, PsychotestAttempt $attempt): RedirectResponse
    {
        $testAttempt = $attempt->testAttempt;
        $this->authorize('update', $testAttempt);

        if ($attempt->status !== 'in_progress') {
            return back()->with('error', 'Test sudah selesai.');
        }

        $validated = $request->validate([
            'psychotest_question_id' => ['required', 'exists:psychotest_questions,id'],
            'psychotest_option_id' => ['required', 'exists:psychotest_question_options,id'],
        ]);

        $attempt->answers()->updateOrCreate(
            ['psychotest_question_id' => $validated['psychotest_question_id']],
            [
                'psychotest_option_id' => $validated['psychotest_option_id'],
                'answered_at' => now(),
            ]
        );

        return back();
    }

    public function submit(Request $request, PsychotestAttempt $attempt): RedirectResponse
    {
        $testAttempt = $attempt->testAttempt;
        $this->authorize('update', $testAttempt);

        if ($attempt->status !== 'in_progress') {
            return back()->with('error', 'Test sudah selesai.');
        }

        $this->submitAttempt($attempt);

        return redirect()->route('user.psychotest.result', $attempt)
            ->with('success', 'Test psikotes berhasil disubmit.');
    }

    public function result(PsychotestAttempt $attempt): Response
    {
        $testAttempt = $attempt->testAttempt;
        $this->authorize('view', $testAttempt);

        $attempt->load([
            'form',
            'resultCharacteristics.characteristic.aspect',
            'resultAspects.aspect',
        ]);

        $testAttempt->load(['test.program:id,name', 'test.testType:id,name']);

        return Inertia::render('User/Tests/Psychotest/Result', compact('attempt', 'testAttempt'));
    }

    protected function submitAttempt(PsychotestAttempt $attempt): void
    {
        $answers = $attempt->answers()
            ->with('option.characteristicMappings')
            ->get();

        // Calculate scores per characteristic
        $characteristicScores = [];
        $aspectScores = [];

        foreach ($answers as $answer) {
            if (!$answer->option) {
                continue;
            }

            foreach ($answer->option->characteristicMappings as $mapping) {
                $charId = $mapping->psychotest_characteristic_id;
                $aspectId = $mapping->psychotest_aspect_id;

                if (!isset($characteristicScores[$charId])) {
                    $characteristicScores[$charId] = 0;
                }
                $characteristicScores[$charId] += $mapping->weight;

                if (!isset($aspectScores[$aspectId])) {
                    $aspectScores[$aspectId] = 0;
                }
                $aspectScores[$aspectId] += $mapping->weight;
            }
        }

        // Save characteristic results
        foreach ($characteristicScores as $characteristicId => $rawScore) {
            $maxWeight = $attempt->form->questions()
                ->where('is_active', true)
                ->count();
            $scaledScore = $maxWeight > 0 ? round(($rawScore / max($maxWeight, 1)) * 10) : 0;

            PsychotestResultCharacteristic::updateOrCreate(
                [
                    'psychotest_attempt_id' => $attempt->id,
                    'psychotest_characteristic_id' => $characteristicId,
                ],
                [
                    'raw_score' => $rawScore,
                    'scaled_score' => min($scaledScore, 10),
                ]
            );
        }

        // Save aspect results (aggregation)
        foreach ($aspectScores as $aspectId => $rawScore) {
            $characteristicCount = \App\Models\Psychotest\PsychotestCharacteristic::where('psychotest_aspect_id', $aspectId)->count();
            $scaledScore = $characteristicCount > 0 ? round($rawScore / max($characteristicCount, 1), 2) : 0;

            PsychotestResultAspect::updateOrCreate(
                [
                    'psychotest_attempt_id' => $attempt->id,
                    'psychotest_aspect_id' => $aspectId,
                ],
                [
                    'raw_score' => $rawScore,
                    'scaled_score' => $scaledScore,
                ]
            );
        }

        $totalScore = array_sum($characteristicScores);

        $attempt->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'score' => $totalScore,
        ]);

        $attempt->testAttempt->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'total_score' => $totalScore,
        ]);
    }
}
