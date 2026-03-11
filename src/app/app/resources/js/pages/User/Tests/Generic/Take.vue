<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Separator } from '@/components/ui/separator';
import { ScrollArea } from '@/components/ui/scroll-area';
import type { TestAttempt, Test, Question, AttemptAnswer, TestSection } from '@/types/models';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Clock, ChevronLeft, ChevronRight, BookmarkPlus, Send } from 'lucide-vue-next';

const props = defineProps<{
    attempt: TestAttempt;
    test: Test;
    questions: Question[];
    answers: Record<string, AttemptAnswer>;
}>();

const currentIndex = ref(0);
const localAnswers = ref<Record<string, { selected_option_id: string | null; answer_text: string | null }>>({});
const submitting = ref(false);

// Initialize local answers from server
for (const q of props.questions) {
    const existing = props.answers[q.id];
    localAnswers.value[q.id] = {
        selected_option_id: existing?.selected_option_id ?? null,
        answer_text: existing?.answer_text ?? null,
    };
}

const currentQuestion = computed(() => props.questions[currentIndex.value]);
const sections = computed(() => props.test.sections ?? []);

// Timer
const remaining = ref(0);
const timerDisplay = computed(() => {
    const m = Math.floor(remaining.value / 60);
    const s = remaining.value % 60;
    return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
});

let timer: ReturnType<typeof setInterval> | null = null;

onMounted(() => {
    if (props.attempt.deadline_at) {
        const deadline = new Date(props.attempt.deadline_at).getTime();
        const update = () => {
            const diff = Math.max(0, Math.floor((deadline - Date.now()) / 1000));
            remaining.value = diff;
            if (diff <= 0 && timer) {
                clearInterval(timer);
                submitTest();
            }
        };
        update();
        timer = setInterval(update, 1000);
    }
});

onUnmounted(() => { if (timer) clearInterval(timer); });

function saveAnswer(questionId: string) {
    const ans = localAnswers.value[questionId];
    if (!ans) return;
    router.post(`/tests/take/${props.attempt.id}/answer`, {
        question_id: questionId,
        selected_option_id: ans.selected_option_id,
        answer_text: ans.answer_text,
    }, { preserveState: true, preserveScroll: true });
}

function selectOption(questionId: string, optionId: string) {
    localAnswers.value[questionId] = { ...localAnswers.value[questionId], selected_option_id: optionId };
    saveAnswer(questionId);
}

function updateEssay(questionId: string, text: string) {
    localAnswers.value[questionId] = { ...localAnswers.value[questionId], answer_text: text };
}

function saveEssay(questionId: string) {
    saveAnswer(questionId);
}

function goTo(index: number) {
    if (index >= 0 && index < props.questions.length) currentIndex.value = index;
}

function submitTest() {
    if (submitting.value) return;
    submitting.value = true;
    router.post(`/tests/take/${props.attempt.id}/submit`, {}, {
        onFinish: () => { submitting.value = false; },
    });
}

function bookmark(questionId: string) {
    router.post('/bookmarks', { question_id: questionId }, { preserveState: true, preserveScroll: true });
}

function isAnswered(questionId: string): boolean {
    const a = localAnswers.value[questionId];
    return !!(a?.selected_option_id || a?.answer_text);
}

function sectionForQuestion(q: Question): TestSection | undefined {
    return sections.value.find(s => s.id === q.test_section_id);
}
</script>

