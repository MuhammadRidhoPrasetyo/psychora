<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle, XCircle, MinusCircle, ArrowLeft, Eye, EyeOff, Trophy } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { AttemptResult } from '@/types/test';

const props = defineProps<{
    data: AttemptResult;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Daftar Tes', href: '/tests' },
    { title: props.data.attempt.test.title, href: `/tests/${props.data.attempt.test.slug}` },
    { title: `Hasil #${props.data.attempt.attempt_no}`, href: '#' },
];

const showAnswers = ref(false);
const optionLabels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

const scoreColor = computed(() => {
    const pct = props.data.result?.percentage ?? 0;
    if (pct >= 80) return 'text-green-600 dark:text-green-400';
    if (pct >= 60) return 'text-yellow-600 dark:text-yellow-400';
    return 'text-red-600 dark:text-red-400';
});

const scoreLabel = computed(() => {
    const pct = props.data.result?.percentage ?? 0;
    if (pct >= 80) return 'Sangat Baik';
    if (pct >= 60) return 'Cukup Baik';
    if (pct >= 40) return 'Perlu Ditingkatkan';
    return 'Kurang';
});

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head :title="`Hasil: ${data.attempt.test.title}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Result summary -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                <div class="mb-6 flex items-center gap-3">
                    <Trophy class="size-8 text-amber-500" />
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Hasil Tes</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ data.attempt.test.title }} &middot; Percobaan #{{ data.attempt.attempt_no }}
                        </p>
                    </div>
                </div>

                <!-- Score card -->
                <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-5 text-center dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Skor Akhir</p>
                        <p class="mt-2 text-4xl font-bold" :class="scoreColor">
                            {{ data.result?.final_score != null ? Math.round(data.result.final_score) : '-' }}
                        </p>
                        <p class="mt-1 text-sm font-medium" :class="scoreColor">{{ scoreLabel }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-5 text-center dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Persentase</p>
                        <p class="mt-2 text-4xl font-bold text-gray-900 dark:text-white">
                            {{ data.result?.percentage != null ? Math.round(data.result.percentage) : '-' }}%
                        </p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-5 text-center dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Benar</p>
                        <p class="mt-2 text-4xl font-bold text-green-600 dark:text-green-400">{{ data.stats.correct }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">dari {{ data.stats.total }} soal</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-5 text-center dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Statistik</p>
                        <div class="mt-2 flex justify-center gap-4 text-sm">
                            <div>
                                <span class="font-bold text-red-600 dark:text-red-400">{{ data.stats.wrong }}</span>
                                <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">salah</span>
                            </div>
                            <div>
                                <span class="font-bold text-gray-400">{{ data.stats.unanswered }}</span>
                                <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">kosong</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Interpretation -->
                <div v-if="data.result?.interpretation" class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950/50">
                    <h3 class="mb-2 text-sm font-semibold text-blue-800 dark:text-blue-300">Interpretasi</h3>
                    <p class="text-sm text-blue-700 dark:text-blue-300">{{ data.result.interpretation }}</p>
                </div>

                <!-- Info -->
                <div class="flex flex-wrap gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <span v-if="data.attempt.started_at">Mulai: {{ formatDate(data.attempt.started_at) }}</span>
                    <span v-if="data.attempt.submitted_at">Selesai: {{ formatDate(data.attempt.submitted_at) }}</span>
                </div>

                <!-- Actions -->
                <div class="mt-6 flex flex-wrap gap-3">
                    <Link
                        :href="`/tests/${data.attempt.test.slug}`"
                        class="flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        <ArrowLeft class="size-4" />
                        Kembali ke Detail Tes
                    </Link>
                    <button
                        @click="showAnswers = !showAnswers"
                        class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500"
                    >
                        <Eye v-if="!showAnswers" class="size-4" />
                        <EyeOff v-else class="size-4" />
                        {{ showAnswers ? 'Sembunyikan Pembahasan' : 'Lihat Pembahasan' }}
                    </button>
                </div>
            </div>

            <!-- Answer review -->
            <div v-if="showAnswers" class="space-y-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pembahasan Soal</h2>

                <div
                    v-for="(answer, idx) in data.answers"
                    :key="answer.question_id"
                    class="rounded-xl border bg-white p-6 dark:bg-gray-900"
                    :class="{
                        'border-green-200 dark:border-green-800': answer.is_correct === true,
                        'border-red-200 dark:border-red-800': answer.is_correct === false,
                        'border-gray-200 dark:border-gray-700': answer.is_correct === null,
                    }"
                >
                    <!-- Question header -->
                    <div class="mb-4 flex items-start gap-3">
                        <span
                            class="flex size-8 shrink-0 items-center justify-center rounded-full text-sm font-bold"
                            :class="{
                                'bg-green-100 text-green-700 dark:bg-green-950 dark:text-green-400': answer.is_correct === true,
                                'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400': answer.is_correct === false,
                                'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400': answer.is_correct === null,
                            }"
                        >
                            {{ idx + 1 }}
                        </span>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <CheckCircle v-if="answer.is_correct === true" class="size-5 text-green-600 dark:text-green-400" />
                                <XCircle v-else-if="answer.is_correct === false" class="size-5 text-red-600 dark:text-red-400" />
                                <MinusCircle v-else class="size-5 text-gray-400" />
                                <span class="text-sm font-medium" :class="{
                                    'text-green-700 dark:text-green-400': answer.is_correct === true,
                                    'text-red-700 dark:text-red-400': answer.is_correct === false,
                                    'text-gray-500 dark:text-gray-400': answer.is_correct === null,
                                }">
                                    {{ answer.is_correct === true ? 'Benar' : answer.is_correct === false ? 'Salah' : 'Tidak dijawab' }}
                                    <span v-if="answer.score != null" class="ml-2 text-xs text-gray-400">(+{{ answer.score }} poin)</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Question content -->
                    <div class="mb-4 text-sm text-gray-700 dark:text-gray-300" v-html="answer.question_content" />

                    <!-- Options review -->
                    <div v-if="answer.options.length > 0" class="mb-4 space-y-2">
                        <div
                            v-for="(option, optIdx) in answer.options"
                            :key="option.id"
                            class="flex items-start gap-3 rounded-lg p-3 text-sm"
                            :class="{
                                'bg-green-50 dark:bg-green-950/50': option.id === answer.correct_option_id,
                                'bg-red-50 dark:bg-red-950/50': option.id === answer.selected_option_id && option.id !== answer.correct_option_id,
                                'bg-gray-50 dark:bg-gray-800/50': option.id !== answer.correct_option_id && option.id !== answer.selected_option_id,
                            }"
                        >
                            <span
                                class="flex size-6 shrink-0 items-center justify-center rounded-full text-xs font-bold"
                                :class="{
                                    'bg-green-600 text-white': option.id === answer.correct_option_id,
                                    'bg-red-600 text-white': option.id === answer.selected_option_id && option.id !== answer.correct_option_id,
                                    'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400': option.id !== answer.correct_option_id && option.id !== answer.selected_option_id,
                                }"
                            >
                                {{ optionLabels[optIdx] || option.option_key }}
                            </span>
                            <div class="flex-1">
                                <span
                                    :class="{
                                        'font-medium text-green-800 dark:text-green-300': option.id === answer.correct_option_id,
                                        'font-medium text-red-800 dark:text-red-300': option.id === answer.selected_option_id && option.id !== answer.correct_option_id,
                                        'text-gray-600 dark:text-gray-400': option.id !== answer.correct_option_id && option.id !== answer.selected_option_id,
                                    }"
                                    v-html="option.content"
                                />
                                <span v-if="option.id === answer.correct_option_id" class="ml-2 text-xs text-green-600 dark:text-green-400">(Jawaban benar)</span>
                                <span v-if="option.id === answer.selected_option_id && option.id !== answer.correct_option_id" class="ml-2 text-xs text-red-600 dark:text-red-400">(Jawaban Anda)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Explanation -->
                    <div v-if="answer.explanation" class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950/50">
                        <p class="mb-1 text-xs font-semibold text-blue-700 dark:text-blue-400">Pembahasan:</p>
                        <div class="prose prose-sm dark:prose-invert max-w-none text-blue-700 dark:text-blue-300" v-html="answer.explanation" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
