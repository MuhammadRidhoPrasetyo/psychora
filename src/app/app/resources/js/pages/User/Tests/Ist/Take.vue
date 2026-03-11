<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Separator } from '@/components/ui/separator';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import type {
    IstAttempt, IstForm, IstSubtest, IstSubtestAttempt,
    IstFormItem, IstInstruction, IstClue, Question, IstAnswer,
} from '@/types/models';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Clock, ChevronLeft, ChevronRight, Send, SkipForward, Info, Lightbulb } from 'lucide-vue-next';

const props = defineProps<{
    attempt: IstAttempt;
    form: IstForm;
    currentSubtestAttempt: IstSubtestAttempt;
    subtest: IstSubtest;
    questions: Question[];
    answers: Record<string, IstAnswer>;
    instructions: IstInstruction[];
    clues: IstClue[];
    allSubtestAttempts: IstSubtestAttempt[];
    formItem: IstFormItem | null;
}>();

const currentIndex = ref(0);
const localAnswers = ref<Record<string, { selected_option_id: string | null; answer_text: string | null }>>({});
const submitting = ref(false);
const showInstructions = ref(true);

// Initialize local answers
for (const q of props.questions) {
    const existing = props.answers[q.id];
    localAnswers.value[q.id] = {
        selected_option_id: existing?.selected_option_id ?? null,
        answer_text: existing?.answer_text ?? null,
    };
}

const currentQuestion = computed(() => props.questions[currentIndex.value]);

// Timer based on formItem or subtest duration
const durationMinutes = computed(() => props.formItem?.duration_minutes ?? props.subtest.duration_minutes ?? 0);

const remaining = ref(0);
const timerDisplay = computed(() => {
    const m = Math.floor(remaining.value / 60);
    const s = remaining.value % 60;
    return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
});

let timer: ReturnType<typeof setInterval> | null = null;

onMounted(() => {
    if (props.currentSubtestAttempt.started_at && durationMinutes.value > 0) {
        const started = new Date(props.currentSubtestAttempt.started_at).getTime();
        const deadline = started + durationMinutes.value * 60 * 1000;
        const update = () => {
            const diff = Math.max(0, Math.floor((deadline - Date.now()) / 1000));
            remaining.value = diff;
            if (diff <= 0 && timer) {
                clearInterval(timer);
                submitSubtest();
            }
        };
        update();
        timer = setInterval(update, 1000);
    }
});

onUnmounted(() => { if (timer) clearInterval(timer); });

function selectOption(questionId: string, optionId: string) {
    localAnswers.value[questionId] = { ...localAnswers.value[questionId], selected_option_id: optionId };
    saveAnswer(questionId);
}

function updateText(questionId: string, text: string) {
    localAnswers.value[questionId] = { ...localAnswers.value[questionId], answer_text: text };
}

function saveTextAnswer(questionId: string) {
    saveAnswer(questionId);
}

function saveAnswer(questionId: string) {
    const ans = localAnswers.value[questionId];
    router.post(`/ist/take/${props.attempt.id}/answer`, {
        question_id: questionId,
        selected_option_id: ans.selected_option_id,
        answer_text: ans.answer_text,
    }, { preserveState: true, preserveScroll: true });
}

function goTo(index: number) {
    if (index >= 0 && index < props.questions.length) currentIndex.value = index;
}

function submitSubtest() {
    if (submitting.value) return;
    submitting.value = true;
    router.post(`/ist/take/${props.attempt.id}/submit-subtest`, {}, {
        onFinish: () => { submitting.value = false; },
    });
}

function nextSubtest() {
    router.post(`/ist/take/${props.attempt.id}/next-subtest`);
}

function isAnswered(questionId: string): boolean {
    const a = localAnswers.value[questionId];
    return !!(a?.selected_option_id || a?.answer_text);
}

function subtestStatus(sa: IstSubtestAttempt): string {
    if (sa.id === props.currentSubtestAttempt.id) return 'current';
    return sa.status;
}
</script>

