<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import PaginationLinks from '@/components/PaginationLinks.vue';
import type { BreadcrumbItem } from '@/types';
import type { TestAttempt, PaginatedData } from '@/types/models';
import { ref, watch } from 'vue';
import { Eye } from 'lucide-vue-next';

const props = defineProps<{ attempts: PaginatedData<TestAttempt> }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'History', href: '#' },
];

const params = new URLSearchParams(window.location.search);
const status = ref(params.get('status') ?? '');

watch(status, () => {
    const q: Record<string, string> = {};
    if (status.value) q.status = status.value;
    router.get('/history', q, { preserveState: true, replace: true });
});

function viewResult(attempt: TestAttempt) {
    const engine = (attempt.test as any)?.testType?.engine_type ?? 'generic';
    if (engine === 'generic') return router.get(`/tests/result/${attempt.id}`);
    router.get(`/${engine}/result/${attempt.id}`);
}
</script>

<template>
    <Head title="Test History" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Test Attempts</h1>
                <Select v-model="status">
                    <SelectTrigger class="w-40"><SelectValue placeholder="All Status" /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">All</SelectItem>
                        <SelectItem value="in_progress">In Progress</SelectItem>
                        <SelectItem value="submitted">Submitted</SelectItem>
                        <SelectItem value="scored">Scored</SelectItem>
                        <SelectItem value="expired">Expired</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Test</TableHead>
                        <TableHead>Program</TableHead>
                        <TableHead>Type</TableHead>
                        <TableHead>#</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Score</TableHead>
                        <TableHead>Date</TableHead>
                        <TableHead class="w-16" />
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="a in attempts.data" :key="a.id">
                        <TableCell class="font-medium">{{ a.test?.title }}</TableCell>
                        <TableCell>{{ (a.test as any)?.program?.name ?? '-' }}</TableCell>
                        <TableCell><Badge variant="outline">{{ (a.test as any)?.testType?.name }}</Badge></TableCell>
                        <TableCell>{{ a.attempt_no }}</TableCell>
                        <TableCell><Badge :variant="a.status === 'scored' ? 'default' : 'secondary'">{{ a.status }}</Badge></TableCell>
                        <TableCell class="font-bold">{{ a.score ?? '-' }}</TableCell>
                        <TableCell>{{ new Date(a.started_at).toLocaleDateString('id-ID') }}</TableCell>
                        <TableCell>
                            <Button v-if="a.status === 'scored' || a.status === 'submitted'" variant="ghost" size="icon" @click="viewResult(a)"><Eye class="h-4 w-4" /></Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <PaginationLinks :paginator="attempts" />
        </div>
    </AppLayout>
</template>
