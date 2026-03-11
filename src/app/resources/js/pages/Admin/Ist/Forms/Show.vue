<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { BreadcrumbItem } from '@/types';
import type { IstForm, Test, IstSubtest } from '@/types/models';

type FormDetail = IstForm & {
    test?: Test;
    subtests?: IstSubtest[];
    form_items?: { id: string; subtest?: { subtest_code: string; subtest_name: string }; sort_order: number }[];
    instructions?: { id: string; title: string; subtest?: { subtest_code: string } }[];
    clues?: { id: string; clue: string; subtest?: { subtest_code: string } }[];
};
const props = defineProps<{ form: FormDetail }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'IST', href: '#' },
    { title: 'Forms', href: '/admin/ist/forms' },
    { title: props.form.name, href: '#' },
];
</script>

<template>
    <Head :title="form.name" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-4xl space-y-6">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>{{ form.name }}</CardTitle>
                        <Button as="a" :href="`/admin/ist/forms/${form.id}/edit`" variant="outline" size="sm">Edit</Button>
                    </div>
                </CardHeader>
                <CardContent class="text-sm space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <span class="text-muted-foreground">Test</span><span>{{ form.test?.title }}</span>
                        <span class="text-muted-foreground">Active</span><Badge :variant="form.is_active ? 'default' : 'secondary'">{{ form.is_active ? 'Yes' : 'No' }}</Badge>
                    </div>
                    <p v-if="form.description" class="text-muted-foreground">{{ form.description }}</p>
                </CardContent>
            </Card>

            <div class="flex gap-2 flex-wrap">
                <Button as="a" :href="`/admin/ist/forms/${form.id}/subtests`" variant="outline">Subtests</Button>
                <Button as="a" :href="`/admin/ist/forms/${form.id}/form-items`" variant="outline">Form Items</Button>
                <Button as="a" :href="`/admin/ist/forms/${form.id}/instructions`" variant="outline">Instructions</Button>
                <Button as="a" :href="`/admin/ist/forms/${form.id}/clues`" variant="outline">Clues</Button>
            </div>

            <Card v-if="form.subtests?.length">
                <CardHeader><CardTitle>Subtests ({{ form.subtests.length }})</CardTitle></CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Code</TableHead>
                                <TableHead>Name</TableHead>
                                <TableHead>Duration</TableHead>
                                <TableHead>Max</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="st in form.subtests" :key="st.id">
                                <TableCell><Badge variant="outline">{{ st.subtest_code }}</Badge></TableCell>
                                <TableCell>{{ st.subtest_name }}</TableCell>
                                <TableCell>{{ st.duration_minutes }} min</TableCell>
                                <TableCell>{{ st.max_score }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
