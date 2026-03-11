<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\PlanEntitlement;
use App\Models\Program;
use App\Models\TestType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionPlanController extends Controller
{
    public function index(Request $request): Response
    {
        $plans = SubscriptionPlan::withCount('subscriptions')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/SubscriptionPlans/Index', compact('plans'));
    }

    public function create(): Response
    {
        $programs = Program::where('is_active', true)->get();
        $testTypes = TestType::where('is_active', true)->get();

        return Inertia::render('Admin/SubscriptionPlans/Create', compact('programs', 'testTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:subscription_plans,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'is_active' => ['boolean'],
            'entitlements' => ['nullable', 'array'],
            'entitlements.*.program_id' => ['nullable', 'exists:programs,id'],
            'entitlements.*.test_type_id' => ['nullable', 'exists:test_types,id'],
            'entitlements.*.access_type' => ['required', Rule::in(['practice', 'tryout', 'discussion', 'report'])],
            'entitlements.*.limit_attempts' => ['nullable', 'integer', 'min:1'],
        ]);

        $plan = SubscriptionPlan::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'duration_days' => $validated['duration_days'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if (!empty($validated['entitlements'])) {
            foreach ($validated['entitlements'] as $entitlement) {
                $plan->entitlements()->create($entitlement);
            }
        }

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Paket langganan berhasil dibuat.');
    }

    public function show(SubscriptionPlan $subscriptionPlan): Response
    {
        $subscriptionPlan->load(['entitlements.program', 'entitlements.testType']);

        return Inertia::render('Admin/SubscriptionPlans/Show', compact('subscriptionPlan'));
    }

    public function edit(SubscriptionPlan $subscriptionPlan): Response
    {
        $subscriptionPlan->load('entitlements');
        $programs = Program::where('is_active', true)->get();
        $testTypes = TestType::where('is_active', true)->get();

        return Inertia::render('Admin/SubscriptionPlans/Edit', compact('subscriptionPlan', 'programs', 'testTypes'));
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('subscription_plans')->ignore($subscriptionPlan->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'is_active' => ['boolean'],
            'entitlements' => ['nullable', 'array'],
            'entitlements.*.program_id' => ['nullable', 'exists:programs,id'],
            'entitlements.*.test_type_id' => ['nullable', 'exists:test_types,id'],
            'entitlements.*.access_type' => ['required', Rule::in(['practice', 'tryout', 'discussion', 'report'])],
            'entitlements.*.limit_attempts' => ['nullable', 'integer', 'min:1'],
        ]);

        $subscriptionPlan->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'duration_days' => $validated['duration_days'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $subscriptionPlan->entitlements()->delete();
        if (!empty($validated['entitlements'])) {
            foreach ($validated['entitlements'] as $entitlement) {
                $subscriptionPlan->entitlements()->create($entitlement);
            }
        }

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Paket langganan berhasil diperbarui.');
    }

    public function destroy(SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        $subscriptionPlan->delete();

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Paket langganan berhasil dihapus.');
    }
}
