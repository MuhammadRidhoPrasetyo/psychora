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
import type { CpnsScoreRule, TestType, PaginatedData } from '@/types/models';
import { ref } from 'vue';
import { Pencil, Trash2 } from 'lucide-vue-next';

type RuleRow = CpnsScoreRule & { test_type?: { id: string; name: string } };
const props = defineProps<{ rules: PaginatedData<RuleRow>; testTypes: TestType[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'CPNS', href: '#' },
    { title: 'Score Rules', href: '/admin/cpns/score-rules' },
];

const editing = ref<string | null>(null);
const form = useForm({ test_type_id: '', category_code: 'TWK', correct_score: 5, wrong_score: 0, empty_score: 0 });
const editForm = useForm({ test_type_id: '', category_code: '', correct_score: 0, wrong_score: 0, empty_score: 0 });

function create() {
    form.post('/admin/cpns/score-rules', { preserveScroll: true, onSuccess: () => form.reset() });
}
function startEdit(r: RuleRow) {
    editing.value = r.id;
    editForm.test_type_id = r.test_type_id;
    editForm.category_code = r.category_code;
    editForm.correct_score = r.correct_score;
    editForm.wrong_score = r.wrong_score;
    editForm.empty_score = r.empty_score;
}
function update(id: string) {
    editForm.put(`/admin/cpns/score-rules/${id}`, { preserveScroll: true, onSuccess: () => (editing.value = null) });
}
function destroy(id: string) {
    if (confirm('Delete?')) router.delete(`/admin/cpns/score-rules/${id}`, { preserveScroll: true });
}
</script>

<template>
    <Head title="CPNS Score Rules" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <Card>
                <CardHeader><CardTitle>Add Score Rule</CardTitle></CardHeader>
                <CardContent>
                    <form @submit.prevent="create" class="flex gap-2 items-end flex-wrap">
                        <div class="grid gap-1">
                            <Label class="text-xs">Test Type</Label>
                            <Select v-model="form.test_type_id">
                                <SelectTrigger class="w-40"><SelectValue placeholder="Select" /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="t in testTypes" :key="t.id" :value="t.id">{{ t.name }}</SelectItem>
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
                            <Label class="text-xs">Correct</Label>
                            <Input type="number" v-model.number="form.correct_score" class="w-20" />
                        </div>
                        <div class="grid gap-1">
                            <Label class="text-xs">Wrong</Label>
                            <Input type="number" v-model.number="form.wrong_score" class="w-20" />
                        </div>
                        <div class="grid gap-1">
                            <Label class="text-xs">Empty</Label>
                            <Input type="number" v-model.number="form.empty_score" class="w-20" />
                        </div>
                        <Button type="submit" :disabled="form.processing">Add</Button>
                    </form>
                    <InputError :message="form.errors.test_type_id" class="mt-1" />
                </CardContent>
            </Card>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Test Type</TableHead>
                        <TableHead>Category</TableHead>
                        <TableHead>Correct</TableHead>
                        <TableHead>Wrong</TableHead>
                        <TableHead>Empty</TableHead>
                        <TableHead class="w-24">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-for="r in rules.data" :key="r.id">
                        <TableRow v-if="editing !== r.id">
                            <TableCell>{{ r.test_type?.name }}</TableCell>
                            <TableCell><Badge variant="outline">{{ r.category_code }}</Badge></TableCell>
                            <TableCell>{{ r.correct_score }}</TableCell>
                            <TableCell>{{ r.wrong_score }}</TableCell>
                            <TableCell>{{ r.empty_score }}</TableCell>
                            <TableCell class="flex gap-1">
                                <Button variant="ghost" size="icon" @click="startEdit(r)"><Pencil class="h-4 w-4" /></Button>
                                <Button variant="ghost" size="icon" @click="destroy(r.id)"><Trash2 class="h-4 w-4" /></Button>
                            </TableCell>
                        </TableRow>
                        <TableRow v-else>
                            <TableCell>
                                <Select v-model="editForm.test_type_id">
                                    <SelectTrigger class="w-32"><SelectValue /></SelectTrigger>
                                    <SelectContent><SelectItem v-for="t in testTypes" :key="t.id" :value="t.id">{{ t.name }}</SelectItem></SelectContent>
                                </Select>
                            </TableCell>
                            <TableCell>
                                <Select v-model="editForm.category_code">
                                    <SelectTrigger class="w-20"><SelectValue /></SelectTrigger>
                                    <SelectContent><SelectItem value="TWK">TWK</SelectItem><SelectItem value="TIU">TIU</SelectItem><SelectItem value="TKP">TKP</SelectItem></SelectContent>
                                </Select>
                            </TableCell>
                            <TableCell><Input type="number" v-model.number="editForm.correct_score" class="w-16" /></TableCell>
                            <TableCell><Input type="number" v-model.number="editForm.wrong_score" class="w-16" /></TableCell>
                            <TableCell><Input type="number" v-model.number="editForm.empty_score" class="w-16" /></TableCell>
                            <TableCell class="flex gap-1">
                                <Button size="sm" @click="update(r.id)">Save</Button>
                                <Button size="sm" variant="ghost" @click="editing = null">Cancel</Button>
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
            <PaginationLinks :links="rules.links" :from="rules.from" :to="rules.to" :total="rules.total" />
        </div>
    </AdminLayout>
</template>
