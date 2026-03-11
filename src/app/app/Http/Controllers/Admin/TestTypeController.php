<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TestTypeController extends Controller
{
    public function index(Request $request): Response
    {
        $testTypes = TestType::when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->engine_type, fn ($q, $e) => $q->where('engine_type', $e))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/TestTypes/Index', compact('testTypes'));
    }

    public function create(): Response
    {
        return Inertia::render('Admin/TestTypes/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:test_types,code'],
            'name' => ['required', 'string', 'max:255'],
            'engine_type' => ['required', Rule::in(['generic', 'disc', 'ist', 'kraepelin', 'psychotest'])],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        TestType::create($validated);

        return redirect()->route('admin.test-types.index')
            ->with('success', 'Jenis tes berhasil dibuat.');
    }

    public function edit(TestType $testType): Response
    {
        return Inertia::render('Admin/TestTypes/Edit', compact('testType'));
    }

    public function update(Request $request, TestType $testType): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('test_types')->ignore($testType->id)],
            'name' => ['required', 'string', 'max:255'],
            'engine_type' => ['required', Rule::in(['generic', 'disc', 'ist', 'kraepelin', 'psychotest'])],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $testType->update($validated);

        return redirect()->route('admin.test-types.index')
            ->with('success', 'Jenis tes berhasil diperbarui.');
    }

    public function destroy(TestType $testType): RedirectResponse
    {
        $testType->delete();

        return redirect()->route('admin.test-types.index')
            ->with('success', 'Jenis tes berhasil dihapus.');
    }
}
