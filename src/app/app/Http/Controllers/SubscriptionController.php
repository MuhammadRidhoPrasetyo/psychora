<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    public function plans(): Response
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get();

        $currentSubscription = auth()->user()->activeSubscription?->load('plan');

        return Inertia::render('Subscription/Plans', [
            'plans' => $plans,
            'currentSubscription' => $currentSubscription,
        ]);
    }

    public function checkout(SubscriptionPlan $plan): Response
    {
        return Inertia::render('Subscription/Checkout', [
            'plan' => $plan,
        ]);
    }

    public function subscribe(Request $request, SubscriptionPlan $plan): RedirectResponse
    {
        $user = $request->user();

        // Create subscription with pending status
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'pending',
            'start_at' => now(),
            'end_at' => now()->addDays($plan->duration_days),
        ]);

        // Create payment record
        Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'amount' => $plan->price,
            'payment_method' => $request->input('payment_method', 'bank_transfer'),
            'status' => 'pending',
        ]);

        return redirect()->route('subscription.status')
            ->with('success', 'Order berhasil dibuat. Silakan lakukan pembayaran dan tunggu konfirmasi admin.');
    }

    public function status(): Response
    {
        $subscriptions = auth()->user()
            ->subscriptions()
            ->with(['plan', 'payments'])
            ->latest()
            ->get();

        return Inertia::render('Subscription/Status', [
            'subscriptions' => $subscriptions,
        ]);
    }
}
