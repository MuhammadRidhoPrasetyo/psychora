<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Users, CreditCard, FileText, DollarSign, ArrowUpRight } from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

type RecentUser = {
    id: number;
    name: string;
    email: string;
    created_at: string;
    is_active: boolean;
};

type RecentPayment = {
    id: number;
    user: { id: number; name: string };
    amount: number;
    status: string;
    created_at: string;
};

type Props = {
    stats: {
        total_users: number;
        active_subscriptions: number;
        total_attempts: number;
        total_revenue: number;
    };
    recentUsers: RecentUser[];
    recentPayments: RecentPayment[];
};

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Dashboard', href: '/admin' },
];

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
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Pengguna</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.total_users.toLocaleString('id-ID') }}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Langganan Aktif</CardTitle>
                        <CreditCard class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.active_subscriptions.toLocaleString('id-ID') }}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Percobaan</CardTitle>
                        <FileText class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.total_attempts.toLocaleString('id-ID') }}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Pendapatan</CardTitle>
                        <DollarSign class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ formatCurrency(stats.total_revenue) }}</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Recent Users & Payments -->
            <div class="grid gap-4 lg:grid-cols-2">
                <!-- Recent Users -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between">
                        <div>
                            <CardTitle>Pengguna Terbaru</CardTitle>
                            <CardDescription>Pengguna yang baru mendaftar</CardDescription>
                        </div>
                        <Link href="/admin/users">
                            <Button variant="outline" size="sm">
                                Lihat Semua
                                <ArrowUpRight class="ml-1 h-4 w-4" />
                            </Button>
                        </Link>
                    </CardHeader>
                    <CardContent>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b text-left">
                                        <th class="pb-2 font-medium">Nama</th>
                                        <th class="pb-2 font-medium">Email</th>
                                        <th class="pb-2 font-medium">Status</th>
                                        <th class="pb-2 font-medium">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in recentUsers" :key="user.id" class="border-b last:border-0">
                                        <td class="py-2">{{ user.name }}</td>
                                        <td class="py-2 text-muted-foreground">{{ user.email }}</td>
                                        <td class="py-2">
                                            <Badge :variant="user.is_active ? 'default' : 'destructive'">
                                                {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                                            </Badge>
                                        </td>
                                        <td class="py-2 text-muted-foreground">{{ formatDate(user.created_at) }}</td>
                                    </tr>
                                    <tr v-if="!recentUsers.length">
                                        <td colspan="4" class="py-4 text-center text-muted-foreground">Belum ada pengguna</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Payments -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between">
                        <div>
                            <CardTitle>Pembayaran Terbaru</CardTitle>
                            <CardDescription>Transaksi pembayaran terbaru</CardDescription>
                        </div>
                        <Link href="/admin/payments">
                            <Button variant="outline" size="sm">
                                Lihat Semua
                                <ArrowUpRight class="ml-1 h-4 w-4" />
                            </Button>
                        </Link>
                    </CardHeader>
                    <CardContent>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b text-left">
                                        <th class="pb-2 font-medium">Pengguna</th>
                                        <th class="pb-2 font-medium">Jumlah</th>
                                        <th class="pb-2 font-medium">Status</th>
                                        <th class="pb-2 font-medium">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="payment in recentPayments" :key="payment.id" class="border-b last:border-0">
                                        <td class="py-2">{{ payment.user.name }}</td>
                                        <td class="py-2">{{ formatCurrency(payment.amount) }}</td>
                                        <td class="py-2">
                                            <Badge :variant="statusColors[payment.status] ?? 'outline'">
                                                {{ payment.status }}
                                            </Badge>
                                        </td>
                                        <td class="py-2 text-muted-foreground">{{ formatDate(payment.created_at) }}</td>
                                    </tr>
                                    <tr v-if="!recentPayments.length">
                                        <td colspan="4" class="py-4 text-center text-muted-foreground">Belum ada pembayaran</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
