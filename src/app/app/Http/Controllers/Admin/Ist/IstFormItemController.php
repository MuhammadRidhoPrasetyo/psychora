<?php

namespace App\Http\Controllers\Admin\Ist;

use App\Http\Controllers\Controller;
use App\Models\Ist\IstForm;
use App\Models\Ist\IstSubtest;
use App\Models\Ist\IstFormItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IstFormItemController extends Controller
{
    public function index(IstForm $form): Response
    {
        $formItems = $form->formItems()
            ->with('subtest:id,subtest_code,subtest_name')
            ->orderBy('sort_order')
            ->get();

        $subtests = IstSubtest::where('ist_form_id', $form->id)->get(['id', 'subtest_code', 'subtest_name']);

        return Inertia::render('Admin/Ist/FormItems/Index', compact('form', 'formItems', 'subtests'));
    }

    public function store(Request $request, IstForm $form): RedirectResponse
    {
        $validated = $request->validate([
            'ist_subtest_id' => ['required', 'exists:ist_subtests,id'],
            'is_randomized' => ['boolean'],
            'number_of_questions' => ['required', 'integer', 'min:1'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'minimum_score' => ['nullable', 'integer', 'min:0'],
            'multiplier' => ['required', 'numeric', 'min:0'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'clue_first' => ['boolean'],
        ]);

        $validated['ist_form_id'] = $form->id;
        IstFormItem::create($validated);

        return back()->with('success', 'Form item IST berhasil ditambahkan.');
    }

    public function update(Request $request, IstForm $form, IstFormItem $formItem): RedirectResponse
    {
        $validated = $request->validate([
            'ist_subtest_id' => ['required', 'exists:ist_subtests,id'],
            'is_randomized' => ['boolean'],
            'number_of_questions' => ['required', 'integer', 'min:1'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'minimum_score' => ['nullable', 'integer', 'min:0'],
            'multiplier' => ['required', 'numeric', 'min:0'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'clue_first' => ['boolean'],
        ]);

        $formItem->update($validated);

        return back()->with('success', 'Form item IST berhasil diperbarui.');
    }

    public function destroy(IstForm $form, IstFormItem $formItem): RedirectResponse
    {
        $formItem->delete();

        return back()->with('success', 'Form item IST berhasil dihapus.');
    }
}
