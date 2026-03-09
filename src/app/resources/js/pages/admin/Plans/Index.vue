<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

type Plan = {
    id: number;
    name: string;
    description: string | null;
    price: number;
    duration_days: number;
    is_active: boolean;
    subscribers_count: number;
};

type Props = {
    plans: Plan[];
};

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Paket Langganan', href: '/admin/plans' },
];

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
}

function confirmDelete(plan: Plan) {
    if (confirm(`Yakin ingin menghapus paket "${plan.name}"?`)) {
        router.delete(`/admin/plans/${plan.id}`);
    }
}
</script>

<template>
    <Head title="Paket Langganan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>Daftar Paket Langganan</CardTitle>
                    <Link href="/admin/plans/create">
                        <Button size="sm">
                            <Plus class="mr-1 h-4 w-4" />
                            Tambah Paket
                        </Button>
                    </Link>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="pb-3 font-medium">Nama</th>
                                    <th class="pb-3 font-medium">Harga</th>
                                    <th class="pb-3 font-medium">Durasi (Hari)</th>
                                    <th class="pb-3 font-medium">Pelanggan</th>
                                    <th class="pb-3 font-medium">Status</th>
                                    <th class="pb-3 font-medium text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="plan in plans" :key="plan.id" class="border-b last:border-0">
                                    <td class="py-3">
                                        <div>
                                            <p class="font-medium">{{ plan.name }}</p>
                                            <p v-if="plan.description" class="text-xs text-muted-foreground">{{ plan.description }}</p>
                                        </div>
                                    </td>
                                    <td class="py-3">{{ formatCurrency(plan.price) }}</td>
                                    <td class="py-3">{{ plan.duration_days }} hari</td>
                                    <td class="py-3">{{ plan.subscribers_count }}</td>
                                    <td class="py-3">
                                        <Badge :variant="plan.is_active ? 'default' : 'outline'">
                                            {{ plan.is_active ? 'Aktif' : 'Nonaktif' }}
                                        </Badge>
                                    </td>
                                    <td class="py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <Link :href="`/admin/plans/${plan.id}/edit`">
                                                <Button variant="ghost" size="sm">
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button variant="ghost" size="sm" @click="confirmDelete(plan)">
                                                <Trash2 class="h-4 w-4 text-destructive" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!plans.length">
                                    <td colspan="6" class="py-8 text-center text-muted-foreground">
                                        Belum ada paket langganan
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
