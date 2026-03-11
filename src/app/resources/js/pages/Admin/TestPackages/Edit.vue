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
import type { TestPackage, Program, Test } from '@/types/models';
import { X } from 'lucide-vue-next';

const props = defineProps<{ testPackage: TestPackage & { items?: { test_id: string; sort_order: number }[] }; programs: Program[]; tests: Test[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Test Packages', href: '/admin/test-packages' },
    { title: 'Edit', href: '#' },
];

const form = useForm({
    program_id: props.testPackage.program_id ?? '',
    code: props.testPackage.code ?? '',
    name: props.testPackage.name ?? '',
    description: props.testPackage.description ?? '',
    is_premium: props.testPackage.is_premium ?? false,
    is_active: props.testPackage.is_active ?? true,
    sort_order: props.testPackage.sort_order ?? 0,
    items: props.testPackage.items?.map(i => ({ test_id: i.test_id, sort_order: i.sort_order })) ?? [],
});

function addItem() {
    form.items.push({ test_id: '', sort_order: form.items.length });
}
function removeItem(i: number) {
    form.items.splice(i, 1);
}
function submit() {
    form.put(`/admin/test-packages/${props.testPackage.id}`);
}
</script>

<template>
    <Head title="Edit Test Package" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl">
            <Card>
                <CardHeader><CardTitle>Edit Test Package</CardTitle></CardHeader>
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
                            <Label for="program_id">Program</Label>
                            <Select v-model="form.program_id">
                                <SelectTrigger><SelectValue placeholder="Select program" /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="p in programs" :key="p.id" :value="p.id">{{ p.name }}</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.program_id" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="description">Description</Label>
                            <Textarea id="description" v-model="form.description" rows="3" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-center gap-2">
                                <Switch :checked="form.is_premium" @update:checked="form.is_premium = $event" />
                                <Label>Premium</Label>
                            </div>
                            <div class="flex items-center gap-2">
                                <Switch :checked="form.is_active" @update:checked="form.is_active = $event" />
                                <Label>Active</Label>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <Label>Tests</Label>
                                <Button type="button" variant="outline" size="sm" @click="addItem">Add Test</Button>
                            </div>
                            <div v-for="(item, i) in form.items" :key="i" class="flex gap-2 items-end">
                                <div class="flex-1">
                                    <Select v-model="item.test_id">
                                        <SelectTrigger><SelectValue placeholder="Select test" /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="t in tests" :key="t.id" :value="t.id">{{ t.title }}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <Input type="number" v-model.number="item.sort_order" placeholder="Order" class="w-20" />
                                <Button type="button" variant="ghost" size="icon" @click="removeItem(i)"><X class="h-4 w-4" /></Button>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <Button type="submit" :disabled="form.processing">Update</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
