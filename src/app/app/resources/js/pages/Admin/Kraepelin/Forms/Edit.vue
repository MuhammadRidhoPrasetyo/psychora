<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
import type { BreadcrumbItem } from '@/types';
import type { KraepelinForm } from '@/types/models';

const props = defineProps<{ form: KraepelinForm; tests: { id: string; title: string }[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Kraepelin', href: '#' },
    { title: 'Forms', href: '/admin/kraepelin/forms' },
    { title: 'Edit', href: '#' },
];

const editForm = useForm({
    test_id: (props.form as any).test_id ?? '',
    title: props.form.title,
    description: props.form.description ?? '',
    is_active: props.form.is_active,
});

function submit() {
    editForm.put(`/admin/kraepelin/forms/${props.form.id}`);
}
</script>

<template>
    <Head title="Edit Kraepelin Form" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <Card class="max-w-2xl">
            <CardHeader><CardTitle>Edit Kraepelin Form</CardTitle></CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid gap-2">
                        <Label>Test</Label>
                        <Select v-model="editForm.test_id">
                            <SelectTrigger><SelectValue placeholder="Select test" /></SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="t in tests" :key="t.id" :value="t.id">{{ t.title }}</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="editForm.errors.test_id" />
                    </div>
                    <div class="grid gap-2">
                        <Label>Title</Label>
                        <Input v-model="editForm.title" />
                        <InputError :message="editForm.errors.title" />
                    </div>
                    <div class="grid gap-2">
                        <Label>Description</Label>
                        <Textarea v-model="editForm.description" rows="3" />
                        <InputError :message="editForm.errors.description" />
                    </div>
                    <div class="flex items-center gap-2">
                        <Switch :model-value="editForm.is_active" @update:model-value="editForm.is_active = $event" />
                        <Label>Active</Label>
                    </div>
                    <div class="flex gap-2">
                        <Button type="submit" :disabled="editForm.processing">Save Changes</Button>
                        <Button type="button" variant="outline" @click="$inertia.visit('/admin/kraepelin/forms')">Cancel</Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </AdminLayout>
</template>
