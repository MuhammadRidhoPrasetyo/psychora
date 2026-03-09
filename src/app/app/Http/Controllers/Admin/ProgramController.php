<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\TestType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProgramController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/Programs/Index', [
            'programs' => Program::with('testTypes')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/Programs/Form', [
            'testTypes' => TestType::all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'test_type_ids' => 'array',
            'test_type_ids.*' => 'exists:test_types,id',
        ]);

        $program = Program::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        if (! empty($validated['test_type_ids'])) {
            $program->testTypes()->sync($validated['test_type_ids']);
        }

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program berhasil ditambahkan.');
    }

    public function edit(Program $program): Response
    {
        $program->load('testTypes');

        return Inertia::render('admin/Programs/Form', [
            'program' => $program,
            'testTypes' => TestType::all(),
        ]);
    }

    public function update(Request $request, Program $program): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'test_type_ids' => 'array',
            'test_type_ids.*' => 'exists:test_types,id',
        ]);

        $program->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        $program->testTypes()->sync($validated['test_type_ids'] ?? []);

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program berhasil diperbarui.');
    }

    public function destroy(Program $program): RedirectResponse
    {
        $program->delete();

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program berhasil dihapus.');
    }
}
