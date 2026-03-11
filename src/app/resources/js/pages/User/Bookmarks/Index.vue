<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import PaginationLinks from '@/components/PaginationLinks.vue';
import type { BreadcrumbItem } from '@/types';
import type { Bookmark, PaginatedData } from '@/types/models';
import { Trash2 } from 'lucide-vue-next';

const props = defineProps<{ bookmarks: PaginatedData<Bookmark> }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Bookmarks', href: '#' },
];

function remove(id: string) {
    if (confirm('Remove bookmark?')) router.delete(`/bookmarks/${id}`, { preserveScroll: true });
}
</script>

<template>
    <Head title="Bookmarks" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <h1 class="text-2xl font-bold">Bookmarked Questions</h1>
            <div v-if="bookmarks.data.length" class="space-y-3">
                <Card v-for="b in bookmarks.data" :key="b.id">
                    <CardContent class="flex items-start justify-between pt-4">
                        <div class="flex-1">
                            <p class="font-medium">{{ b.question?.content ?? b.question_id }}</p>
                            <p class="text-xs text-muted-foreground mt-1">Type: {{ b.question?.question_type ?? '-' }}</p>
                            <p v-if="b.notes" class="text-sm text-muted-foreground mt-1 italic">{{ b.notes }}</p>
                        </div>
                        <Button variant="ghost" size="icon" @click="remove(b.id)"><Trash2 class="h-4 w-4" /></Button>
                    </CardContent>
                </Card>
            </div>
            <p v-else class="text-muted-foreground text-center py-8">No bookmarks yet.</p>
            <PaginationLinks :paginator="bookmarks" />
        </div>
    </AppLayout>
</template>
