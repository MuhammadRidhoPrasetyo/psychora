<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\SubscriptionPlan;
use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    public function index(): Response
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get();

        $programs = Program::with('testTypes')->get();

        return Inertia::render('Welcome', [
            'plans' => $plans,
            'programs' => $programs,
        ]);
    }
}
