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
    { title: props.aspect.name, href: `/admin/psychotest/aspects/${props.aspect.id}` },
    { title: 'New Characteristic', href: '#' },
];

const form = useForm({ code: '', name: '', description: '', sort_order: 0 });

function submit() {
    form.post(`/admin/psychotest/aspects/${props.aspect.id}/characteristics`);
}
</script>

<template>
    <Head title="Create Characteristic" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <Card class="max-w-2xl">
            <CardHeader><CardTitle>Create Characteristic for {{ aspect.name }}</CardTitle></CardHeader>
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
                    </div>
                    <div class="flex gap-2">
                        <Button type="submit" :disabled="form.processing">Create</Button>
                        <Button type="button" variant="outline" @click="$inertia.visit(`/admin/psychotest/aspects/${aspect.id}/characteristics`)">Cancel</Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </AdminLayout>
</template>
