<?php

namespace App\Http\Controllers\Admin\Ist;

use App\Http\Controllers\Controller;
use App\Models\Ist\IstForm;
use App\Models\Ist\IstSubtest;
use App\Models\Ist\IstClue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IstClueController extends Controller
{
    public function index(IstForm $form): Response
    {
        $clues = IstClue::where('ist_form_id', $form->id)
            ->orWhereIn('ist_subtest_id', $form->subtests()->pluck('id'))
            ->with('subtest:id,subtest_code,subtest_name')
            ->get();

        $subtests = $form->subtests()->get(['id', 'subtest_code', 'subtest_name']);

        return Inertia::render('Admin/Ist/Clues/Index', compact('form', 'clues', 'subtests'));
    }

    public function store(Request $request, IstForm $form): RedirectResponse
    {
        $validated = $request->validate([
            'ist_subtest_id' => ['nullable', 'exists:ist_subtests,id'],
            'clue' => ['required', 'string'],
            'duration' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['ist_form_id'] = $validated['ist_subtest_id'] ? null : $form->id;

        IstClue::create($validated);

        return back()->with('success', 'Clue IST berhasil ditambahkan.');
    }

    public function update(Request $request, IstForm $form, IstClue $clue): RedirectResponse
    {
        $validated = $request->validate([
            'ist_subtest_id' => ['nullable', 'exists:ist_subtests,id'],
            'clue' => ['required', 'string'],
            'duration' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['ist_form_id'] = $validated['ist_subtest_id'] ? null : $form->id;

        $clue->update($validated);

        return back()->with('success', 'Clue IST berhasil diperbarui.');
    }

    public function destroy(IstForm $form, IstClue $clue): RedirectResponse
    {
        $clue->delete();

        return back()->with('success', 'Clue IST berhasil dihapus.');
    }
}
