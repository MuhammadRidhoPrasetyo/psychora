<?php

namespace App\Http\Controllers\Admin\Cpns;

use App\Http\Controllers\Controller;
use App\Models\Cpns\CpnsScoreRule;
use App\Models\TestType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CpnsScoreRuleController extends Controller
{
    public function index(Request $request): Response
    {
        $rules = CpnsScoreRule::with('testType:id,name')
            ->when($request->category_code, fn ($q, $c) => $q->where('category_code', $c))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $testTypes = TestType::where('engine_type', 'generic')->get(['id', 'name']);

        return Inertia::render('Admin/Cpns/ScoreRules/Index', compact('rules', 'testTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'test_type_id' => ['required', 'exists:test_types,id'],
            'category_code' => ['required', Rule::in(['TWK', 'TIU', 'TKP'])],
            'correct_score' => ['required', 'numeric'],
            'wrong_score' => ['required', 'numeric'],
            'empty_score' => ['required', 'numeric'],
        ]);

        CpnsScoreRule::create($validated);

        return back()->with('success', 'Score rule CPNS berhasil dibuat.');
    }

    public function update(Request $request, CpnsScoreRule $scoreRule): RedirectResponse
    {
        $validated = $request->validate([
            'test_type_id' => ['required', 'exists:test_types,id'],
            'category_code' => ['required', Rule::in(['TWK', 'TIU', 'TKP'])],
            'correct_score' => ['required', 'numeric'],
            'wrong_score' => ['required', 'numeric'],
            'empty_score' => ['required', 'numeric'],
        ]);

        $scoreRule->update($validated);

        return back()->with('success', 'Score rule CPNS berhasil diperbarui.');
    }

    public function destroy(CpnsScoreRule $scoreRule): RedirectResponse
    {
        $scoreRule->delete();

        return back()->with('success', 'Score rule CPNS berhasil dihapus.');
    }
}
