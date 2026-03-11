<?php

namespace App\Http\Controllers\Admin\Ist;

use App\Http\Controllers\Controller;
use App\Models\Ist\IstForm;
use App\Models\Test;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IstFormController extends Controller
{
    public function index(Request $request): Response
    {
        $forms = IstForm::with('test:id,title')
            ->withCount('subtests')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Ist/Forms/Index', compact('forms'));
    }

    public function create(): Response
    {
        $tests = Test::whereHas('testType', fn ($q) => $q->where('engine_type', 'ist'))
            ->get(['id', 'title']);

        return Inertia::render('Admin/Ist/Forms/Create', compact('tests'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'test_id' => ['required', 'exists:tests,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        IstForm::create($validated);

        return redirect()->route('admin.ist.forms.index')
            ->with('success', 'Form IST berhasil dibuat.');
    }

    public function show(IstForm $form): Response
    {
        $form->load(['test', 'subtests', 'formItems.subtest', 'instructions', 'clues']);

        return Inertia::render('Admin/Ist/Forms/Show', compact('form'));
    }

    public function edit(IstForm $form): Response
    {
        $tests = Test::whereHas('testType', fn ($q) => $q->where('engine_type', 'ist'))
            ->get(['id', 'title']);

        return Inertia::render('Admin/Ist/Forms/Edit', compact('form', 'tests'));
    }

    public function update(Request $request, IstForm $form): RedirectResponse
    {
        $validated = $request->validate([
            'test_id' => ['required', 'exists:tests,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $form->update($validated);

        return redirect()->route('admin.ist.forms.index')
            ->with('success', 'Form IST berhasil diperbarui.');
    }

    public function destroy(IstForm $form): RedirectResponse
    {
        $form->delete();

        return redirect()->route('admin.ist.forms.index')
            ->with('success', 'Form IST berhasil dihapus.');
    }
}
