<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import type { KraepelinAttempt, KraepelinAttemptColumn, KraepelinAnswer } from '@/types/models';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Clock, Send, ChevronRight } from 'lucide-vue-next';

const props = defineProps<{
    attempt: KraepelinAttempt;
    columns: KraepelinAttemptColumn[];
    existingAnswers: Record<string, KraepelinAnswer[]>;
}>();

const currentColumnIndex = ref(0);
const submitting = ref(false);
const localAnswers = ref<Record<string, Record<number, string>>>({});

// Initialize local answers from existing
for (const col of props.columns) {
    localAnswers.value[col.id] = {};
    const existing = props.existingAnswers[col.id] ?? [];
    for (const ans of existing) {
        localAnswers.value[col.id][ans.position] = String(ans.user_answer ?? '');
    }
}

const currentColumn = computed(() => props.columns[currentColumnIndex.value]);
const numbers = computed(() => currentColumn.value?.numbers ?? []);

// Timer per column
const remaining = ref(0);
const timerDisplay = computed(() => {
    const m = Math.floor(remaining.value / 60);
    const s = remaining.value % 60;
    return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
});

let timer: ReturnType<typeof setInterval> | null = null;

function startColumnTimer() {
    if (timer) clearInterval(timer);
    remaining.value = props.attempt.duration_per_column;
    timer = setInterval(() => {
        remaining.value = Math.max(0, remaining.value - 1);
        if (remaining.value <= 0) {
            if (timer) clearInterval(timer);
            saveBatchAndAdvance();
        }
    }, 1000);
}

onMounted(() => { startColumnTimer(); });
onUnmounted(() => { if (timer) clearInterval(timer); });

function saveBatchAndAdvance() {
    const col = currentColumn.value;
    if (!col) return;
    const answers = Object.entries(localAnswers.value[col.id] ?? {}).map(([position, value]) => ({
        position: Number(position),
        user_answer: value !== '' ? Number(value) : null,
    }));

    router.post(`/kraepelin/take/${props.attempt.id}/batch-answer`, {
        kraepelin_attempt_column_id: col.id,
        answers,
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            if (currentColumnIndex.value < props.columns.length - 1) {
                currentColumnIndex.value++;
                startColumnTimer();
            }
        },
    });
}

function submitTest() {
    if (submitting.value) return;
    submitting.value = true;
    // Save current column first, then submit
    const col = currentColumn.value;
    const answers = Object.entries(localAnswers.value[col.id] ?? {}).map(([position, value]) => ({
        position: Number(position),
        user_answer: value !== '' ? Number(value) : null,
    }));

    router.post(`/kraepelin/take/${props.attempt.id}/batch-answer`, {
        kraepelin_attempt_column_id: col.id,
        answers,
    }, {
        preserveState: true,
        onSuccess: () => {
            router.post(`/kraepelin/take/${props.attempt.id}/submit`, {}, {
                onFinish: () => { submitting.value = false; },
            });
        },
    });
}

function updateAnswer(position: number, value: string) {
    const colId = currentColumn.value.id;
    if (!localAnswers.value[colId]) localAnswers.value[colId] = {};
    localAnswers.value[colId][position] = value;
}

// Pairs: each pair is (numbers[i], numbers[i+1]) → user adds them
const pairs = computed(() => {
    const nums = numbers.value;
    const result: { position: number; top: number; bottom: number }[] = [];
    for (let i = 0; i < nums.length - 1; i++) {
        result.push({ position: i, top: nums[i].number_value, bottom: nums[i + 1].number_value });
    }
    return result;
});
</script>

<template>
    <Head title="Kraepelin Test" />
    <div class="flex h-screen flex-col">
        <!-- Header -->
        <div class="bg-background border-b px-4 py-3">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-semibold">Kraepelin Test</h1>
                    <p class="text-muted-foreground text-sm">
                        Column {{ currentColumnIndex + 1 }} of {{ columns.length }}
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <Clock class="h-4 w-4" />
                        <span class="font-mono text-lg" :class="remaining < 10 ? 'text-red-500 font-bold animate-pulse' : ''">{{ timerDisplay }}</span>
                    </div>
                    <Button variant="destructive" size="sm" @click="submitTest" :disabled="submitting">
                        <Send class="mr-2 h-4 w-4" /> Submit All
                    </Button>
                </div>
            </div>
            <!-- Column progress -->
            <div class="mt-2 flex gap-0.5">
                <div
                    v-for="(col, i) in columns"
                    :key="col.id"
                    class="h-1.5 flex-1 rounded-full transition-colors"
                    :class="{
                        'bg-primary': i === currentColumnIndex,
                        'bg-green-500': i < currentColumnIndex,
                        'bg-muted': i > currentColumnIndex,
                    }"
                />
            </div>
        </div>

        <!-- Main content -->
        <div class="flex-1 overflow-y-auto p-6">
            <div class="mx-auto max-w-md">
                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-center">Column {{ currentColumnIndex + 1 }}</CardTitle>
                        <p class="text-center text-muted-foreground text-xs">
                            Add each pair of adjacent numbers. Write the last digit of the sum between them.
                        </p>
                    </CardHeader>
                    <CardContent>
                        <div class="flex flex-col items-center gap-0">
                            <template v-for="(pair, i) in pairs" :key="pair.position">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 text-center text-lg font-mono font-bold">{{ pair.top }}</div>
                                    <Input
                                        type="text"
                                        inputmode="numeric"
                                        maxlength="1"
                                        class="h-8 w-12 text-center font-mono"
                                        :model-value="localAnswers[currentColumn.id]?.[pair.position] ?? ''"
                                        @input="(e: Event) => updateAnswer(pair.position, (e.target as HTMLInputElement).value)"
                                    />
                                </div>
                                <template v-if="i < pairs.length - 1">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 text-center text-lg font-mono font-bold">{{ pair.bottom }}</div>
                                        <div class="w-12" />
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 text-center text-lg font-mono font-bold">{{ pair.bottom }}</div>
                                        <div class="w-12" />
                                    </div>
                                </template>
                            </template>
                        </div>
                    </CardContent>
                </Card>

                <div class="mt-4 flex justify-end">
                    <Button
                        v-if="currentColumnIndex < columns.length - 1"
                        @click="saveBatchAndAdvance"
                    >
                        Next Column <ChevronRight class="ml-1 h-4 w-4" />
                    </Button>
                    <Button
                        v-else
                        variant="destructive"
                        @click="submitTest"
                        :disabled="submitting"
                    >
                        <Send class="mr-2 h-4 w-4" /> Finish
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
