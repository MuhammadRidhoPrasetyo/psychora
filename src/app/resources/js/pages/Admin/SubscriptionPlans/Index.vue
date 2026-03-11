<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Plus, Pencil, Trash2, Eye } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { SubscriptionPlan } from '@/types/models';

type Props = { plans: SubscriptionPlan[] };
defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Subscription Plans', href: '/admin/subscription-plans' },
];

function destroy(plan: SubscriptionPlan) {
    if (confirm(`Delete "${plan.name}"?`)) router.delete(`/admin/subscription-plans/${plan.id}`);
}
</script>

<template>
    <Head title="Subscription Plans" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold tracking-tight">Subscription Plans</h2>
                <Button as-child><Link href="/admin/subscription-plans/create"><Plus class="mr-2 h-4 w-4" /> Add Plan</Link></Button>
            </div>
            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Code</TableHead>
                            <TableHead>Price</TableHead>
                            <TableHead>Duration</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="plan in plans" :key="plan.id">
                            <TableCell class="font-medium">{{ plan.name }}</TableCell>
                            <TableCell>{{ plan.code }}</TableCell>
                            <TableCell>Rp {{ plan.price.toLocaleString('id-ID') }}</TableCell>
                            <TableCell>{{ plan.duration_days }} days</TableCell>
                            <TableCell><Badge :variant="plan.is_active ? 'default' : 'secondary'">{{ plan.is_active ? 'Active' : 'Inactive' }}</Badge></TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-1">
                                    <Button variant="ghost" size="icon" as-child><Link :href="`/admin/subscription-plans/${plan.id}`"><Eye class="h-4 w-4" /></Link></Button>
                                    <Button variant="ghost" size="icon" as-child><Link :href="`/admin/subscription-plans/${plan.id}/edit`"><Pencil class="h-4 w-4" /></Link></Button>
                                    <Button variant="ghost" size="icon" @click="destroy(plan)"><Trash2 class="h-4 w-4" /></Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AdminLayout>
</template>
