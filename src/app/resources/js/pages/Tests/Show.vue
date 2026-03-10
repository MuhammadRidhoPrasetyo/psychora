<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Clock, FileText, BookOpen, AlertTriangle, Play } from 'lucide-vue-next';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { TestDetail, TestAttempt } from '@/types/test';

const props = defineProps<{
    test: TestDetail;
    previousAttempts: TestAttempt[];
    canStart: boolean;
    accessMessage?: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Daftar Tes', href: '/tests' },
    { title: props.test.title, href: `/tests/${props.test.slug}` },
];

const isStarting = ref(false);
const showConfirm = ref(false);

const startTest = () => {
    if (isStarting.value) return;
    isStarting.value = true;
    router.post(`/tests/${props.test.slug}/start`, {}, {
        onFinish: () => {
            isStarting.value = false;
            showConfirm.value = false;
        },
    });
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head :title="test.title" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Test Info -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                <div class="mb-4 flex items-start justify-between">
                    <div>
                        <span class="rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-400">
                            {{ test.test_type.name }}
                        </span>
                        <span class="ml-2 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                            {{ test.program.name }}
                        </span>
                    </div>
                    <span
                        class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                        :class="{
                            'bg-green-100 text-green-700 dark:bg-green-950 dark:text-green-400': test.visibility === 'free',
                            'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400': test.visibility === 'premium',
                        }"
                    >
                        {{ test.visibility === 'free' ? 'Gratis' : 'Premium' }}
                    </span>
                </div>

                <h1 class="mb-2 text-2xl font-bold text-gray-900 dark:text-white">{{ test.title }}</h1>
                <p v-if="test.description" class="mb-6 text-gray-600 dark:text-gray-400">{{ test.description }}</p>

                <!-- Stats -->
                <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                            <Clock class="size-4" />
                            <span class="text-xs">Durasi</span>
                        </div>
                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                            {{ test.duration_minutes ? `${test.duration_minutes} menit` : 'Tidak dibatasi' }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                            <FileText class="size-4" />
                            <span class="text-xs">Jumlah Soal</span>
                        </div>
                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                            {{ test.total_questions ?? '-' }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                            <BookOpen class="size-4" />
                            <span class="text-xs">Metode Skor</span>
                        </div>
                        <p class="mt-1 text-lg font-semibold capitalize text-gray-900 dark:text-white">
                            {{ test.scoring_method }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                            <FileText class="size-4" />
                            <span class="text-xs">Bagian</span>
                        </div>
                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                            {{ test.sections.length || 1 }}
                        </p>
                    </div>
                </div>

                <!-- Instruction -->
                <div v-if="test.instruction" class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950/50">
                    <h3 class="mb-2 text-sm font-semibold text-blue-800 dark:text-blue-300">Petunjuk Pengerjaan</h3>
                    <div class="prose prose-sm dark:prose-invert max-w-none text-blue-700 dark:text-blue-300" v-html="test.instruction" />
                </div>

                <!-- Sections info -->
                <div v-if="test.sections.length > 0" class="mb-6">
                    <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Bagian Tes</h3>
                    <div class="space-y-2">
                        <div
                            v-for="(section, idx) in test.sections"
                            :key="section.id"
                            class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3 dark:bg-gray-800"
                        >
                            <div class="flex items-center gap-3">
                                <span class="flex size-7 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-400">{{ idx + 1 }}</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ section.title }}</span>
                            </div>
                            <span v-if="section.duration_minutes" class="text-xs text-gray-500 dark:text-gray-400">
                                {{ section.duration_minutes }} menit
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Access message -->
                <div v-if="!canStart && accessMessage" class="mb-6 flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-950/50">
                    <AlertTriangle class="mt-0.5 size-5 shrink-0 text-amber-600 dark:text-amber-400" />
                    <p class="text-sm text-amber-700 dark:text-amber-300">{{ accessMessage }}</p>
                </div>

                <!-- Start button -->
                <div class="flex items-center gap-4">
                    <button
                        v-if="canStart && !showConfirm"
                        @click="showConfirm = true"
                        class="flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-3 font-semibold text-white transition hover:bg-indigo-500"
                    >
                        <Play class="size-5" />
                        Mulai Tes
                    </button>

                    <template v-if="showConfirm">
                        <div class="flex items-center gap-3 rounded-lg border border-amber-300 bg-amber-50 p-4 dark:border-amber-700 dark:bg-amber-950/50">
                            <AlertTriangle class="size-5 shrink-0 text-amber-600 dark:text-amber-400" />
                            <div>
                                <p class="text-sm font-medium text-amber-800 dark:text-amber-200">Apakah Anda yakin ingin memulai tes?</p>
                                <p class="text-xs text-amber-600 dark:text-amber-400">Timer akan langsung berjalan setelah tes dimulai.</p>
                            </div>
                            <div class="flex gap-2">
                                <button
                                    @click="showConfirm = false"
                                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                                >
                                    Batal
                                </button>
                                <button
                                    @click="startTest()"
                                    :disabled="isStarting"
                                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500 disabled:opacity-50"
                                >
                                    {{ isStarting ? 'Memulai...' : 'Ya, Mulai' }}
                                </button>
                            </div>
                        </div>
                    </template>

                    <Link
                        v-if="!canStart"
                        href="/subscription/plans"
                        class="rounded-lg bg-amber-600 px-6 py-3 font-semibold text-white transition hover:bg-amber-500"
                    >
                        Berlangganan
                    </Link>
                </div>
            </div>

            <!-- Previous attempts -->
            <div v-if="previousAttempts.length > 0" class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Riwayat Pengerjaan</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-left text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                <th class="pb-3 font-medium">Percobaan</th>
                                <th class="pb-3 font-medium">Skor</th>
                                <th class="pb-3 font-medium">Status</th>
                                <th class="pb-3 font-medium">Mulai</th>
                                <th class="pb-3 font-medium">Selesai</th>
                                <th class="pb-3 font-medium"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <tr v-for="attempt in previousAttempts" :key="attempt.id">
                                <td class="py-3 font-medium text-gray-900 dark:text-white">#{{ attempt.attempt_no }}</td>
                                <td class="py-3 text-gray-700 dark:text-gray-300">
                                    {{ attempt.total_score != null ? Math.round(attempt.total_score) : '-' }}
                                    <span v-if="attempt.percentage != null" class="text-xs text-gray-400">({{ Math.round(attempt.percentage) }}%)</span>
                                </td>
                                <td class="py-3">
                                    <span
                                        class="rounded-full px-2 py-0.5 text-xs font-medium"
                                        :class="{
                                            'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300': attempt.status === 'submitted' || attempt.status === 'evaluated',
                                            'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300': attempt.status === 'in_progress',
                                            'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300': attempt.status === 'expired',
                                        }"
                                    >
                                        {{ attempt.status === 'submitted' || attempt.status === 'evaluated' ? 'Selesai' : attempt.status === 'in_progress' ? 'Berlangsung' : 'Kadaluarsa' }}
                                    </span>
                                </td>
                                <td class="py-3 text-gray-500 dark:text-gray-400">{{ attempt.started_at ? formatDate(attempt.started_at) : '-' }}</td>
                                <td class="py-3 text-gray-500 dark:text-gray-400">{{ attempt.submitted_at ? formatDate(attempt.submitted_at) : '-' }}</td>
                                <td class="py-3">
                                    <Link
                                        v-if="attempt.status === 'submitted' || attempt.status === 'evaluated'"
                                        :href="`/tests/attempts/${attempt.id}/result`"
                                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    >
                                        Lihat Hasil
                                    </Link>
                                    <Link
                                        v-else-if="attempt.status === 'in_progress'"
                                        :href="`/tests/attempts/${attempt.id}`"
                                        class="text-sm font-medium text-amber-600 hover:text-amber-500 dark:text-amber-400 dark:hover:text-amber-300"
                                    >
                                        Lanjutkan
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
