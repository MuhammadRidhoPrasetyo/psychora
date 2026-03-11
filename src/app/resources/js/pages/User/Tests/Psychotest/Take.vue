<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { ScrollArea } from '@/components/ui/scroll-area';
import type { PsychotestAttempt, PsychotestForm, PsychotestQuestion, PsychotestAnswer } from '@/types/models';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Clock, Send, ChevronLeft, ChevronRight } from 'lucide-vue-next';

const props = defineProps<{
    attempt: PsychotestAttempt;
    form: PsychotestForm;
    questions: PsychotestQuestion[];
    answers: Record<string, PsychotestAnswer>;
}>();

const currentIndex = ref(0);
const localAnswers = ref<Record<string, string | null>>({});
const submitting = ref(false);

// Initialize local answers
for (const q of props.questions) {
    const existing = props.answers[q.id];
    localAnswers.value[q.id] = existing?.psychotest_question_option_id ?? null;
}

const currentQuestion = computed(() => props.questions[currentIndex.value]);

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

function selectOption(questionId: string, optionId: string) {
    localAnswers.value[questionId] = optionId;
    router.post(`/psychotest/take/${props.attempt.id}/answer`, {
        psychotest_question_id: questionId,
        psychotest_question_option_id: optionId,
    }, { preserveState: true, preserveScroll: true });
}

function goTo(index: number) {
    if (index >= 0 && index < props.questions.length) currentIndex.value = index;
}

function submitTest() {
    if (submitting.value) return;
    submitting.value = true;
    router.post(`/psychotest/take/${props.attempt.id}/submit`, {}, {
        onFinish: () => { submitting.value = false; },
    });
}

function isAnswered(questionId: string): boolean {
    return !!localAnswers.value[questionId];
}

const answeredCount = computed(() => props.questions.filter(q => isAnswered(q.id)).length);
</script>

<template>
    <Head :title="`${form.name} - Psychotest`" />
    <div class="flex h-screen flex-col">
        <!-- Header -->
        <div class="bg-background border-b px-4 py-3">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-semibold">{{ form.name }}</h1>
                    <p class="text-muted-foreground text-sm">
                        Question {{ currentIndex + 1 }} of {{ questions.length }}
                        &middot; {{ answeredCount }} answered
                    </p>
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
            <!-- Navigator -->
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
                </ScrollArea>
            </div>

            <!-- Main -->
            <div class="flex-1 overflow-y-auto p-6">
                <div class="mx-auto max-w-2xl space-y-4">
                    <Card v-if="currentQuestion">
                        <CardHeader>
                            <CardTitle class="text-base">Question {{ currentQuestion.question_number }}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-muted-foreground mb-4 text-sm">Choose the statement that best describes you.</p>
                            <RadioGroup
                                :model-value="localAnswers[currentQuestion.id] ?? ''"
                                @update:model-value="(v: string) => selectOption(currentQuestion.id, v)"
                                class="space-y-2"
                            >
                                <div
                                    v-for="opt in currentQuestion.options"
                                    :key="opt.id"
                                    class="border rounded-lg p-3 flex items-start gap-3 cursor-pointer hover:bg-muted/50 transition-colors"
                                    :class="localAnswers[currentQuestion.id] === opt.id ? 'border-primary bg-primary/5' : ''"
                                    @click="selectOption(currentQuestion.id, opt.id)"
                                >
                                    <RadioGroupItem :value="opt.id" :id="opt.id" />
                                    <Label :for="opt.id" class="cursor-pointer flex-1">
                                        <span class="mr-2 font-semibold text-xs text-muted-foreground">{{ opt.label }}</span>
                                        <span class="text-sm">{{ opt.statement }}</span>
                                    </Label>
                                </div>
                            </RadioGroup>
                        </CardContent>
                    </Card>

                    <!-- Navigation -->
                    <div class="flex items-center justify-between">
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
