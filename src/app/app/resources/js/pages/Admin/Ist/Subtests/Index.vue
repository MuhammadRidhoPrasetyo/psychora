<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import InputError from '@/components/InputError.vue';
import type { BreadcrumbItem } from '@/types';
import type { IstForm, IstSubtest } from '@/types/models';
import { ref } from 'vue';
import { Pencil, Trash2 } from 'lucide-vue-next';

const props = defineProps<{ form: IstForm; subtests: (IstSubtest & { questions_count?: number })[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'IST', href: '#' },
    { title: props.form.name, href: `/admin/ist/forms/${props.form.id}` },
    { title: 'Subtests', href: '#' },
];

const subtestCodes = ['SE', 'WA', 'AN', 'GE', 'ME', 'RA', 'ZR', 'FA', 'WU'];
const editing = ref<string | null>(null);
const addForm = useForm({ subtest_code: 'SE', subtest_name: '', sort_order: 0, duration_minutes: 0, max_score: 0 });
const editForm = useForm({ subtest_code: '', subtest_name: '', sort_order: 0, duration_minutes: 0, max_score: 0 });

function create() {
    addForm.post(`/admin/ist/forms/${props.form.id}/subtests`, { preserveScroll: true, onSuccess: () => addForm.reset() });
}
function startEdit(s: IstSubtest) {
    editing.value = s.id;
    editForm.subtest_code = s.subtest_code;
    editForm.subtest_name = s.subtest_name;
    editForm.sort_order = s.sort_order ?? 0;
    editForm.duration_minutes = s.duration_minutes ?? 0;
    editForm.max_score = s.max_score ?? 0;
}
function update(id: string) {
    editForm.put(`/admin/ist/forms/${props.form.id}/subtests/${id}`, { preserveScroll: true, onSuccess: () => (editing.value = null) });
}
function destroy(id: string) {
    if (confirm('Delete?')) router.delete(`/admin/ist/forms/${props.form.id}/subtests/${id}`, { preserveScroll: true });
}
</script>

<template>
    <Head title="IST Subtests" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <Card>
                <CardHeader><CardTitle>Add Subtest</CardTitle></CardHeader>
                <CardContent>
                    <form @submit.prevent="create" class="flex gap-2 items-end flex-wrap">
                        <div class="grid gap-1">
                            <Label class="text-xs">Code</Label>
                            <Select v-model="addForm.subtest_code">
                                <SelectTrigger class="w-20"><SelectValue /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="c in subtestCodes" :key="c" :value="c">{{ c }}</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="grid gap-1">
                            <Label class="text-xs">Name</Label>
                            <Input v-model="addForm.subtest_name" class="w-40" required />
                        </div>
                        <div class="grid gap-1">
                            <Label class="text-xs">Order</Label>
                            <Input type="number" v-model.number="addForm.sort_order" class="w-16" />
                        </div>
                        <div class="grid gap-1">
                            <Label class="text-xs">Duration</Label>
                            <Input type="number" v-model.number="addForm.duration_minutes" class="w-20" />
                        </div>
                        <div class="grid gap-1">
                            <Label class="text-xs">Max Score</Label>
                            <Input type="number" v-model.number="addForm.max_score" class="w-20" />
                        </div>
                        <Button type="submit" :disabled="addForm.processing">Add</Button>
                    </form>
                    <InputError :message="addForm.errors.subtest_code" class="mt-1" />
                </CardContent>
            </Card>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-16">#</TableHead>
                        <TableHead>Code</TableHead>
                        <TableHead>Name</TableHead>
                        <TableHead>Duration</TableHead>
                        <TableHead>Max</TableHead>
                        <TableHead>Qs</TableHead>
                        <TableHead class="w-28">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-for="s in subtests" :key="s.id">
                        <TableRow v-if="editing !== s.id">
                            <TableCell>{{ s.sort_order }}</TableCell>
                            <TableCell><Badge variant="outline">{{ s.subtest_code }}</Badge></TableCell>
                            <TableCell>{{ s.subtest_name }}</TableCell>
                            <TableCell>{{ s.duration_minutes }} min</TableCell>
                            <TableCell>{{ s.max_score }}</TableCell>
                            <TableCell>{{ s.questions_count ?? 0 }}</TableCell>
                            <TableCell class="flex gap-1">
                                <Button variant="ghost" size="icon" as="a" :href="`/admin/ist/subtests/${s.id}/questions`">Q</Button>
                                <Button variant="ghost" size="icon" @click="startEdit(s)"><Pencil class="h-4 w-4" /></Button>
                                <Button variant="ghost" size="icon" @click="destroy(s.id)"><Trash2 class="h-4 w-4" /></Button>
                            </TableCell>
                        </TableRow>
                        <TableRow v-else>
                            <TableCell><Input type="number" v-model.number="editForm.sort_order" class="w-14" /></TableCell>
                            <TableCell>
                                <Select v-model="editForm.subtest_code">
                                    <SelectTrigger class="w-16"><SelectValue /></SelectTrigger>
                                    <SelectContent><SelectItem v-for="c in subtestCodes" :key="c" :value="c">{{ c }}</SelectItem></SelectContent>
                                </Select>
                            </TableCell>
                            <TableCell><Input v-model="editForm.subtest_name" /></TableCell>
                            <TableCell><Input type="number" v-model.number="editForm.duration_minutes" class="w-16" /></TableCell>
                            <TableCell><Input type="number" v-model.number="editForm.max_score" class="w-16" /></TableCell>
                            <TableCell />
                            <TableCell class="flex gap-1">
                                <Button size="sm" @click="update(s.id)">Save</Button>
                                <Button size="sm" variant="ghost" @click="editing = null">Cancel</Button>
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
        </div>
    </AdminLayout>
</template>
