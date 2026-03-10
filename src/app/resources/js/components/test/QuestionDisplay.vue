<script setup lang="ts">
import { Bookmark, BookmarkCheck, Eraser } from 'lucide-vue-next';
import type { Question, AttemptAnswer } from '@/types/test';

const props = defineProps<{
    question: Question;
    questionNumber: number;
    totalQuestions: number;
    answer?: AttemptAnswer;
    isBookmarked: boolean;
    isSaving: boolean;
}>();

const emit = defineEmits<{
    selectOption: [questionId: string, optionId: string];
    setAnswerText: [questionId: string, text: string];
    clearAnswer: [questionId: string];
    toggleBookmark: [questionId: string];
}>();

const optionLabels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
</script>

<template>
    <div class="space-y-6">
        <!-- Question header -->
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-400">
                    {{ questionNumber }}
                </span>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        Soal {{ questionNumber }} dari {{ totalQuestions }}
                    </span>
                    <div v-if="question.section" class="text-xs text-gray-400 dark:text-gray-500">
                        {{ question.section.title }}
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <!-- Saving indicator -->
                <span v-if="isSaving" class="text-xs text-gray-400">
                    Menyimpan...
                </span>
                <!-- Bookmark -->
                <button
                    @click="emit('toggleBookmark', question.id)"
                    class="rounded-lg p-2 transition-colors"
                    :class="isBookmarked
                        ? 'bg-orange-100 text-orange-600 dark:bg-orange-950 dark:text-orange-400'
                        : 'text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-300'"
                    :title="isBookmarked ? 'Hapus tanda' : 'Tandai soal ini'"
                >
                    <BookmarkCheck v-if="isBookmarked" class="size-5" />
                    <Bookmark v-else class="size-5" />
                </button>
            </div>
        </div>

        <!-- Question content -->
        <div class="prose prose-sm dark:prose-invert max-w-none rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
            <div v-html="question.content" />
            <img
                v-if="question.media_url"
                :src="question.media_url"
                alt="Media soal"
                class="mt-4 max-h-80 rounded-lg"
            />
        </div>

        <!-- Multiple choice options -->
        <div v-if="question.question_type === 'multiple_choice' || question.question_type === 'true_false'" class="space-y-3">
            <button
                v-for="(option, idx) in question.options"
                :key="option.id"
                @click="emit('selectOption', question.id, option.id)"
                class="flex w-full items-start gap-4 rounded-xl border-2 p-4 text-left transition-all"
                :class="answer?.selected_option_id === option.id
                    ? 'border-indigo-500 bg-indigo-50 dark:border-indigo-500 dark:bg-indigo-950/50'
                    : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:hover:border-gray-600 dark:hover:bg-gray-800'"
            >
                <span
                    class="flex size-8 shrink-0 items-center justify-center rounded-full text-sm font-bold transition-colors"
                    :class="answer?.selected_option_id === option.id
                        ? 'bg-indigo-600 text-white'
                        : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'"
                >
                    {{ optionLabels[idx] || option.option_key }}
                </span>
                <span
                    class="text-sm leading-relaxed"
                    :class="answer?.selected_option_id === option.id
                        ? 'font-medium text-indigo-900 dark:text-indigo-100'
                        : 'text-gray-700 dark:text-gray-300'"
                    v-html="option.content"
                />
            </button>
        </div>

        <!-- Essay / text input -->
        <div v-else-if="question.question_type === 'essay'" class="space-y-2">
            <textarea
                :value="answer?.answer_text ?? ''"
                @blur="emit('setAnswerText', question.id, ($event.target as HTMLTextAreaElement).value)"
                rows="6"
                class="w-full rounded-xl border-2 border-gray-200 bg-white p-4 text-sm focus:border-indigo-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-indigo-500"
                placeholder="Tulis jawaban Anda di sini..."
            />
        </div>

        <!-- Number input -->
        <div v-else-if="question.question_type === 'number_input'" class="space-y-2">
            <input
                type="number"
                :value="answer?.answer_text ?? ''"
                @blur="emit('setAnswerText', question.id, ($event.target as HTMLInputElement).value)"
                class="w-full rounded-xl border-2 border-gray-200 bg-white p-4 text-sm focus:border-indigo-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-indigo-500"
                placeholder="Masukkan angka jawaban..."
            />
        </div>

        <!-- Clear answer -->
        <div v-if="answer?.selected_option_id || answer?.answer_text" class="flex justify-end">
            <button
                @click="emit('clearAnswer', question.id)"
                class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-300"
            >
                <Eraser class="size-3.5" />
                Hapus Jawaban
            </button>
        </div>
    </div>
</template>
