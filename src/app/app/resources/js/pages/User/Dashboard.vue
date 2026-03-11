<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
import type { BreadcrumbItem } from '@/types';
import type { Subscription, TestAttempt, UserProgress } from '@/types/models';

const props = defineProps<{
    activeSubscription: (Subscription & { plan?: { id: string; name: string; code: string } }) | null;
    recentAttempts: TestAttempt[];
    progress: UserProgress[];
    stats: { total_attempts: number; completed_attempts: number; average_score: number };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <Card>
                    <CardHeader class="pb-2"><CardTitle class="text-sm text-muted-foreground">Total Attempts</CardTitle></CardHeader>
                    <CardContent><div class="text-2xl font-bold">{{ stats.total_attempts }}</div></CardContent>
                </Card>
                <Card>
                    <CardHeader class="pb-2"><CardTitle class="text-sm text-muted-foreground">Completed</CardTitle></CardHeader>
                    <CardContent><div class="text-2xl font-bold">{{ stats.completed_attempts }}</div></CardContent>
                </Card>
                <Card>
                    <CardHeader class="pb-2"><CardTitle class="text-sm text-muted-foreground">Average Score</CardTitle></CardHeader>
                    <CardContent><div class="text-2xl font-bold">{{ stats.average_score.toFixed(1) }}</div></CardContent>
                </Card>
            </div>

            <!-- Active Subscription -->
            <Card>
                <CardHeader>
                    <CardTitle>Subscription</CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="activeSubscription" class="flex items-center justify-between">
                        <div>
                            <p class="font-medium">{{ activeSubscription.plan?.name }}</p>
                            <p class="text-sm text-muted-foreground">Expires {{ new Date(activeSubscription.end_at).toLocaleDateString('id-ID') }}</p>
                        </div>
                        <Badge>Active</Badge>
                    </div>
                    <div v-else class="text-center py-4">
                        <p class="text-muted-foreground mb-2">No active subscription</p>
                        <Button @click="router.get('/subscriptions/plans')">View Plans</Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Recent Attempts -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>Recent Attempts</CardTitle>
                        <Button variant="link" @click="router.get('/history')">View All</Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div v-if="recentAttempts.length" class="space-y-3">
                        <div v-for="a in recentAttempts" :key="a.id" class="flex items-center justify-between border-b pb-3 last:border-0">
                            <div>
                                <p class="font-medium">{{ a.test?.title }}</p>
                                <p class="text-xs text-muted-foreground">{{ (a.test as any)?.program?.name }} · {{ (a.test as any)?.testType?.name }}</p>
                            </div>
                            <div class="text-right">
                                <Badge :variant="a.status === 'scored' ? 'default' : 'secondary'">{{ a.status }}</Badge>
                                <p v-if="a.score !== null" class="text-sm font-bold mt-1">{{ a.score }}</p>
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-muted-foreground text-sm text-center py-4">No attempts yet. Start a test to see your progress!</p>
                </CardContent>
            </Card>

            <!-- Progress -->
            <Card v-if="progress.length">
                <CardHeader><CardTitle>Progress by Program</CardTitle></CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div v-for="p in progress" :key="p.id">
                            <div class="flex justify-between text-sm mb-1">
                                <span>{{ p.program?.name ?? 'General' }} — {{ p.testType?.name ?? '' }}</span>
                                <span class="font-medium">{{ p.average_score.toFixed(1) }}%</span>
                            </div>
                            <Progress :model-value="p.average_score" class="h-2" />
                            <p class="text-xs text-muted-foreground mt-1">{{ p.total_attempts }} attempts · {{ p.total_correct }}/{{ p.total_questions_attempted }} correct</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
