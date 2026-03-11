<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import PaginationLinks from '@/components/PaginationLinks.vue';
import type { BreadcrumbItem } from '@/types';
import type { DiscForm, DiscQuestion, PaginatedData } from '@/types/models';

type QRow = DiscQuestion & {
    options?: { id: string; option_text: string; scorings?: { response_type: string; disc_code: string; score_value: number }[] }[];
};
const props = defineProps<{ form: DiscForm; questions: PaginatedData<QRow> }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'DISC', href: '#' },
    { title: 'Forms', href: '/admin/disc/forms' },
    { title: props.form.name, href: `/admin/disc/forms/${props.form.id}` },
    { title: 'Questions', href: '#' },
];

function destroy(id: string) {
    if (confirm('Delete?')) router.delete(`/admin/disc/forms/${props.form.id}/questions/${id}`, { preserveScroll: true });
}
</script>

<template>
    <Head title="DISC Questions" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Questions for {{ form.name }}</h2>
            <Button as="a" :href="`/admin/disc/forms/${form.id}/questions/create`">Add Question</Button>
        </div>
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead class="w-16">#</TableHead>
                    <TableHead>Options</TableHead>
                    <TableHead>Scorings</TableHead>
                    <TableHead class="w-28">Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="q in questions.data" :key="q.id">
                    <TableCell>{{ q.number }}</TableCell>
                    <TableCell>
                        <div v-for="opt in q.options" :key="opt.id" class="text-sm">{{ opt.option_text }}</div>
                    </TableCell>
                    <TableCell>
                        <div v-for="opt in q.options" :key="opt.id" class="text-sm">
                            <Badge v-for="s in opt.scorings" :key="s.disc_code" variant="outline" class="mr-1 text-xs">
                                {{ s.response_type }}:{{ s.disc_code }}={{ s.score_value }}
                            </Badge>
                        </div>
                    </TableCell>
                    <TableCell class="flex gap-1">
                        <Button variant="ghost" size="sm" as="a" :href="`/admin/disc/forms/${form.id}/questions/${q.id}/edit`">Edit</Button>
                        <Button variant="ghost" size="sm" @click="destroy(q.id)">Delete</Button>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
        <PaginationLinks :links="questions.links" :from="questions.from" :to="questions.to" :total="questions.total" class="mt-4" />
    </AdminLayout>
</template>
