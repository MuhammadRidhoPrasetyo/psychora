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
import type { Test, TestSection, Question, PaginatedData } from '@/types/models';
import { ref } from 'vue';

type QRow = Question & { section?: TestSection; options?: { id: string; label: string }[]; essay_answers?: { id: string }[] };
const props = defineProps<{ test: Test; questions: PaginatedData<QRow>; sections: TestSection[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Tests', href: '/admin/tests' },
    { title: props.test.title, href: `/admin/tests/${props.test.id}` },
    { title: 'Questions', href: '#' },
];

const sectionFilter = ref('');
const typeFilter = ref('');

function filter() {
    router.get(`/admin/tests/${props.test.id}/questions`, {
        section_id: sectionFilter.value || undefined,
        question_type: typeFilter.value || undefined,
    }, { preserveState: true });
}

function destroy(id: string) {
    if (confirm('Delete this question?')) router.delete(`/admin/tests/${props.test.id}/questions/${id}`);
}
</script>

<template>
    <Head title="Questions" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="flex items-center justify-between mb-4">
            <div class="flex gap-2">
                <Select v-model="sectionFilter" @update:model-value="filter">
                    <SelectTrigger class="w-40"><SelectValue placeholder="All Sections" /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">All Sections</SelectItem>
                        <SelectItem v-for="s in sections" :key="s.id" :value="s.id">{{ s.title }}</SelectItem>
                    </SelectContent>
                </Select>
                <Select v-model="typeFilter" @update:model-value="filter">
                    <SelectTrigger class="w-40"><SelectValue placeholder="All Types" /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">All Types</SelectItem>
                        <SelectItem value="multiple_choice">Multiple Choice</SelectItem>
                        <SelectItem value="essay">Essay</SelectItem>
                        <SelectItem value="true_false">True/False</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <Button as="a" :href="`/admin/tests/${test.id}/questions/create`">Add Question</Button>
        </div>
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead class="w-16">#</TableHead>
                    <TableHead>Content</TableHead>
                    <TableHead>Type</TableHead>
                    <TableHead>Section</TableHead>
                    <TableHead>Options</TableHead>
                    <TableHead class="w-36">Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="(q, i) in questions.data" :key="q.id">
                    <TableCell>{{ q.sort_order ?? i + 1 }}</TableCell>
                    <TableCell class="max-w-xs truncate">{{ q.content }}</TableCell>
                    <TableCell><Badge variant="outline">{{ q.question_type }}</Badge></TableCell>
                    <TableCell>{{ q.section?.title ?? '-' }}</TableCell>
                    <TableCell>{{ q.options?.length ?? 0 }}</TableCell>
                    <TableCell class="flex gap-1">
                        <Button variant="ghost" size="sm" as="a" :href="`/admin/tests/${test.id}/questions/${q.id}`">View</Button>
                        <Button variant="ghost" size="sm" as="a" :href="`/admin/tests/${test.id}/questions/${q.id}/edit`">Edit</Button>
                        <Button variant="ghost" size="sm" @click="destroy(q.id)">Delete</Button>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
        <PaginationLinks :links="questions.links" :from="questions.from" :to="questions.to" :total="questions.total" class="mt-4" />
    </AdminLayout>
</template>
