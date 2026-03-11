<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import PaginationLinks from '@/components/PaginationLinks.vue';
import type { BreadcrumbItem } from '@/types';
import type { Test, Program, TestType, PaginatedData } from '@/types/models';
import { ref } from 'vue';

type TestRow = Test & { program?: { id: string; name: string }; test_type?: { id: string; name: string; engine_type: string }; sections_count?: number; questions_count?: number };
const props = defineProps<{ tests: PaginatedData<TestRow>; programs: Program[]; testTypes: TestType[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Tests', href: '/admin/tests' },
];

const search = ref('');
const programFilter = ref('');
const testTypeFilter = ref('');
const statusFilter = ref('');

function filter() {
    router.get('/admin/tests', {
        search: search.value || undefined,
        program_id: programFilter.value || undefined,
        test_type_id: testTypeFilter.value || undefined,
        status: statusFilter.value || undefined,
    }, { preserveState: true });
}

function destroy(id: string) {
    if (confirm('Delete this test?')) router.delete(`/admin/tests/${id}`);
}

function statusVariant(s: string) {
    return s === 'published' ? 'default' : s === 'draft' ? 'secondary' : 'outline';
}
</script>

<template>
    <Head title="Tests" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="flex items-center justify-between mb-4">
            <div class="flex gap-2">
                <Input v-model="search" placeholder="Search..." class="w-40" @keyup.enter="filter" />
                <Select v-model="programFilter" @update:model-value="filter">
                    <SelectTrigger class="w-36"><SelectValue placeholder="All Programs" /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">All Programs</SelectItem>
                        <SelectItem v-for="p in programs" :key="p.id" :value="p.id">{{ p.name }}</SelectItem>
                    </SelectContent>
                </Select>
                <Select v-model="testTypeFilter" @update:model-value="filter">
                    <SelectTrigger class="w-36"><SelectValue placeholder="All Types" /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">All Types</SelectItem>
                        <SelectItem v-for="t in testTypes" :key="t.id" :value="t.id">{{ t.name }}</SelectItem>
                    </SelectContent>
                </Select>
                <Select v-model="statusFilter" @update:model-value="filter">
                    <SelectTrigger class="w-32"><SelectValue placeholder="All Status" /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">All Status</SelectItem>
                        <SelectItem value="draft">Draft</SelectItem>
                        <SelectItem value="published">Published</SelectItem>
                        <SelectItem value="archived">Archived</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <Button as="a" href="/admin/tests/create">Create Test</Button>
        </div>
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>Title</TableHead>
                    <TableHead>Program</TableHead>
                    <TableHead>Type</TableHead>
                    <TableHead>Sections</TableHead>
                    <TableHead>Questions</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead class="w-36">Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="test in tests.data" :key="test.id">
                    <TableCell class="font-medium">{{ test.title }}</TableCell>
                    <TableCell>{{ test.program?.name }}</TableCell>
                    <TableCell><Badge variant="outline">{{ test.test_type?.name }}</Badge></TableCell>
                    <TableCell>{{ test.sections_count ?? 0 }}</TableCell>
                    <TableCell>{{ test.questions_count ?? 0 }}</TableCell>
                    <TableCell><Badge :variant="statusVariant(test.status)">{{ test.status }}</Badge></TableCell>
                    <TableCell class="flex gap-1">
                        <Button variant="ghost" size="sm" as="a" :href="`/admin/tests/${test.id}`">View</Button>
                        <Button variant="ghost" size="sm" as="a" :href="`/admin/tests/${test.id}/edit`">Edit</Button>
                        <Button variant="ghost" size="sm" @click="destroy(test.id)">Delete</Button>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
        <PaginationLinks :links="tests.links" :from="tests.from" :to="tests.to" :total="tests.total" class="mt-4" />
    </AdminLayout>
</template>
