<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface Plan {
    id: string;
    name: string;
    slug: string;
    description: string | null;
    price: number;
    duration_days: number;
}

const props = defineProps<{
    plan: Plan;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Langganan', href: '/subscription/plans' },
    { title: 'Checkout', href: '#' },
];

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
};

const form = useForm({
    payment_method: 'bank_transfer',
});

const submit = () => {
    form.post(`/subscription/subscribe/${props.plan.id}`);
};
</script>

<template>
    <Head title="Checkout" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Checkout</h1>

            <div class="mx-auto grid w-full max-w-4xl gap-6 lg:grid-cols-2">
                <!-- Order Summary -->
                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Ringkasan Pesanan</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Paket</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ plan.name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Durasi</span>
                            <span class="text-gray-900 dark:text-white">{{ plan.duration_days }} hari</span>
                        </div>
                        <div class="border-t border-gray-200 pt-3 dark:border-gray-700">
                            <div class="flex justify-between text-lg font-bold">
                                <span class="text-gray-900 dark:text-white">Total</span>
                                <span class="text-indigo-600 dark:text-indigo-400">{{ formatPrice(plan.price) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Metode Pembayaran</h2>
                    <form @submit.prevent="submit">
                        <div class="space-y-3">
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-200 p-4 transition hover:border-indigo-300 dark:border-gray-700 dark:hover:border-indigo-700"
                                :class="{ 'border-indigo-500 bg-indigo-50 dark:border-indigo-500 dark:bg-indigo-950': form.payment_method === 'bank_transfer' }">
                                <input type="radio" v-model="form.payment_method" value="bank_transfer" class="text-indigo-600" />
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Transfer Bank</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">BCA, Mandiri, BNI, BRI</p>
                                </div>
                            </label>
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-200 p-4 transition hover:border-indigo-300 dark:border-gray-700 dark:hover:border-indigo-700"
                                :class="{ 'border-indigo-500 bg-indigo-50 dark:border-indigo-500 dark:bg-indigo-950': form.payment_method === 'ewallet' }">
                                <input type="radio" v-model="form.payment_method" value="ewallet" class="text-indigo-600" />
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">E-Wallet</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">GoPay, OVO, DANA</p>
                                </div>
                            </label>
                        </div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="mt-6 w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-500 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Memproses...' : 'Bayar Sekarang' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
