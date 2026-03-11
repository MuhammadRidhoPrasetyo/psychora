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
import type { Test, Program, TestType } from '@/types/models';

const props = defineProps<{ test: Test; programs: Program[]; testTypes: TestType[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Tests', href: '/admin/tests' },
    { title: 'Edit', href: '#' },
];

const form = useForm({
    program_id: props.test.program_id ?? '',
    test_type_id: props.test.test_type_id ?? '',
    title: props.test.title ?? '',
    description: props.test.description ?? '',
    instruction: props.test.instruction ?? '',
    duration_minutes: props.test.duration_minutes ?? 0,
    passing_score: props.test.passing_score ?? 0,
    max_attempts: props.test.max_attempts ?? 1,
    scoring_method: props.test.scoring_method ?? 'sum',
    is_randomized: props.test.is_randomized ?? false,
    show_result: props.test.show_result ?? true,
    show_explanation: props.test.show_explanation ?? true,
    visibility: props.test.visibility ?? 'public',
    status: props.test.status ?? 'draft',
});

function submit() {
    form.put(`/admin/tests/${props.test.id}`);
}
</script>

<template>
    <Head title="Edit Test" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl">
            <Card>
                <CardHeader><CardTitle>Edit Test</CardTitle></CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label>Program</Label>
                                <Select v-model="form.program_id">
                                    <SelectTrigger><SelectValue placeholder="Select program" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="p in programs" :key="p.id" :value="p.id">{{ p.name }}</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.program_id" />
                            </div>
                            <div class="grid gap-2">
                                <Label>Test Type</Label>
                                <Select v-model="form.test_type_id">
                                    <SelectTrigger><SelectValue placeholder="Select type" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="t in testTypes" :key="t.id" :value="t.id">{{ t.name }}</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.test_type_id" />
                            </div>
                        </div>
                        <div class="grid gap-2">
                            <Label for="title">Title</Label>
                            <Input id="title" v-model="form.title" required />
                            <InputError :message="form.errors.title" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="description">Description</Label>
                            <Textarea id="description" v-model="form.description" rows="3" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="instruction">Instruction</Label>
                            <Textarea id="instruction" v-model="form.instruction" rows="3" />
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="grid gap-2">
                                <Label>Duration (min)</Label>
                                <Input type="number" v-model.number="form.duration_minutes" />
                            </div>
                            <div class="grid gap-2">
                                <Label>Passing Score</Label>
                                <Input type="number" v-model.number="form.passing_score" />
                            </div>
                            <div class="grid gap-2">
                                <Label>Max Attempts</Label>
                                <Input type="number" v-model.number="form.max_attempts" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label>Scoring Method</Label>
                                <Select v-model="form.scoring_method">
                                    <SelectTrigger><SelectValue /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="sum">Sum</SelectItem>
                                        <SelectItem value="average">Average</SelectItem>
                                        <SelectItem value="weighted">Weighted</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="grid gap-2">
                                <Label>Visibility</Label>
                                <Select v-model="form.visibility">
                                    <SelectTrigger><SelectValue /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="public">Public</SelectItem>
                                        <SelectItem value="private">Private</SelectItem>
                                        <SelectItem value="subscribers">Subscribers</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                        <div class="grid gap-2">
                            <Label>Status</Label>
                            <Select v-model="form.status">
                                <SelectTrigger class="w-40"><SelectValue /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="draft">Draft</SelectItem>
                                    <SelectItem value="published">Published</SelectItem>
                                    <SelectItem value="archived">Archived</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="flex flex-wrap gap-6">
                            <div class="flex items-center gap-2">
                                <Switch :checked="form.is_randomized" @update:checked="form.is_randomized = $event" />
                                <Label>Randomize</Label>
                            </div>
                            <div class="flex items-center gap-2">
                                <Switch :checked="form.show_result" @update:checked="form.show_result = $event" />
                                <Label>Show Result</Label>
                            </div>
                            <div class="flex items-center gap-2">
                                <Switch :checked="form.show_explanation" @update:checked="form.show_explanation = $event" />
                                <Label>Show Explanation</Label>
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
