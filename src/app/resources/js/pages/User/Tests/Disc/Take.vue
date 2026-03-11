<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import type { DiscAttempt, DiscForm, DiscAnswer, DiscQuestion, DiscOption } from '@/types/models';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Clock, Send } from 'lucide-vue-next';

const props = defineProps<{
    attempt: DiscAttempt;
    form: DiscForm;
    answers: Record<string, DiscAnswer>;
}>();

const localAnswers = ref<Record<string, { most_option_id: string | null; least_option_id: string | null }>>({});
const submitting = ref(false);

const questions = computed(() => props.form.questions ?? []);

// Initialize local answers
for (const q of questions.value) {
    const existing = props.answers[q.id];
    localAnswers.value[q.id] = {
        most_option_id: existing?.most_option_id ?? null,
        least_option_id: existing?.least_option_id ?? null,
    };
}

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

function selectMost(questionId: string, optionId: string) {
    const current = localAnswers.value[questionId];
    // Can't select same option for both most and least
    if (current.least_option_id === optionId) return;
    localAnswers.value[questionId] = { ...current, most_option_id: optionId };
    saveAnswer(questionId);
}

function selectLeast(questionId: string, optionId: string) {
    const current = localAnswers.value[questionId];
    if (current.most_option_id === optionId) return;
    localAnswers.value[questionId] = { ...current, least_option_id: optionId };
    saveAnswer(questionId);
}

function saveAnswer(questionId: string) {
    const ans = localAnswers.value[questionId];
    router.post(`/disc/take/${props.attempt.id}/answer`, {
        disc_question_id: questionId,
        most_option_id: ans.most_option_id,
        least_option_id: ans.least_option_id,
    }, { preserveState: true, preserveScroll: true });
}

function submitTest() {
    if (submitting.value) return;
    submitting.value = true;
    router.post(`/disc/take/${props.attempt.id}/submit`, {}, {
        onFinish: () => { submitting.value = false; },
    });
}

const answeredCount = computed(() => {
    return questions.value.filter(q => {
        const a = localAnswers.value[q.id];
        return a?.most_option_id && a?.least_option_id;
    }).length;
});
</script>

<template>
    <Head :title="`${form.name} - DISC Test`" />
    <div class="flex h-screen flex-col">
        <!-- Header -->
        <div class="bg-background border-b px-4 py-3">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-semibold">{{ form.name }}</h1>
                    <p class="text-muted-foreground text-sm">
                        {{ answeredCount }} / {{ questions.length }} answered
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

        <!-- Questions -->
        <div class="flex-1 overflow-y-auto p-6">
            <div class="mx-auto max-w-3xl space-y-6">
                <p class="text-muted-foreground text-sm">
                    For each group of statements, select the one that is <strong>MOST</strong> like you and the one that is <strong>LEAST</strong> like you.
                </p>

                <Card v-for="q in questions" :key="q.id">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-base">Question {{ q.question_number }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div class="grid grid-cols-[1fr_80px_80px] items-center gap-2 text-xs font-semibold text-muted-foreground">
                                <span>Statement</span>
                                <span class="text-center">Most</span>
                                <span class="text-center">Least</span>
                            </div>
                            <Separator />
                            <div
                                v-for="opt in q.options"
                                :key="opt.id"
                                class="grid grid-cols-[1fr_80px_80px] items-center gap-2 rounded p-2 hover:bg-muted/50"
                            >
                                <span class="text-sm">{{ opt.option_text }}</span>
                                <div class="flex justify-center">
                                    <button
                                        @click="selectMost(q.id, opt.id)"
                                        class="h-5 w-5 rounded-full border-2 transition-colors"
                                        :class="localAnswers[q.id]?.most_option_id === opt.id ? 'border-primary bg-primary' : 'border-muted-foreground'"
                                    />
                                </div>
                                <div class="flex justify-center">
                                    <button
                                        @click="selectLeast(q.id, opt.id)"
                                        class="h-5 w-5 rounded-full border-2 transition-colors"
                                        :class="localAnswers[q.id]?.least_option_id === opt.id ? 'border-primary bg-primary' : 'border-muted-foreground'"
                                    />
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div class="flex justify-end pb-6">
                    <Button variant="destructive" @click="submitTest" :disabled="submitting">
                        <Send class="mr-2 h-4 w-4" /> Submit Test
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
