<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TestSectionController extends Controller
{
    public function index(Test $test): Response
    {
        $sections = $test->sections()
            ->withCount('questions')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Admin/Tests/Sections/Index', compact('test', 'sections'));
    }

    public function store(Request $request, Test $test): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'instruction' => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $test->sections()->create($validated);

        return back()->with('success', 'Section berhasil ditambahkan.');
    }

    public function update(Request $request, Test $test, TestSection $section): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'instruction' => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $section->update($validated);

        return back()->with('success', 'Section berhasil diperbarui.');
    }

    public function destroy(Test $test, TestSection $section): RedirectResponse
    {
        $section->delete();

        return back()->with('success', 'Section berhasil dihapus.');
    }

    public function reorder(Request $request, Test $test): RedirectResponse
    {
        $validated = $request->validate([
            'sections' => ['required', 'array'],
            'sections.*.id' => ['required', 'exists:test_sections,id'],
            'sections.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($validated['sections'] as $item) {
            TestSection::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return back()->with('success', 'Urutan section berhasil diperbarui.');
    }
}
