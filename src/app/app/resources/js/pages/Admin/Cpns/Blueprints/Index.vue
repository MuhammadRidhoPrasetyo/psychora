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
import PaginationLinks from '@/components/PaginationLinks.vue';
import type { BreadcrumbItem } from '@/types';
import type { CpnsTestBlueprint, Test, PaginatedData } from '@/types/models';
import { ref } from 'vue';
import { Pencil, Trash2 } from 'lucide-vue-next';

type BpRow = CpnsTestBlueprint & { test?: { id: string; title: string } };
const props = defineProps<{ blueprints: PaginatedData<BpRow>; tests: Test[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'CPNS', href: '#' },
    { title: 'Blueprints', href: '/admin/cpns/blueprints' },
];

const editing = ref<string | null>(null);
const form = useForm({ test_id: '', category_code: 'TWK', total_questions: 0, passing_score: 0 });
const editForm = useForm({ test_id: '', category_code: '', total_questions: 0, passing_score: 0 });

function create() {
    form.post('/admin/cpns/blueprints', { preserveScroll: true, onSuccess: () => form.reset() });
}
function startEdit(bp: BpRow) {
    editing.value = bp.id;
    editForm.test_id = bp.test_id;
    editForm.category_code = bp.category_code;
    editForm.total_questions = bp.total_questions;
    editForm.passing_score = bp.passing_score;
}
function update(id: string) {
    editForm.put(`/admin/cpns/blueprints/${id}`, { preserveScroll: true, onSuccess: () => (editing.value = null) });
}
function destroy(id: string) {
    if (confirm('Delete?')) router.delete(`/admin/cpns/blueprints/${id}`, { preserveScroll: true });
}
</script>

<template>
    <Head title="CPNS Blueprints" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <Card>
                <CardHeader><CardTitle>Add Blueprint</CardTitle></CardHeader>
                <CardContent>
                    <form @submit.prevent="create" class="flex gap-2 items-end flex-wrap">
                        <div class="grid gap-1">
                            <Label class="text-xs">Test</Label>
                            <Select v-model="form.test_id">
                                <SelectTrigger class="w-48"><SelectValue placeholder="Select test" /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="t in tests" :key="t.id" :value="t.id">{{ t.title }}</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="grid gap-1">
                            <Label class="text-xs">Category</Label>
                            <Select v-model="form.category_code">
                                <SelectTrigger class="w-24"><SelectValue /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="TWK">TWK</SelectItem>
                                    <SelectItem value="TIU">TIU</SelectItem>
                                    <SelectItem value="TKP">TKP</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="grid gap-1">
                            <Label class="text-xs">Total Questions</Label>
                            <Input type="number" v-model.number="form.total_questions" class="w-28" />
                        </div>
                        <div class="grid gap-1">
                            <Label class="text-xs">Passing Score</Label>
                            <Input type="number" v-model.number="form.passing_score" class="w-28" />
                        </div>
                        <Button type="submit" :disabled="form.processing">Add</Button>
                    </form>
                    <InputError :message="form.errors.test_id" class="mt-1" />
                </CardContent>
            </Card>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Test</TableHead>
                        <TableHead>Category</TableHead>
                        <TableHead>Questions</TableHead>
                        <TableHead>Passing Score</TableHead>
                        <TableHead class="w-24">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-for="bp in blueprints.data" :key="bp.id">
                        <TableRow v-if="editing !== bp.id">
                            <TableCell>{{ bp.test?.title }}</TableCell>
                            <TableCell><Badge variant="outline">{{ bp.category_code }}</Badge></TableCell>
                            <TableCell>{{ bp.total_questions }}</TableCell>
                            <TableCell>{{ bp.passing_score }}</TableCell>
                            <TableCell class="flex gap-1">
                                <Button variant="ghost" size="icon" @click="startEdit(bp)"><Pencil class="h-4 w-4" /></Button>
                                <Button variant="ghost" size="icon" @click="destroy(bp.id)"><Trash2 class="h-4 w-4" /></Button>
                            </TableCell>
                        </TableRow>
                        <TableRow v-else>
                            <TableCell>
                                <Select v-model="editForm.test_id">
                                    <SelectTrigger class="w-40"><SelectValue /></SelectTrigger>
                                    <SelectContent><SelectItem v-for="t in tests" :key="t.id" :value="t.id">{{ t.title }}</SelectItem></SelectContent>
                                </Select>
                            </TableCell>
                            <TableCell>
                                <Select v-model="editForm.category_code">
                                    <SelectTrigger class="w-20"><SelectValue /></SelectTrigger>
                                    <SelectContent><SelectItem value="TWK">TWK</SelectItem><SelectItem value="TIU">TIU</SelectItem><SelectItem value="TKP">TKP</SelectItem></SelectContent>
                                </Select>
                            </TableCell>
                            <TableCell><Input type="number" v-model.number="editForm.total_questions" class="w-20" /></TableCell>
                            <TableCell><Input type="number" v-model.number="editForm.passing_score" class="w-20" /></TableCell>
                            <TableCell class="flex gap-1">
                                <Button size="sm" @click="update(bp.id)">Save</Button>
                                <Button size="sm" variant="ghost" @click="editing = null">Cancel</Button>
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
            <PaginationLinks :links="blueprints.links" :from="blueprints.from" :to="blueprints.to" :total="blueprints.total" />
        </div>
    </AdminLayout>
</template>
