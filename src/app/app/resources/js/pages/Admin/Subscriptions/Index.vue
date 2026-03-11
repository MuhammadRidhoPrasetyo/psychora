<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import PaginationLinks from '@/components/PaginationLinks.vue';
import { Eye } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { Subscription, PaginatedData } from '@/types/models';
import { ref } from 'vue';

type Props = { subscriptions: PaginatedData<Subscription>; filters: { status?: string } };
const props = defineProps<Props>();
const statusFilter = ref(props.filters.status ?? '');

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Subscriptions', href: '/admin/subscriptions' },
];

const statusVariant: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    active: 'default', pending: 'secondary', expired: 'destructive', cancelled: 'outline',
};

function filterByStatus(val: string) {
    router.get('/admin/subscriptions', val ? { status: val } : {}, { preserveState: true });
}
</script>

<template>
    <Head title="Subscriptions" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold tracking-tight">Subscriptions</h2>
                <Select :model-value="statusFilter" @update:model-value="filterByStatus">
                    <SelectTrigger class="w-40"><SelectValue placeholder="All Status" /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">All</SelectItem>
                        <SelectItem value="active">Active</SelectItem>
                        <SelectItem value="pending">Pending</SelectItem>
                        <SelectItem value="expired">Expired</SelectItem>
                        <SelectItem value="cancelled">Cancelled</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>User</TableHead>
                            <TableHead>Plan</TableHead>
                            <TableHead>Start</TableHead>
                            <TableHead>End</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="sub in subscriptions.data" :key="sub.id">
                            <TableCell>{{ sub.user?.name }}</TableCell>
                            <TableCell>{{ sub.plan?.name }}</TableCell>
                            <TableCell>{{ new Date(sub.start_at).toLocaleDateString('id-ID') }}</TableCell>
                            <TableCell>{{ new Date(sub.end_at).toLocaleDateString('id-ID') }}</TableCell>
                            <TableCell><Badge :variant="statusVariant[sub.status] ?? 'secondary'">{{ sub.status }}</Badge></TableCell>
                            <TableCell class="text-right">
                                <Button variant="ghost" size="icon" as-child><Link :href="`/admin/subscriptions/${sub.id}`"><Eye class="h-4 w-4" /></Link></Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
            <PaginationLinks :links="subscriptions.links" :from="subscriptions.from" :to="subscriptions.to" :total="subscriptions.total" />
        </div>
    </AdminLayout>
</template>
