<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\TestPackage;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function programs(): Response
    {
        $programs = Program::with('testTypes')->get();

        return Inertia::render('Catalog/Programs', [
            'programs' => $programs,
        ]);
    }

    public function programDetail(Program $program): Response
    {
        $program->load(['testTypes', 'testPackages' => function ($q) {
            $q->where('is_active', true);
        }]);

        return Inertia::render('Catalog/ProgramDetail', [
            'program' => $program,
        ]);
    }

    public function packages(): Response
    {
        $packages = TestPackage::with(['tests.testType'])
            ->where('is_active', true)
            ->get();

        return Inertia::render('Catalog/Packages', [
            'packages' => $packages,
        ]);
    }
}
