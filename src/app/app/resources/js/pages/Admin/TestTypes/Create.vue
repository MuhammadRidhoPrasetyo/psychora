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

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Test Types', href: '/admin/test-types' },
    { title: 'Create', href: '/admin/test-types/create' },
];

const form = useForm({
    code: '',
    name: '',
    description: '',
    engine_type: 'generic',
    is_active: true,
    sort_order: 0,
});

function submit() {
    form.post('/admin/test-types');
}
</script>

<template>
    <Head title="Create Test Type" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl">
            <Card>
                <CardHeader><CardTitle>Create Test Type</CardTitle></CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="code">Code</Label>
                                <Input id="code" v-model="form.code" required />
                                <InputError :message="form.errors.code" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="name">Name</Label>
                                <Input id="name" v-model="form.name" required />
                                <InputError :message="form.errors.name" />
                            </div>
                        </div>
                        <div class="grid gap-2">
                            <Label for="description">Description</Label>
                            <Textarea id="description" v-model="form.description" rows="3" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="engine_type">Engine Type</Label>
                            <Select v-model="form.engine_type">
                                <SelectTrigger><SelectValue /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="generic">Generic</SelectItem>
                                    <SelectItem value="disc">DISC</SelectItem>
                                    <SelectItem value="ist">IST</SelectItem>
                                    <SelectItem value="kraepelin">Kraepelin</SelectItem>
                                    <SelectItem value="psychotest">Psychotest</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.engine_type" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="sort_order">Sort Order</Label>
                            <Input id="sort_order" type="number" v-model.number="form.sort_order" />
                        </div>
                        <div class="flex items-center gap-2">
                            <Switch :checked="form.is_active" @update:checked="form.is_active = $event" />
                            <Label>Active</Label>
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
