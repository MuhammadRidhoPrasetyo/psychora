<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface Payment {
    id: string;
    amount: number;
    status: string;
    payment_method: string;
    paid_at: string | null;
    created_at: string;
}

interface Subscription {
    id: string;
    status: string;
    start_at: string;
    end_at: string;
    created_at: string;
    plan: {
        id: string;
        name: string;
        price: number;
    };
    payments: Payment[];
}

const props = defineProps<{
    subscriptions: Subscription[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Status Langganan', href: '/subscription/status' },
];

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(price);
};

const statusColor = (status: string) => {
    const colors: Record<string, string> = {
        active: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        pending: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
        expired: 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
        cancelled: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
    };
    return colors[status] || colors['pending'];
};
</script>

<template>
    <Head title="Status Langganan" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Riwayat Langganan</h1>

            <div v-if="subscriptions.length === 0" class="rounded-xl border border-gray-200 bg-white p-12 text-center dark:border-gray-700 dark:bg-gray-900">
                <p class="text-gray-500 dark:text-gray-400">Belum ada riwayat langganan.</p>
            </div>

            <div v-for="sub in subscriptions" :key="sub.id" class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ sub.plan.name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ formatDate(sub.start_at) }} - {{ formatDate(sub.end_at) }}
                        </p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="statusColor(sub.status)">
                        {{ sub.status }}
                    </span>
                </div>
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Pembayaran:</p>
                    <div v-for="pay in sub.payments" :key="pay.id" class="mt-2 flex items-center justify-between rounded-lg bg-gray-50 px-4 py-2 text-sm dark:bg-gray-800">
                        <span class="text-gray-600 dark:text-gray-400">{{ formatPrice(pay.amount) }} - {{ pay.payment_method }}</span>
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="statusColor(pay.status)">
                            {{ pay.status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
