<?php

namespace App\Http\Controllers\Admin\Ist;

use App\Http\Controllers\Controller;
use App\Models\Ist\IstSubtest;
use App\Models\Ist\IstSubtestQuestion;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IstSubtestQuestionController extends Controller
{
    public function index(IstSubtest $subtest): Response
    {
        $subtestQuestions = $subtest->questions()
            ->with('question:id,content,question_type,score')
            ->orderBy('sort_order')
            ->paginate(20);

        $subtest->load('form:id,name,test_id');

        return Inertia::render('Admin/Ist/SubtestQuestions/Index', compact('subtest', 'subtestQuestions'));
    }

    public function store(Request $request, IstSubtest $subtest): RedirectResponse
    {
        $validated = $request->validate([
            'question_id' => ['required', 'exists:questions,id'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $subtest->questions()->create($validated);

        return back()->with('success', 'Soal berhasil ditambahkan ke subtest.');
    }

    public function update(Request $request, IstSubtest $subtest, IstSubtestQuestion $subtestQuestion): RedirectResponse
    {
        $validated = $request->validate([
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $subtestQuestion->update($validated);

        return back()->with('success', 'Urutan soal berhasil diperbarui.');
    }

    public function destroy(IstSubtest $subtest, IstSubtestQuestion $subtestQuestion): RedirectResponse
    {
        $subtestQuestion->delete();

        return back()->with('success', 'Soal berhasil dihapus dari subtest.');
    }

    public function reorder(Request $request, IstSubtest $subtest): RedirectResponse
    {
        $validated = $request->validate([
            'questions' => ['required', 'array'],
            'questions.*.id' => ['required', 'exists:ist_subtest_questions,id'],
            'questions.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($validated['questions'] as $item) {
            IstSubtestQuestion::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return back()->with('success', 'Urutan soal berhasil diperbarui.');
    }
}
