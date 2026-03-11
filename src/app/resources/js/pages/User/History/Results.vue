<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import PaginationLinks from '@/components/PaginationLinks.vue';
import type { BreadcrumbItem } from '@/types';
import type { TestResult, PaginatedData } from '@/types/models';

defineProps<{ results: PaginatedData<TestResult> }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Results', href: '#' },
];
</script>

<template>
    <Head title="Test Results" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <h1 class="text-2xl font-bold">Test Results</h1>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Test</TableHead>
                        <TableHead>Program</TableHead>
                        <TableHead>Type</TableHead>
                        <TableHead>Attempt #</TableHead>
                        <TableHead>Score</TableHead>
                        <TableHead>Percentage</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Date</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="r in results.data" :key="r.id">
                        <TableCell class="font-medium">{{ (r as any).test?.title ?? '-' }}</TableCell>
                        <TableCell>{{ (r as any).test?.program?.name ?? '-' }}</TableCell>
                        <TableCell><Badge variant="outline">{{ (r as any).test?.testType?.name }}</Badge></TableCell>
                        <TableCell>{{ (r as any).attempt?.attempt_no ?? '-' }}</TableCell>
                        <TableCell class="font-bold">{{ r.total_score }}</TableCell>
                        <TableCell>{{ r.percentage }}%</TableCell>
                        <TableCell>
                            <Badge :variant="r.is_passed ? 'default' : 'destructive'">
                                {{ r.is_passed ? 'Passed' : 'Failed' }}
                            </Badge>
                        </TableCell>
                        <TableCell>{{ new Date(r.created_at).toLocaleDateString('id-ID') }}</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <PaginationLinks :paginator="results" />
        </div>
    </AppLayout>
</template>
