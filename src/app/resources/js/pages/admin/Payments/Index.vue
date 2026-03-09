<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Search, Check, X } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

type Payment = {
    id: number;
    user: { id: number; name: string; email: string };
    plan: { id: number; name: string };
    amount: number;
    status: string;
    payment_method: string | null;
    proof_file: string | null;
    created_at: string;
};

type PaginatedPayments = {
    data: Payment[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
    total: number;
};

type Props = {
    payments: PaginatedPayments;
    filters: { search?: string; status?: string };
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Pembayaran', href: '/admin/payments' },
];

const search = ref(props.filters.search ?? '');
const statusFilter = ref(props.filters.status ?? 'all');

function applyFilters() {
    router.get('/admin/payments', {
        search: search.value || undefined,
        status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    }, { preserveState: true, replace: true });
}

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});

watch(statusFilter, () => {
    applyFilters();
});

function confirmPayment(payment: Payment) {
    if (confirm(`Konfirmasi pembayaran #${payment.id} dari ${payment.user.name}?`)) {
        router.put(`/admin/payments/${payment.id}/confirm`, {}, { preserveScroll: true });
    }
}

function rejectPayment(payment: Payment) {
    if (confirm(`Tolak pembayaran #${payment.id} dari ${payment.user.name}?`)) {
        router.put(`/admin/payments/${payment.id}/reject`, {}, { preserveScroll: true });
    }
}

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}

const statusColors: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    confirmed: 'default',
    pending: 'secondary',
    rejected: 'destructive',
    expired: 'outline',
};

const statusLabels: Record<string, string> = {
    pending: 'Menunggu',
    confirmed: 'Dikonfirmasi',
    rejected: 'Ditolak',
    expired: 'Kedaluwarsa',
};
</script>

<template>
    <Head title="Pembayaran" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card>
                <CardHeader>
                    <CardTitle>Daftar Pembayaran</CardTitle>
                </CardHeader>
                <CardContent>
                    <!-- Filters -->
                    <div class="mb-4 flex flex-col gap-2 sm:flex-row">
                        <div class="relative flex-1">
                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <Input v-model="search" placeholder="Cari nama atau email pengguna..." class="pl-9" />
                        </div>
                        <Select v-model="statusFilter">
                            <SelectTrigger class="w-full sm:w-48">
                                <SelectValue placeholder="Semua Status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">Semua Status</SelectItem>
                                <SelectItem value="pending">Menunggu</SelectItem>
                                <SelectItem value="confirmed">Dikonfirmasi</SelectItem>
                                <SelectItem value="rejected">Ditolak</SelectItem>
                                <SelectItem value="expired">Kedaluwarsa</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="pb-3 font-medium">ID</th>
                                    <th class="pb-3 font-medium">Pengguna</th>
                                    <th class="pb-3 font-medium">Paket</th>
                                    <th class="pb-3 font-medium">Jumlah</th>
                                    <th class="pb-3 font-medium">Metode</th>
                                    <th class="pb-3 font-medium">Status</th>
                                    <th class="pb-3 font-medium">Tanggal</th>
                                    <th class="pb-3 font-medium text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="payment in payments.data" :key="payment.id" class="border-b last:border-0">
                                    <td class="py-3 font-mono text-xs">#{{ payment.id }}</td>
                                    <td class="py-3">
                                        <div>
                                            <p class="font-medium">{{ payment.user.name }}</p>
                                            <p class="text-xs text-muted-foreground">{{ payment.user.email }}</p>
                                        </div>
                                    </td>
                                    <td class="py-3">{{ payment.plan.name }}</td>
                                    <td class="py-3">{{ formatCurrency(payment.amount) }}</td>
                                    <td class="py-3 text-muted-foreground">{{ payment.payment_method ?? '-' }}</td>
                                    <td class="py-3">
                                        <Badge :variant="statusColors[payment.status] ?? 'outline'">
                                            {{ statusLabels[payment.status] ?? payment.status }}
                                        </Badge>
                                    </td>
                                    <td class="py-3 text-muted-foreground">{{ formatDate(payment.created_at) }}</td>
                                    <td class="py-3 text-right">
                                        <div v-if="payment.status === 'pending'" class="flex items-center justify-end gap-1">
                                            <Button variant="outline" size="sm" class="text-green-600" @click="confirmPayment(payment)">
                                                <Check class="mr-1 h-4 w-4" />
                                                Konfirmasi
                                            </Button>
                                            <Button variant="outline" size="sm" class="text-destructive" @click="rejectPayment(payment)">
                                                <X class="mr-1 h-4 w-4" />
                                                Tolak
                                            </Button>
                                        </div>
                                        <span v-else class="text-xs text-muted-foreground">-</span>
                                    </td>
                                </tr>
                                <tr v-if="!payments.data.length">
                                    <td colspan="8" class="py-8 text-center text-muted-foreground">
                                        Tidak ada pembayaran ditemukan
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="payments.last_page > 1" class="mt-4 flex items-center justify-between">
                        <p class="text-sm text-muted-foreground">Total {{ payments.total }} pembayaran</p>
                        <div class="flex gap-1">
                            <template v-for="link in payments.links" :key="link.label">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    preserve-state
                                    class="inline-flex h-8 min-w-8 items-center justify-center rounded-md border px-2 text-sm"
                                    :class="{ 'bg-primary text-primary-foreground': link.active }"
                                    v-html="link.label"
                                />
                                <span
                                    v-else
                                    class="inline-flex h-8 min-w-8 items-center justify-center rounded-md px-2 text-sm text-muted-foreground"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
