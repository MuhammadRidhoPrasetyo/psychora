<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import PaginationLinks from '@/components/PaginationLinks.vue';
import type { BreadcrumbItem } from '@/types';
import type { KraepelinForm, PaginatedData, Test } from '@/types/models';
import { ref, watch } from 'vue';
import { Pencil, Trash2, Plus } from 'lucide-vue-next';

const props = defineProps<{ forms: PaginatedData<KraepelinForm & { test?: { id: string; title: string } }> }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Kraepelin', href: '#' },
    { title: 'Forms', href: '#' },
];

const search = ref(new URLSearchParams(window.location.search).get('search') ?? '');
let debounce: ReturnType<typeof setTimeout>;
watch(search, (v) => {
    clearTimeout(debounce);
    debounce = setTimeout(() => router.get('/admin/kraepelin/forms', v ? { search: v } : {}, { preserveState: true, replace: true }), 300);
});

function destroy(id: string) {
    if (confirm('Delete this form?')) router.delete(`/admin/kraepelin/forms/${id}`);
}
</script>

<template>
    <Head title="Kraepelin Forms" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <Input v-model="search" placeholder="Search forms..." class="max-w-sm" />
                <Button as="a" href="/admin/kraepelin/forms/create"><Plus class="mr-2 h-4 w-4" />Create Form</Button>
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Title</TableHead>
                        <TableHead>Test</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Created</TableHead>
                        <TableHead class="w-24">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="form in forms.data" :key="form.id">
                        <TableCell class="font-medium">{{ form.title }}</TableCell>
                        <TableCell>{{ form.test?.title ?? '-' }}</TableCell>
                        <TableCell><Badge :variant="form.is_active ? 'default' : 'secondary'">{{ form.is_active ? 'Active' : 'Inactive' }}</Badge></TableCell>
                        <TableCell>{{ new Date(form.created_at).toLocaleDateString('id-ID') }}</TableCell>
                        <TableCell class="flex gap-1">
                            <Button variant="ghost" size="icon" @click="router.get(`/admin/kraepelin/forms/${form.id}/edit`)"><Pencil class="h-4 w-4" /></Button>
                            <Button variant="ghost" size="icon" @click="destroy(form.id)"><Trash2 class="h-4 w-4" /></Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <PaginationLinks :paginator="forms" />
        </div>
    </AdminLayout>
</template>