<template>
    <Head :title="`${test.title} - Test`" />
    <div class="flex h-screen flex-col">
        <!-- Header -->
        <div class="bg-background border-b px-4 py-3">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-semibold">{{ test.title }}</h1>
                    <p class="text-muted-foreground text-sm">Question {{ currentIndex + 1 }} of {{ questions.length }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <div v-if="attempt.deadline_at" class="flex items-center gap-2">
                        <Clock class="h-4 w-4" />
                        <span class="font-mono text-lg" :class="remaining < 300 ? 'text-red-500 font-bold' : ''">{{ timerDisplay }}</span>
                    </div>
                    <Button variant="destructive" size="sm" @click="submitTest" :disabled="submitting">
                        <Send class="mr-2 h-4 w-4" /> Submit
                    </Button>
                </div>
            </div>
        </div>

        <div class="flex flex-1 overflow-hidden">
            <!-- Question Navigator Sidebar -->
            <div class="border-r w-64 hidden md:block">
                <ScrollArea class="h-full p-4">
                    <h3 class="mb-3 text-sm font-semibold">Questions</h3>
                    <div class="grid grid-cols-5 gap-2">
                        <button
                            v-for="(q, i) in questions"
                            :key="q.id"
                            @click="goTo(i)"
                            class="flex h-8 w-8 items-center justify-center rounded text-xs font-medium transition-colors"
                            :class="[
                                i === currentIndex ? 'bg-primary text-primary-foreground' :
                                isAnswered(q.id) ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' :
                                'bg-muted hover:bg-muted/80'
                            ]"
                        >
                            {{ i + 1 }}
                        </button>
                    </div>
                    <Separator class="my-3" />
                    <div class="text-muted-foreground space-y-1 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="inline-block h-3 w-3 rounded bg-green-100 dark:bg-green-900" /> Answered
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="bg-muted inline-block h-3 w-3 rounded" /> Not answered
                        </div>
                    </div>
                </ScrollArea>
            </div>

            <!-- Main Question Area -->
            <div class="flex-1 overflow-y-auto p-6">
                <div class="mx-auto max-w-3xl">
                    <Card v-if="currentQuestion">
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <div>
                                    <Badge v-if="sectionForQuestion(currentQuestion)" variant="outline" class="mb-2">
                                        {{ sectionForQuestion(currentQuestion)!.title }}
                                    </Badge>
                                    <CardTitle class="text-base">Question {{ currentIndex + 1 }}</CardTitle>
                                </div>
                                <Button variant="ghost" size="icon" @click="bookmark(currentQuestion.id)" title="Bookmark">
                                    <BookmarkPlus class="h-4 w-4" />
                                </Button>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="prose dark:prose-invert max-w-none text-sm" v-html="currentQuestion.question_text" />

                            <!-- Multiple Choice / True-False -->
                            <template v-if="currentQuestion.question_type !== 'essay'">
                                <RadioGroup
                                    :model-value="localAnswers[currentQuestion.id]?.selected_option_id ?? ''"
                                    @update:model-value="(v: string) => selectOption(currentQuestion.id, v)"
                                    class="space-y-2"
                                >
                                    <div
                                        v-for="opt in currentQuestion.options"
                                        :key="opt.id"
                                        class="border rounded-lg p-3 flex items-start gap-3 cursor-pointer hover:bg-muted/50 transition-colors"
                                        :class="localAnswers[currentQuestion.id]?.selected_option_id === opt.id ? 'border-primary bg-primary/5' : ''"
                                        @click="selectOption(currentQuestion.id, opt.id)"
                                    >
                                        <RadioGroupItem :value="opt.id" :id="opt.id" />
                                        <Label :for="opt.id" class="cursor-pointer flex-1 text-sm">{{ opt.option_text }}</Label>
                                    </div>
                                </RadioGroup>
                            </template>

                            <!-- Essay -->
                            <template v-else>
                                <Textarea
                                    :model-value="localAnswers[currentQuestion.id]?.answer_text ?? ''"
                                    @update:model-value="(v: string) => updateEssay(currentQuestion.id, v)"
                                    @blur="saveEssay(currentQuestion.id)"
                                    placeholder="Type your answer here..."
                                    rows="6"
                                />
                            </template>
                        </CardContent>
                    </Card>

                    <!-- Navigation -->
                    <div class="mt-4 flex items-center justify-between">
                        <Button variant="outline" :disabled="currentIndex === 0" @click="goTo(currentIndex - 1)">
                            <ChevronLeft class="mr-1 h-4 w-4" /> Previous
                        </Button>
                        <span class="text-muted-foreground text-sm">{{ currentIndex + 1 }} / {{ questions.length }}</span>
                        <Button variant="outline" :disabled="currentIndex === questions.length - 1" @click="goTo(currentIndex + 1)">
                            Next <ChevronRight class="ml-1 h-4 w-4" />
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
