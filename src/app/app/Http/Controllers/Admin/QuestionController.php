<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestSection;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionEssayAnswer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class QuestionController extends Controller
{
    public function index(Request $request, Test $test): Response
    {
        $questions = $test->questions()
            ->with(['options', 'essayAnswers', 'section:id,title'])
            ->when($request->section_id, fn ($q, $s) => $q->where('test_section_id', $s))
            ->when($request->question_type, fn ($q, $t) => $q->where('question_type', $t))
            ->orderBy('sort_order')
            ->paginate(20)
            ->withQueryString();

        $sections = $test->sections()->orderBy('sort_order')->get(['id', 'title']);

        return Inertia::render('Admin/Tests/Questions/Index', compact('test', 'questions', 'sections'));
    }

    public function create(Test $test): Response
    {
        $sections = $test->sections()->orderBy('sort_order')->get(['id', 'title']);

        return Inertia::render('Admin/Tests/Questions/Create', compact('test', 'sections'));
    }

    public function store(Request $request, Test $test): RedirectResponse
    {
        $validated = $request->validate([
            'test_section_id' => ['nullable', 'exists:test_sections,id'],
            'question_type' => ['required', Rule::in(['multiple_choice', 'multi_select', 'essay', 'true_false', 'number_input', 'matrix'])],
            'content' => ['required', 'string'],
            'media_url' => ['nullable', 'string', 'max:500'],
            'explanation' => ['nullable', 'string'],
            'difficulty' => ['nullable', Rule::in(['easy', 'medium', 'hard'])],
            'score' => ['required', 'numeric', 'min:0'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'options' => ['nullable', 'array'],
            'options.*.option_key' => ['required', 'string', 'max:10'],
            'options.*.content' => ['required', 'string'],
            'options.*.is_correct' => ['boolean'],
            'options.*.score' => ['nullable', 'numeric'],
            'options.*.sort_order' => ['required', 'integer', 'min:0'],
            'essay_answers' => ['nullable', 'array'],
            'essay_answers.*.answer_text' => ['required', 'string'],
            'essay_answers.*.score' => ['required', 'integer', 'min:0'],
            'essay_answers.*.match_type' => ['required', Rule::in(['exact', 'fuzzy', 'contains', 'regex'])],
            'essay_answers.*.priority' => ['required', 'integer', 'min:0'],
        ]);

        $question = $test->questions()->create([
            'test_section_id' => $validated['test_section_id'] ?? null,
            'question_type' => $validated['question_type'],
            'content' => $validated['content'],
            'media_url' => $validated['media_url'] ?? null,
            'explanation' => $validated['explanation'] ?? null,
            'difficulty' => $validated['difficulty'] ?? null,
            'score' => $validated['score'],
            'sort_order' => $validated['sort_order'],
            'is_active' => $validated['is_active'] ?? true,
            'created_by' => $request->user()->id,
        ]);

        if (!empty($validated['options'])) {
            foreach ($validated['options'] as $option) {
                $question->options()->create($option);
            }
        }

        if (!empty($validated['essay_answers'])) {
            foreach ($validated['essay_answers'] as $answer) {
                $question->essayAnswers()->create($answer);
            }
        }

        return redirect()->route('admin.tests.questions.index', $test)
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    public function show(Test $test, Question $question): Response
    {
        $question->load(['options', 'essayAnswers', 'section']);

        return Inertia::render('Admin/Tests/Questions/Show', compact('test', 'question'));
    }

    public function edit(Test $test, Question $question): Response
    {
        $question->load(['options', 'essayAnswers']);
        $sections = $test->sections()->orderBy('sort_order')->get(['id', 'title']);

        return Inertia::render('Admin/Tests/Questions/Edit', compact('test', 'question', 'sections'));
    }

    public function update(Request $request, Test $test, Question $question): RedirectResponse
    {
        $validated = $request->validate([
            'test_section_id' => ['nullable', 'exists:test_sections,id'],
            'question_type' => ['required', Rule::in(['multiple_choice', 'multi_select', 'essay', 'true_false', 'number_input', 'matrix'])],
            'content' => ['required', 'string'],
            'media_url' => ['nullable', 'string', 'max:500'],
            'explanation' => ['nullable', 'string'],
            'difficulty' => ['nullable', Rule::in(['easy', 'medium', 'hard'])],
            'score' => ['required', 'numeric', 'min:0'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'options' => ['nullable', 'array'],
            'options.*.id' => ['nullable', 'exists:question_options,id'],
            'options.*.option_key' => ['required', 'string', 'max:10'],
            'options.*.content' => ['required', 'string'],
            'options.*.is_correct' => ['boolean'],
            'options.*.score' => ['nullable', 'numeric'],
            'options.*.sort_order' => ['required', 'integer', 'min:0'],
            'essay_answers' => ['nullable', 'array'],
            'essay_answers.*.id' => ['nullable', 'exists:question_essay_answers,id'],
            'essay_answers.*.answer_text' => ['required', 'string'],
            'essay_answers.*.score' => ['required', 'integer', 'min:0'],
            'essay_answers.*.match_type' => ['required', Rule::in(['exact', 'fuzzy', 'contains', 'regex'])],
            'essay_answers.*.priority' => ['required', 'integer', 'min:0'],
        ]);

        $question->update([
            'test_section_id' => $validated['test_section_id'] ?? null,
            'question_type' => $validated['question_type'],
            'content' => $validated['content'],
            'media_url' => $validated['media_url'] ?? null,
            'explanation' => $validated['explanation'] ?? null,
            'difficulty' => $validated['difficulty'] ?? null,
            'score' => $validated['score'],
            'sort_order' => $validated['sort_order'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Sync options
        if (isset($validated['options'])) {
            $existingIds = collect($validated['options'])->pluck('id')->filter()->toArray();
            $question->options()->whereNotIn('id', $existingIds)->delete();

            foreach ($validated['options'] as $optionData) {
                if (!empty($optionData['id'])) {
                    $question->options()->where('id', $optionData['id'])->update($optionData);
                } else {
                    $question->options()->create($optionData);
                }
            }
        }

        // Sync essay answers
        if (isset($validated['essay_answers'])) {
            $existingIds = collect($validated['essay_answers'])->pluck('id')->filter()->toArray();
            $question->essayAnswers()->whereNotIn('id', $existingIds)->delete();

            foreach ($validated['essay_answers'] as $answerData) {
                if (!empty($answerData['id'])) {
                    $question->essayAnswers()->where('id', $answerData['id'])->update($answerData);
                } else {
                    $question->essayAnswers()->create($answerData);
                }
            }
        }

        return redirect()->route('admin.tests.questions.index', $test)
            ->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Test $test, Question $question): RedirectResponse
    {
        $question->delete();

        return redirect()->route('admin.tests.questions.index', $test)
            ->with('success', 'Soal berhasil dihapus.');
    }

    public function reorder(Request $request, Test $test): RedirectResponse
    {
        $validated = $request->validate([
            'questions' => ['required', 'array'],
            'questions.*.id' => ['required', 'exists:questions,id'],
            'questions.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($validated['questions'] as $item) {
            Question::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return back()->with('success', 'Urutan soal berhasil diperbarui.');
    }
}
