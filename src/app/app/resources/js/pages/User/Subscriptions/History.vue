<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import PaginationLinks from '@/components/PaginationLinks.vue';
import type { BreadcrumbItem } from '@/types';
import type { Subscription, PaginatedData } from '@/types/models';

const props = defineProps<{ subscriptions: PaginatedData<Subscription> }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Subscription History', href: '#' },
];

function formatPrice(price: number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(price);
}
const statusColors: Record<string, string> = { active: 'default', pending: 'secondary', expired: 'outline', cancelled: 'destructive' };
</script>

<template>
    <Head title="Subscription History" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <h1 class="text-2xl font-bold">Subscription History</h1>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Plan</TableHead>
                        <TableHead>Price</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Start</TableHead>
                        <TableHead>End</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="s in subscriptions.data" :key="s.id">
                        <TableCell class="font-medium">{{ s.plan?.name }}</TableCell>
                        <TableCell>{{ formatPrice(s.plan?.price ?? 0) }}</TableCell>
                        <TableCell><Badge :variant="(statusColors[s.status] ?? 'default') as any">{{ s.status }}</Badge></TableCell>
                        <TableCell>{{ new Date(s.start_at).toLocaleDateString('id-ID') }}</TableCell>
                        <TableCell>{{ new Date(s.end_at).toLocaleDateString('id-ID') }}</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <PaginationLinks :paginator="subscriptions" />
        </div>
    </AppLayout>
</template>
