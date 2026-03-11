<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestAttempt;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TestAttemptController extends Controller
{
    public function index(Request $request): Response
    {
        $attempts = TestAttempt::with([
                'user:id,name,email',
                'test:id,title,program_id,test_type_id',
                'test.program:id,name',
                'test.testType:id,name',
            ])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->user_id, fn ($q, $u) => $q->where('user_id', $u))
            ->when($request->test_id, fn ($q, $t) => $q->where('test_id', $t))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/TestAttempts/Index', compact('attempts'));
    }

    public function show(TestAttempt $attempt): Response
    {
        $attempt->load([
            'user:id,name,email',
            'test.program',
            'test.testType',
            'answers.question',
            'answers.selectedOption',
            'result',
        ]);

        return Inertia::render('Admin/TestAttempts/Show', compact('attempt'));
    }
}
