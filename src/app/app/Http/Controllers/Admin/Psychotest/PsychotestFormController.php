<?php

namespace App\Http\Controllers\Admin\Psychotest;

use App\Http\Controllers\Controller;
use App\Models\Psychotest\PsychotestForm;
use App\Models\Test;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PsychotestFormController extends Controller
{
    public function index(Request $request): Response
    {
        $forms = PsychotestForm::with('test:id,title')
            ->withCount('questions')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Psychotest/Forms/Index', compact('forms'));
    }

    public function create(): Response
    {
        $tests = Test::whereHas('testType', fn ($q) => $q->where('engine_type', 'psychotest'))
            ->get(['id', 'title']);

        return Inertia::render('Admin/Psychotest/Forms/Create', compact('tests'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'test_id' => ['required', 'exists:tests,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        PsychotestForm::create($validated);

        return redirect()->route('admin.psychotest.forms.index')
            ->with('success', 'Form psikotes berhasil dibuat.');
    }

    public function show(PsychotestForm $form): Response
    {
        $form->load(['test', 'questions.options.characteristicMappings.characteristic', 'questions.options.characteristicMappings.aspect']);

        return Inertia::render('Admin/Psychotest/Forms/Show', compact('form'));
    }

    public function edit(PsychotestForm $form): Response
    {
        $tests = Test::whereHas('testType', fn ($q) => $q->where('engine_type', 'psychotest'))
            ->get(['id', 'title']);

        return Inertia::render('Admin/Psychotest/Forms/Edit', compact('form', 'tests'));
    }

    public function update(Request $request, PsychotestForm $form): RedirectResponse
    {
        $validated = $request->validate([
            'test_id' => ['required', 'exists:tests,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $form->update($validated);

        return redirect()->route('admin.psychotest.forms.index')
            ->with('success', 'Form psikotes berhasil diperbarui.');
    }

    public function destroy(PsychotestForm $form): RedirectResponse
    {
        $form->delete();

        return redirect()->route('admin.psychotest.forms.index')
            ->with('success', 'Form psikotes berhasil dihapus.');
    }
}
