<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\Program;
use App\Models\TestType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TestController extends Controller
{
    public function index(Request $request): Response
    {
        $tests = Test::with(['program:id,name', 'testType:id,name,engine_type'])
            ->withCount(['sections', 'questions'])
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->program_id, fn ($q, $p) => $q->where('program_id', $p))
            ->when($request->test_type_id, fn ($q, $t) => $q->where('test_type_id', $t))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $programs = Program::where('is_active', true)->get(['id', 'name']);
        $testTypes = TestType::where('is_active', true)->get(['id', 'name', 'engine_type']);

        return Inertia::render('Admin/Tests/Index', compact('tests', 'programs', 'testTypes'));
    }

    public function create(): Response
    {
        $programs = Program::where('is_active', true)->get(['id', 'name']);
        $testTypes = TestType::where('is_active', true)->get(['id', 'name', 'engine_type']);

        return Inertia::render('Admin/Tests/Create', compact('programs', 'testTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'program_id' => ['required', 'exists:programs,id'],
            'test_type_id' => ['required', 'exists:test_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'instruction' => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'total_questions' => ['nullable', 'integer', 'min:1'],
            'scoring_method' => ['required', Rule::in(['standard', 'weighted', 'profile', 'manual'])],
            'visibility' => ['required', Rule::in(['free', 'premium', 'private'])],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        $validated['created_by'] = $request->user()->id;

        Test::create($validated);

        return redirect()->route('admin.tests.index')
            ->with('success', 'Test berhasil dibuat.');
    }

    public function show(Test $test): Response
    {
        $test->load(['program', 'testType', 'sections.questions.options', 'questions.options', 'questions.essayAnswers']);

        return Inertia::render('Admin/Tests/Show', compact('test'));
    }

    public function edit(Test $test): Response
    {
        $programs = Program::where('is_active', true)->get(['id', 'name']);
        $testTypes = TestType::where('is_active', true)->get(['id', 'name', 'engine_type']);

        return Inertia::render('Admin/Tests/Edit', compact('test', 'programs', 'testTypes'));
    }

    public function update(Request $request, Test $test): RedirectResponse
    {
        $validated = $request->validate([
            'program_id' => ['required', 'exists:programs,id'],
            'test_type_id' => ['required', 'exists:test_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'instruction' => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'total_questions' => ['nullable', 'integer', 'min:1'],
            'scoring_method' => ['required', Rule::in(['standard', 'weighted', 'profile', 'manual'])],
            'visibility' => ['required', Rule::in(['free', 'premium', 'private'])],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
        ]);

        $test->update($validated);

        return redirect()->route('admin.tests.index')
            ->with('success', 'Test berhasil diperbarui.');
    }

    public function destroy(Test $test): RedirectResponse
    {
        $test->delete();

        return redirect()->route('admin.tests.index')
            ->with('success', 'Test berhasil dihapus.');
    }

    public function updateStatus(Request $request, Test $test): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
        ]);

        $test->update($validated);

        return back()->with('success', 'Status test berhasil diperbarui.');
    }
}
