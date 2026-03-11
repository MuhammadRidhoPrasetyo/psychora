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
import type { DiscForm, Test } from '@/types/models';

const props = defineProps<{ form: DiscForm; tests: Test[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'DISC', href: '#' },
    { title: 'Forms', href: '/admin/disc/forms' },
    { title: 'Edit', href: '#' },
];

const formData = useForm({
    test_id: props.form.test_id ?? '',
    name: props.form.name ?? '',
    description: props.form.description ?? '',
});

function submit() {
    formData.put(`/admin/disc/forms/${props.form.id}`);
}
</script>

<template>
    <Head title="Edit DISC Form" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl">
            <Card>
                <CardHeader><CardTitle>Edit DISC Form</CardTitle></CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid gap-2">
                            <Label>Test</Label>
                            <Select v-model="formData.test_id">
                                <SelectTrigger><SelectValue placeholder="Select DISC test" /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="t in tests" :key="t.id" :value="t.id">{{ t.title }}</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="formData.errors.test_id" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Name</Label>
                            <Input v-model="formData.name" required />
                            <InputError :message="formData.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Description</Label>
                            <Textarea v-model="formData.description" rows="3" />
                        </div>
                        <div class="flex justify-end">
                            <Button type="submit" :disabled="formData.processing">Update</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
