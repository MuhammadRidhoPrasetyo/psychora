<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import PaginationLinks from '@/components/PaginationLinks.vue';
import type { BreadcrumbItem } from '@/types';
import type { PsychotestForm, PsychotestQuestion, PaginatedData } from '@/types/models';
import { Pencil, Trash2, Plus } from 'lucide-vue-next';

const props = defineProps<{ form: PsychotestForm; questions: PaginatedData<PsychotestQuestion> }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Psychotest', href: '#' },
    { title: props.form.name, href: `/admin/psychotest/forms/${props.form.id}` },
    { title: 'Questions', href: '#' },
];

function destroy(id: string) {
    if (confirm('Delete?')) router.delete(`/admin/psychotest/forms/${props.form.id}/questions/${id}`, { preserveScroll: true });
}
</script>

<template>
    <Head title="Psychotest Questions" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">{{ form.name }} — Questions</h2>
                <Button @click="router.get(`/admin/psychotest/forms/${form.id}/questions/create`)"><Plus class="mr-2 h-4 w-4" />Add Question</Button>
            </div>
            <div v-for="q in questions.data" :key="q.id" class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex gap-2">
                        <Badge>Q{{ q.question_number }}</Badge>
                        <Badge :variant="q.is_active ? 'default' : 'secondary'">{{ q.is_active ? 'Active' : 'Inactive' }}</Badge>
                    </div>
                    <div class="flex gap-1">
                        <Button variant="ghost" size="icon" @click="router.get(`/admin/psychotest/forms/${form.id}/questions/${q.id}/edit`)"><Pencil class="h-4 w-4" /></Button>
                        <Button variant="ghost" size="icon" @click="destroy(q.id)"><Trash2 class="h-4 w-4" /></Button>
                    </div>
                </div>
                <div v-if="q.options?.length" class="space-y-1 ml-4">
                    <div v-for="opt in q.options" :key="opt.id" class="flex items-start gap-2 text-sm">
                        <Badge variant="outline" class="shrink-0">{{ opt.label }}</Badge>
                        <span class="flex-1">{{ opt.statement }}</span>
                        <span v-if="opt.mappings?.length" class="text-muted-foreground text-xs">
                            {{ opt.mappings.map(m => `${m.aspect?.code}/${m.characteristic?.code}:${m.weight}`).join(' · ') }}
                        </span>
                    </div>
                </div>
            </div>
            <PaginationLinks :paginator="questions" />
        </div>
    </AdminLayout>
</template>
