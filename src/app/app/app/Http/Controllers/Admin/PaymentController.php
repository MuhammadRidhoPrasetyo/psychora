<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(Request $request): Response
    {
        $payments = Payment::with(['user:id,name,email', 'subscription.plan:id,name'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, fn ($q, $s) => $q->where('invoice_number', 'like', "%{$s}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Payments/Index', compact('payments'));
    }

    public function show(Payment $payment): Response
    {
        $payment->load(['user.profile', 'subscription.plan']);

        return Inertia::render('Admin/Payments/Show', compact('payment'));
    }

    public function updateStatus(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'paid', 'failed', 'expired', 'refunded'])],
        ]);

        $payment->update($validated);

        if ($validated['status'] === 'paid' && !$payment->paid_at) {
            $payment->update(['paid_at' => now()]);
        }

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}
