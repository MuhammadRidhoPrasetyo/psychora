<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import type { BreadcrumbItem } from '@/types';
import type { Payment } from '@/types/models';

const props = defineProps<{ payment: Payment }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Payment', href: '#' },
];

function formatPrice(price: number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(price);
}

const statusColors: Record<string, string> = { pending: 'secondary', paid: 'default', failed: 'destructive', refunded: 'outline' };
</script>

<template>
    <Head title="Payment" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-xl mx-auto">
            <Card>
                <CardHeader>
                    <CardTitle>Payment Status</CardTitle>
                    <CardDescription>Invoice: {{ payment.invoice_number }}</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="text-center py-6 border rounded-lg">
                        <Badge :variant="(statusColors[payment.status] ?? 'default') as any" class="text-lg px-4 py-1">
                            {{ payment.status.toUpperCase() }}
                        </Badge>
                        <div class="mt-3 text-2xl font-bold">{{ formatPrice(payment.amount) }}</div>
                    </div>

                    <dl class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <dt class="text-muted-foreground">Payment Method</dt>
                            <dd class="font-medium">{{ payment.payment_method }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Plan</dt>
                            <dd class="font-medium">{{ payment.subscription?.plan?.name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Created</dt>
                            <dd>{{ new Date(payment.created_at).toLocaleString('id-ID') }}</dd>
                        </div>
                        <div v-if="payment.paid_at">
                            <dt class="text-muted-foreground">Paid At</dt>
                            <dd>{{ new Date(payment.paid_at).toLocaleString('id-ID') }}</dd>
                        </div>
                    </dl>

                    <div v-if="payment.status === 'pending'" class="border rounded-lg p-4 bg-muted/50 text-center">
                        <p class="text-sm text-muted-foreground">Please complete your payment. This page will update automatically once payment is confirmed.</p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
