<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { BreadcrumbItem } from '@/types';
import type { TestPackage } from '@/types/models';

const props = defineProps<{ package: TestPackage }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Programs', href: '/programs' },
    { title: props.package.program?.name ?? 'Program', href: props.package.program ? `/programs/${props.package.program.id}` : '/programs' },
    { title: props.package.name, href: '#' },
];
</script>

<template>
    <Head :title="package.name" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <div>
                <h1 class="text-2xl font-bold">{{ package.name }}</h1>
                <p v-if="package.description" class="text-muted-foreground mt-1">{{ package.description }}</p>
            </div>

            <Card v-if="package.items?.length">
                <CardHeader><CardTitle>Included Tests</CardTitle></CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>#</TableHead>
                                <TableHead>Test</TableHead>
                                <TableHead>Type</TableHead>
                                <TableHead>Duration</TableHead>
                                <TableHead class="w-24" />
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="(item, i) in package.items" :key="item.id">
                                <TableCell>{{ i + 1 }}</TableCell>
                                <TableCell class="font-medium">{{ item.test?.title }}</TableCell>
                                <TableCell><Badge variant="outline">{{ item.test?.testType?.name }}</Badge></TableCell>
                                <TableCell>{{ item.test?.duration_minutes ? `${item.test.duration_minutes} min` : '-' }}</TableCell>
                                <TableCell>
                                    <Button size="sm" variant="outline" @click="router.get(`/programs/tests/${item.test?.id}`)">Detail</Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
