<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
import type { BreadcrumbItem } from '@/types';
import type { PsychotestAspect } from '@/types/models';

const props = defineProps<{ aspect: PsychotestAspect }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Psychotest', href: '#' },
    { title: 'Aspects', href: '/admin/psychotest/aspects' },
    { title: 'Edit', href: '#' },
];

const form = useForm({
    code: props.aspect.code,
    name: props.aspect.name,
    description: props.aspect.description ?? '',
    sort_order: props.aspect.sort_order,
});

function submit() {
    form.put(`/admin/psychotest/aspects/${props.aspect.id}`);
}
</script>

<template>
    <Head title="Edit Aspect" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <Card class="max-w-2xl">
            <CardHeader><CardTitle>Edit Aspect</CardTitle></CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label>Code</Label>
                            <Input v-model="form.code" />
                            <InputError :message="form.errors.code" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Sort Order</Label>
                            <Input type="number" v-model.number="form.sort_order" />
                            <InputError :message="form.errors.sort_order" />
                        </div>
                    </div>
                    <div class="grid gap-2">
                        <Label>Name</Label>
                        <Input v-model="form.name" />
                        <InputError :message="form.errors.name" />
                    </div>
                    <div class="grid gap-2">
                        <Label>Description</Label>
                        <Textarea v-model="form.description" rows="3" />
                        <InputError :message="form.errors.description" />
                    </div>
                    <div class="flex gap-2">
                        <Button type="submit" :disabled="form.processing">Save Changes</Button>
                        <Button type="button" variant="outline" @click="$inertia.visit('/admin/psychotest/aspects')">Cancel</Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </AdminLayout>
</template>
