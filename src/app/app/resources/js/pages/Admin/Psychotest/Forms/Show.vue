<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { BreadcrumbItem } from '@/types';
import type { PsychotestForm } from '@/types/models';
import { Pencil, List } from 'lucide-vue-next';

const props = defineProps<{ form: PsychotestForm }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Psychotest', href: '#' },
    { title: 'Forms', href: '/admin/psychotest/forms' },
    { title: props.form.name, href: '#' },
];
</script>

<template>
    <Head :title="form.name" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>{{ form.name }}</CardTitle>
                            <CardDescription>
                                <Badge :variant="form.is_active ? 'default' : 'secondary'" class="mr-2">{{ form.is_active ? 'Active' : 'Inactive' }}</Badge>
                                {{ form.questions?.length ?? 0 }} questions
                            </CardDescription>
                        </div>
                        <div class="flex gap-2">
                            <Button variant="outline" @click="router.get(`/admin/psychotest/forms/${form.id}/questions`)">
                                <List class="mr-2 h-4 w-4" />Manage Questions
                            </Button>
                            <Button variant="outline" @click="router.get(`/admin/psychotest/forms/${form.id}/edit`)">
                                <Pencil class="mr-2 h-4 w-4" />Edit
                            </Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent v-if="form.description">
                    <p class="text-muted-foreground">{{ form.description }}</p>
                </CardContent>
            </Card>

            <Card v-if="form.questions?.length">
                <CardHeader><CardTitle>Questions Preview</CardTitle></CardHeader>
                <CardContent>
                    <div v-for="q in form.questions" :key="q.id" class="border rounded-lg p-4 mb-4 last:mb-0">
                        <div class="flex items-center gap-2 mb-2">
                            <Badge>Q{{ q.question_number }}</Badge>
                            <Badge :variant="q.is_active ? 'default' : 'secondary'">{{ q.is_active ? 'Active' : 'Inactive' }}</Badge>
                        </div>
                        <div v-if="q.options?.length" class="ml-4 space-y-2">
                            <div v-for="opt in q.options" :key="opt.id" class="flex items-start gap-2 text-sm">
                                <Badge variant="outline" class="shrink-0">{{ opt.label }}</Badge>
                                <span>{{ opt.statement }}</span>
                                <span v-if="opt.mappings?.length" class="text-muted-foreground ml-auto shrink-0">
                                    → {{ opt.mappings.map(m => `${m.aspect?.code}/${m.characteristic?.code} (${m.weight})`).join(', ') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
