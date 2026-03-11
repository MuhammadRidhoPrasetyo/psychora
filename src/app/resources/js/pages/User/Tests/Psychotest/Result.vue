<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { Separator } from '@/components/ui/separator';
import type { BreadcrumbItem } from '@/types';
import type { PsychotestAttempt, TestAttempt } from '@/types/models';
import { computed } from 'vue';

const props = defineProps<{
    attempt: PsychotestAttempt;
    testAttempt: TestAttempt;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'History', href: '/history' },
    { title: 'Psychotest Result', href: '#' },
];

const resultAspects = computed(() => props.attempt.resultAspects ?? []);
const resultCharacteristics = computed(() => props.attempt.resultCharacteristics ?? []);

const maxAspectScore = computed(() => {
    const max = Math.max(...resultAspects.value.map(a => a.raw_score), 1);
    return max;
});
</script>

<template>
    <Head :title="`Psychotest Result - ${attempt.form?.name}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl space-y-6">
            <!-- Summary -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ attempt.form?.name }}</CardTitle>
                    <CardDescription>
                        {{ (testAttempt.test as any)?.program?.name }} &middot; {{ (testAttempt.test as any)?.testType?.name }}
                    </CardDescription>
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

            <!-- Aspect Scores -->
            <Card v-if="resultAspects.length">
                <CardHeader>
                    <CardTitle>Aspect Scores</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div v-for="ra in resultAspects" :key="ra.id" class="space-y-1">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium">{{ ra.aspect?.name ?? ra.psychotest_aspect_id }}</span>
                            <span class="font-bold">{{ ra.scaled_score ?? ra.raw_score }}</span>
                        </div>
                        <Progress :model-value="(ra.raw_score / maxAspectScore) * 100" class="h-2" />
                        <p v-if="ra.aspect?.description" class="text-muted-foreground text-xs">{{ ra.aspect.description }}</p>
                    </div>
                </CardContent>
            </Card>

            <!-- Characteristics -->
            <Card v-if="resultCharacteristics.length">
                <CardHeader>
                    <CardTitle>Characteristic Scores</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div v-for="rc in resultCharacteristics" :key="rc.id">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-medium">{{ rc.characteristic?.name ?? '-' }}</span>
                                <Badge v-if="rc.characteristic?.aspect" variant="outline" class="ml-2 text-xs">
                                    {{ rc.characteristic.aspect.name }}
                                </Badge>
                            </div>
                            <span class="font-bold text-sm">{{ rc.scaled_score ?? rc.raw_score }}</span>
                        </div>
                        <Separator class="mt-2" />
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
