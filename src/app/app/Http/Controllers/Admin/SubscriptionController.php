<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    public function index(Request $request): Response
    {
        $subscriptions = Subscription::with(['user:id,name,email', 'plan:id,name,code,price'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, fn ($q, $s) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Subscriptions/Index', compact('subscriptions'));
    }

    public function show(Subscription $subscription): Response
    {
        $subscription->load(['user.profile', 'plan.entitlements']);

        return Inertia::render('Admin/Subscriptions/Show', compact('subscription'));
    }

    public function updateStatus(Request $request, Subscription $subscription): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'active', 'expired', 'cancelled'])],
        ]);

        $subscription->update($validated);

        return back()->with('success', 'Status langganan berhasil diperbarui.');
    }
}
