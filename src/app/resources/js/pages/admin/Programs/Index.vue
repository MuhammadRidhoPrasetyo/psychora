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
};

type Program = {
    id: number;
    name: string;
    description: string | null;
    is_active: boolean;
    test_types: TestType[];
};

type Props = {
    programs: Program[];
};

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Program', href: '/admin/programs' },
];

function confirmDelete(program: Program) {
    if (confirm(`Yakin ingin menghapus program "${program.name}"?`)) {
        router.delete(`/admin/programs/${program.id}`);
    }
}
</script>

<template>
    <Head title="Program" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>Daftar Program</CardTitle>
                    <Link href="/admin/programs/create">
                        <Button size="sm">
                            <Plus class="mr-1 h-4 w-4" />
                            Tambah Program
                        </Button>
                    </Link>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="pb-3 font-medium">Nama</th>
                                    <th class="pb-3 font-medium">Jenis Tes</th>
                                    <th class="pb-3 font-medium">Status</th>
                                    <th class="pb-3 font-medium text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="program in programs" :key="program.id" class="border-b last:border-0">
                                    <td class="py-3">
                                        <div>
                                            <p class="font-medium">{{ program.name }}</p>
                                            <p v-if="program.description" class="text-xs text-muted-foreground">{{ program.description }}</p>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="flex flex-wrap gap-1">
                                            <Badge v-for="tt in program.test_types" :key="tt.id" variant="secondary">
                                                {{ tt.name }}
                                            </Badge>
                                            <span v-if="!program.test_types.length" class="text-muted-foreground">-</span>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <Badge :variant="program.is_active ? 'default' : 'outline'">
                                            {{ program.is_active ? 'Aktif' : 'Nonaktif' }}
                                        </Badge>
                                    </td>
                                    <td class="py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <Link :href="`/admin/programs/${program.id}/edit`">
                                                <Button variant="ghost" size="sm">
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button variant="ghost" size="sm" @click="confirmDelete(program)">
                                                <Trash2 class="h-4 w-4 text-destructive" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!programs.length">
                                    <td colspan="4" class="py-8 text-center text-muted-foreground">
                                        Belum ada program
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
