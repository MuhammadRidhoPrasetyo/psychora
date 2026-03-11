<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { BreadcrumbItem } from '@/types';
import type { PsychotestAspect } from '@/types/models';
import { Pencil, Plus } from 'lucide-vue-next';

const props = defineProps<{ aspect: PsychotestAspect }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Psychotest', href: '#' },
    { title: 'Aspects', href: '/admin/psychotest/aspects' },
    { title: props.aspect.name, href: '#' },
];
</script>

<template>
    <Head :title="aspect.name" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>{{ aspect.name }}</CardTitle>
                            <CardDescription>Code: {{ aspect.code }} · Order: {{ aspect.sort_order }}</CardDescription>
                        </div>
                        <Button variant="outline" @click="router.get(`/admin/psychotest/aspects/${aspect.id}/edit`)">
                            <Pencil class="mr-2 h-4 w-4" />Edit
                        </Button>
                    </div>
                </CardHeader>
                <CardContent v-if="aspect.description">
                    <p class="text-muted-foreground">{{ aspect.description }}</p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>Characteristics</CardTitle>
                        <Button size="sm" @click="router.get(`/admin/psychotest/aspects/${aspect.id}/characteristics/create`)">
                            <Plus class="mr-2 h-4 w-4" />Add
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <Table v-if="aspect.characteristics?.length">
                        <TableHeader>
                            <TableRow>
                                <TableHead>#</TableHead>
                                <TableHead>Code</TableHead>
                                <TableHead>Name</TableHead>
                                <TableHead>Scores</TableHead>
                                <TableHead class="w-24">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="c in aspect.characteristics" :key="c.id">
                                <TableCell>{{ c.sort_order }}</TableCell>
                                <TableCell><Badge variant="outline">{{ c.code }}</Badge></TableCell>
                                <TableCell class="font-medium">{{ c.name }}</TableCell>
                                <TableCell>{{ c.scores?.length ?? 0 }} levels</TableCell>
                                <TableCell>
                                    <Button variant="ghost" size="sm" @click="router.get(`/admin/psychotest/aspects/${aspect.id}/characteristics/${c.id}`)">View</Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                    <p v-else class="text-muted-foreground text-sm">No characteristics yet.</p>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
