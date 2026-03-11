<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { BreadcrumbItem } from '@/types';
import type { TestAttempt } from '@/types/models';

const props = defineProps<{ attempt: TestAttempt }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Test Attempts', href: '/admin/test-attempts' },
    { title: `Attempt #${props.attempt.attempt_no}`, href: '#' },
];
</script>

<template>
    <Head title="Test Attempt Detail" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>Attempt #{{ attempt.attempt_no }}</CardTitle>
                    <CardDescription>
                        <Badge :variant="attempt.status === 'scored' ? 'default' : 'secondary'" class="mr-2">{{ attempt.status }}</Badge>
                        {{ attempt.user?.name }} ({{ attempt.user?.email }})
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <dt class="text-muted-foreground">Test</dt>
                            <dd class="font-medium">{{ attempt.test?.title }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Program</dt>
                            <dd>{{ (attempt.test as any)?.program?.name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Type</dt>
                            <dd>{{ (attempt.test as any)?.testType?.name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Score</dt>
                            <dd class="text-xl font-bold">{{ attempt.score ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Correct</dt>
                            <dd>{{ attempt.total_correct ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Wrong</dt>
                            <dd>{{ attempt.total_wrong ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Unanswered</dt>
                            <dd>{{ attempt.total_unanswered ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Started</dt>
                            <dd>{{ new Date(attempt.started_at).toLocaleString('id-ID') }}</dd>
                        </div>
                        <div v-if="attempt.submitted_at">
                            <dt class="text-muted-foreground">Submitted</dt>
                            <dd>{{ new Date(attempt.submitted_at).toLocaleString('id-ID') }}</dd>
                        </div>
                    </dl>
                </CardContent>
            </Card>

            <Card v-if="attempt.result">
                <CardHeader><CardTitle>Result</CardTitle></CardHeader>
                <CardContent>
                    <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <dt class="text-muted-foreground">Total Score</dt>
                            <dd class="font-bold text-lg">{{ attempt.result.total_score }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Percentage</dt>
                            <dd>{{ attempt.result.percentage }}%</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Passed</dt>
                            <dd><Badge :variant="attempt.result.is_passed ? 'default' : 'destructive'">{{ attempt.result.is_passed ? 'Yes' : 'No' }}</Badge></dd>
                        </div>
                    </dl>
                </CardContent>
            </Card>

            <Card v-if="attempt.answers?.length">
                <CardHeader><CardTitle>Answers ({{ attempt.answers.length }})</CardTitle></CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>#</TableHead>
                                <TableHead>Question</TableHead>
                                <TableHead>Answer</TableHead>
                                <TableHead>Correct</TableHead>
                                <TableHead>Score</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="(ans, i) in attempt.answers" :key="ans.id">
                                <TableCell>{{ i + 1 }}</TableCell>
                                <TableCell class="max-w-xs truncate">{{ ans.question?.content ?? ans.question_id }}</TableCell>
                                <TableCell>{{ ans.answer_text ?? (ans.selected_option_id ? 'Option selected' : '-') }}</TableCell>
                                <TableCell>
                                    <Badge v-if="ans.is_correct !== null" :variant="ans.is_correct ? 'default' : 'destructive'">{{ ans.is_correct ? '✓' : '✗' }}</Badge>
                                    <span v-else class="text-muted-foreground">-</span>
                                </TableCell>
                                <TableCell>{{ ans.score ?? '-' }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
