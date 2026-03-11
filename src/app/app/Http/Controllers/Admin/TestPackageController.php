<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestPackage;
use App\Models\TestPackageItem;
use App\Models\Program;
use App\Models\Test;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TestPackageController extends Controller
{
    public function index(Request $request): Response
    {
        $packages = TestPackage::with('program:id,name')
            ->withCount('items')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->program_id, fn ($q, $p) => $q->where('program_id', $p))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $programs = Program::where('is_active', true)->get(['id', 'name']);

        return Inertia::render('Admin/TestPackages/Index', compact('packages', 'programs'));
    }

    public function create(): Response
    {
        $programs = Program::where('is_active', true)->get(['id', 'name']);
        $tests = Test::where('status', 'published')->get(['id', 'title']);

        return Inertia::render('Admin/TestPackages/Create', compact('programs', 'tests'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'program_id' => ['required', 'exists:programs,id'],
            'code' => ['required', 'string', 'max:50', 'unique:test_packages,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_premium' => ['boolean'],
            'is_active' => ['boolean'],
            'items' => ['nullable', 'array'],
            'items.*.test_id' => ['required', 'exists:tests,id'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $package = TestPackage::create([
            'program_id' => $validated['program_id'],
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_premium' => $validated['is_premium'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                $package->items()->create($item);
            }
        }

        return redirect()->route('admin.test-packages.index')
            ->with('success', 'Paket tes berhasil dibuat.');
    }

    public function show(TestPackage $testPackage): Response
    {
        $testPackage->load(['program', 'items.test']);

        return Inertia::render('Admin/TestPackages/Show', compact('testPackage'));
    }

    public function edit(TestPackage $testPackage): Response
    {
        $testPackage->load('items');
        $programs = Program::where('is_active', true)->get(['id', 'name']);
        $tests = Test::where('status', 'published')->get(['id', 'title']);

        return Inertia::render('Admin/TestPackages/Edit', compact('testPackage', 'programs', 'tests'));
    }

    public function update(Request $request, TestPackage $testPackage): RedirectResponse
    {
        $validated = $request->validate([
            'program_id' => ['required', 'exists:programs,id'],
            'code' => ['required', 'string', 'max:50', Rule::unique('test_packages')->ignore($testPackage->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_premium' => ['boolean'],
            'is_active' => ['boolean'],
            'items' => ['nullable', 'array'],
            'items.*.test_id' => ['required', 'exists:tests,id'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $testPackage->update([
            'program_id' => $validated['program_id'],
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_premium' => $validated['is_premium'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $testPackage->items()->delete();
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                $testPackage->items()->create($item);
            }
        }

        return redirect()->route('admin.test-packages.index')
            ->with('success', 'Paket tes berhasil diperbarui.');
    }

    public function destroy(TestPackage $testPackage): RedirectResponse
    {
        $testPackage->delete();

        return redirect()->route('admin.test-packages.index')
            ->with('success', 'Paket tes berhasil dihapus.');
    }
}
