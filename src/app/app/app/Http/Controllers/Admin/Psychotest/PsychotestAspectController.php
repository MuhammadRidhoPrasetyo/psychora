<?php

namespace App\Http\Controllers\Admin\Psychotest;

use App\Http\Controllers\Controller;
use App\Models\Psychotest\PsychotestAspect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PsychotestAspectController extends Controller
{
    public function index(Request $request): Response
    {
        $aspects = PsychotestAspect::withCount('characteristics')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('code', 'like', "%{$s}%"))
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Psychotest/Aspects/Index', compact('aspects'));
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Psychotest/Aspects/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:psychotest_aspects,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        PsychotestAspect::create($validated);

        return redirect()->route('admin.psychotest.aspects.index')
            ->with('success', 'Aspek psikotes berhasil dibuat.');
    }

    public function show(PsychotestAspect $aspect): Response
    {
        $aspect->load(['characteristics.scores']);

        return Inertia::render('Admin/Psychotest/Aspects/Show', compact('aspect'));
    }

    public function edit(PsychotestAspect $aspect): Response
    {
        return Inertia::render('Admin/Psychotest/Aspects/Edit', compact('aspect'));
    }

    public function update(Request $request, PsychotestAspect $aspect): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('psychotest_aspects')->ignore($aspect->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $aspect->update($validated);

        return redirect()->route('admin.psychotest.aspects.index')
            ->with('success', 'Aspek psikotes berhasil diperbarui.');
    }

    public function destroy(PsychotestAspect $aspect): RedirectResponse
    {
        $aspect->delete();

        return redirect()->route('admin.psychotest.aspects.index')
            ->with('success', 'Aspek psikotes berhasil dihapus.');
    }
}
