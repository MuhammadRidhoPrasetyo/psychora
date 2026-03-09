<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

type TestType = {
    id: number;
    name: string;
    engine_type: string;
    description: string | null;
    tests_count: number;
};

type Props = {
    testTypes: TestType[];
};

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Jenis Tes', href: '/admin/test-types' },
];

const engineColors: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    classic: 'default',
    disc: 'secondary',
    cfit: 'outline',
};

function confirmDelete(testType: TestType) {
    if (confirm(`Yakin ingin menghapus jenis tes "${testType.name}"?`)) {
        router.delete(`/admin/test-types/${testType.id}`);
    }
}
</script>

<template>
    <Head title="Jenis Tes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>Daftar Jenis Tes</CardTitle>
                    <Link href="/admin/test-types/create">
                        <Button size="sm">
                            <Plus class="mr-1 h-4 w-4" />
                            Tambah Jenis Tes
                        </Button>
                    </Link>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="pb-3 font-medium">Nama</th>
                                    <th class="pb-3 font-medium">Tipe Engine</th>
                                    <th class="pb-3 font-medium">Jumlah Tes</th>
                                    <th class="pb-3 font-medium text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="tt in testTypes" :key="tt.id" class="border-b last:border-0">
                                    <td class="py-3">
                                        <div>
                                            <p class="font-medium">{{ tt.name }}</p>
                                            <p v-if="tt.description" class="text-xs text-muted-foreground">{{ tt.description }}</p>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <Badge :variant="engineColors[tt.engine_type] ?? 'outline'">
                                            {{ tt.engine_type }}
                                        </Badge>
                                    </td>
                                    <td class="py-3">{{ tt.tests_count }}</td>
                                    <td class="py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <Link :href="`/admin/test-types/${tt.id}/edit`">
                                                <Button variant="ghost" size="sm">
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button variant="ghost" size="sm" @click="confirmDelete(tt)">
                                                <Trash2 class="h-4 w-4 text-destructive" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!testTypes.length">
                                    <td colspan="4" class="py-8 text-center text-muted-foreground">
                                        Belum ada jenis tes
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
