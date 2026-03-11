<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { BreadcrumbItem } from '@/types';
import type { Test, TestAttempt } from '@/types/models';

const props = defineProps<{ test: Test; attemptCount: number; lastAttempt: TestAttempt | null }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Programs', href: '/programs' },
    { title: props.test.program?.name ?? 'Program', href: props.test.program ? `/programs/${props.test.program.id}` : '/programs' },
    { title: props.test.title, href: '#' },
];

function getStartUrl() {
    const engine = props.test.testType?.engine_type ?? 'generic';
    if (engine === 'generic') return `/tests/${props.test.id}/start`;
    return `/${engine}/${props.test.id}/start`;
}
</script>

<template>
    <Head :title="test.title" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>{{ test.title }}</CardTitle>
                    <CardDescription>
                        <Badge variant="outline" class="mr-2">{{ test.testType?.name }}</Badge>
                        <Badge variant="outline" class="mr-2">{{ test.program?.name }}</Badge>
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <p v-if="test.description" class="text-muted-foreground">{{ test.description }}</p>

                    <dl class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <dt class="text-muted-foreground">Duration</dt>
                            <dd class="font-medium">{{ test.duration_minutes ? `${test.duration_minutes} minutes` : 'No limit' }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Questions</dt>
                            <dd class="font-medium">{{ test.total_questions }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Pass Score</dt>
                            <dd class="font-medium">{{ test.pass_score ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Your Attempts</dt>
                            <dd class="font-medium">{{ attemptCount }}</dd>
                        </div>
                    </dl>

                    <div v-if="lastAttempt" class="border rounded-lg p-3">
                        <p class="text-sm text-muted-foreground">Last Attempt</p>
                        <div class="flex items-center justify-between mt-1">
                            <Badge :variant="lastAttempt.status === 'scored' ? 'default' : 'secondary'">{{ lastAttempt.status }}</Badge>
                            <span v-if="lastAttempt.score !== null" class="font-bold">Score: {{ lastAttempt.score }}</span>
                        </div>
                    </div>

                    <form @submit.prevent="router.post(getStartUrl())">
                        <Button type="submit" class="w-full" size="lg">
                            {{ attemptCount > 0 ? 'Retake Test' : 'Start Test' }}
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
