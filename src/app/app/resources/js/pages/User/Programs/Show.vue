<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { BreadcrumbItem } from '@/types';
import type { Program, TestPackage } from '@/types/models';

const props = defineProps<{ program: Program; packages: TestPackage[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Programs', href: '/programs' },
    { title: props.program.name, href: '#' },
];
</script>

<template>
    <Head :title="program.name" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <div>
                <h1 class="text-2xl font-bold">{{ program.name }}</h1>
                <p v-if="program.description" class="text-muted-foreground mt-1">{{ program.description }}</p>
            </div>

            <div v-if="program.test_types?.length">
                <h2 class="text-lg font-semibold mb-3">Test Types</h2>
                <div class="flex flex-wrap gap-2">
                    <Badge v-for="tt in program.test_types" :key="tt.id" variant="outline">{{ tt.name }}</Badge>
                </div>
            </div>

            <div v-if="packages.length">
                <h2 class="text-lg font-semibold mb-3">Test Packages</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <Card v-for="pkg in packages" :key="pkg.id" class="cursor-pointer hover:shadow-md transition-shadow" @click="router.get(`/programs/packages/${pkg.id}`)">
                        <CardHeader>
                            <CardTitle>{{ pkg.name }}</CardTitle>
                            <CardDescription>{{ pkg.items_count ?? 0 }} tests included</CardDescription>
                        </CardHeader>
                        <CardContent v-if="pkg.description">
                            <p class="text-sm text-muted-foreground line-clamp-2">{{ pkg.description }}</p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
