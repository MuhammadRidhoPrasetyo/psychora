<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import type { BreadcrumbItem } from '@/types';
import type { DiscAttempt, TestAttempt } from '@/types/models';
import { computed } from 'vue';

const props = defineProps<{
    attempt: DiscAttempt;
    testAttempt: TestAttempt;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'History', href: '/history' },
    { title: 'DISC Result', href: '#' },
];

const result = computed(() => props.attempt.result);

const profiles = computed(() => {
    if (!result.value) return [];
    return [
        { code: 'D', label: 'Dominance', score: result.value.score_d, color: 'bg-red-500' },
        { code: 'I', label: 'Influence', score: result.value.score_i, color: 'bg-yellow-500' },
        { code: 'S', label: 'Steadiness', score: result.value.score_s, color: 'bg-green-500' },
        { code: 'C', label: 'Compliance', score: result.value.score_c, color: 'bg-blue-500' },
    ];
});

const maxScore = computed(() => {
    if (!result.value) return 1;
    return Math.max(result.value.score_d, result.value.score_i, result.value.score_s, result.value.score_c, 1);
});
</script>

<template>
    <Head :title="`DISC Result - ${attempt.form?.name}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>{{ attempt.form?.name }}</CardTitle>
                    <CardDescription>
                        {{ (testAttempt.test as any)?.program?.name }} &middot; {{ (testAttempt.test as any)?.testType?.name }}
                    </CardDescription>
                </CardHeader>
                <CardContent class="text-center">
                    <div class="mb-2 text-sm text-muted-foreground">Dominant Profile</div>
                    <div class="text-4xl font-bold">{{ result?.dominant_profile ?? '-' }}</div>
                </CardContent>
            </Card>

            <!-- DISC Scores -->
            <Card>
                <CardHeader>
                    <CardTitle>DISC Profile Scores</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div v-for="p in profiles" :key="p.code" class="space-y-1">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium">
                                <span class="mr-2 inline-block h-3 w-3 rounded-full" :class="p.color" />
                                {{ p.code }} - {{ p.label }}
                            </span>
                            <span class="font-bold">{{ p.score }}</span>
                        </div>
                        <Progress :model-value="(p.score / maxScore) * 100" class="h-3" />
                    </div>
                </CardContent>
            </Card>

            <!-- Most/Least Breakdown -->
            <Card v-if="result">
                <CardHeader>
                    <CardTitle>Detailed Breakdown</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="mb-2 text-sm font-semibold">Most Like You</h4>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between"><span>D</span><span class="font-medium">{{ result.most_d }}</span></div>
                                <div class="flex justify-between"><span>I</span><span class="font-medium">{{ result.most_i }}</span></div>
                                <div class="flex justify-between"><span>S</span><span class="font-medium">{{ result.most_s }}</span></div>
                                <div class="flex justify-between"><span>C</span><span class="font-medium">{{ result.most_c }}</span></div>
                                <div class="flex justify-between"><span>★</span><span class="font-medium">{{ result.most_star }}</span></div>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-2 text-sm font-semibold">Least Like You</h4>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between"><span>D</span><span class="font-medium">{{ result.least_d }}</span></div>
                                <div class="flex justify-between"><span>I</span><span class="font-medium">{{ result.least_i }}</span></div>
                                <div class="flex justify-between"><span>S</span><span class="font-medium">{{ result.least_s }}</span></div>
                                <div class="flex justify-between"><span>C</span><span class="font-medium">{{ result.least_c }}</span></div>
                                <div class="flex justify-between"><span>★</span><span class="font-medium">{{ result.least_star }}</span></div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Interpretation -->
            <Card v-if="result?.interpretation">
                <CardHeader>
                    <CardTitle>Interpretation</CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-sm whitespace-pre-line">{{ result.interpretation }}</p>
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
