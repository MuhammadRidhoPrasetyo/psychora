<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\TestSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class QuestionController extends Controller
{
    public function index(Request $request, string $testId): Response
    {
        $questions = Question::with(['options', 'section'])
            ->whereHas('section', fn ($q) => $q->where('test_id', $testId))
            ->when($request->section_id, fn ($q, $id) => $q->where('test_section_id', $id))
            ->orderBy('order')
            ->paginate(20)
            ->withQueryString();

        $sections = TestSection::where('test_id', $testId)->orderBy('order')->get();

        return Inertia::render('admin/Questions/Index', [
            'testId' => $testId,
            'questions' => $questions,
            'sections' => $sections,
            'filters' => $request->only('section_id'),
        ]);
    }

    public function create(string $testId): Response
    {
        $sections = TestSection::where('test_id', $testId)->orderBy('order')->get();

        return Inertia::render('admin/Questions/Form', [
            'testId' => $testId,
            'sections' => $sections,
        ]);
    }

    public function store(Request $request, string $testId): RedirectResponse
    {
        $validated = $request->validate([
            'test_section_id' => 'required|exists:test_sections,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,essay',
            'explanation' => 'nullable|string',
            'score_weight' => 'nullable|numeric|min:0',
            'order' => 'nullable|integer|min:0',
            'options' => 'required_if:question_type,multiple_choice|array|min:2',
            'options.*.option_text' => 'required|string',
            'options.*.is_correct' => 'boolean',
            'options.*.score' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($validated) {
            $question = Question::create([
                'test_section_id' => $validated['test_section_id'],
                'question_text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
                'explanation' => $validated['explanation'] ?? null,
                'score_weight' => $validated['score_weight'] ?? 1,
                'order' => $validated['order'] ?? 0,
            ]);

            if (! empty($validated['options'])) {
                foreach ($validated['options'] as $index => $option) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $option['option_text'],
                        'is_correct' => $option['is_correct'] ?? false,
                        'score' => $option['score'] ?? 0,
                        'order' => $index,
                    ]);
                }
            }
        });

        return redirect()->route('admin.tests.questions.index', $testId)
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    public function edit(string $testId, Question $question): Response
    {
        $question->load('options');
        $sections = TestSection::where('test_id', $testId)->orderBy('order')->get();

        return Inertia::render('admin/Questions/Form', [
            'testId' => $testId,
            'question' => $question,
            'sections' => $sections,
        ]);
    }

    public function update(Request $request, string $testId, Question $question): RedirectResponse
    {
        $validated = $request->validate([
            'test_section_id' => 'required|exists:test_sections,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,essay',
            'explanation' => 'nullable|string',
            'score_weight' => 'nullable|numeric|min:0',
            'order' => 'nullable|integer|min:0',
            'options' => 'required_if:question_type,multiple_choice|array|min:2',
            'options.*.id' => 'nullable|exists:question_options,id',
            'options.*.option_text' => 'required|string',
            'options.*.is_correct' => 'boolean',
            'options.*.score' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($validated, $question) {
            $question->update([
                'test_section_id' => $validated['test_section_id'],
                'question_text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
                'explanation' => $validated['explanation'] ?? null,
                'score_weight' => $validated['score_weight'] ?? 1,
                'order' => $validated['order'] ?? 0,
            ]);

            if (! empty($validated['options'])) {
                $existingIds = [];
                foreach ($validated['options'] as $index => $option) {
                    if (! empty($option['id'])) {
                        QuestionOption::where('id', $option['id'])->update([
                            'option_text' => $option['option_text'],
                            'is_correct' => $option['is_correct'] ?? false,
                            'score' => $option['score'] ?? 0,
                            'order' => $index,
                        ]);
                        $existingIds[] = $option['id'];
                    } else {
                        $newOption = QuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $option['option_text'],
                            'is_correct' => $option['is_correct'] ?? false,
                            'score' => $option['score'] ?? 0,
                            'order' => $index,
                        ]);
                        $existingIds[] = $newOption->id;
                    }
                }
                $question->options()->whereNotIn('id', $existingIds)->delete();
            }
        });

        return redirect()->route('admin.tests.questions.index', $testId)
            ->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(string $testId, Question $question): RedirectResponse
    {
        $question->options()->delete();
        $question->delete();

        return redirect()->route('admin.tests.questions.index', $testId)
            ->with('success', 'Soal berhasil dihapus.');
    }
}
