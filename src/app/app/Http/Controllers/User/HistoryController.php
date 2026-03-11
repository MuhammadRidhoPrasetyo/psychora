<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TestAttempt;
use App\Models\TestResult;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HistoryController extends Controller
{
    public function attempts(Request $request): Response
    {
        $attempts = $request->user()->testAttempts()
            ->with([
                'test:id,title,program_id,test_type_id',
                'test.program:id,name',
                'test.testType:id,name,engine_type',
            ])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->test_type_id, fn ($q, $t) => $q->whereHas('test', fn ($tq) => $tq->where('test_type_id', $t)))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('User/History/Attempts', compact('attempts'));
    }

    public function results(Request $request): Response
    {
        $results = TestResult::where('user_id', $request->user()->id)
            ->with([
                'test:id,title,program_id,test_type_id',
                'test.program:id,name',
                'test.testType:id,name',
                'attempt:id,attempt_no,submitted_at',
            ])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('User/History/Results', compact('results'));
    }

    public function progress(Request $request): Response
    {
        $progress = UserProgress::where('user_id', $request->user()->id)
            ->with(['program:id,name', 'testType:id,name'])
            ->get();

        return Inertia::render('User/History/Progress', compact('progress'));
    }
}
