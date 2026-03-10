import { router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import type { Question, AttemptAnswer } from '@/types/test';

export function useTestEngine(
    attemptId: string,
    questions: Question[],
    initialAnswers: AttemptAnswer[],
) {
    const currentIndex = ref(0);
    const answers = ref<Map<string, AttemptAnswer>>(new Map());
    const isSaving = ref(false);
    const isSubmitting = ref(false);
    const bookmarkedQuestions = ref<Set<string>>(new Set());

    // Initialize answers from server
    initialAnswers.forEach((a) => {
        answers.value.set(a.question_id, { ...a });
    });

    const currentQuestion = computed(() => questions[currentIndex.value]);
    const totalQuestions = computed(() => questions.length);
    const isFirstQuestion = computed(() => currentIndex.value === 0);
    const isLastQuestion = computed(() => currentIndex.value === questions.length - 1);

    const answeredCount = computed(() => {
        let count = 0;
        answers.value.forEach((a) => {
            if (a.selected_option_id || a.answer_text) count++;
        });
        return count;
    });

    const unansweredCount = computed(() => totalQuestions.value - answeredCount.value);

    const getAnswer = (questionId: string): AttemptAnswer | undefined => {
        return answers.value.get(questionId);
    };

    const isAnswered = (questionId: string): boolean => {
        const a = answers.value.get(questionId);
        return !!a && !!(a.selected_option_id || a.answer_text);
    };

    const isBookmarked = (questionId: string): boolean => {
        return bookmarkedQuestions.value.has(questionId);
    };

    const toggleBookmark = (questionId: string) => {
        if (bookmarkedQuestions.value.has(questionId)) {
            bookmarkedQuestions.value.delete(questionId);
        } else {
            bookmarkedQuestions.value.add(questionId);
        }
    };

    const selectOption = (questionId: string, optionId: string) => {
        const existing = answers.value.get(questionId);
        const answer: AttemptAnswer = {
            question_id: questionId,
            selected_option_id: optionId,
            answer_text: existing?.answer_text ?? null,
        };
        answers.value.set(questionId, answer);
        saveAnswer(answer);
    };

    const setAnswerText = (questionId: string, text: string) => {
        const existing = answers.value.get(questionId);
        const answer: AttemptAnswer = {
            question_id: questionId,
            selected_option_id: existing?.selected_option_id ?? null,
            answer_text: text || null,
        };
        answers.value.set(questionId, answer);
        saveAnswer(answer);
    };

    const clearAnswer = (questionId: string) => {
        const answer: AttemptAnswer = {
            question_id: questionId,
            selected_option_id: null,
            answer_text: null,
        };
        answers.value.set(questionId, answer);
        saveAnswer(answer);
    };

    const saveAnswer = (answer: AttemptAnswer) => {
        isSaving.value = true;
        router.post(
            `/tests/attempts/${attemptId}/answer`,
            {
                question_id: answer.question_id,
                selected_option_id: answer.selected_option_id,
                answer_text: answer.answer_text,
            },
            {
                preserveScroll: true,
                preserveState: true,
                onFinish: () => {
                    isSaving.value = false;
                },
            },
        );
    };

    const goToQuestion = (index: number) => {
        if (index >= 0 && index < questions.length) {
            currentIndex.value = index;
        }
    };

    const nextQuestion = () => {
        if (!isLastQuestion.value) {
            currentIndex.value++;
        }
    };

    const prevQuestion = () => {
        if (!isFirstQuestion.value) {
            currentIndex.value--;
        }
    };

    const submitTest = () => {
        if (isSubmitting.value) return;
        isSubmitting.value = true;
        router.post(`/tests/attempts/${attemptId}/submit`, {}, {
            onFinish: () => {
                isSubmitting.value = false;
            },
        });
    };

    return {
        currentIndex,
        currentQuestion,
        totalQuestions,
        isFirstQuestion,
        isLastQuestion,
        answeredCount,
        unansweredCount,
        isSaving,
        isSubmitting,
        bookmarkedQuestions,
        getAnswer,
        isAnswered,
        isBookmarked,
        toggleBookmark,
        selectOption,
        setAnswerText,
        clearAnswer,
        goToQuestion,
        nextQuestion,
        prevQuestion,
        submitTest,
    };
}
