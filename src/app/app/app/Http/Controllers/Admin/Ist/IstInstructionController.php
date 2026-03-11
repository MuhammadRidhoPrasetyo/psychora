<?php

namespace App\Http\Controllers\Admin\Ist;

use App\Http\Controllers\Controller;
use App\Models\Ist\IstForm;
use App\Models\Ist\IstSubtest;
use App\Models\Ist\IstInstruction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IstInstructionController extends Controller
{
    public function index(IstForm $form): Response
    {
        $instructions = IstInstruction::where('ist_form_id', $form->id)
            ->orWhereIn('ist_subtest_id', $form->subtests()->pluck('id'))
            ->with('subtest:id,subtest_code,subtest_name')
            ->orderBy('sort_order')
            ->get();

        $subtests = $form->subtests()->get(['id', 'subtest_code', 'subtest_name']);

        return Inertia::render('Admin/Ist/Instructions/Index', compact('form', 'instructions', 'subtests'));
    }

    public function store(Request $request, IstForm $form): RedirectResponse
    {
        $validated = $request->validate([
            'ist_subtest_id' => ['nullable', 'exists:ist_subtests,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $validated['ist_form_id'] = $validated['ist_subtest_id'] ? null : $form->id;

        IstInstruction::create($validated);

        return back()->with('success', 'Instruksi IST berhasil ditambahkan.');
    }

    public function update(Request $request, IstForm $form, IstInstruction $instruction): RedirectResponse
    {
        $validated = $request->validate([
            'ist_subtest_id' => ['nullable', 'exists:ist_subtests,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $validated['ist_form_id'] = $validated['ist_subtest_id'] ? null : $form->id;

        $instruction->update($validated);

        return back()->with('success', 'Instruksi IST berhasil diperbarui.');
    }

    public function destroy(IstForm $form, IstInstruction $instruction): RedirectResponse
    {
        $instruction->delete();

        return back()->with('success', 'Instruksi IST berhasil dihapus.');
    }
}
