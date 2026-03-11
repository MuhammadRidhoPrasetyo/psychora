<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { BreadcrumbItem } from '@/types';
import type { Program, TestType } from '@/types/models';
import { X } from 'lucide-vue-next';

const props = defineProps<{ program: Program & { test_types?: TestType[] }; allTestTypes: TestType[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Programs', href: '/admin/programs' },
    { title: props.program.name, href: `/admin/programs/${props.program.id}` },
    { title: 'Test Types', href: '#' },
];

const form = useForm({ test_type_id: '' });

function attach() {
    form.post(`/admin/programs/${props.program.id}/test-types`, { preserveScroll: true, onSuccess: () => form.reset() });
}

function detach(testTypeId: string) {
    router.delete(`/admin/programs/${props.program.id}/test-types/${testTypeId}`, { preserveScroll: true });
}
</script>

<template>
    <Head :title="`${program.name} - Test Types`" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>Assign Test Type to {{ program.name }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="attach" class="flex gap-2">
                        <Select v-model="form.test_type_id">
                            <SelectTrigger class="w-64"><SelectValue placeholder="Select test type" /></SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="tt in allTestTypes" :key="tt.id" :value="tt.id">{{ tt.name }}</SelectItem>
                            </SelectContent>
                        </Select>
                        <Button type="submit" :disabled="form.processing || !form.test_type_id">Attach</Button>
                    </form>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Assigned Test Types</CardTitle></CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Name</TableHead>
                                <TableHead>Engine</TableHead>
                                <TableHead class="w-20">Action</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="tt in program.test_types" :key="tt.id">
                                <TableCell>{{ tt.name }}</TableCell>
                                <TableCell><Badge variant="outline">{{ tt.engine_type }}</Badge></TableCell>
                                <TableCell>
                                    <Button variant="ghost" size="icon" @click="detach(tt.id)"><X class="h-4 w-4" /></Button>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="!program.test_types?.length">
                                <TableCell colspan="3" class="text-center text-muted-foreground">No test types assigned</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
