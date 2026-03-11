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
import type { IstForm, IstClue, IstSubtest } from '@/types/models';
import { ref } from 'vue';
import { Pencil, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    form: IstForm;
    clues: (IstClue & { subtest?: { id: string; subtest_code: string; subtest_name: string } })[];
    subtests: IstSubtest[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'IST', href: '#' },
    { title: props.form.name, href: `/admin/ist/forms/${props.form.id}` },
    { title: 'Clues', href: '#' },
];

const editing = ref<string | null>(null);
const addForm = useForm({ ist_subtest_id: '', clue_text: '', duration: 0 });
const editForm = useForm({ ist_subtest_id: '', clue_text: '', duration: 0 });

function create() {
    addForm.post(`/admin/ist/forms/${props.form.id}/clues`, { preserveScroll: true, onSuccess: () => addForm.reset() });
}
function startEdit(clue: IstClue) {
    editing.value = clue.id;
    editForm.ist_subtest_id = clue.ist_subtest_id ?? '';
    editForm.clue_text = clue.clue_text;
    editForm.duration = clue.duration ?? 0;
}
function update(id: string) {
    editForm.put(`/admin/ist/forms/${props.form.id}/clues/${id}`, { preserveScroll: true, onSuccess: () => (editing.value = null) });
}
function destroy(id: string) {
    if (confirm('Delete this clue?')) router.delete(`/admin/ist/forms/${props.form.id}/clues/${id}`, { preserveScroll: true });
}
</script>

<template>
    <Head title="IST Clues" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <Card>
                <CardHeader><CardTitle>Add Clue</CardTitle></CardHeader>
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
                            <div class="grid gap-1 w-24">
                                <Label class="text-xs">Duration (s)</Label>
                                <Input type="number" v-model.number="addForm.duration" />
                            </div>
                        </div>
                        <div class="grid gap-1">
                            <Label class="text-xs">Clue Text</Label>
                            <Textarea v-model="addForm.clue_text" rows="3" placeholder="Clue content..." />
                            <InputError :message="addForm.errors.clue_text" />
                        </div>
                        <Button type="submit" :disabled="addForm.processing">Add</Button>
                    </form>
                </CardContent>
            </Card>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Subtest</TableHead>
                        <TableHead>Clue Text</TableHead>
                        <TableHead>Duration</TableHead>
                        <TableHead class="w-24">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-for="clue in clues" :key="clue.id">
                        <TableRow v-if="editing !== clue.id">
                            <TableCell><Badge variant="outline">{{ clue.subtest?.subtest_code ?? 'General' }}</Badge></TableCell>
                            <TableCell class="max-w-md truncate">{{ clue.clue_text }}</TableCell>
                            <TableCell>{{ clue.duration ? `${clue.duration}s` : '-' }}</TableCell>
                            <TableCell class="flex gap-1">
                                <Button variant="ghost" size="icon" @click="startEdit(clue)"><Pencil class="h-4 w-4" /></Button>
                                <Button variant="ghost" size="icon" @click="destroy(clue.id)"><Trash2 class="h-4 w-4" /></Button>
                            </TableCell>
                        </TableRow>
                        <TableRow v-else>
                            <TableCell>
                                <Select v-model="editForm.ist_subtest_id">
                                    <SelectTrigger class="w-24"><SelectValue /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">General</SelectItem>
                                        <SelectItem v-for="s in subtests" :key="s.id" :value="s.id">{{ s.subtest_code }}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </TableCell>
                            <TableCell><Textarea v-model="editForm.clue_text" rows="2" /></TableCell>
                            <TableCell><Input type="number" v-model.number="editForm.duration" class="w-20" /></TableCell>
                            <TableCell class="flex gap-1">
                                <Button size="sm" @click="update(clue.id)">Save</Button>
                                <Button size="sm" variant="ghost" @click="editing = null">Cancel</Button>
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
        </div>
    </AdminLayout>
</template>
