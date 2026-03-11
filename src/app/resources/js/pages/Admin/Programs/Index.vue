<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Plus, Pencil, Trash2, Layers } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { Program } from '@/types/models';

type Props = { programs: Program[] };
defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Programs', href: '/admin/programs' },
];

function destroy(program: Program) {
    if (confirm(`Are you sure you want to delete "${program.name}"?`)) {
        router.delete(`/admin/programs/${program.id}`);
    }
}
</script>

<template>
    <Head title="Programs" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold tracking-tight">Programs</h2>
                <Button as-child><Link href="/admin/programs/create"><Plus class="mr-2 h-4 w-4" /> Add Program</Link></Button>
            </div>
            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Slug</TableHead>
                            <TableHead>Test Types</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Order</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="program in programs" :key="program.id">
                            <TableCell class="font-medium">{{ program.name }}</TableCell>
                            <TableCell class="text-muted-foreground">{{ program.slug }}</TableCell>
                            <TableCell>{{ program.test_types_count ?? 0 }}</TableCell>
                            <TableCell><Badge :variant="program.is_active ? 'default' : 'secondary'">{{ program.is_active ? 'Active' : 'Inactive' }}</Badge></TableCell>
                            <TableCell>{{ program.sort_order }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-1">
                                    <Button variant="ghost" size="icon" as-child><Link :href="`/admin/programs/${program.id}/test-types`"><Layers class="h-4 w-4" /></Link></Button>
                                    <Button variant="ghost" size="icon" as-child><Link :href="`/admin/programs/${program.id}/edit`"><Pencil class="h-4 w-4" /></Link></Button>
                                    <Button variant="ghost" size="icon" @click="destroy(program)"><Trash2 class="h-4 w-4" /></Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AdminLayout>
</template>
