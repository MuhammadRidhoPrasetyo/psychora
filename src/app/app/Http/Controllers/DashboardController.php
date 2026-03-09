<?php

namespace App\Http\Controllers;

use App\Models\TestAttempt;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $user->load(['activeSubscription.plan', 'roles']);

        $recentAttempts = TestAttempt::with(['test.testType'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'totalAttempts' => TestAttempt::where('user_id', $user->id)->count(),
            'completedAttempts' => TestAttempt::where('user_id', $user->id)->where('status', 'completed')->count(),
            'averageScore' => TestAttempt::where('user_id', $user->id)
                ->where('status', 'completed')
                ->avg('total_score') ?? 0,
        ];

        return Inertia::render('Dashboard', [
            'subscription' => $user->activeSubscription,
            'recentAttempts' => $recentAttempts,
            'stats' => $stats,
        ]);
    }
}
