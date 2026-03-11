<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookmarkController extends Controller
{
    public function index(Request $request): Response
    {
        $bookmarks = $request->user()->bookmarks()
            ->with('question:id,content,question_type,test_id')
            ->latest()
            ->paginate(15);

        return Inertia::render('User/Bookmarks/Index', compact('bookmarks'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'question_id' => ['required', 'exists:questions,id'],
        ]);

        $request->user()->bookmarks()->firstOrCreate([
            'question_id' => $validated['question_id'],
        ]);

        return back()->with('success', 'Soal berhasil ditandai.');
    }

    public function destroy(Bookmark $bookmark): RedirectResponse
    {
        $this->authorize('delete', $bookmark);

        $bookmark->delete();

        return back()->with('success', 'Bookmark berhasil dihapus.');
    }
}
