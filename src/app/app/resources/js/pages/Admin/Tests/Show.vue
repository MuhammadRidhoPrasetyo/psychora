<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { BreadcrumbItem } from '@/types';
import type { Test, Program, TestType, TestSection, Question } from '@/types/models';

type TestWithRelations = Test & {
    program?: Program;
    test_type?: TestType;
    sections?: (TestSection & { questions?: Question[] })[];
    questions?: (Question & { options?: { id: string; label: string; is_correct: boolean }[] })[];
};
const props = defineProps<{ test: TestWithRelations }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Tests', href: '/admin/tests' },
    { title: props.test.title, href: '#' },
];

function updateStatus(status: string) {
    router.patch(`/admin/tests/${props.test.id}/status`, { status }, { preserveScroll: true });
}
</script>

<template>
    <Head :title="test.title" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-4xl space-y-6">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>{{ test.title }}</CardTitle>
                        <div class="flex gap-2">
                            <Select :model-value="test.status" @update:model-value="updateStatus">
                                <SelectTrigger class="w-32"><SelectValue /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="draft">Draft</SelectItem>
                                    <SelectItem value="published">Published</SelectItem>
                                    <SelectItem value="archived">Archived</SelectItem>
                                </SelectContent>
                            </Select>
                            <Button as="a" :href="`/admin/tests/${test.id}/edit`" variant="outline" size="sm">Edit</Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="space-y-3 text-sm">
                    <div class="grid grid-cols-2 gap-2">
                        <span class="text-muted-foreground">Program</span><span>{{ test.program?.name }}</span>
                        <span class="text-muted-foreground">Test Type</span><span>{{ test.test_type?.name }} ({{ test.test_type?.engine_type }})</span>
                        <span class="text-muted-foreground">Duration</span><span>{{ test.duration_minutes }} minutes</span>
                        <span class="text-muted-foreground">Passing Score</span><span>{{ test.passing_score }}</span>
                        <span class="text-muted-foreground">Max Attempts</span><span>{{ test.max_attempts }}</span>
                        <span class="text-muted-foreground">Scoring</span><span>{{ test.scoring_method }}</span>
                        <span class="text-muted-foreground">Visibility</span><span>{{ test.visibility }}</span>
                        <span class="text-muted-foreground">Randomized</span><span>{{ test.is_randomized ? 'Yes' : 'No' }}</span>
                    </div>
                    <p v-if="test.description" class="pt-2 text-muted-foreground">{{ test.description }}</p>
                    <p v-if="test.instruction" class="text-muted-foreground italic">{{ test.instruction }}</p>
                </CardContent>
            </Card>

            <div class="flex gap-2">
                <Button as="a" :href="`/admin/tests/${test.id}/sections`" variant="outline">Manage Sections</Button>
                <Button as="a" :href="`/admin/tests/${test.id}/questions`" variant="outline">Manage Questions</Button>
            </div>

            <Card v-if="test.sections?.length">
                <CardHeader><CardTitle>Sections</CardTitle></CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>#</TableHead>
                                <TableHead>Title</TableHead>
                                <TableHead>Questions</TableHead>
                                <TableHead>Duration</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="section in test.sections" :key="section.id">
                                <TableCell>{{ section.sort_order }}</TableCell>
                                <TableCell>{{ section.title }}</TableCell>
                                <TableCell>{{ section.questions?.length ?? 0 }}</TableCell>
                                <TableCell>{{ section.duration_minutes }} min</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <Card v-if="test.questions?.length">
                <CardHeader><CardTitle>Questions ({{ test.questions.length }})</CardTitle></CardHeader>
                <CardContent>
                    <div v-for="(q, i) in test.questions" :key="q.id" class="border-b py-3 last:border-0">
                        <p class="font-medium">{{ i + 1 }}. {{ q.content }}</p>
                        <div class="mt-1 ml-4 space-y-1 text-sm text-muted-foreground">
                            <Badge variant="outline" class="mr-2">{{ q.question_type }}</Badge>
                            <div v-for="opt in q.options" :key="opt.id" :class="{ 'text-green-600 font-medium': opt.is_correct }">
                                {{ opt.label }} <span v-if="opt.is_correct">(correct)</span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
