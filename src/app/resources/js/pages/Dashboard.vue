<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, CheckCircle, CreditCard, TrendingUp } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface Subscription {
    id: string;
    status: string;
    start_at: string;
    end_at: string;
    plan: {
        id: string;
        name: string;
        slug: string;
    };
}

interface Attempt {
    id: string;
    status: string;
    total_score: number | null;
    started_at: string;
    submitted_at: string | null;
    test: {
        id: string;
        title: string;
        test_type: {
            name: string;
            engine_type: string;
        };
    };
}

const props = defineProps<{
    subscription: Subscription | null;
    recentAttempts: Attempt[];
    stats: {
        totalAttempts: number;
        completedAttempts: number;
        averageScore: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Welcome -->
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Selamat datang, {{ $page.props.auth.user.name }}! 👋
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Berikut ringkasan aktivitas latihan Anda.
                </p>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-blue-50 p-2 text-blue-600 dark:bg-blue-950 dark:text-blue-400">
                            <BookOpen class="size-5" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Latihan</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.totalAttempts }}</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-green-50 p-2 text-green-600 dark:bg-green-950 dark:text-green-400">
                            <CheckCircle class="size-5" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Selesai</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.completedAttempts }}</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-purple-50 p-2 text-purple-600 dark:bg-purple-950 dark:text-purple-400">
                            <TrendingUp class="size-5" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Rata-rata Skor</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ Math.round(stats.averageScore) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscription Status -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Status Langganan</h2>
                <template v-if="subscription">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ subscription.plan.name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Aktif hingga {{ formatDate(subscription.end_at) }}
                            </p>
                        </div>
                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700 dark:bg-green-900 dark:text-green-300">
                            Aktif
                        </span>
                    </div>
                </template>
                <template v-else>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Anda belum memiliki langganan aktif.</p>
                        </div>
                        <Link
                            href="/subscription/plans"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500"
                        >
                            Pilih Paket
                        </Link>
                    </div>
                </template>
            </div>

            <!-- Recent Attempts -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Riwayat Latihan Terbaru</h2>
                <template v-if="recentAttempts.length > 0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 text-left text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                    <th class="pb-3 font-medium">Tes</th>
                                    <th class="pb-3 font-medium">Jenis</th>
                                    <th class="pb-3 font-medium">Skor</th>
                                    <th class="pb-3 font-medium">Status</th>
                                    <th class="pb-3 font-medium">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                <tr v-for="attempt in recentAttempts" :key="attempt.id">
                                    <td class="py-3 font-medium text-gray-900 dark:text-white">{{ attempt.test.title }}</td>
                                    <td class="py-3">
                                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs dark:bg-gray-800">
                                            {{ attempt.test.test_type.name }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-gray-700 dark:text-gray-300">{{ attempt.total_score != null ? Math.round(attempt.total_score) : '-' }}</td>
                                    <td class="py-3">
                                        <span
                                            class="rounded-full px-2 py-0.5 text-xs font-medium"
                                            :class="{
                                                'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300': attempt.status === 'completed',
                                                'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300': attempt.status === 'in_progress',
                                                'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300': attempt.status === 'abandoned',
                                            }"
                                        >
                                            {{ attempt.status === 'completed' ? 'Selesai' : attempt.status === 'in_progress' ? 'Berlangsung' : 'Ditinggalkan' }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-gray-500 dark:text-gray-400">{{ formatDate(attempt.started_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>
                <template v-else>
                    <p class="text-center text-gray-500 dark:text-gray-400">Belum ada riwayat latihan.</p>
                </template>
            </div>
        </div>
    </AppLayout>
</template>
