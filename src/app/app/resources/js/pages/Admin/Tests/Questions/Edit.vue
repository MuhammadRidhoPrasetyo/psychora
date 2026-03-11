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
import type { Test, TestSection, Question } from '@/types/models';
import { X } from 'lucide-vue-next';

type QWithRelations = Question & {
    options?: { label: string; content: string; is_correct: boolean }[];
    essay_answers?: { answer_text: string; score: number }[];
};
const props = defineProps<{ test: Test; question: QWithRelations; sections: TestSection[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Tests', href: '/admin/tests' },
    { title: props.test.title, href: `/admin/tests/${props.test.id}` },
    { title: 'Edit Question', href: '#' },
];

const form = useForm({
    test_section_id: props.question.test_section_id ?? '',
    question_type: props.question.question_type ?? 'multiple_choice',
    content: props.question.content ?? '',
    explanation: props.question.explanation ?? '',
    score: props.question.score ?? 1,
    sort_order: props.question.sort_order ?? 0,
    options: props.question.options?.map(o => ({ label: o.label, content: o.content, is_correct: o.is_correct })) ?? [],
    essay_answers: props.question.essay_answers?.map(a => ({ answer_text: a.answer_text, score: a.score })) ?? [],
});

function addOption() {
    form.options.push({ label: String.fromCharCode(65 + form.options.length), content: '', is_correct: false });
}
function removeOption(i: number) { form.options.splice(i, 1); }
function addEssayAnswer() { form.essay_answers.push({ answer_text: '', score: 1 }); }
function removeEssayAnswer(i: number) { form.essay_answers.splice(i, 1); }

function submit() {
    form.put(`/admin/tests/${props.test.id}/questions/${props.question.id}`);
}
</script>

<template>
    <Head title="Edit Question" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl">
            <Card>
                <CardHeader><CardTitle>Edit Question</CardTitle></CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label>Type</Label>
                                <Select v-model="form.question_type">
                                    <SelectTrigger><SelectValue /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="multiple_choice">Multiple Choice</SelectItem>
                                        <SelectItem value="essay">Essay</SelectItem>
                                        <SelectItem value="true_false">True/False</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.question_type" />
                            </div>
                            <div class="grid gap-2">
                                <Label>Section</Label>
                                <Select v-model="form.test_section_id">
                                    <SelectTrigger><SelectValue placeholder="No section" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">No section</SelectItem>
                                        <SelectItem v-for="s in sections" :key="s.id" :value="s.id">{{ s.title }}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                        <div class="grid gap-2">
                            <Label>Content</Label>
                            <Textarea v-model="form.content" rows="4" required />
                            <InputError :message="form.errors.content" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Explanation</Label>
                            <Textarea v-model="form.explanation" rows="2" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label>Score</Label>
                                <Input type="number" v-model.number="form.score" />
                            </div>
                            <div class="grid gap-2">
                                <Label>Sort Order</Label>
                                <Input type="number" v-model.number="form.sort_order" />
                            </div>
                        </div>

                        <div v-if="form.question_type === 'multiple_choice' || form.question_type === 'true_false'" class="space-y-3">
                            <div class="flex items-center justify-between">
                                <Label>Options</Label>
                                <Button type="button" variant="outline" size="sm" @click="addOption">Add Option</Button>
                            </div>
                            <div v-for="(opt, i) in form.options" :key="i" class="flex gap-2 items-center">
                                <Input v-model="opt.label" class="w-16" />
                                <Input v-model="opt.content" class="flex-1" />
                                <div class="flex items-center gap-1">
                                    <Switch :checked="opt.is_correct" @update:checked="opt.is_correct = $event" />
                                    <span class="text-xs">Correct</span>
                                </div>
                                <Button type="button" variant="ghost" size="icon" @click="removeOption(i)"><X class="h-4 w-4" /></Button>
                            </div>
                        </div>

                        <div v-if="form.question_type === 'essay'" class="space-y-3">
                            <div class="flex items-center justify-between">
                                <Label>Essay Answers</Label>
                                <Button type="button" variant="outline" size="sm" @click="addEssayAnswer">Add Answer</Button>
                            </div>
                            <div v-for="(ans, i) in form.essay_answers" :key="i" class="flex gap-2 items-center">
                                <Input v-model="ans.answer_text" class="flex-1" />
                                <Input type="number" v-model.number="ans.score" class="w-20" />
                                <Button type="button" variant="ghost" size="icon" @click="removeEssayAnswer(i)"><X class="h-4 w-4" /></Button>
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
