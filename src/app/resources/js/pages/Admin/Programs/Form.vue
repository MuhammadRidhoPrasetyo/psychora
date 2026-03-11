<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
import type { BreadcrumbItem } from '@/types';
import type { Program } from '@/types/models';

type Props = { program?: Program };
const props = defineProps<Props>();

const isEdit = !!props.program;
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Programs', href: '/admin/programs' },
    { title: isEdit ? 'Edit' : 'Create', href: isEdit ? `/admin/programs/${props.program!.id}/edit` : '/admin/programs/create' },
];

const form = useForm({
    name: props.program?.name ?? '',
    description: props.program?.description ?? '',
    icon: props.program?.icon ?? '',
    is_active: props.program?.is_active ?? true,
    sort_order: props.program?.sort_order ?? 0,
});

function submit() {
    if (isEdit) {
        form.put(`/admin/programs/${props.program!.id}`);
    } else {
        form.post('/admin/programs');
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Edit Program' : 'Create Program'" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl">
            <Card>
                <CardHeader><CardTitle>{{ isEdit ? 'Edit Program' : 'Create Program' }}</CardTitle></CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input id="name" v-model="form.name" required />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="description">Description</Label>
                            <Textarea id="description" v-model="form.description" rows="3" />
                            <InputError :message="form.errors.description" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="icon">Icon</Label>
                            <Input id="icon" v-model="form.icon" placeholder="e.g., shield, briefcase" />
                            <InputError :message="form.errors.icon" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="sort_order">Sort Order</Label>
                            <Input id="sort_order" type="number" v-model.number="form.sort_order" />
                            <InputError :message="form.errors.sort_order" />
                        </div>
                        <div class="flex items-center gap-2">
                            <Switch id="is_active" :checked="form.is_active" @update:checked="form.is_active = $event" />
                            <Label for="is_active">Active</Label>
                        </div>
                        <div class="flex justify-end">
                            <Button type="submit" :disabled="form.processing">{{ isEdit ? 'Update' : 'Create' }}</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
