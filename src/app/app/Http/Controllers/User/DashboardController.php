<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\TestAttempt;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $activeSubscription = $user->subscriptions()
            ->with('plan:id,name,code')
            ->where('status', 'active')
            ->where('end_at', '>', now())
            ->first();

        $recentAttempts = $user->testAttempts()
            ->with(['test:id,title,program_id,test_type_id', 'test.program:id,name', 'test.testType:id,name'])
            ->latest()
            ->take(5)
            ->get();

        $progress = UserProgress::where('user_id', $user->id)
            ->with(['program:id,name', 'testType:id,name'])
            ->get();

        $stats = [
            'total_attempts' => $user->testAttempts()->count(),
            'completed_attempts' => $user->testAttempts()->where('status', 'submitted')->count(),
            'average_score' => $user->testAttempts()->where('status', 'submitted')->avg('percentage'),
        ];

        return Inertia::render('User/Dashboard', compact(
            'activeSubscription',
            'recentAttempts',
            'progress',
            'stats',
        ));
    }
}
