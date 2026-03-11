<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import PaginationLinks from '@/components/PaginationLinks.vue';
import type { BreadcrumbItem } from '@/types';
import type { PsychotestAspect, PaginatedData } from '@/types/models';
import { ref, watch } from 'vue';
import { Eye, Pencil, Trash2, Plus } from 'lucide-vue-next';

const props = defineProps<{ aspects: PaginatedData<PsychotestAspect> }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Psychotest', href: '#' },
    { title: 'Aspects', href: '#' },
];

const search = ref(new URLSearchParams(window.location.search).get('search') ?? '');
let debounce: ReturnType<typeof setTimeout>;
watch(search, (v) => {
    clearTimeout(debounce);
    debounce = setTimeout(() => router.get('/admin/psychotest/aspects', v ? { search: v } : {}, { preserveState: true, replace: true }), 300);
});

function destroy(id: string) {
    if (confirm('Delete this aspect?')) router.delete(`/admin/psychotest/aspects/${id}`);
}
</script>

<template>
    <Head title="Psychotest Aspects" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <Input v-model="search" placeholder="Search aspects..." class="max-w-sm" />
                <Button as="a" href="/admin/psychotest/aspects/create"><Plus class="mr-2 h-4 w-4" />Create Aspect</Button>
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-14">#</TableHead>
                        <TableHead>Code</TableHead>
                        <TableHead>Name</TableHead>
                        <TableHead>Characteristics</TableHead>
                        <TableHead class="w-32">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="a in aspects.data" :key="a.id">
                        <TableCell>{{ a.sort_order }}</TableCell>
                        <TableCell class="font-mono">{{ a.code }}</TableCell>
                        <TableCell class="font-medium">{{ a.name }}</TableCell>
                        <TableCell>{{ a.characteristics_count ?? 0 }}</TableCell>
                        <TableCell class="flex gap-1">
                            <Button variant="ghost" size="icon" @click="router.get(`/admin/psychotest/aspects/${a.id}`)"><Eye class="h-4 w-4" /></Button>
                            <Button variant="ghost" size="icon" @click="router.get(`/admin/psychotest/aspects/${a.id}/edit`)"><Pencil class="h-4 w-4" /></Button>
                            <Button variant="ghost" size="icon" @click="destroy(a.id)"><Trash2 class="h-4 w-4" /></Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <PaginationLinks :paginator="aspects" />
        </div>
    </AdminLayout>
</template>
