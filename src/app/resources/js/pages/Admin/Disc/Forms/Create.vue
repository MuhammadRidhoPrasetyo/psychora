<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
import type { BreadcrumbItem } from '@/types';
import type { Test } from '@/types/models';

const props = defineProps<{ tests: Test[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'DISC', href: '#' },
    { title: 'Forms', href: '/admin/disc/forms' },
    { title: 'Create', href: '#' },
];

const form = useForm({ test_id: '', name: '', description: '' });

function submit() {
    form.post('/admin/disc/forms');
}
</script>

<template>
    <Head title="Create DISC Form" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl">
            <Card>
                <CardHeader><CardTitle>Create DISC Form</CardTitle></CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid gap-2">
                            <Label>Test</Label>
                            <Select v-model="form.test_id">
                                <SelectTrigger><SelectValue placeholder="Select DISC test" /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="t in tests" :key="t.id" :value="t.id">{{ t.title }}</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.test_id" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Name</Label>
                            <Input v-model="form.name" required />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Description</Label>
                            <Textarea v-model="form.description" rows="3" />
                        </div>
                        <div class="flex justify-end">
                            <Button type="submit" :disabled="form.processing">Create</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
