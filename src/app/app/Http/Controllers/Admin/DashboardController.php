<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\TestAttempt;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'totalUsers' => User::count(),
                'activeSubscriptions' => Subscription::where('status', 'active')->count(),
                'totalAttempts' => TestAttempt::count(),
                'totalRevenue' => Payment::where('status', 'paid')->sum('amount'),
            ],
            'recentUsers' => User::latest()->take(5)->get(['id', 'name', 'email', 'created_at']),
            'recentPayments' => Payment::with('user:id,name,email')
                ->latest()
                ->take(5)
                ->get(['id', 'user_id', 'amount', 'status', 'created_at']),
        ]);
    }
}
