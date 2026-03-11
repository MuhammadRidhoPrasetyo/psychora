<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import InputError from '@/components/InputError.vue';
import type { BreadcrumbItem } from '@/types';
import type { IstForm, IstSubtest } from '@/types/models';
import { ref } from 'vue';
import { Pencil, Trash2 } from 'lucide-vue-next';

type FormItemRow = {
    id: string; ist_subtest_id: string; is_randomized: boolean;
    number_of_questions: number; sort_order: number; minimum_score: number;
    multiplier: number; duration_minutes: number; clue_first: boolean;
    subtest?: { id: string; subtest_code: string; subtest_name: string };
};
const props = defineProps<{ form: IstForm; formItems: FormItemRow[]; subtests: IstSubtest[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'IST', href: '#' },
    { title: props.form.name, href: `/admin/ist/forms/${props.form.id}` },
    { title: 'Form Items', href: '#' },
];

const editing = ref<string | null>(null);
const addForm = useForm({ ist_subtest_id: '', is_randomized: false, number_of_questions: 0, sort_order: 0, minimum_score: 0, multiplier: 1, duration_minutes: 0, clue_first: false });
const editForm = useForm({ ist_subtest_id: '', is_randomized: false, number_of_questions: 0, sort_order: 0, minimum_score: 0, multiplier: 1, duration_minutes: 0, clue_first: false });

function create() {
    addForm.post(`/admin/ist/forms/${props.form.id}/form-items`, { preserveScroll: true, onSuccess: () => addForm.reset() });
}
function startEdit(fi: FormItemRow) {
    editing.value = fi.id;
    Object.assign(editForm, { ist_subtest_id: fi.ist_subtest_id, is_randomized: fi.is_randomized, number_of_questions: fi.number_of_questions, sort_order: fi.sort_order, minimum_score: fi.minimum_score, multiplier: fi.multiplier, duration_minutes: fi.duration_minutes, clue_first: fi.clue_first });
}
function update(id: string) {
    editForm.put(`/admin/ist/forms/${props.form.id}/form-items/${id}`, { preserveScroll: true, onSuccess: () => (editing.value = null) });
}
function destroy(id: string) {
    if (confirm('Delete?')) router.delete(`/admin/ist/forms/${props.form.id}/form-items/${id}`, { preserveScroll: true });
}
</script>

<template>
    <Head title="IST Form Items" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <Card>
                <CardHeader><CardTitle>Add Form Item</CardTitle></CardHeader>
                <CardContent>
                    <form @submit.prevent="create" class="flex gap-2 items-end flex-wrap">
                        <div class="grid gap-1">
                            <Label class="text-xs">Subtest</Label>
                            <Select v-model="addForm.ist_subtest_id">
                                <SelectTrigger class="w-32"><SelectValue placeholder="Select" /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="s in subtests" :key="s.id" :value="s.id">{{ s.subtest_code }}</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="grid gap-1">
                            <Label class="text-xs">Questions</Label>
                            <Input type="number" v-model.number="addForm.number_of_questions" class="w-20" />
                        </div>
                        <div class="grid gap-1">
                            <Label class="text-xs">Order</Label>
                            <Input type="number" v-model.number="addForm.sort_order" class="w-16" />
                        </div>
                        <div class="grid gap-1">
                            <Label class="text-xs">Duration</Label>
                            <Input type="number" v-model.number="addForm.duration_minutes" class="w-16" />
                        </div>
                        <Button type="submit" :disabled="addForm.processing">Add</Button>
                    </form>
                    <InputError :message="addForm.errors.ist_subtest_id" class="mt-1" />
                </CardContent>
            </Card>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>#</TableHead>
                        <TableHead>Subtest</TableHead>
                        <TableHead>Qs</TableHead>
                        <TableHead>Duration</TableHead>
                        <TableHead>Min</TableHead>
                        <TableHead>Mult</TableHead>
                        <TableHead>Rand</TableHead>
                        <TableHead class="w-24">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-for="fi in formItems" :key="fi.id">
                        <TableRow v-if="editing !== fi.id">
                            <TableCell>{{ fi.sort_order }}</TableCell>
                            <TableCell><Badge variant="outline">{{ fi.subtest?.subtest_code }}</Badge></TableCell>
                            <TableCell>{{ fi.number_of_questions }}</TableCell>
                            <TableCell>{{ fi.duration_minutes }} min</TableCell>
                            <TableCell>{{ fi.minimum_score }}</TableCell>
                            <TableCell>{{ fi.multiplier }}x</TableCell>
                            <TableCell>{{ fi.is_randomized ? 'Yes' : 'No' }}</TableCell>
                            <TableCell class="flex gap-1">
                                <Button variant="ghost" size="icon" @click="startEdit(fi)"><Pencil class="h-4 w-4" /></Button>
                                <Button variant="ghost" size="icon" @click="destroy(fi.id)"><Trash2 class="h-4 w-4" /></Button>
                            </TableCell>
                        </TableRow>
                        <TableRow v-else>
                            <TableCell><Input type="number" v-model.number="editForm.sort_order" class="w-14" /></TableCell>
                            <TableCell>
                                <Select v-model="editForm.ist_subtest_id">
                                    <SelectTrigger class="w-20"><SelectValue /></SelectTrigger>
                                    <SelectContent><SelectItem v-for="s in subtests" :key="s.id" :value="s.id">{{ s.subtest_code }}</SelectItem></SelectContent>
                                </Select>
                            </TableCell>
                            <TableCell><Input type="number" v-model.number="editForm.number_of_questions" class="w-14" /></TableCell>
                            <TableCell><Input type="number" v-model.number="editForm.duration_minutes" class="w-14" /></TableCell>
                            <TableCell><Input type="number" v-model.number="editForm.minimum_score" class="w-14" /></TableCell>
                            <TableCell><Input type="number" v-model.number="editForm.multiplier" class="w-14" /></TableCell>
                            <TableCell />
                            <TableCell class="flex gap-1">
                                <Button size="sm" @click="update(fi.id)">Save</Button>
                                <Button size="sm" variant="ghost" @click="editing = null">Cancel</Button>
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
        </div>
    </AdminLayout>
</template>
