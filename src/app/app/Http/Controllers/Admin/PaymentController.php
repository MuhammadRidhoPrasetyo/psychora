<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(Request $request): Response
    {
        $payments = Payment::with(['user:id,name,email', 'subscription.plan'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/Payments/Index', [
            'payments' => $payments,
            'filters' => $request->only('status'),
        ]);
    }

    public function show(Payment $payment): Response
    {
        $payment->load(['user', 'subscription.plan']);

        return Inertia::render('admin/Payments/Show', [
            'payment' => $payment,
        ]);
    }

    public function confirm(Payment $payment): RedirectResponse
    {
        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            if ($payment->subscription) {
                $plan = $payment->subscription->plan;
                $payment->subscription->update([
                    'status' => 'active',
                    'start_at' => now(),
                    'end_at' => now()->addDays($plan->duration_days),
                ]);
            }
        });

        return redirect()->route('admin.payments.index')
            ->with('success', 'Pembayaran berhasil dikonfirmasi dan subscription diaktifkan.');
    }

    public function reject(Payment $payment): RedirectResponse
    {
        $payment->update([
            'status' => 'failed',
        ]);

        if ($payment->subscription) {
            $payment->subscription->update([
                'status' => 'cancelled',
            ]);
        }

        return redirect()->route('admin.payments.index')
            ->with('success', 'Pembayaran ditolak.');
    }
}
