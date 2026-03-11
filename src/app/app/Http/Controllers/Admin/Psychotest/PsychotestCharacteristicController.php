<?php

namespace App\Http\Controllers\Admin\Psychotest;

use App\Http\Controllers\Controller;
use App\Models\Psychotest\PsychotestAspect;
use App\Models\Psychotest\PsychotestCharacteristic;
use App\Models\Psychotest\PsychotestCharacteristicScore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PsychotestCharacteristicController extends Controller
{
    public function index(PsychotestAspect $aspect): Response
    {
        $characteristics = $aspect->characteristics()
            ->withCount('scores')
            ->orderBy('sort_order')
            ->paginate(15);

        return Inertia::render('Admin/Psychotest/Characteristics/Index', compact('aspect', 'characteristics'));
    }

    public function create(PsychotestAspect $aspect): Response
    {
        return Inertia::render('Admin/Psychotest/Characteristics/Create', compact('aspect'));
    }

    public function store(Request $request, PsychotestAspect $aspect): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('psychotest_characteristics')->where('psychotest_aspect_id', $aspect->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'scores' => ['nullable', 'array'],
            'scores.*.score' => ['required', 'integer', 'min:0'],
            'scores.*.description' => ['required', 'string'],
        ]);

        $characteristic = $aspect->characteristics()->create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'],
        ]);

        if (!empty($validated['scores'])) {
            foreach ($validated['scores'] as $score) {
                $characteristic->scores()->create($score);
            }
        }

        return redirect()->route('admin.psychotest.characteristics.index', $aspect)
            ->with('success', 'Karakteristik psikotes berhasil dibuat.');
    }

    public function show(PsychotestAspect $aspect, PsychotestCharacteristic $characteristic): Response
    {
        $characteristic->load('scores');

        return Inertia::render('Admin/Psychotest/Characteristics/Show', compact('aspect', 'characteristic'));
    }

    public function edit(PsychotestAspect $aspect, PsychotestCharacteristic $characteristic): Response
    {
        $characteristic->load('scores');

        return Inertia::render('Admin/Psychotest/Characteristics/Edit', compact('aspect', 'characteristic'));
    }

    public function update(Request $request, PsychotestAspect $aspect, PsychotestCharacteristic $characteristic): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('psychotest_characteristics')
                ->where('psychotest_aspect_id', $aspect->id)
                ->ignore($characteristic->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'scores' => ['nullable', 'array'],
            'scores.*.id' => ['nullable', 'exists:psychotest_characteristic_scores,id'],
            'scores.*.score' => ['required', 'integer', 'min:0'],
            'scores.*.description' => ['required', 'string'],
        ]);

        $characteristic->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'],
        ]);

        if (isset($validated['scores'])) {
            $existingIds = collect($validated['scores'])->pluck('id')->filter()->toArray();
            $characteristic->scores()->whereNotIn('id', $existingIds)->delete();

            foreach ($validated['scores'] as $scoreData) {
                if (!empty($scoreData['id'])) {
                    $characteristic->scores()->where('id', $scoreData['id'])->update($scoreData);
                } else {
                    $characteristic->scores()->create($scoreData);
                }
            }
        }

        return redirect()->route('admin.psychotest.characteristics.index', $aspect)
            ->with('success', 'Karakteristik psikotes berhasil diperbarui.');
    }

    public function destroy(PsychotestAspect $aspect, PsychotestCharacteristic $characteristic): RedirectResponse
    {
        $characteristic->delete();

        return redirect()->route('admin.psychotest.characteristics.index', $aspect)
            ->with('success', 'Karakteristik psikotes berhasil dihapus.');
    }
}
