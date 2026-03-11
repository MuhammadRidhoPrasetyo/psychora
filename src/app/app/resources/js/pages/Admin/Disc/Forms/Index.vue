<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import PaginationLinks from '@/components/PaginationLinks.vue';
import type { BreadcrumbItem } from '@/types';
import type { DiscForm, PaginatedData } from '@/types/models';
import { ref } from 'vue';

type FormRow = DiscForm & { test?: { id: string; title: string }; questions_count?: number };
const props = defineProps<{ forms: PaginatedData<FormRow> }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'DISC', href: '#' },
    { title: 'Forms', href: '/admin/disc/forms' },
];

const search = ref('');
function filter() {
    router.get('/admin/disc/forms', { search: search.value || undefined }, { preserveState: true });
}
function destroy(id: string) {
    if (confirm('Delete this DISC form?')) router.delete(`/admin/disc/forms/${id}`);
}
</script>

<template>
    <Head title="DISC Forms" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="flex items-center justify-between mb-4">
            <Input v-model="search" placeholder="Search..." class="w-48" @keyup.enter="filter" />
            <Button as="a" href="/admin/disc/forms/create">Create Form</Button>
        </div>
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>Name</TableHead>
                    <TableHead>Test</TableHead>
                    <TableHead>Questions</TableHead>
                    <TableHead class="w-36">Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="f in forms.data" :key="f.id">
                    <TableCell class="font-medium">{{ f.name }}</TableCell>
                    <TableCell>{{ f.test?.title }}</TableCell>
                    <TableCell>{{ f.questions_count ?? 0 }}</TableCell>
                    <TableCell class="flex gap-1">
                        <Button variant="ghost" size="sm" as="a" :href="`/admin/disc/forms/${f.id}`">View</Button>
                        <Button variant="ghost" size="sm" as="a" :href="`/admin/disc/forms/${f.id}/edit`">Edit</Button>
                        <Button variant="ghost" size="sm" @click="destroy(f.id)">Delete</Button>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
        <PaginationLinks :links="forms.links" :from="forms.from" :to="forms.to" :total="forms.total" class="mt-4" />
    </AdminLayout>
</template>
