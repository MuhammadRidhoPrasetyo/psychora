<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, Send, Menu, X } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';
import QuestionDisplay from '@/components/test/QuestionDisplay.vue';
import QuestionNavigation from '@/components/test/QuestionNavigation.vue';
import TestTimer from '@/components/test/TestTimer.vue';
import { useTestEngine } from '@/composables/useTestEngine';
import { useTestTimer } from '@/composables/useTestTimer';
import type { ActiveAttempt } from '@/types/test';

const props = defineProps<{
    attempt: ActiveAttempt;
}>();

const showSubmitDialog = ref(false);
const showSidebar = ref(false);

const {
    formatted: timerFormatted,
    isWarning: timerWarning,
    isDanger: timerDanger,
    start: startTimer,
} = useTestTimer(props.attempt.expired_at, () => {
    // Auto-submit when timer expires
    engine.submitTest();
});

const engine = useTestEngine(
    props.attempt.id,
    props.attempt.questions,
    props.attempt.answers,
);

onMounted(() => {
    startTimer();
});

const handleSubmitConfirm = () => {
    showSubmitDialog.value = false;
    engine.submitTest();
};
</script>

<template>
    <Head :title="`Mengerjakan: ${attempt.test.title}`" />

    <div class="flex h-screen flex-col bg-gray-50 dark:bg-gray-950">
        <!-- Top bar -->
        <header class="flex items-center justify-between border-b border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-3">
                <button
                    @click="showSidebar = !showSidebar"
                    class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 lg:hidden"
                >
                    <Menu v-if="!showSidebar" class="size-5" />
                    <X v-else class="size-5" />
                </button>
                <div>
                    <h1 class="text-sm font-semibold text-gray-900 dark:text-white">{{ attempt.test.title }}</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ attempt.test.test_type.name }} &middot; {{ attempt.test.program.name }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden text-xs text-gray-500 dark:text-gray-400 sm:block">
                    <span class="font-medium text-green-600 dark:text-green-400">{{ engine.answeredCount.value }}</span>
                    / {{ engine.totalQuestions.value }} dijawab
                </div>
                <TestTimer
                    v-if="attempt.expired_at"
                    :formatted="timerFormatted"
                    :is-warning="timerWarning"
                    :is-danger="timerDanger"
                />
                <button
                    @click="showSubmitDialog = true"
                    class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500"
                >
                    <Send class="size-4" />
                    <span class="hidden sm:inline">Selesai</span>
                </button>
            </div>
        </header>

        <!-- Main content -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar - Question navigation -->
            <aside
                class="w-64 shrink-0 overflow-y-auto border-r border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900"
                :class="{
                    'hidden lg:block': !showSidebar,
                    'absolute inset-y-14.25 left-0 z-30 block shadow-xl': showSidebar,
                }"
            >
                <QuestionNavigation
                    :questions="attempt.questions"
                    :current-index="engine.currentIndex.value"
                    :is-answered="engine.isAnswered"
                    :is-bookmarked="engine.isBookmarked"
                    @navigate="(idx) => { engine.goToQuestion(idx); showSidebar = false; }"
                />

                <!-- Summary -->
                <div class="mt-4 space-y-2 border-t border-gray-200 pt-4 dark:border-gray-700">
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>Dijawab</span>
                        <span class="font-medium text-green-600 dark:text-green-400">{{ engine.answeredCount.value }}</span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>Belum dijawab</span>
                        <span class="font-medium text-red-600 dark:text-red-400">{{ engine.unansweredCount.value }}</span>
                    </div>
                </div>
            </aside>

            <!-- Overlay for mobile sidebar -->
            <div
                v-if="showSidebar"
                @click="showSidebar = false"
                class="fixed inset-0 z-20 bg-black/20 lg:hidden"
            />

            <!-- Question area -->
            <main class="flex flex-1 flex-col overflow-y-auto">
                <div class="mx-auto w-full max-w-3xl flex-1 p-4 sm:p-6">
                    <QuestionDisplay
                        v-if="engine.currentQuestion.value"
                        :question="engine.currentQuestion.value"
                        :question-number="engine.currentIndex.value + 1"
                        :total-questions="engine.totalQuestions.value"
                        :answer="engine.getAnswer(engine.currentQuestion.value.id)"
                        :is-bookmarked="engine.isBookmarked(engine.currentQuestion.value.id)"
                        :is-saving="engine.isSaving.value"
                        @select-option="engine.selectOption"
                        @set-answer-text="engine.setAnswerText"
                        @clear-answer="engine.clearAnswer"
                        @toggle-bookmark="engine.toggleBookmark"
                    />
                </div>

                <!-- Bottom navigation -->
                <div class="sticky bottom-0 border-t border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
                    <div class="mx-auto flex max-w-3xl items-center justify-between">
                        <button
                            @click="engine.prevQuestion()"
                            :disabled="engine.isFirstQuestion.value"
                            class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 disabled:opacity-40 disabled:hover:bg-transparent dark:text-gray-300 dark:hover:bg-gray-800"
                        >
                            <ChevronLeft class="size-4" />
                            Sebelumnya
                        </button>

                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ engine.currentIndex.value + 1 }} / {{ engine.totalQuestions.value }}
                        </span>

                        <button
                            @click="engine.nextQuestion()"
                            :disabled="engine.isLastQuestion.value"
                            class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 disabled:opacity-40 disabled:hover:bg-transparent dark:text-gray-300 dark:hover:bg-gray-800"
                        >
                            Selanjutnya
                            <ChevronRight class="size-4" />
                        </button>
                    </div>
                </div>
            </main>
        </div>

        <!-- Submit confirmation dialog -->
        <Teleport to="body">
            <div v-if="showSubmitDialog" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Selesaikan Tes?</h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Setelah diserahkan, Anda tidak dapat mengubah jawaban lagi.
                    </p>

                    <div class="mt-4 rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Soal dijawab</span>
                            <span class="font-semibold text-green-600 dark:text-green-400">{{ engine.answeredCount.value }} / {{ engine.totalQuestions.value }}</span>
                        </div>
                        <div v-if="engine.unansweredCount.value > 0" class="mt-2 flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Belum dijawab</span>
                            <span class="font-semibold text-red-600 dark:text-red-400">{{ engine.unansweredCount.value }}</span>
                        </div>
                    </div>

                    <div v-if="engine.unansweredCount.value > 0" class="mt-3 rounded-lg border border-amber-200 bg-amber-50 p-3 dark:border-amber-800 dark:bg-amber-950/50">
                        <p class="text-xs text-amber-700 dark:text-amber-300">
                            Masih ada {{ engine.unansweredCount.value }} soal yang belum dijawab. Yakin ingin mengakhiri?
                        </p>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button
                            @click="showSubmitDialog = false"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800"
                        >
                            Kembali
                        </button>
                        <button
                            @click="handleSubmitConfirm()"
                            :disabled="engine.isSubmitting.value"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500 disabled:opacity-50"
                        >
                            {{ engine.isSubmitting.value ? 'Mengirim...' : 'Ya, Serahkan' }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>
