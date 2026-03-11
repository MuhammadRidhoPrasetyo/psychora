<?php

namespace App\Http\Controllers\Admin\Disc;

use App\Http\Controllers\Controller;
use App\Models\Disc\DiscForm;
use App\Models\Disc\DiscQuestion;
use App\Models\Disc\DiscOption;
use App\Models\Disc\DiscOptionScoring;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DiscQuestionController extends Controller
{
    public function index(DiscForm $form): Response
    {
        $questions = $form->questions()
            ->with(['options.scorings'])
            ->orderBy('number')
            ->paginate(15);

        return Inertia::render('Admin/Disc/Questions/Index', compact('form', 'questions'));
    }

    public function create(DiscForm $form): Response
    {
        return Inertia::render('Admin/Disc/Questions/Create', compact('form'));
    }

    public function store(Request $request, DiscForm $form): RedirectResponse
    {
        $validated = $request->validate([
            'number' => ['required', 'integer', 'min:1'],
            'options' => ['required', 'array', 'size:4'],
            'options.*.option_text' => ['required', 'string'],
            'options.*.sort_order' => ['required', 'integer', 'min:0'],
            'options.*.scorings' => ['required', 'array', 'size:2'],
            'options.*.scorings.*.response_type' => ['required', Rule::in(['most', 'least'])],
            'options.*.scorings.*.disc_code' => ['required', Rule::in(['D', 'I', 'S', 'C', 'star'])],
            'options.*.scorings.*.score_value' => ['required', 'integer'],
        ]);

        $question = $form->questions()->create([
            'number' => $validated['number'],
        ]);

        foreach ($validated['options'] as $optionData) {
            $option = $question->options()->create([
                'option_text' => $optionData['option_text'],
                'sort_order' => $optionData['sort_order'],
            ]);

            foreach ($optionData['scorings'] as $scoring) {
                $option->scorings()->create($scoring);
            }
        }

        return redirect()->route('admin.disc.questions.index', $form)
            ->with('success', 'Pertanyaan DISC berhasil ditambahkan.');
    }

    public function edit(DiscForm $form, DiscQuestion $question): Response
    {
        $question->load('options.scorings');

        return Inertia::render('Admin/Disc/Questions/Edit', compact('form', 'question'));
    }

    public function update(Request $request, DiscForm $form, DiscQuestion $question): RedirectResponse
    {
        $validated = $request->validate([
            'number' => ['required', 'integer', 'min:1'],
            'options' => ['required', 'array', 'size:4'],
            'options.*.id' => ['nullable', 'exists:disc_options,id'],
            'options.*.option_text' => ['required', 'string'],
            'options.*.sort_order' => ['required', 'integer', 'min:0'],
            'options.*.scorings' => ['required', 'array', 'size:2'],
            'options.*.scorings.*.response_type' => ['required', Rule::in(['most', 'least'])],
            'options.*.scorings.*.disc_code' => ['required', Rule::in(['D', 'I', 'S', 'C', 'star'])],
            'options.*.scorings.*.score_value' => ['required', 'integer'],
        ]);

        $question->update(['number' => $validated['number']]);

        // Delete existing options and recreate
        $question->options()->each(function ($option) {
            $option->scorings()->delete();
            $option->delete();
        });

        foreach ($validated['options'] as $optionData) {
            $option = $question->options()->create([
                'option_text' => $optionData['option_text'],
                'sort_order' => $optionData['sort_order'],
            ]);

            foreach ($optionData['scorings'] as $scoring) {
                $option->scorings()->create($scoring);
            }
        }

        return redirect()->route('admin.disc.questions.index', $form)
            ->with('success', 'Pertanyaan DISC berhasil diperbarui.');
    }

    public function destroy(DiscForm $form, DiscQuestion $question): RedirectResponse
    {
        $question->options()->each(function ($option) {
            $option->scorings()->delete();
        });
        $question->options()->delete();
        $question->delete();

        return back()->with('success', 'Pertanyaan DISC berhasil dihapus.');
    }
}
