<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Progress } from '@/components/ui/progress';
import type { BreadcrumbItem } from '@/types';
import type { UserProgress } from '@/types/models';

defineProps<{ progress: UserProgress[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Progress', href: '#' },
];
</script>

<template>
    <Head title="Learning Progress" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <h1 class="text-2xl font-bold">Learning Progress</h1>
            <div v-if="progress.length === 0" class="text-muted-foreground py-8 text-center">
                No progress data yet. Start taking tests to see your progress.
            </div>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="p in progress" :key="p.id">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-base">{{ p.program?.name ?? 'General' }}</CardTitle>
                        <Badge variant="outline" class="w-fit">{{ p.testType?.name ?? '-' }}</Badge>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-muted-foreground">Average Score</span>
                            <span class="font-bold">{{ p.average_score?.toFixed(1) ?? 0 }}%</span>
                        </div>
                        <Progress :model-value="p.average_score ?? 0" class="h-2" />
                        <div class="grid grid-cols-3 gap-2 text-center text-sm">
                            <div>
                                <div class="text-muted-foreground text-xs">Attempts</div>
                                <div class="font-semibold">{{ p.total_attempts }}</div>
                            </div>
                            <div>
                                <div class="text-muted-foreground text-xs">Correct</div>
                                <div class="font-semibold text-green-600">{{ p.total_correct }}</div>
                            </div>
                            <div>
                                <div class="text-muted-foreground text-xs">Questions</div>
                                <div class="font-semibold">{{ p.total_questions_attempted }}</div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
