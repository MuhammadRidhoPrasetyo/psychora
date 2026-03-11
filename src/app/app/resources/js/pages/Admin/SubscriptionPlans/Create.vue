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
import type { Program, TestType } from '@/types/models';

const props = defineProps<{ programs: Program[]; testTypes: TestType[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Subscription Plans', href: '/admin/subscription-plans' },
    { title: 'Create', href: '/admin/subscription-plans/create' },
];

const form = useForm({
    name: '',
    code: '',
    description: '',
    price: 0,
    duration_days: 30,
    is_active: true,
    sort_order: 0,
    entitlements: [] as { program_id: string; test_type_id: string }[],
});

function addEntitlement() {
    form.entitlements.push({ program_id: '', test_type_id: '' });
}
function removeEntitlement(i: number) {
    form.entitlements.splice(i, 1);
}
function submit() {
    form.post('/admin/subscription-plans');
}
</script>

<template>
    <Head title="Create Plan" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl">
            <Card>
                <CardHeader><CardTitle>Create Subscription Plan</CardTitle></CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="name">Name</Label>
                                <Input id="name" v-model="form.name" required />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="code">Code</Label>
                                <Input id="code" v-model="form.code" required />
                                <InputError :message="form.errors.code" />
                            </div>
                        </div>
                        <div class="grid gap-2">
                            <Label for="description">Description</Label>
                            <Textarea id="description" v-model="form.description" rows="3" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="price">Price (IDR)</Label>
                                <Input id="price" type="number" v-model.number="form.price" required />
                                <InputError :message="form.errors.price" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="duration_days">Duration (days)</Label>
                                <Input id="duration_days" type="number" v-model.number="form.duration_days" required />
                                <InputError :message="form.errors.duration_days" />
                            </div>
                        </div>
                        <div class="grid gap-2">
                            <Label for="sort_order">Sort Order</Label>
                            <Input id="sort_order" type="number" v-model.number="form.sort_order" />
                        </div>
                        <div class="flex items-center gap-2">
                            <Switch :checked="form.is_active" @update:checked="form.is_active = $event" />
                            <Label>Active</Label>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <Label>Entitlements</Label>
                                <Button type="button" variant="outline" size="sm" @click="addEntitlement">Add Entitlement</Button>
                            </div>
                            <div v-for="(ent, i) in form.entitlements" :key="i" class="flex gap-2 items-end">
                                <div class="flex-1">
                                    <Label class="text-xs">Program</Label>
                                    <select v-model="ent.program_id" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm">
                                        <option value="">All Programs</option>
                                        <option v-for="p in programs" :key="p.id" :value="p.id">{{ p.name }}</option>
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <Label class="text-xs">Test Type</Label>
                                    <select v-model="ent.test_type_id" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm">
                                        <option value="">All Test Types</option>
                                        <option v-for="t in testTypes" :key="t.id" :value="t.id">{{ t.name }}</option>
                                    </select>
                                </div>
                                <Button type="button" variant="destructive" size="sm" @click="removeEntitlement(i)">Remove</Button>
                            </div>
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
