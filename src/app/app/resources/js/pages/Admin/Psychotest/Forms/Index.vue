<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import PaginationLinks from '@/components/PaginationLinks.vue';
import type { BreadcrumbItem } from '@/types';
import type { PsychotestForm, PaginatedData } from '@/types/models';
import { ref, watch } from 'vue';
import { Eye, Pencil, Trash2, Plus } from 'lucide-vue-next';

const props = defineProps<{ forms: PaginatedData<PsychotestForm & { test?: { id: string; title: string } }> }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Psychotest', href: '#' },
    { title: 'Forms', href: '#' },
];

const search = ref(new URLSearchParams(window.location.search).get('search') ?? '');
let debounce: ReturnType<typeof setTimeout>;
watch(search, (v) => {
    clearTimeout(debounce);
    debounce = setTimeout(() => router.get('/admin/psychotest/forms', v ? { search: v } : {}, { preserveState: true, replace: true }), 300);
});

function destroy(id: string) {
    if (confirm('Delete this form?')) router.delete(`/admin/psychotest/forms/${id}`);
}
</script>

<template>
    <Head title="Psychotest Forms" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <Input v-model="search" placeholder="Search forms..." class="max-w-sm" />
                <Button as="a" href="/admin/psychotest/forms/create"><Plus class="mr-2 h-4 w-4" />Create Form</Button>
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Name</TableHead>
                        <TableHead>Test</TableHead>
                        <TableHead>Questions</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Created</TableHead>
                        <TableHead class="w-32">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="f in forms.data" :key="f.id">
                        <TableCell class="font-medium">{{ f.name }}</TableCell>
                        <TableCell>{{ f.test?.title ?? '-' }}</TableCell>
                        <TableCell>{{ f.questions_count ?? 0 }}</TableCell>
                        <TableCell><Badge :variant="f.is_active ? 'default' : 'secondary'">{{ f.is_active ? 'Active' : 'Inactive' }}</Badge></TableCell>
                        <TableCell>{{ new Date(f.created_at).toLocaleDateString('id-ID') }}</TableCell>
                        <TableCell class="flex gap-1">
                            <Button variant="ghost" size="icon" @click="router.get(`/admin/psychotest/forms/${f.id}`)"><Eye class="h-4 w-4" /></Button>
                            <Button variant="ghost" size="icon" @click="router.get(`/admin/psychotest/forms/${f.id}/edit`)"><Pencil class="h-4 w-4" /></Button>
                            <Button variant="ghost" size="icon" @click="destroy(f.id)"><Trash2 class="h-4 w-4" /></Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <PaginationLinks :paginator="forms" />
        </div>
    </AdminLayout>
</template>
