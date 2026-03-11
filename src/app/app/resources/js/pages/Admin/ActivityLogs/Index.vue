<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import PaginationLinks from '@/components/PaginationLinks.vue';
import type { BreadcrumbItem } from '@/types';
import type { ActivityLog, PaginatedData } from '@/types/models';
import { ref, watch } from 'vue';
import { Eye } from 'lucide-vue-next';

const props = defineProps<{ logs: PaginatedData<ActivityLog> }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Activity Logs', href: '#' },
];

const params = new URLSearchParams(window.location.search);
const search = ref(params.get('search') ?? '');
const action = ref(params.get('action') ?? '');

let debounce: ReturnType<typeof setTimeout>;
watch([search, action], () => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        const q: Record<string, string> = {};
        if (search.value) q.search = search.value;
        if (action.value) q.action = action.value;
        router.get('/admin/activity-logs', q, { preserveState: true, replace: true });
    }, 300);
});
</script>

<template>
    <Head title="Activity Logs" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <div class="flex gap-3 flex-wrap">
                <Input v-model="search" placeholder="Search logs..." class="max-w-sm" />
                <Select v-model="action">
                    <SelectTrigger class="w-40"><SelectValue placeholder="All Actions" /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">All</SelectItem>
                        <SelectItem value="created">Created</SelectItem>
                        <SelectItem value="updated">Updated</SelectItem>
                        <SelectItem value="deleted">Deleted</SelectItem>
                        <SelectItem value="login">Login</SelectItem>
                        <SelectItem value="logout">Logout</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Time</TableHead>
                        <TableHead>User</TableHead>
                        <TableHead>Action</TableHead>
                        <TableHead>Subject</TableHead>
                        <TableHead>Description</TableHead>
                        <TableHead class="w-16">View</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="log in logs.data" :key="log.id">
                        <TableCell class="whitespace-nowrap text-sm">{{ new Date(log.created_at).toLocaleString('id-ID') }}</TableCell>
                        <TableCell>{{ log.user?.name ?? 'System' }}</TableCell>
                        <TableCell><Badge variant="outline">{{ log.action }}</Badge></TableCell>
                        <TableCell class="text-sm">{{ log.subject_type?.split('\\').pop() ?? '-' }}</TableCell>
                        <TableCell class="max-w-xs truncate text-sm">{{ log.description ?? '-' }}</TableCell>
                        <TableCell>
                            <Button variant="ghost" size="icon" @click="router.get(`/admin/activity-logs/${log.id}`)"><Eye class="h-4 w-4" /></Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <PaginationLinks :paginator="logs" />
        </div>
    </AdminLayout>
</template>
