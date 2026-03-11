<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { BreadcrumbItem } from '@/types';
import type { TestPackage, Program, Test } from '@/types/models';

type PackageWithRelations = TestPackage & { program?: Program; items?: { id: string; test: Test; sort_order: number }[] };
const props = defineProps<{ testPackage: PackageWithRelations }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Test Packages', href: '/admin/test-packages' },
    { title: props.testPackage.name, href: '#' },
];
</script>

<template>
    <Head :title="testPackage.name" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl space-y-6">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>{{ testPackage.name }}</CardTitle>
                        <Button as="a" :href="`/admin/test-packages/${testPackage.id}/edit`" variant="outline" size="sm">Edit</Button>
                    </div>
                </CardHeader>
                <CardContent class="space-y-2 text-sm">
                    <div class="grid grid-cols-2 gap-2">
                        <span class="text-muted-foreground">Code</span><span>{{ testPackage.code }}</span>
                        <span class="text-muted-foreground">Program</span><span>{{ testPackage.program?.name }}</span>
                        <span class="text-muted-foreground">Premium</span><Badge :variant="testPackage.is_premium ? 'default' : 'outline'">{{ testPackage.is_premium ? 'Yes' : 'No' }}</Badge>
                        <span class="text-muted-foreground">Status</span><Badge :variant="testPackage.is_active ? 'default' : 'secondary'">{{ testPackage.is_active ? 'Active' : 'Inactive' }}</Badge>
                    </div>
                    <p v-if="testPackage.description" class="pt-2 text-muted-foreground">{{ testPackage.description }}</p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Included Tests</CardTitle></CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>#</TableHead>
                                <TableHead>Test</TableHead>
                                <TableHead>Status</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="item in testPackage.items" :key="item.id">
                                <TableCell>{{ item.sort_order }}</TableCell>
                                <TableCell>{{ item.test?.title }}</TableCell>
                                <TableCell><Badge variant="outline">{{ item.test?.status }}</Badge></TableCell>
                            </TableRow>
                            <TableRow v-if="!testPackage.items?.length">
                                <TableCell colspan="3" class="text-center text-muted-foreground">No tests in this package</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
