<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SectionController extends Controller
{
    public function store(Request $request, Test $test): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'duration_minutes' => 'nullable|integer|min:1',
        ]);

        $test->sections()->create($validated);

        return redirect()->route('admin.tests.show', $test)
            ->with('success', 'Seksi berhasil ditambahkan.');
    }

    public function update(Request $request, Test $test, TestSection $section): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'duration_minutes' => 'nullable|integer|min:1',
        ]);

        $section->update($validated);

        return redirect()->route('admin.tests.show', $test)
            ->with('success', 'Seksi berhasil diperbarui.');
    }

    public function destroy(Test $test, TestSection $section): RedirectResponse
    {
        $section->questions()->each(function ($question) {
            $question->options()->delete();
            $question->delete();
        });
        $section->delete();

        return redirect()->route('admin.tests.show', $test)
            ->with('success', 'Seksi berhasil dihapus.');
    }
}
