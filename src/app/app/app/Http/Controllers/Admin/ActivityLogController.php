<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    public function index(Request $request): Response
    {
        $logs = ActivityLog::with('user:id,name,email')
            ->when($request->action, fn ($q, $a) => $q->where('action', $a))
            ->when($request->user_id, fn ($q, $u) => $q->where('user_id', $u))
            ->when($request->search, fn ($q, $s) => $q->where('action', 'like', "%{$s}%")
                ->orWhere('subject_type', 'like', "%{$s}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/ActivityLogs/Index', compact('logs'));
    }

    public function show(ActivityLog $activityLog): Response
    {
        $activityLog->load('user:id,name,email');

        return Inertia::render('Admin/ActivityLogs/Show', compact('activityLog'));
    }
}
