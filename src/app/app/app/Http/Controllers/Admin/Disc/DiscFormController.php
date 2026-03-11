<?php

namespace App\Http\Controllers\Admin\Disc;

use App\Http\Controllers\Controller;
use App\Models\Disc\DiscForm;
use App\Models\Test;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DiscFormController extends Controller
{
    public function index(Request $request): Response
    {
        $forms = DiscForm::with('test:id,title')
            ->withCount('questions')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Disc/Forms/Index', compact('forms'));
    }

    public function create(): Response
    {
        $tests = Test::whereHas('testType', fn ($q) => $q->where('engine_type', 'disc'))
            ->get(['id', 'title']);

        return Inertia::render('Admin/Disc/Forms/Create', compact('tests'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'test_id' => ['required', 'exists:tests,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        DiscForm::create($validated);

        return redirect()->route('admin.disc.forms.index')
            ->with('success', 'Form DISC berhasil dibuat.');
    }

    public function show(DiscForm $form): Response
    {
        $form->load(['test', 'questions.options.scorings']);

        return Inertia::render('Admin/Disc/Forms/Show', compact('form'));
    }

    public function edit(DiscForm $form): Response
    {
        $tests = Test::whereHas('testType', fn ($q) => $q->where('engine_type', 'disc'))
            ->get(['id', 'title']);

        return Inertia::render('Admin/Disc/Forms/Edit', compact('form', 'tests'));
    }

    public function update(Request $request, DiscForm $form): RedirectResponse
    {
        $validated = $request->validate([
            'test_id' => ['required', 'exists:tests,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $form->update($validated);

        return redirect()->route('admin.disc.forms.index')
            ->with('success', 'Form DISC berhasil diperbarui.');
    }

    public function destroy(DiscForm $form): RedirectResponse
    {
        $form->delete();

        return redirect()->route('admin.disc.forms.index')
            ->with('success', 'Form DISC berhasil dihapus.');
    }
}
