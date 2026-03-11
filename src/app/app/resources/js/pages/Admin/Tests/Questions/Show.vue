<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { BreadcrumbItem } from '@/types';
import type { Test, Question, TestSection } from '@/types/models';

type QWithRelations = Question & {
    section?: TestSection;
    options?: { id: string; label: string; content: string; is_correct: boolean }[];
    essay_answers?: { id: string; answer_text: string; score: number }[];
};
const props = defineProps<{ test: Test; question: QWithRelations }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Tests', href: '/admin/tests' },
    { title: props.test.title, href: `/admin/tests/${props.test.id}` },
    { title: 'Question', href: '#' },
];
</script>

<template>
    <Head title="Question Detail" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl space-y-6">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>Question Detail</CardTitle>
                        <Button as="a" :href="`/admin/tests/${test.id}/questions/${question.id}/edit`" variant="outline" size="sm">Edit</Button>
                    </div>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <span class="text-muted-foreground">Type</span><Badge variant="outline">{{ question.question_type }}</Badge>
                        <span class="text-muted-foreground">Section</span><span>{{ question.section?.title ?? '-' }}</span>
                        <span class="text-muted-foreground">Score</span><span>{{ question.score }}</span>
                        <span class="text-muted-foreground">Sort Order</span><span>{{ question.sort_order }}</span>
                    </div>
                    <div>
                        <p class="text-sm text-muted-foreground">Content</p>
                        <p class="mt-1">{{ question.content }}</p>
                    </div>
                    <div v-if="question.explanation">
                        <p class="text-sm text-muted-foreground">Explanation</p>
                        <p class="mt-1 text-sm italic">{{ question.explanation }}</p>
                    </div>
                </CardContent>
            </Card>

            <Card v-if="question.options?.length">
                <CardHeader><CardTitle>Options</CardTitle></CardHeader>
                <CardContent>
                    <div v-for="opt in question.options" :key="opt.id" class="flex items-center gap-3 border-b py-2 last:border-0">
                        <Badge variant="outline">{{ opt.label }}</Badge>
                        <span class="flex-1">{{ opt.content }}</span>
                        <Badge v-if="opt.is_correct" variant="default">Correct</Badge>
                    </div>
                </CardContent>
            </Card>

            <Card v-if="question.essay_answers?.length">
                <CardHeader><CardTitle>Essay Answers</CardTitle></CardHeader>
                <CardContent>
                    <div v-for="ans in question.essay_answers" :key="ans.id" class="border-b py-2 last:border-0 text-sm">
                        <p>{{ ans.answer_text }}</p>
                        <p class="text-muted-foreground">Score: {{ ans.score }}</p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
