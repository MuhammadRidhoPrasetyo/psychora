<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import PaginationLinks from '@/components/PaginationLinks.vue';
import InputError from '@/components/InputError.vue';
import type { BreadcrumbItem } from '@/types';
import type { IstSubtest, PaginatedData } from '@/types/models';
import { ref } from 'vue';
import { Pencil, Trash2 } from 'lucide-vue-next';

type SubtestQuestion = {
    id: string; sort_order: number;
    question?: { id: string; content: string; question_type: string; score: number };
};
const props = defineProps<{
    subtest: IstSubtest & { form?: { id: string; name: string; test_id: string } };
    subtestQuestions: PaginatedData<SubtestQuestion>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'IST', href: '#' },
    { title: props.subtest.form?.name ?? 'Form', href: props.subtest.form ? `/admin/ist/forms/${props.subtest.form.id}` : '#' },
    { title: props.subtest.subtest_code, href: '#' },
    { title: 'Questions', href: '#' },
];

const editing = ref<string | null>(null);
const addForm = useForm({ question_id: '', sort_order: 0 });
const editForm = useForm({ sort_order: 0 });

function create() {
    addForm.post(`/admin/ist/subtests/${props.subtest.id}/questions`, { preserveScroll: true, onSuccess: () => addForm.reset() });
}
function startEdit(sq: SubtestQuestion) {
    editing.value = sq.id;
    editForm.sort_order = sq.sort_order;
}
function update(id: string) {
    editForm.put(`/admin/ist/subtests/${props.subtest.id}/questions/${id}`, { preserveScroll: true, onSuccess: () => (editing.value = null) });
}
function destroy(id: string) {
    if (confirm('Remove this question?')) router.delete(`/admin/ist/subtests/${props.subtest.id}/questions/${id}`, { preserveScroll: true });
}
</script>

<template>
    <Head :title="`IST ${subtest.subtest_code} Questions`" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <Card>
                <CardHeader><CardTitle>Add Question to {{ subtest.subtest_code }}</CardTitle></CardHeader>
                <CardContent>
                    <form @submit.prevent="create" class="flex gap-3 items-end flex-wrap">
                        <div class="grid gap-1 flex-1 min-w-[200px]">
                            <Label class="text-xs">Question ID</Label>
                            <Input v-model="addForm.question_id" placeholder="Enter question UUID" />
                            <InputError :message="addForm.errors.question_id" />
                        </div>
                        <div class="grid gap-1 w-24">
                            <Label class="text-xs">Order</Label>
                            <Input type="number" v-model.number="addForm.sort_order" />
                        </div>
                        <Button type="submit" :disabled="addForm.processing">Add</Button>
                    </form>
                </CardContent>
            </Card>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-14">#</TableHead>
                        <TableHead>Question</TableHead>
                        <TableHead>Type</TableHead>
                        <TableHead>Score</TableHead>
                        <TableHead class="w-24">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-for="sq in subtestQuestions.data" :key="sq.id">
                        <TableRow v-if="editing !== sq.id">
                            <TableCell>{{ sq.sort_order }}</TableCell>
                            <TableCell class="max-w-md truncate">{{ sq.question?.content ?? sq.id }}</TableCell>
                            <TableCell>{{ sq.question?.question_type ?? '-' }}</TableCell>
                            <TableCell>{{ sq.question?.score ?? 0 }}</TableCell>
                            <TableCell class="flex gap-1">
                                <Button variant="ghost" size="icon" @click="startEdit(sq)"><Pencil class="h-4 w-4" /></Button>
                                <Button variant="ghost" size="icon" @click="destroy(sq.id)"><Trash2 class="h-4 w-4" /></Button>
                            </TableCell>
                        </TableRow>
                        <TableRow v-else>
                            <TableCell><Input type="number" v-model.number="editForm.sort_order" class="w-14" /></TableCell>
                            <TableCell colspan="2">{{ sq.question?.content ?? sq.id }}</TableCell>
                            <TableCell />
                            <TableCell class="flex gap-1">
                                <Button size="sm" @click="update(sq.id)">Save</Button>
                                <Button size="sm" variant="ghost" @click="editing = null">Cancel</Button>
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
            <PaginationLinks :paginator="subtestQuestions" />
        </div>
    </AdminLayout>
</template>
