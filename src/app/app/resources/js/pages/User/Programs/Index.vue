<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { BreadcrumbItem } from '@/types';
import type { Program } from '@/types/models';

const props = defineProps<{ programs: Program[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Programs', href: '#' },
];
</script>

<template>
    <Head title="Programs" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <h1 class="text-2xl font-bold">Available Programs</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <Card v-for="p in programs" :key="p.id" class="cursor-pointer hover:shadow-md transition-shadow" @click="router.get(`/programs/${p.id}`)">
                    <CardHeader>
                        <CardTitle>{{ p.name }}</CardTitle>
                        <CardDescription>{{ p.test_types_count ?? 0 }} test types</CardDescription>
                    </CardHeader>
                    <CardContent v-if="p.description">
                        <p class="text-sm text-muted-foreground line-clamp-3">{{ p.description }}</p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
