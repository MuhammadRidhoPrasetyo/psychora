<?php

namespace App\Http\Controllers\Admin\Kraepelin;

use App\Http\Controllers\Controller;
use App\Models\Kraepelin\KraepelinForm;
use App\Models\Test;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KraepelinFormController extends Controller
{
    public function index(Request $request): Response
    {
        $forms = KraepelinForm::with('test:id,title')
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Kraepelin/Forms/Index', compact('forms'));
    }

    public function create(): Response
    {
        $tests = Test::whereHas('testType', fn ($q) => $q->where('engine_type', 'kraepelin'))
            ->get(['id', 'title']);

        return Inertia::render('Admin/Kraepelin/Forms/Create', compact('tests'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'test_id' => ['required', 'exists:tests,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        KraepelinForm::create($validated);

        return redirect()->route('admin.kraepelin.forms.index')
            ->with('success', 'Form Kraepelin berhasil dibuat.');
    }

    public function edit(KraepelinForm $form): Response
    {
        $tests = Test::whereHas('testType', fn ($q) => $q->where('engine_type', 'kraepelin'))
            ->get(['id', 'title']);

        return Inertia::render('Admin/Kraepelin/Forms/Edit', compact('form', 'tests'));
    }

    public function update(Request $request, KraepelinForm $form): RedirectResponse
    {
        $validated = $request->validate([
            'test_id' => ['required', 'exists:tests,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $form->update($validated);

        return redirect()->route('admin.kraepelin.forms.index')
            ->with('success', 'Form Kraepelin berhasil diperbarui.');
    }

    public function destroy(KraepelinForm $form): RedirectResponse
    {
        $form->delete();

        return redirect()->route('admin.kraepelin.forms.index')
            ->with('success', 'Form Kraepelin berhasil dihapus.');
    }
}
