<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import InputError from '@/components/InputError.vue';
import type { BreadcrumbItem } from '@/types';
import type { Test, TestSection } from '@/types/models';
import { ref } from 'vue';
import { Pencil, Trash2 } from 'lucide-vue-next';

const props = defineProps<{ test: Test; sections: (TestSection & { questions_count?: number })[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Tests', href: '/admin/tests' },
    { title: props.test.title, href: `/admin/tests/${props.test.id}` },
    { title: 'Sections', href: '#' },
];

const editing = ref<string | null>(null);
const form = useForm({ title: '', instruction: '', duration_minutes: 0, sort_order: 0 });
const editForm = useForm({ title: '', instruction: '', duration_minutes: 0, sort_order: 0 });

function create() {
    form.post(`/admin/tests/${props.test.id}/sections`, { preserveScroll: true, onSuccess: () => form.reset() });
}

function startEdit(s: TestSection) {
    editing.value = s.id;
    editForm.title = s.title;
    editForm.instruction = s.instruction ?? '';
    editForm.duration_minutes = s.duration_minutes ?? 0;
    editForm.sort_order = s.sort_order ?? 0;
}

function update(sectionId: string) {
    editForm.put(`/admin/tests/${props.test.id}/sections/${sectionId}`, { preserveScroll: true, onSuccess: () => (editing.value = null) });
}

function destroy(sectionId: string) {
    if (confirm('Delete this section?')) router.delete(`/admin/tests/${props.test.id}/sections/${sectionId}`, { preserveScroll: true });
}
</script>

<template>
    <Head title="Manage Sections" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl space-y-6">
            <Card>
                <CardHeader><CardTitle>Add Section</CardTitle></CardHeader>
                <CardContent>
                    <form @submit.prevent="create" class="grid grid-cols-4 gap-2 items-end">
                        <div class="grid gap-1">
                            <Label class="text-xs">Title</Label>
                            <Input v-model="form.title" required />
                            <InputError :message="form.errors.title" />
                        </div>
                        <div class="grid gap-1">
                            <Label class="text-xs">Instruction</Label>
                            <Input v-model="form.instruction" />
                        </div>
                        <div class="grid gap-1">
                            <Label class="text-xs">Duration (min)</Label>
                            <Input type="number" v-model.number="form.duration_minutes" />
                        </div>
                        <Button type="submit" :disabled="form.processing">Add</Button>
                    </form>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Sections</CardTitle></CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-16">#</TableHead>
                                <TableHead>Title</TableHead>
                                <TableHead>Duration</TableHead>
                                <TableHead>Questions</TableHead>
                                <TableHead class="w-24">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <template v-for="s in sections" :key="s.id">
                                <TableRow v-if="editing !== s.id">
                                    <TableCell>{{ s.sort_order }}</TableCell>
                                    <TableCell>{{ s.title }}</TableCell>
                                    <TableCell>{{ s.duration_minutes }} min</TableCell>
                                    <TableCell>{{ s.questions_count ?? 0 }}</TableCell>
                                    <TableCell class="flex gap-1">
                                        <Button variant="ghost" size="icon" @click="startEdit(s)"><Pencil class="h-4 w-4" /></Button>
                                        <Button variant="ghost" size="icon" @click="destroy(s.id)"><Trash2 class="h-4 w-4" /></Button>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-else>
                                    <TableCell><Input type="number" v-model.number="editForm.sort_order" class="w-16" /></TableCell>
                                    <TableCell><Input v-model="editForm.title" /></TableCell>
                                    <TableCell><Input type="number" v-model.number="editForm.duration_minutes" class="w-20" /></TableCell>
                                    <TableCell />
                                    <TableCell class="flex gap-1">
                                        <Button size="sm" @click="update(s.id)" :disabled="editForm.processing">Save</Button>
                                        <Button size="sm" variant="ghost" @click="editing = null">Cancel</Button>
                                    </TableCell>
                                </TableRow>
                            </template>
                            <TableRow v-if="!sections.length">
                                <TableCell colspan="5" class="text-center text-muted-foreground">No sections yet</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
