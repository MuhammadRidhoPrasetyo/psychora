<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { BreadcrumbItem } from '@/types';
import type { DiscForm, Test } from '@/types/models';

type FormWithRelations = DiscForm & {
    test?: Test;
    questions?: {
        id: string; number: number;
        options?: {
            id: string; option_text: string;
            scorings?: { response_type: string; disc_code: string; score_value: number }[];
        }[];
    }[];
};
const props = defineProps<{ form: FormWithRelations }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'DISC', href: '#' },
    { title: 'Forms', href: '/admin/disc/forms' },
    { title: props.form.name, href: '#' },
];
</script>

<template>
    <Head :title="form.name" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl space-y-6">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>{{ form.name }}</CardTitle>
                        <div class="flex gap-2">
                            <Button as="a" :href="`/admin/disc/forms/${form.id}/questions`" variant="outline" size="sm">Manage Questions</Button>
                            <Button as="a" :href="`/admin/disc/forms/${form.id}/edit`" variant="outline" size="sm">Edit</Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="text-sm space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <span class="text-muted-foreground">Test</span><span>{{ form.test?.title }}</span>
                    </div>
                    <p v-if="form.description" class="text-muted-foreground">{{ form.description }}</p>
                </CardContent>
            </Card>

            <Card v-if="form.questions?.length">
                <CardHeader><CardTitle>Questions ({{ form.questions.length }})</CardTitle></CardHeader>
                <CardContent class="space-y-4">
                    <div v-for="q in form.questions" :key="q.id" class="border-b pb-3 last:border-0">
                        <p class="font-medium">Q{{ q.number }}</p>
                        <div class="mt-1 ml-4 space-y-1">
                            <div v-for="opt in q.options" :key="opt.id" class="text-sm">
                                <span>{{ opt.option_text }}</span>
                                <span v-for="s in opt.scorings" :key="s.disc_code" class="ml-2">
                                    <Badge variant="outline" class="text-xs">{{ s.response_type }}: {{ s.disc_code }} ({{ s.score_value }})</Badge>
                                </span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
