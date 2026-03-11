<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { TestType } from '@/types/models';

type Props = { testTypes: TestType[] };
defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Test Types', href: '/admin/test-types' },
];

function destroy(tt: TestType) {
    if (confirm(`Delete "${tt.name}"?`)) router.delete(`/admin/test-types/${tt.id}`);
}
</script>

<template>
    <Head title="Test Types" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold tracking-tight">Test Types</h2>
                <Button as-child><Link href="/admin/test-types/create"><Plus class="mr-2 h-4 w-4" /> Add Test Type</Link></Button>
            </div>
            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Engine</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Order</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="tt in testTypes" :key="tt.id">
                            <TableCell class="font-medium">{{ tt.name }}</TableCell>
                            <TableCell><Badge variant="outline">{{ tt.engine_type }}</Badge></TableCell>
                            <TableCell><Badge :variant="tt.is_active ? 'default' : 'secondary'">{{ tt.is_active ? 'Active' : 'Inactive' }}</Badge></TableCell>
                            <TableCell>{{ tt.sort_order }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-1">
                                    <Button variant="ghost" size="icon" as-child><Link :href="`/admin/test-types/${tt.id}/edit`"><Pencil class="h-4 w-4" /></Link></Button>
                                    <Button variant="ghost" size="icon" @click="destroy(tt)"><Trash2 class="h-4 w-4" /></Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AdminLayout>
</template>
