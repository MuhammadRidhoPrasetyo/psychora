<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { BreadcrumbItem } from '@/types';
import type { Subscription } from '@/types/models';

type Props = { subscription: Subscription };
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Subscriptions', href: '/admin/subscriptions' },
    { title: `#${props.subscription.id.slice(0, 8)}`, href: '#' },
];

function updateStatus(status: string) {
    router.patch(`/admin/subscriptions/${props.subscription.id}/status`, { status }, { preserveScroll: true });
}
</script>

<template>
    <Head title="Subscription Detail" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl space-y-6">
            <Card>
                <CardHeader><CardTitle>Subscription Detail</CardTitle></CardHeader>
                <CardContent class="space-y-3">
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <span class="text-muted-foreground">User</span><span>{{ subscription.user?.name }}</span>
                        <span class="text-muted-foreground">Plan</span><span>{{ subscription.plan?.name }}</span>
                        <span class="text-muted-foreground">Start</span><span>{{ new Date(subscription.start_at).toLocaleDateString('id-ID') }}</span>
                        <span class="text-muted-foreground">End</span><span>{{ new Date(subscription.end_at).toLocaleDateString('id-ID') }}</span>
                        <span class="text-muted-foreground">Status</span>
                        <div>
                            <Badge>{{ subscription.status }}</Badge>
                        </div>
                    </div>
                    <div class="pt-4">
                        <label class="text-sm text-muted-foreground">Update Status</label>
                        <Select :model-value="subscription.status" @update:model-value="updateStatus">
                            <SelectTrigger class="mt-1 w-48"><SelectValue /></SelectTrigger>
                            <SelectContent>
                                <SelectItem value="pending">Pending</SelectItem>
                                <SelectItem value="active">Active</SelectItem>
                                <SelectItem value="expired">Expired</SelectItem>
                                <SelectItem value="cancelled">Cancelled</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
