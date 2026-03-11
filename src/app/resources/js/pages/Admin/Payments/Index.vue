<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import PaginationLinks from '@/components/PaginationLinks.vue';
import { Eye } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { Payment, PaginatedData } from '@/types/models';

type Props = { payments: PaginatedData<Payment> };
defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Payments', href: '/admin/payments' },
];

const statusVariant: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    paid: 'default', pending: 'secondary', failed: 'destructive', refunded: 'outline',
};
</script>

<template>
    <Head title="Payments" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <h2 class="text-2xl font-bold tracking-tight">Payments</h2>
            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Invoice</TableHead>
                            <TableHead>User</TableHead>
                            <TableHead>Amount</TableHead>
                            <TableHead>Method</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Date</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="payment in payments.data" :key="payment.id">
                            <TableCell class="font-medium">{{ payment.invoice_number }}</TableCell>
                            <TableCell>{{ payment.user?.name }}</TableCell>
                            <TableCell>Rp {{ payment.amount.toLocaleString('id-ID') }}</TableCell>
                            <TableCell>{{ payment.payment_method }}</TableCell>
                            <TableCell><Badge :variant="statusVariant[payment.status] ?? 'secondary'">{{ payment.status }}</Badge></TableCell>
                            <TableCell>{{ new Date(payment.created_at).toLocaleDateString('id-ID') }}</TableCell>
                            <TableCell class="text-right">
                                <Button variant="ghost" size="icon" as-child><Link :href="`/admin/payments/${payment.id}`"><Eye class="h-4 w-4" /></Link></Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
            <PaginationLinks :links="payments.links" :from="payments.from" :to="payments.to" :total="payments.total" />
        </div>
    </AdminLayout>
</template>