<template>
    <Head :title="`${subtest.subtest_name} - IST`" />
    <div class="flex h-screen flex-col">
        <!-- Header -->
        <div class="bg-background border-b px-4 py-3">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-semibold">{{ form.name }}</h1>
                    <p class="text-muted-foreground text-sm">
                        {{ subtest.subtest_code }} - {{ subtest.subtest_name }}
                        <span v-if="questions.length"> &middot; Question {{ currentIndex + 1 }} / {{ questions.length }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div v-if="durationMinutes > 0" class="flex items-center gap-2">
                        <Clock class="h-4 w-4" />
                        <span class="font-mono text-lg" :class="remaining < 60 ? 'text-red-500 font-bold' : ''">{{ timerDisplay }}</span>
                    </div>
                    <Button variant="outline" size="sm" @click="submitSubtest" :disabled="submitting">
                        <Send class="mr-2 h-4 w-4" /> Submit Subtest
                    </Button>
                </div>
            </div>
            <!-- Subtest progress bar -->
            <div class="mt-2 flex gap-1">
                <div
                    v-for="sa in allSubtestAttempts"
                    :key="sa.id"
                    class="h-1.5 flex-1 rounded-full transition-colors"
                    :class="{
                        'bg-primary': subtestStatus(sa) === 'current',
                        'bg-green-500': sa.status === 'submitted',
                        'bg-muted': sa.status === 'pending',
                        'bg-yellow-500': sa.status === 'in_progress' && sa.id !== currentSubtestAttempt.id,
                    }"
                    :title="`${sa.subtest_code}: ${sa.status}`"
                />
            </div>
        </div>

        <div class="flex flex-1 overflow-hidden">
            <!-- Left sidebar: navigator + clues -->
            <div class="border-r w-64 hidden lg:block">
                <ScrollArea class="h-full p-4">
                    <!-- Instructions toggle -->
                    <Button v-if="instructions.length" variant="ghost" size="sm" class="mb-3 w-full" @click="showInstructions = !showInstructions">
                        <Info class="mr-2 h-4 w-4" /> {{ showInstructions ? 'Hide' : 'Show' }} Instructions
                    </Button>

                    <!-- Clues -->
                    <div v-if="clues.length" class="mb-4 space-y-2">
                        <h3 class="flex items-center gap-1 text-sm font-semibold">
                            <Lightbulb class="h-4 w-4" /> Clues
                        </h3>
                        <div v-for="clue in clues" :key="clue.id" class="bg-muted rounded p-2 text-xs">
                            {{ clue.clue_text }}
                        </div>
                    </div>

                    <Separator class="my-3" />

                    <!-- Question navigator -->
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
                </ScrollArea>
            </div>

            <!-- Main content -->
            <div class="flex-1 overflow-y-auto p-6">
                <div class="mx-auto max-w-3xl space-y-4">
                    <!-- Instructions -->
                    <Alert v-if="showInstructions && instructions.length">
                        <Info class="h-4 w-4" />
                        <AlertTitle>Instructions</AlertTitle>
                        <AlertDescription>
                            <div v-for="inst in instructions" :key="inst.id" class="mb-2">
                                <strong v-if="inst.title">{{ inst.title }}:</strong>
                                <div class="text-sm whitespace-pre-line" v-html="inst.content" />
                            </div>
                        </AlertDescription>
                    </Alert>

                    <!-- Current question -->
                    <Card v-if="currentQuestion">
                        <CardHeader>
                            <CardTitle class="text-base">Question {{ currentIndex + 1 }}</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="prose dark:prose-invert max-w-none text-sm" v-html="currentQuestion.question_text" />

                            <!-- Multiple choice -->
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

                            <!-- Text input -->
                            <template v-else>
                                <Input
                                    :model-value="localAnswers[currentQuestion.id]?.answer_text ?? ''"
                                    @update:model-value="(v: string) => updateText(currentQuestion.id, v)"
                                    @blur="saveTextAnswer(currentQuestion.id)"
                                    placeholder="Type your answer..."
                                />
                            </template>
                        </CardContent>
                    </Card>

                    <!-- Navigation -->
                    <div class="flex items-center justify-between">
                        <Button variant="outline" :disabled="currentIndex === 0" @click="goTo(currentIndex - 1)">
                            <ChevronLeft class="mr-1 h-4 w-4" /> Previous
                        </Button>
                        <span class="text-muted-foreground text-sm">{{ currentIndex + 1 }} / {{ questions.length }}</span>
                        <Button v-if="currentIndex < questions.length - 1" variant="outline" @click="goTo(currentIndex + 1)">
                            Next <ChevronRight class="ml-1 h-4 w-4" />
                        </Button>
                        <Button v-else variant="default" @click="submitSubtest" :disabled="submitting">
                            <SkipForward class="mr-1 h-4 w-4" /> Finish Subtest
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
