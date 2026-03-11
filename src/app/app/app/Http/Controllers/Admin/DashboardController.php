<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\TestAttempt;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'total_users' => User::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'total_revenue' => Payment::where('status', 'paid')->sum('amount'),
            'total_attempts' => TestAttempt::count(),
            'recent_users' => User::latest()->take(5)->get(['id', 'name', 'email', 'created_at']),
            'recent_payments' => Payment::with('user:id,name')
                ->where('status', 'paid')
                ->latest('paid_at')
                ->take(5)
                ->get(),
        ];

        return Inertia::render('Admin/Dashboard', compact('stats'));
    }
}
