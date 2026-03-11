<?php

namespace App\Http\Controllers\Admin\Psychotest;

use App\Http\Controllers\Controller;
use App\Models\Psychotest\PsychotestForm;
use App\Models\Psychotest\PsychotestQuestion;
use App\Models\Psychotest\PsychotestQuestionOption;
use App\Models\Psychotest\PsychotestAspect;
use App\Models\Psychotest\PsychotestCharacteristic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PsychotestQuestionController extends Controller
{
    public function index(PsychotestForm $form): Response
    {
        $questions = $form->questions()
            ->with(['options.characteristicMappings.characteristic:id,name,code', 'options.characteristicMappings.aspect:id,name,code'])
            ->orderBy('number')
            ->paginate(15);

        return Inertia::render('Admin/Psychotest/Questions/Index', compact('form', 'questions'));
    }

    public function create(PsychotestForm $form): Response
    {
        $aspects = PsychotestAspect::with('characteristics:id,psychotest_aspect_id,code,name')
            ->orderBy('sort_order')
            ->get(['id', 'code', 'name']);

        return Inertia::render('Admin/Psychotest/Questions/Create', compact('form', 'aspects'));
    }

    public function store(Request $request, PsychotestForm $form): RedirectResponse
    {
        $validated = $request->validate([
            'number' => ['required', 'integer', 'min:1'],
            'is_active' => ['boolean'],
            'options' => ['required', 'array', 'min:2'],
            'options.*.label' => ['required', 'string', 'max:10'],
            'options.*.statement' => ['required', 'string'],
            'options.*.sort_order' => ['required', 'integer', 'min:0'],
            'options.*.mappings' => ['nullable', 'array'],
            'options.*.mappings.*.psychotest_aspect_id' => ['required', 'exists:psychotest_aspects,id'],
            'options.*.mappings.*.psychotest_characteristic_id' => ['required', 'exists:psychotest_characteristics,id'],
            'options.*.mappings.*.weight' => ['required', 'integer', 'min:0'],
        ]);

        $question = $form->questions()->create([
            'number' => $validated['number'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        foreach ($validated['options'] as $optionData) {
            $option = $question->options()->create([
                'label' => $optionData['label'],
                'statement' => $optionData['statement'],
                'sort_order' => $optionData['sort_order'],
            ]);

            if (!empty($optionData['mappings'])) {
                foreach ($optionData['mappings'] as $mapping) {
                    $option->characteristicMappings()->create([
                        'psychotest_aspect_id' => $mapping['psychotest_aspect_id'],
                        'psychotest_characteristic_id' => $mapping['psychotest_characteristic_id'],
                        'weight' => $mapping['weight'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.psychotest.questions.index', $form)
            ->with('success', 'Pertanyaan psikotes berhasil ditambahkan.');
    }

    public function edit(PsychotestForm $form, PsychotestQuestion $question): Response
    {
        $question->load('options.characteristicMappings');
        $aspects = PsychotestAspect::with('characteristics:id,psychotest_aspect_id,code,name')
            ->orderBy('sort_order')
            ->get(['id', 'code', 'name']);

        return Inertia::render('Admin/Psychotest/Questions/Edit', compact('form', 'question', 'aspects'));
    }

    public function update(Request $request, PsychotestForm $form, PsychotestQuestion $question): RedirectResponse
    {
        $validated = $request->validate([
            'number' => ['required', 'integer', 'min:1'],
            'is_active' => ['boolean'],
            'options' => ['required', 'array', 'min:2'],
            'options.*.id' => ['nullable', 'exists:psychotest_question_options,id'],
            'options.*.label' => ['required', 'string', 'max:10'],
            'options.*.statement' => ['required', 'string'],
            'options.*.sort_order' => ['required', 'integer', 'min:0'],
            'options.*.mappings' => ['nullable', 'array'],
            'options.*.mappings.*.psychotest_aspect_id' => ['required', 'exists:psychotest_aspects,id'],
            'options.*.mappings.*.psychotest_characteristic_id' => ['required', 'exists:psychotest_characteristics,id'],
            'options.*.mappings.*.weight' => ['required', 'integer', 'min:0'],
        ]);

        $question->update([
            'number' => $validated['number'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Delete existing options and mappings, then recreate
        $question->options()->each(function ($option) {
            $option->characteristicMappings()->delete();
            $option->delete();
        });

        foreach ($validated['options'] as $optionData) {
            $option = $question->options()->create([
                'label' => $optionData['label'],
                'statement' => $optionData['statement'],
                'sort_order' => $optionData['sort_order'],
            ]);

            if (!empty($optionData['mappings'])) {
                foreach ($optionData['mappings'] as $mapping) {
                    $option->characteristicMappings()->create([
                        'psychotest_aspect_id' => $mapping['psychotest_aspect_id'],
                        'psychotest_characteristic_id' => $mapping['psychotest_characteristic_id'],
                        'weight' => $mapping['weight'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.psychotest.questions.index', $form)
            ->with('success', 'Pertanyaan psikotes berhasil diperbarui.');
    }

    public function destroy(PsychotestForm $form, PsychotestQuestion $question): RedirectResponse
    {
        $question->options()->each(function ($option) {
            $option->characteristicMappings()->delete();
        });
        $question->options()->delete();
        $question->delete();

        return back()->with('success', 'Pertanyaan psikotes berhasil dihapus.');
    }
}
