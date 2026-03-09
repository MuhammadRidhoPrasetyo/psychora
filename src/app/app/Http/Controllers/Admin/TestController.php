<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class TestController extends Controller
{
    public function index(Request $request): Response
    {
        $tests = Test::with('testType')
            ->when($request->search, fn ($q, $s) => $q->where('title', 'ilike', "%{$s}%"))
            ->when($request->test_type_id, fn ($q, $id) => $q->where('test_type_id', $id))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/Tests/Index', [
            'tests' => $tests,
            'testTypes' => TestType::all(),
            'filters' => $request->only('search', 'test_type_id'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/Tests/Form', [
            'testTypes' => TestType::all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'test_type_id' => 'required|exists:test_types,id',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'total_questions' => 'required|integer|min:1',
            'passing_score' => 'nullable|numeric|min:0',
            'is_published' => 'boolean',
            'is_free' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        Test::create($validated);

        return redirect()->route('admin.tests.index')
            ->with('success', 'Tes berhasil ditambahkan.');
    }

    public function show(Test $test): Response
    {
        $test->load(['testType', 'sections.questions.options']);

        return Inertia::render('admin/Tests/Show', [
            'test' => $test,
        ]);
    }

    public function edit(Test $test): Response
    {
        return Inertia::render('admin/Tests/Form', [
            'test' => $test,
            'testTypes' => TestType::all(),
        ]);
    }

    public function update(Request $request, Test $test): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'test_type_id' => 'required|exists:test_types,id',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'total_questions' => 'required|integer|min:1',
            'passing_score' => 'nullable|numeric|min:0',
            'is_published' => 'boolean',
            'is_free' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        $test->update($validated);

        return redirect()->route('admin.tests.index')
            ->with('success', 'Tes berhasil diperbarui.');
    }

    public function destroy(Test $test): RedirectResponse
    {
        $test->delete();

        return redirect()->route('admin.tests.index')
            ->with('success', 'Tes berhasil dihapus.');
    }
}
