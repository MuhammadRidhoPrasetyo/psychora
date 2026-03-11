<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Test;
use App\Models\TestPackage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProgramCatalogController extends Controller
{
    public function index(): Response
    {
        $programs = Program::where('is_active', true)
            ->withCount('testTypes')
            ->get();

        return Inertia::render('User/Programs/Index', compact('programs'));
    }

    public function show(Program $program): Response
    {
        $program->load(['testTypes' => fn ($q) => $q->where('is_active', true)]);

        $packages = TestPackage::where('program_id', $program->id)
            ->where('is_active', true)
            ->withCount('items')
            ->get();

        return Inertia::render('User/Programs/Show', compact('program', 'packages'));
    }

    public function showPackage(TestPackage $package): Response
    {
        $package->load(['program', 'items.test.testType']);

        return Inertia::render('User/Programs/Package', compact('package'));
    }

    public function showTest(Test $test): Response
    {
        $user = request()->user();

        $test->load(['program:id,name', 'testType:id,name,engine_type']);

        $attemptCount = $user->testAttempts()->where('test_id', $test->id)->count();
        $lastAttempt = $user->testAttempts()
            ->where('test_id', $test->id)
            ->latest()
            ->first();

        return Inertia::render('User/Programs/Test', compact('test', 'attemptCount', 'lastAttempt'));
    }
}
