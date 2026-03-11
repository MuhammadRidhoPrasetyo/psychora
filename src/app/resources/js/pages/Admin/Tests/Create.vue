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
import type { Program, TestType } from '@/types/models';

const props = defineProps<{ programs: Program[]; testTypes: TestType[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Tests', href: '/admin/tests' },
    { title: 'Create', href: '/admin/tests/create' },
];

const form = useForm({
    program_id: '',
    test_type_id: '',
    title: '',
    description: '',
    instruction: '',
    duration_minutes: 0,
    passing_score: 0,
    max_attempts: 1,
    scoring_method: 'sum',
    is_randomized: false,
    show_result: true,
    show_explanation: true,
    visibility: 'public',
    status: 'draft',
});

function submit() {
    form.post('/admin/tests');
}
</script>

<template>
    <Head title="Create Test" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl">
            <Card>
                <CardHeader><CardTitle>Create Test</CardTitle></CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
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
                                <Label for="test_type_id">Test Type</Label>
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
                                <Label for="duration_minutes">Duration (min)</Label>
                                <Input id="duration_minutes" type="number" v-model.number="form.duration_minutes" />
                                <InputError :message="form.errors.duration_minutes" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="passing_score">Passing Score</Label>
                                <Input id="passing_score" type="number" v-model.number="form.passing_score" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="max_attempts">Max Attempts</Label>
                                <Input id="max_attempts" type="number" v-model.number="form.max_attempts" />
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
                            <Button type="submit" :disabled="form.processing">Create</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
