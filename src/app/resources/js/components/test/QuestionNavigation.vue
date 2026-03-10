<script setup lang="ts">
import type { Question } from '@/types/test';

const props = defineProps<{
    questions: Question[];
    currentIndex: number;
    isAnswered: (questionId: string) => boolean;
    isBookmarked: (questionId: string) => boolean;
}>();

const emit = defineEmits<{
    navigate: [index: number];
}>();
</script>

<template>
    <div class="space-y-3">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Navigasi Soal</h3>
        <div class="grid grid-cols-5 gap-2">
            <button
                v-for="(question, index) in questions"
                :key="question.id"
                @click="emit('navigate', index)"
                class="relative flex size-10 items-center justify-center rounded-lg border text-sm font-medium transition-all"
                :class="{
                    'border-indigo-600 bg-indigo-600 text-white shadow-md': index === currentIndex,
                    'border-green-500 bg-green-50 text-green-700 dark:bg-green-950 dark:text-green-400': index !== currentIndex && isAnswered(question.id),
                    'border-gray-300 bg-white text-gray-600 hover:border-gray-400 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:border-gray-500': index !== currentIndex && !isAnswered(question.id),
                }"
            >
                {{ index + 1 }}
                <span
                    v-if="isBookmarked(question.id)"
                    class="absolute -right-1 -top-1 size-2.5 rounded-full bg-orange-400"
                />
            </button>
        </div>

        <!-- Legend -->
        <div class="space-y-1.5 border-t border-gray-200 pt-3 text-xs text-gray-500 dark:border-gray-700 dark:text-gray-400">
            <div class="flex items-center gap-2">
                <span class="inline-block size-3 rounded border border-indigo-600 bg-indigo-600" />
                <span>Soal saat ini</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-block size-3 rounded border border-green-500 bg-green-50 dark:bg-green-950" />
                <span>Sudah dijawab</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-block size-3 rounded border border-gray-300 bg-white dark:border-gray-600 dark:bg-gray-800" />
                <span>Belum dijawab</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-block size-2.5 rounded-full bg-orange-400" />
                <span>Ditandai (ragu)</span>
            </div>
        </div>
    </div>
</template>
