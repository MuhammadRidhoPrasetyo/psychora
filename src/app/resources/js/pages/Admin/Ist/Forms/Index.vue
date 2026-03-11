<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import PaginationLinks from '@/components/PaginationLinks.vue';
import type { BreadcrumbItem } from '@/types';
import type { IstForm, PaginatedData } from '@/types/models';
import { ref } from 'vue';

type FormRow = IstForm & { test?: { id: string; title: string }; subtests_count?: number };
const props = defineProps<{ forms: PaginatedData<FormRow> }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'IST', href: '#' },
    { title: 'Forms', href: '/admin/ist/forms' },
];

const search = ref('');
function filter() {
    router.get('/admin/ist/forms', { search: search.value || undefined }, { preserveState: true });
}
function destroy(id: string) {
    if (confirm('Delete this IST form?')) router.delete(`/admin/ist/forms/${id}`);
}
</script>

<template>
    <Head title="IST Forms" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="flex items-center justify-between mb-4">
            <Input v-model="search" placeholder="Search..." class="w-48" @keyup.enter="filter" />
            <Button as="a" href="/admin/ist/forms/create">Create Form</Button>
        </div>
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>Name</TableHead>
                    <TableHead>Test</TableHead>
                    <TableHead>Subtests</TableHead>
                    <TableHead class="w-36">Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="f in forms.data" :key="f.id">
                    <TableCell class="font-medium">{{ f.name }}</TableCell>
                    <TableCell>{{ f.test?.title }}</TableCell>
                    <TableCell>{{ f.subtests_count ?? 0 }}</TableCell>
                    <TableCell class="flex gap-1">
                        <Button variant="ghost" size="sm" as="a" :href="`/admin/ist/forms/${f.id}`">View</Button>
                        <Button variant="ghost" size="sm" as="a" :href="`/admin/ist/forms/${f.id}/edit`">Edit</Button>
                        <Button variant="ghost" size="sm" @click="destroy(f.id)">Delete</Button>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
        <PaginationLinks :links="forms.links" :from="forms.from" :to="forms.to" :total="forms.total" class="mt-4" />
    </AdminLayout>
</template>
