<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    public function plans(): Response
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->with('entitlements.program:id,name')
            ->get();

        $activeSubscription = request()->user()->subscriptions()
            ->where('status', 'active')
            ->where('end_at', '>', now())
            ->first();

        return Inertia::render('User/Subscriptions/Plans', compact('plans', 'activeSubscription'));
    }

    public function checkout(SubscriptionPlan $plan): Response
    {
        $plan->load('entitlements.program:id,name');

        return Inertia::render('User/Subscriptions/Checkout', compact('plan'));
    }

    public function subscribe(Request $request, SubscriptionPlan $plan): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'max:50'],
        ]);

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'start_at' => now(),
            'end_at' => now()->addDays($plan->duration_days),
            'status' => 'pending',
        ]);

        $payment = Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
            'amount' => $plan->price,
            'payment_method' => $validated['payment_method'],
            'status' => 'pending',
        ]);

        return redirect()->route('user.subscriptions.payment', $payment)
            ->with('success', 'Silakan lakukan pembayaran.');
    }

    public function payment(Payment $payment): Response
    {
        $this->authorize('view', $payment);

        $payment->load('subscription.plan');

        return Inertia::render('User/Subscriptions/Payment', compact('payment'));
    }

    public function history(Request $request): Response
    {
        $subscriptions = $request->user()->subscriptions()
            ->with('plan:id,name,code,price')
            ->latest()
            ->paginate(10);

        return Inertia::render('User/Subscriptions/History', compact('subscriptions'));
    }

    public function invoices(Request $request): Response
    {
        $payments = $request->user()->payments()
            ->with('subscription.plan:id,name')
            ->latest()
            ->paginate(10);

        return Inertia::render('User/Subscriptions/Invoices', compact('payments'));
    }
}
