<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { BreadcrumbItem } from '@/types';
import type { PsychotestAspect, PsychotestCharacteristic } from '@/types/models';
import { Pencil } from 'lucide-vue-next';

const props = defineProps<{ aspect: PsychotestAspect; characteristic: PsychotestCharacteristic }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Psychotest', href: '#' },
    { title: props.aspect.name, href: `/admin/psychotest/aspects/${props.aspect.id}` },
    { title: props.characteristic.name, href: '#' },
];
</script>

<template>
    <Head :title="characteristic.name" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>{{ characteristic.name }}</CardTitle>
                            <CardDescription>Code: {{ characteristic.code }} · Aspect: {{ aspect.name }} · Order: {{ characteristic.sort_order }}</CardDescription>
                        </div>
                        <Button variant="outline" @click="router.get(`/admin/psychotest/aspects/${aspect.id}/characteristics/${characteristic.id}/edit`)">
                            <Pencil class="mr-2 h-4 w-4" />Edit
                        </Button>
                    </div>
                </CardHeader>
                <CardContent v-if="characteristic.description">
                    <p class="text-muted-foreground">{{ characteristic.description }}</p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Score Levels</CardTitle></CardHeader>
                <CardContent>
                    <Table v-if="characteristic.scores?.length">
                        <TableHeader>
                            <TableRow>
                                <TableHead>Level</TableHead>
                                <TableHead>Min Score</TableHead>
                                <TableHead>Max Score</TableHead>
                                <TableHead>Interpretation</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="s in characteristic.scores" :key="s.id">
                                <TableCell><Badge variant="outline">{{ s.level }}</Badge></TableCell>
                                <TableCell>{{ s.min_score }}</TableCell>
                                <TableCell>{{ s.max_score }}</TableCell>
                                <TableCell class="max-w-md">{{ s.interpretation }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                    <p v-else class="text-muted-foreground text-sm">No score levels defined yet.</p>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
