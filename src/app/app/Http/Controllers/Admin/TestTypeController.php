<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class TestTypeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/TestTypes/Index', [
            'testTypes' => TestType::withCount('programs')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/TestTypes/Form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'engine_type' => 'required|in:generic,disc,ist,kraepelin,papikostick',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        TestType::create($validated);

        return redirect()->route('admin.test-types.index')
            ->with('success', 'Jenis Tes berhasil ditambahkan.');
    }

    public function edit(TestType $testType): Response
    {
        return Inertia::render('admin/TestTypes/Form', [
            'testType' => $testType,
        ]);
    }

    public function update(Request $request, TestType $testType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'engine_type' => 'required|in:generic,disc,ist,kraepelin,papikostick',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $testType->update($validated);

        return redirect()->route('admin.test-types.index')
            ->with('success', 'Jenis Tes berhasil diperbarui.');
    }

    public function destroy(TestType $testType): RedirectResponse
    {
        $testType->delete();

        return redirect()->route('admin.test-types.index')
            ->with('success', 'Jenis Tes berhasil dihapus.');
    }
}
