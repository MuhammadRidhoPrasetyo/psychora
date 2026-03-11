<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { BreadcrumbItem } from '@/types';
import type { IstAttempt, TestAttempt } from '@/types/models';
import { computed } from 'vue';

const props = defineProps<{
    attempt: IstAttempt;
    testAttempt: TestAttempt;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'History', href: '/history' },
    { title: 'IST Result', href: '#' },
];

const subtestAttempts = computed(() => props.attempt.subtestAttempts ?? []);
const results = computed(() => props.attempt.results ?? []);

const overallResult = computed(() => results.value.find(r => r.category === 'overall'));
const categoryResults = computed(() => results.value.filter(r => r.category !== 'overall'));

const maxSubtestScore = computed(() => {
    const max = Math.max(...subtestAttempts.value.map(sa => sa.scaled_score ?? sa.raw_score ?? 0), 1);
    return max;
});
</script>

<template>
    <Head :title="`IST Result - ${attempt.form?.name}`" />
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
                <CardContent>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 text-center">
                        <div>
                            <div class="text-muted-foreground text-xs">Total Score</div>
                            <div class="text-2xl font-bold">{{ attempt.total_score ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-muted-foreground text-xs">IQ Score</div>
                            <div class="text-2xl font-bold">{{ attempt.iq_score ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-muted-foreground text-xs">Status</div>
                            <Badge :variant="attempt.status === 'scored' ? 'default' : 'secondary'" class="mt-1">
                                {{ attempt.status }}
                            </Badge>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Category Results -->
            <Card v-if="categoryResults.length">
                <CardHeader>
                    <CardTitle>Category Scores</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div v-for="r in categoryResults" :key="r.id" class="space-y-1">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium capitalize">{{ r.category }}</span>
                            <div class="flex items-center gap-2">
                                <span class="font-bold">{{ r.scaled_score ?? r.raw_score }}</span>
                                <Badge v-if="r.interpretation" variant="outline" class="text-xs">{{ r.interpretation }}</Badge>
                            </div>
                        </div>
                        <Progress :model-value="r.percentile ?? 50" class="h-2" />
                        <div class="text-muted-foreground text-xs">
                            Raw: {{ r.raw_score }}
                            <span v-if="r.percentile !== null"> &middot; Percentile: {{ r.percentile }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Subtest Scores -->
            <Card>
                <CardHeader>
                    <CardTitle>Subtest Scores</CardTitle>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Code</TableHead>
                                <TableHead>Subtest</TableHead>
                                <TableHead>Raw Score</TableHead>
                                <TableHead>Scaled Score</TableHead>
                                <TableHead>Status</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="sa in subtestAttempts" :key="sa.id">
                                <TableCell class="font-mono font-bold">{{ sa.subtest_code }}</TableCell>
                                <TableCell>{{ sa.subtest?.subtest_name ?? sa.subtest_code }}</TableCell>
                                <TableCell>{{ sa.raw_score ?? '-' }}</TableCell>
                                <TableCell class="font-bold">{{ sa.scaled_score ?? '-' }}</TableCell>
                                <TableCell>
                                    <Badge :variant="sa.status === 'submitted' ? 'default' : 'secondary'">{{ sa.status }}</Badge>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
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
