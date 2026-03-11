<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import InputError from '@/components/InputError.vue';
import type { BreadcrumbItem } from '@/types';
import type { IstForm, IstInstruction, IstSubtest } from '@/types/models';
import { ref } from 'vue';
import { Pencil, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    form: IstForm;
    instructions: (IstInstruction & { subtest?: { id: string; subtest_code: string; subtest_name: string } })[];
    subtests: IstSubtest[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'IST', href: '#' },
    { title: props.form.name, href: `/admin/ist/forms/${props.form.id}` },
    { title: 'Instructions', href: '#' },
];

const editing = ref<string | null>(null);
const addForm = useForm({ ist_subtest_id: '', title: '', content: '', sort_order: 0 });
const editForm = useForm({ ist_subtest_id: '', title: '', content: '', sort_order: 0 });

function create() {
    addForm.post(`/admin/ist/forms/${props.form.id}/instructions`, { preserveScroll: true, onSuccess: () => addForm.reset() });
}
function startEdit(ins: IstInstruction) {
    editing.value = ins.id;
    editForm.ist_subtest_id = ins.ist_subtest_id ?? '';
    editForm.title = ins.title;
    editForm.content = ins.content;
    editForm.sort_order = ins.sort_order;
}
function update(id: string) {
    editForm.put(`/admin/ist/forms/${props.form.id}/instructions/${id}`, { preserveScroll: true, onSuccess: () => (editing.value = null) });
}
function destroy(id: string) {
    if (confirm('Delete this instruction?')) router.delete(`/admin/ist/forms/${props.form.id}/instructions/${id}`, { preserveScroll: true });
}
</script>

<template>
    <Head title="IST Instructions" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <Card>
                <CardHeader><CardTitle>Add Instruction</CardTitle></CardHeader>
                <CardContent>
                    <form @submit.prevent="create" class="space-y-3">
                        <div class="flex gap-3 flex-wrap">
                            <div class="grid gap-1">
                                <Label class="text-xs">Subtest (optional)</Label>
                                <Select v-model="addForm.ist_subtest_id">
                                    <SelectTrigger class="w-36"><SelectValue placeholder="General" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">General</SelectItem>
                                        <SelectItem v-for="s in subtests" :key="s.id" :value="s.id">{{ s.subtest_code }}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="grid gap-1 flex-1 min-w-[200px]">
                                <Label class="text-xs">Title</Label>
                                <Input v-model="addForm.title" placeholder="Instruction title" />
                                <InputError :message="addForm.errors.title" />
                            </div>
                            <div class="grid gap-1 w-20">
                                <Label class="text-xs">Order</Label>
                                <Input type="number" v-model.number="addForm.sort_order" />
                            </div>
                        </div>
                        <div class="grid gap-1">
                            <Label class="text-xs">Content</Label>
                            <Textarea v-model="addForm.content" rows="3" placeholder="Instruction content..." />
                            <InputError :message="addForm.errors.content" />
                        </div>
                        <Button type="submit" :disabled="addForm.processing">Add</Button>
                    </form>
                </CardContent>
            </Card>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-14">#</TableHead>
                        <TableHead>Subtest</TableHead>
                        <TableHead>Title</TableHead>
                        <TableHead>Content</TableHead>
                        <TableHead class="w-24">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-for="ins in instructions" :key="ins.id">
                        <TableRow v-if="editing !== ins.id">
                            <TableCell>{{ ins.sort_order }}</TableCell>
                            <TableCell><Badge variant="outline">{{ ins.subtest?.subtest_code ?? 'General' }}</Badge></TableCell>
                            <TableCell class="font-medium">{{ ins.title }}</TableCell>
                            <TableCell class="max-w-sm truncate">{{ ins.content }}</TableCell>
                            <TableCell class="flex gap-1">
                                <Button variant="ghost" size="icon" @click="startEdit(ins)"><Pencil class="h-4 w-4" /></Button>
                                <Button variant="ghost" size="icon" @click="destroy(ins.id)"><Trash2 class="h-4 w-4" /></Button>
                            </TableCell>
                        </TableRow>
                        <TableRow v-else>
                            <TableCell><Input type="number" v-model.number="editForm.sort_order" class="w-14" /></TableCell>
                            <TableCell>
                                <Select v-model="editForm.ist_subtest_id">
                                    <SelectTrigger class="w-24"><SelectValue /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">General</SelectItem>
                                        <SelectItem v-for="s in subtests" :key="s.id" :value="s.id">{{ s.subtest_code }}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </TableCell>
                            <TableCell><Input v-model="editForm.title" /></TableCell>
                            <TableCell><Textarea v-model="editForm.content" rows="2" /></TableCell>
                            <TableCell class="flex gap-1">
                                <Button size="sm" @click="update(ins.id)">Save</Button>
                                <Button size="sm" variant="ghost" @click="editing = null">Cancel</Button>
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
        </div>
    </AdminLayout>
</template>
