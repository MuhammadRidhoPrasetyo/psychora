<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Pencil } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { SubscriptionPlan } from '@/types/models';

type Props = { plan: SubscriptionPlan };
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Subscription Plans', href: '/admin/subscription-plans' },
    { title: props.plan.name, href: '#' },
];
</script>

<template>
    <Head :title="plan.name" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold">{{ plan.name }}</h2>
                <Button as-child variant="outline"><Link :href="`/admin/subscription-plans/${plan.id}/edit`"><Pencil class="mr-2 h-4 w-4" /> Edit</Link></Button>
            </div>
            <Card>
                <CardHeader><CardTitle>Plan Details</CardTitle></CardHeader>
                <CardContent class="space-y-3">
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <span class="text-muted-foreground">Code</span><span>{{ plan.code }}</span>
                        <span class="text-muted-foreground">Price</span><span>Rp {{ plan.price.toLocaleString('id-ID') }}</span>
                        <span class="text-muted-foreground">Duration</span><span>{{ plan.duration_days }} days</span>
                        <span class="text-muted-foreground">Status</span><Badge :variant="plan.is_active ? 'default' : 'secondary'">{{ plan.is_active ? 'Active' : 'Inactive' }}</Badge>
                    </div>
                </CardContent>
            </Card>
            <Card v-if="plan.entitlements && plan.entitlements.length">
                <CardHeader><CardTitle>Entitlements</CardTitle></CardHeader>
                <CardContent>
                    <div v-for="ent in plan.entitlements" :key="ent.id" class="flex gap-2 py-1 text-sm">
                        <Badge variant="outline">{{ ent.program?.name ?? 'All Programs' }}</Badge>
                        <Badge variant="outline">{{ ent.test_type?.name ?? 'All Test Types' }}</Badge>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
