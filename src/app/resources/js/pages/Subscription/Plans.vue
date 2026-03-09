<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { CheckCircle } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface Plan {
    id: string;
    name: string;
    slug: string;
    description: string | null;
    price: number;
    duration_days: number;
    features: Record<string, unknown> | null;
    is_active: boolean;
}

interface Subscription {
    id: string;
    status: string;
    end_at: string;
    plan: Plan;
}

const props = defineProps<{
    plans: Plan[];
    currentSubscription: Subscription | null;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Langganan', href: '/subscription/plans' },
];

const formatPrice = (price: number) => {
    if (price === 0) return 'Gratis';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
};
</script>

<template>
    <Head title="Paket Langganan" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Paket Langganan</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pilih paket yang sesuai dengan kebutuhan Anda.</p>
            </div>

            <!-- Current subscription -->
            <div v-if="currentSubscription" class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-950">
                <p class="text-sm text-green-700 dark:text-green-300">
                    Anda sedang berlangganan <strong>{{ currentSubscription.plan.name }}</strong> hingga
                    {{ new Date(currentSubscription.end_at).toLocaleDateString('id-ID') }}.
                </p>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="plan in plans"
                    :key="plan.id"
                    class="relative overflow-hidden rounded-2xl border bg-white p-6 transition hover:shadow-lg dark:bg-gray-900"
                    :class="[
                        plan.slug === 'all-access'
                            ? 'border-indigo-600 shadow-lg dark:border-indigo-500'
                            : 'border-gray-200 dark:border-gray-700',
                    ]"
                >
                    <div
                        v-if="plan.slug === 'all-access'"
                        class="absolute right-0 top-0 rounded-bl-lg bg-indigo-600 px-3 py-1 text-xs font-semibold text-white"
                    >
                        Populer
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ plan.name }}</h3>
                    <p v-if="plan.description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ plan.description }}</p>
                    <div class="mt-4">
                        <span class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ formatPrice(plan.price) }}</span>
                        <span v-if="plan.price > 0" class="text-sm text-gray-500 dark:text-gray-400"> / {{ plan.duration_days }} hari</span>
                    </div>
                    <ul v-if="plan.features" class="mt-6 space-y-2">
                        <li v-for="(value, key) in plan.features" :key="String(key)" class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <CheckCircle class="mt-0.5 size-4 shrink-0 text-green-500" />
                            <span>{{ key }}: {{ value }}</span>
                        </li>
                    </ul>
                    <Link
                        v-if="plan.price > 0"
                        :href="`/subscription/checkout/${plan.id}`"
                        class="mt-6 block rounded-lg px-4 py-2.5 text-center text-sm font-semibold transition"
                        :class="[
                            plan.slug === 'all-access'
                                ? 'bg-indigo-600 text-white hover:bg-indigo-500'
                                : 'border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800',
                        ]"
                    >
                        Pilih Paket
                    </Link>
                    <div v-else class="mt-6 rounded-lg bg-gray-100 px-4 py-2.5 text-center text-sm font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                        Paket Aktif
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
