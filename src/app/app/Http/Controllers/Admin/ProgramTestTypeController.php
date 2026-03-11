<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\TestType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProgramTestTypeController extends Controller
{
    public function index(Program $program): Response
    {
        $program->load('testTypes');
        $allTestTypes = TestType::where('is_active', true)->get();

        return Inertia::render('Admin/Programs/TestTypes', compact('program', 'allTestTypes'));
    }

    public function store(Request $request, Program $program): RedirectResponse
    {
        $validated = $request->validate([
            'test_type_id' => ['required', 'exists:test_types,id'],
        ]);

        $program->testTypes()->syncWithoutDetaching([$validated['test_type_id']]);

        return back()->with('success', 'Jenis tes berhasil ditambahkan ke program.');
    }

    public function destroy(Program $program, TestType $testType): RedirectResponse
    {
        $program->testTypes()->detach($testType->id);

        return back()->with('success', 'Jenis tes berhasil dihapus dari program.');
    }
}
