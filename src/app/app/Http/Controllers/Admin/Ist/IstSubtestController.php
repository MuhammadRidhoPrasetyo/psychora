<?php

namespace App\Http\Controllers\Admin\Ist;

use App\Http\Controllers\Controller;
use App\Models\Ist\IstForm;
use App\Models\Ist\IstSubtest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class IstSubtestController extends Controller
{
    public function index(IstForm $form): Response
    {
        $subtests = $form->subtests()
            ->withCount('questions')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Admin/Ist/Subtests/Index', compact('form', 'subtests'));
    }

    public function store(Request $request, IstForm $form): RedirectResponse
    {
        $validated = $request->validate([
            'subtest_code' => ['required', 'string', 'max:10', Rule::in(['SE', 'WA', 'AN', 'GE', 'ME', 'RA', 'ZR', 'FA', 'WU'])],
            'subtest_name' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:1', 'max:9'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'max_score' => ['nullable', 'integer', 'min:1'],
        ]);

        $form->subtests()->create($validated);

        return back()->with('success', 'Subtest IST berhasil ditambahkan.');
    }

    public function update(Request $request, IstForm $form, IstSubtest $subtest): RedirectResponse
    {
        $validated = $request->validate([
            'subtest_code' => ['required', 'string', 'max:10', Rule::in(['SE', 'WA', 'AN', 'GE', 'ME', 'RA', 'ZR', 'FA', 'WU'])],
            'subtest_name' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:1', 'max:9'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'max_score' => ['nullable', 'integer', 'min:1'],
        ]);

        $subtest->update($validated);

        return back()->with('success', 'Subtest IST berhasil diperbarui.');
    }

    public function destroy(IstForm $form, IstSubtest $subtest): RedirectResponse
    {
        $subtest->delete();

        return back()->with('success', 'Subtest IST berhasil dihapus.');
    }
}
