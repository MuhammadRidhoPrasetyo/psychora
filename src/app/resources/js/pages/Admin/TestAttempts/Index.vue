<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import PaginationLinks from '@/components/PaginationLinks.vue';
import type { BreadcrumbItem } from '@/types';
import type { TestAttempt, PaginatedData } from '@/types/models';
import { ref, watch } from 'vue';
import { Eye } from 'lucide-vue-next';

const props = defineProps<{ attempts: PaginatedData<TestAttempt> }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Test Attempts', href: '#' },
];

const params = new URLSearchParams(window.location.search);
const status = ref(params.get('status') ?? '');

watch(status, () => applyFilters());
function applyFilters() {
    const q: Record<string, string> = {};
    if (status.value) q.status = status.value;
    router.get('/admin/test-attempts', q, { preserveState: true, replace: true });
}

const statusColors: Record<string, string> = {
    in_progress: 'default',
    submitted: 'secondary',
    scored: 'outline',
    expired: 'destructive',
};
</script>

<template>
    <Head title="Test Attempts" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <div class="flex gap-3 flex-wrap">
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
                        <TableHead>User</TableHead>
                        <TableHead>Test</TableHead>
                        <TableHead>Program</TableHead>
                        <TableHead>Attempt #</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Score</TableHead>
                        <TableHead>Started</TableHead>
                        <TableHead class="w-16">View</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="a in attempts.data" :key="a.id">
                        <TableCell>
                            <div>{{ a.user?.name }}</div>
                            <div class="text-xs text-muted-foreground">{{ a.user?.email }}</div>
                        </TableCell>
                        <TableCell>{{ a.test?.title }}</TableCell>
                        <TableCell>{{ (a.test as any)?.program?.name ?? '-' }}</TableCell>
                        <TableCell>{{ a.attempt_no }}</TableCell>
                        <TableCell><Badge :variant="(statusColors[a.status] ?? 'default') as any">{{ a.status }}</Badge></TableCell>
                        <TableCell>{{ a.score ?? '-' }}</TableCell>
                        <TableCell>{{ new Date(a.started_at).toLocaleString('id-ID') }}</TableCell>
                        <TableCell>
                            <Button variant="ghost" size="icon" @click="router.get(`/admin/test-attempts/${a.id}`)"><Eye class="h-4 w-4" /></Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <PaginationLinks :paginator="attempts" />
        </div>
    </AdminLayout>
</template>
