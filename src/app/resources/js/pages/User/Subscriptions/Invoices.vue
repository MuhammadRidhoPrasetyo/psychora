<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import PaginationLinks from '@/components/PaginationLinks.vue';
import type { BreadcrumbItem } from '@/types';
import type { Payment, PaginatedData } from '@/types/models';

const props = defineProps<{ payments: PaginatedData<Payment> }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Invoices', href: '#' },
];

function formatPrice(price: number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(price);
}
const statusColors: Record<string, string> = { pending: 'secondary', paid: 'default', failed: 'destructive', refunded: 'outline' };
</script>

<template>
    <Head title="Invoices" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <h1 class="text-2xl font-bold">Invoices & Payments</h1>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Invoice #</TableHead>
                        <TableHead>Plan</TableHead>
                        <TableHead>Amount</TableHead>
                        <TableHead>Method</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Date</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="p in payments.data" :key="p.id">
                        <TableCell class="font-mono text-sm">{{ p.invoice_number }}</TableCell>
                        <TableCell>{{ p.subscription?.plan?.name ?? '-' }}</TableCell>
                        <TableCell class="font-medium">{{ formatPrice(p.amount) }}</TableCell>
                        <TableCell>{{ p.payment_method }}</TableCell>
                        <TableCell><Badge :variant="(statusColors[p.status] ?? 'default') as any">{{ p.status }}</Badge></TableCell>
                        <TableCell>{{ new Date(p.created_at).toLocaleDateString('id-ID') }}</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <PaginationLinks :paginator="payments" />
        </div>
    </AppLayout>
</template>
