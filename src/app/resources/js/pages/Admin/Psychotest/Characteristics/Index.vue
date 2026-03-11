<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import PaginationLinks from '@/components/PaginationLinks.vue';
import type { BreadcrumbItem } from '@/types';
import type { PsychotestAspect, PsychotestCharacteristic, PaginatedData } from '@/types/models';
import { Eye, Pencil, Trash2, Plus } from 'lucide-vue-next';

const props = defineProps<{ aspect: PsychotestAspect; characteristics: PaginatedData<PsychotestCharacteristic> }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Psychotest', href: '#' },
    { title: 'Aspects', href: '/admin/psychotest/aspects' },
    { title: props.aspect.name, href: `/admin/psychotest/aspects/${props.aspect.id}` },
    { title: 'Characteristics', href: '#' },
];

function destroy(id: string) {
    if (confirm('Delete?')) router.delete(`/admin/psychotest/aspects/${props.aspect.id}/characteristics/${id}`);
}
</script>

<template>
    <Head title="Characteristics" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">{{ aspect.name }} — Characteristics</h2>
                <Button @click="router.get(`/admin/psychotest/aspects/${aspect.id}/characteristics/create`)"><Plus class="mr-2 h-4 w-4" />Add</Button>
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-14">#</TableHead>
                        <TableHead>Code</TableHead>
                        <TableHead>Name</TableHead>
                        <TableHead>Score Levels</TableHead>
                        <TableHead class="w-32">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="c in characteristics.data" :key="c.id">
                        <TableCell>{{ c.sort_order }}</TableCell>
                        <TableCell class="font-mono">{{ c.code }}</TableCell>
                        <TableCell class="font-medium">{{ c.name }}</TableCell>
                        <TableCell>{{ (c as any).scores_count ?? 0 }}</TableCell>
                        <TableCell class="flex gap-1">
                            <Button variant="ghost" size="icon" @click="router.get(`/admin/psychotest/aspects/${aspect.id}/characteristics/${c.id}`)"><Eye class="h-4 w-4" /></Button>
                            <Button variant="ghost" size="icon" @click="router.get(`/admin/psychotest/aspects/${aspect.id}/characteristics/${c.id}/edit`)"><Pencil class="h-4 w-4" /></Button>
                            <Button variant="ghost" size="icon" @click="destroy(c.id)"><Trash2 class="h-4 w-4" /></Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <PaginationLinks :paginator="characteristics" />
        </div>
    </AdminLayout>
</template>
