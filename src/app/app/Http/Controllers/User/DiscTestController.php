<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\Disc\DiscForm;
use App\Models\Disc\DiscAttempt;
use App\Models\Disc\DiscAnswer;
use App\Models\Disc\DiscResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DiscTestController extends Controller
{
    public function start(Request $request, Test $test): RedirectResponse
    {
        $user = $request->user();

        $discForm = DiscForm::where('test_id', $test->id)->firstOrFail();

        // Check for existing in-progress attempt
        $existingAttempt = TestAttempt::where('user_id', $user->id)
            ->where('test_id', $test->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            $discAttempt = DiscAttempt::where('test_attempt_id', $existingAttempt->id)->first();
            if ($discAttempt) {
                return redirect()->route('user.disc.take', $discAttempt);
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

        $discAttempt = DiscAttempt::create([
            'test_attempt_id' => $testAttempt->id,
            'disc_form_id' => $discForm->id,
            'attempt_number' => $attemptNo,
            'status' => 'in_progress',
            'started_at' => now(),
            'deadline_at' => $test->duration_minutes ? now()->addMinutes($test->duration_minutes) : null,
        ]);

        return redirect()->route('user.disc.take', $discAttempt);
    }

    public function take(Request $request, DiscAttempt $attempt): Response|RedirectResponse
    {
        $testAttempt = $attempt->testAttempt;
        $this->authorize('view', $testAttempt);

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('user.disc.result', $attempt);
        }

        if ($attempt->deadline_at && $attempt->deadline_at->isPast()) {
            $this->submitAttempt($attempt);
            return redirect()->route('user.disc.result', $attempt);
        }

        $form = $attempt->form()->with(['questions' => fn ($q) => $q->orderBy('number')])
            ->first();

        $form->questions->load('options');

        $answers = $attempt->answers()
            ->get()
            ->keyBy('disc_question_id');

        return Inertia::render('User/Tests/Disc/Take', compact('attempt', 'form', 'answers'));
    }

    public function saveAnswer(Request $request, DiscAttempt $attempt): RedirectResponse
    {
        $testAttempt = $attempt->testAttempt;
        $this->authorize('update', $testAttempt);

        if ($attempt->status !== 'in_progress') {
            return back()->with('error', 'Test sudah selesai.');
        }

        $validated = $request->validate([
            'disc_question_id' => ['required', 'exists:disc_questions,id'],
            'most_option_id' => ['required', 'exists:disc_options,id'],
            'least_option_id' => ['required', 'exists:disc_options,id', 'different:most_option_id'],
        ]);

        $attempt->answers()->updateOrCreate(
            ['disc_question_id' => $validated['disc_question_id']],
            [
                'most_option_id' => $validated['most_option_id'],
                'least_option_id' => $validated['least_option_id'],
                'answered_at' => now(),
            ]
        );

        return back();
    }

    public function submit(Request $request, DiscAttempt $attempt): RedirectResponse
    {
        $testAttempt = $attempt->testAttempt;
        $this->authorize('update', $testAttempt);

        if ($attempt->status !== 'in_progress') {
            return back()->with('error', 'Test sudah selesai.');
        }

        $this->submitAttempt($attempt);

        return redirect()->route('user.disc.result', $attempt)
            ->with('success', 'Test DISC berhasil disubmit.');
    }

    public function result(DiscAttempt $attempt): Response
    {
        $testAttempt = $attempt->testAttempt;
        $this->authorize('view', $testAttempt);

        $attempt->load(['form', 'result']);
        $testAttempt->load(['test.program:id,name', 'test.testType:id,name']);

        return Inertia::render('User/Tests/Disc/Result', compact('attempt', 'testAttempt'));
    }

    protected function submitAttempt(DiscAttempt $attempt): void
    {
        $answers = $attempt->answers()->with([
            'mostOption.scorings',
            'leastOption.scorings',
        ])->get();

        $scores = [
            'most_d' => 0, 'most_i' => 0, 'most_s' => 0, 'most_c' => 0, 'most_star' => 0,
            'least_d' => 0, 'least_i' => 0, 'least_s' => 0, 'least_c' => 0, 'least_star' => 0,
        ];

        foreach ($answers as $answer) {
            // Most option scorings
            foreach ($answer->mostOption->scorings as $scoring) {
                if ($scoring->response_type === 'most') {
                    $key = 'most_' . strtolower($scoring->disc_code);
                    if (isset($scores[$key])) {
                        $scores[$key] += $scoring->score_value;
                    }
                }
            }

            // Least option scorings
            foreach ($answer->leastOption->scorings as $scoring) {
                if ($scoring->response_type === 'least') {
                    $key = 'least_' . strtolower($scoring->disc_code);
                    if (isset($scores[$key])) {
                        $scores[$key] += $scoring->score_value;
                    }
                }
            }
        }

        $scores['score_d'] = $scores['most_d'] - $scores['least_d'];
        $scores['score_i'] = $scores['most_i'] - $scores['least_i'];
        $scores['score_s'] = $scores['most_s'] - $scores['least_s'];
        $scores['score_c'] = $scores['most_c'] - $scores['least_c'];

        // Determine dominant profile
        $profileScores = [
            'D' => $scores['score_d'],
            'I' => $scores['score_i'],
            'S' => $scores['score_s'],
            'C' => $scores['score_c'],
        ];
        arsort($profileScores);
        $scores['dominant_profile'] = implode('', array_slice(array_keys($profileScores), 0, 2));

        $attempt->result()->updateOrCreate(
            ['disc_attempt_id' => $attempt->id],
            $scores
        );

        $attempt->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $attempt->testAttempt->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }
}
