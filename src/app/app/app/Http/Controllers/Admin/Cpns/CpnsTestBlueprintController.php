<?php

namespace App\Http\Controllers\Admin\Cpns;

use App\Http\Controllers\Controller;
use App\Models\Cpns\CpnsTestBlueprint;
use App\Models\Test;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CpnsTestBlueprintController extends Controller
{
    public function index(Request $request): Response
    {
        $blueprints = CpnsTestBlueprint::with('test:id,title')
            ->when($request->test_id, fn ($q, $t) => $q->where('test_id', $t))
            ->when($request->category_code, fn ($q, $c) => $q->where('category_code', $c))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $tests = Test::whereHas('testType', fn ($q) => $q->where('engine_type', 'generic'))
            ->get(['id', 'title']);

        return Inertia::render('Admin/Cpns/Blueprints/Index', compact('blueprints', 'tests'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'test_id' => ['required', 'exists:tests,id'],
            'category_code' => ['required', Rule::in(['TWK', 'TIU', 'TKP'])],
            'total_questions' => ['required', 'integer', 'min:1'],
            'passing_score' => ['nullable', 'integer', 'min:0'],
        ]);

        CpnsTestBlueprint::create($validated);

        return back()->with('success', 'Blueprint CPNS berhasil dibuat.');
    }

    public function update(Request $request, CpnsTestBlueprint $blueprint): RedirectResponse
    {
        $validated = $request->validate([
            'test_id' => ['required', 'exists:tests,id'],
            'category_code' => ['required', Rule::in(['TWK', 'TIU', 'TKP'])],
            'total_questions' => ['required', 'integer', 'min:1'],
            'passing_score' => ['nullable', 'integer', 'min:0'],
        ]);

        $blueprint->update($validated);

        return back()->with('success', 'Blueprint CPNS berhasil diperbarui.');
    }

    public function destroy(CpnsTestBlueprint $blueprint): RedirectResponse
    {
        $blueprint->delete();

        return back()->with('success', 'Blueprint CPNS berhasil dihapus.');
    }
}
