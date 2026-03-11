<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import type { BreadcrumbItem } from '@/types';
import type { TestAttempt, AttemptAnswer } from '@/types/models';
import { CheckCircle, XCircle, MinusCircle } from 'lucide-vue-next';

const props = defineProps<{
    attempt: TestAttempt;
    answers: (AttemptAnswer & { selectedOption?: { id: string; option_text: string } })[];
    showExplanation: boolean;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'History', href: '/history' },
    { title: 'Result', href: '#' },
];

const result = props.attempt.result;
</script>

<template>
    <Head :title="`Result - ${attempt.test?.title}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl space-y-6">
            <!-- Score Summary -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ attempt.test?.title }}</CardTitle>
                    <CardDescription>
                        {{ (attempt.test as any)?.program?.name }} &middot; {{ (attempt.test as any)?.testType?.name }}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 text-center">
                        <div>
                            <div class="text-muted-foreground text-xs">Score</div>
                            <div class="text-2xl font-bold">{{ result?.total_score ?? attempt.score ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-muted-foreground text-xs">Percentage</div>
                            <div class="text-2xl font-bold">{{ result?.percentage ?? '-' }}%</div>
                        </div>
                        <div>
                            <div class="text-muted-foreground text-xs">Correct</div>
                            <div class="text-2xl font-bold text-green-600">{{ attempt.total_correct ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-muted-foreground text-xs">Status</div>
                            <Badge :variant="result?.is_passed ? 'default' : 'destructive'" class="mt-1">
                                {{ result?.is_passed ? 'PASSED' : 'FAILED' }}
                            </Badge>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Answer Review -->
            <Card>
                <CardHeader>
                    <CardTitle>Answer Review</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div v-for="(ans, i) in answers" :key="ans.id" class="space-y-2">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5">
                                <CheckCircle v-if="ans.is_correct === true" class="h-5 w-5 text-green-500" />
                                <XCircle v-else-if="ans.is_correct === false" class="h-5 w-5 text-red-500" />
                                <MinusCircle v-else class="text-muted-foreground h-5 w-5" />
                            </div>
                            <div class="flex-1 space-y-1">
                                <div class="text-sm font-medium">Question {{ i + 1 }}</div>
                                <div class="prose dark:prose-invert max-w-none text-sm" v-html="ans.question?.question_text" />

                                <!-- Options display -->
                                <div v-if="ans.question?.options" class="mt-2 space-y-1">
                                    <div
                                        v-for="opt in ans.question.options"
                                        :key="opt.id"
                                        class="rounded px-3 py-1.5 text-sm"
                                        :class="[
                                            opt.is_correct ? 'bg-green-50 text-green-800 dark:bg-green-950 dark:text-green-300' : '',
                                            opt.id === ans.selected_option_id && !opt.is_correct ? 'bg-red-50 text-red-800 dark:bg-red-950 dark:text-red-300' : '',
                                        ]"
                                    >
                                        {{ opt.option_text }}
                                        <span v-if="opt.is_correct" class="ml-1 text-xs font-semibold">(Correct)</span>
                                        <span v-if="opt.id === ans.selected_option_id" class="ml-1 text-xs font-semibold">(Your answer)</span>
                                    </div>
                                </div>

                                <!-- Essay answer -->
                                <div v-if="ans.answer_text" class="bg-muted mt-2 rounded p-2 text-sm">
                                    <strong>Your answer:</strong> {{ ans.answer_text }}
                                </div>

                                <!-- Explanation -->
                                <div v-if="showExplanation && ans.question?.explanation" class="bg-blue-50 dark:bg-blue-950 mt-2 rounded p-2 text-sm text-blue-800 dark:text-blue-300">
                                    <strong>Explanation:</strong> {{ ans.question.explanation }}
                                </div>

                                <div v-if="ans.score !== null" class="text-muted-foreground text-xs">
                                    Score: {{ ans.score }}
                                </div>
                            </div>
                        </div>
                        <Separator v-if="i < answers.length - 1" />
                    </div>
                </CardContent>
            </Card>

            <div class="flex justify-center">
                <Button as-child variant="outline">
                    <Link href="/history">Back to History</Link>
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
