<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import type { BreadcrumbItem } from '@/types';
import type { KraepelinAttempt, TestAttempt } from '@/types/models';
import { computed } from 'vue';

const props = defineProps<{
    attempt: KraepelinAttempt;
    testAttempt: TestAttempt;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'History', href: '/history' },
    { title: 'Kraepelin Result', href: '#' },
];

const scores = computed(() => [
    { label: 'Speed', value: props.attempt.speed_score, max: 100 },
    { label: 'Accuracy', value: props.attempt.accuracy_score, max: 100 },
    { label: 'Stability', value: props.attempt.stability_score, max: 100 },
]);
</script>

<template>
    <Head title="Kraepelin Result" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>Kraepelin Test Result</CardTitle>
                    <CardDescription>
                        {{ (testAttempt.test as any)?.program?.name }} &middot; {{ (testAttempt.test as any)?.testType?.name }}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-muted-foreground text-xs">Columns</div>
                            <div class="text-lg font-bold">{{ attempt.columns_count }}</div>
                        </div>
                        <div>
                            <div class="text-muted-foreground text-xs">Numbers/Column</div>
                            <div class="text-lg font-bold">{{ attempt.numbers_per_column }}</div>
                        </div>
                        <div>
                            <div class="text-muted-foreground text-xs">Skipped</div>
                            <div class="text-lg font-bold">{{ attempt.total_skipped ?? 0 }}</div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Performance Scores</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div v-for="s in scores" :key="s.label" class="space-y-1">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium">{{ s.label }}</span>
                            <span class="font-bold">{{ s.value ?? '-' }}</span>
                        </div>
                        <Progress :model-value="s.value ?? 0" class="h-3" />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Status</CardTitle>
                </CardHeader>
                <CardContent class="text-center">
                    <Badge :variant="attempt.status === 'scored' ? 'default' : 'secondary'" class="text-sm">
                        {{ attempt.status }}
                    </Badge>
                    <p class="text-muted-foreground mt-2 text-xs">
                        Submitted: {{ attempt.submitted_at ? new Date(attempt.submitted_at).toLocaleString('id-ID') : '-' }}
                    </p>
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
