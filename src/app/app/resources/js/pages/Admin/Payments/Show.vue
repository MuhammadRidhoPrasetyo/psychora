<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { BreadcrumbItem } from '@/types';
import type { Payment } from '@/types/models';

type Props = { payment: Payment };
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Payments', href: '/admin/payments' },
    { title: props.payment.invoice_number, href: '#' },
];

function updateStatus(status: string) {
    router.patch(`/admin/payments/${props.payment.id}/status`, { status }, { preserveScroll: true });
}
</script>

<template>
    <Head :title="payment.invoice_number" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl">
            <Card>
                <CardHeader><CardTitle>Payment {{ payment.invoice_number }}</CardTitle></CardHeader>
                <CardContent class="space-y-3">
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <span class="text-muted-foreground">User</span><span>{{ payment.user?.name }}</span>
                        <span class="text-muted-foreground">Amount</span><span>Rp {{ payment.amount.toLocaleString('id-ID') }}</span>
                        <span class="text-muted-foreground">Method</span><span>{{ payment.payment_method }}</span>
                        <span class="text-muted-foreground">Status</span><Badge>{{ payment.status }}</Badge>
                        <span class="text-muted-foreground">Created</span><span>{{ new Date(payment.created_at).toLocaleDateString('id-ID') }}</span>
                        <span class="text-muted-foreground">Paid</span><span>{{ payment.paid_at ? new Date(payment.paid_at).toLocaleDateString('id-ID') : '-' }}</span>
                    </div>
                    <div class="pt-4">
                        <label class="text-sm text-muted-foreground">Update Status</label>
                        <Select :model-value="payment.status" @update:model-value="updateStatus">
                            <SelectTrigger class="mt-1 w-48"><SelectValue /></SelectTrigger>
                            <SelectContent>
                                <SelectItem value="pending">Pending</SelectItem>
                                <SelectItem value="paid">Paid</SelectItem>
                                <SelectItem value="failed">Failed</SelectItem>
                                <SelectItem value="refunded">Refunded</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
