<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Pencil, ArrowLeft } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

type Subscription = {
    id: number;
    plan: { id: number; name: string };
    start_at: string;
    end_at: string;
    status: string;
};

type Payment = {
    id: number;
    amount: number;
    status: string;
    payment_method: string | null;
    created_at: string;
};

type User = {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    is_active: boolean;
    email_verified_at: string | null;
    roles: { id: number; name: string }[];
    subscriptions: Subscription[];
    payments: Payment[];
    created_at: string;
};

type Props = {
    user: User;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Pengguna', href: '/admin/users' },
    { title: props.user.name, href: `/admin/users/${props.user.id}` },
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
    <Head :title="`Pengguna - ${user.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <Link href="/admin/users">
                        <Button variant="ghost" size="sm">
                            <ArrowLeft class="h-4 w-4" />
                        </Button>
                    </Link>
                    <h2 class="text-xl font-semibold">Detail Pengguna</h2>
                </div>
                <Link :href="`/admin/users/${user.id}/edit`">
                    <Button variant="outline" size="sm">
                        <Pencil class="mr-1 h-4 w-4" />
                        Edit
                    </Button>
                </Link>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <!-- Profile Info -->
                <Card>
                    <CardHeader>
                        <CardTitle>Informasi Profil</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-muted-foreground">Nama</p>
                                <p class="font-medium">{{ user.name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Email</p>
                                <p class="font-medium">{{ user.email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Telepon</p>
                                <p class="font-medium">{{ user.phone ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Status</p>
                                <Badge :variant="user.is_active ? 'default' : 'destructive'">
                                    {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                                </Badge>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Email Terverifikasi</p>
                                <p class="font-medium">{{ user.email_verified_at ? formatDate(user.email_verified_at) : 'Belum' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Bergabung</p>
                                <p class="font-medium">{{ formatDate(user.created_at) }}</p>
                            </div>
                        </div>

                        <Separator />

                        <div>
                            <p class="mb-2 text-sm text-muted-foreground">Role</p>
                            <div class="flex gap-1">
                                <Badge v-for="role in user.roles" :key="role.id" variant="outline">
                                    {{ role.name }}
                                </Badge>
                                <span v-if="!user.roles.length" class="text-sm text-muted-foreground">Tidak ada role</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Subscription History -->
                <Card>
                    <CardHeader>
                        <CardTitle>Riwayat Langganan</CardTitle>
                        <CardDescription>Daftar langganan pengguna</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b text-left">
                                        <th class="pb-2 font-medium">Paket</th>
                                        <th class="pb-2 font-medium">Mulai</th>
                                        <th class="pb-2 font-medium">Berakhir</th>
                                        <th class="pb-2 font-medium">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="sub in user.subscriptions" :key="sub.id" class="border-b last:border-0">
                                        <td class="py-2">{{ sub.plan.name }}</td>
                                        <td class="py-2 text-muted-foreground">{{ formatDate(sub.start_at) }}</td>
                                        <td class="py-2 text-muted-foreground">{{ formatDate(sub.end_at) }}</td>
                                        <td class="py-2">
                                            <Badge :variant="sub.status === 'active' ? 'default' : 'outline'">
                                                {{ sub.status === 'active' ? 'Aktif' : 'Berakhir' }}
                                            </Badge>
                                        </td>
                                    </tr>
                                    <tr v-if="!user.subscriptions.length">
                                        <td colspan="4" class="py-4 text-center text-muted-foreground">Belum ada langganan</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Payment History -->
            <Card>
                <CardHeader>
                    <CardTitle>Riwayat Pembayaran</CardTitle>
                    <CardDescription>Daftar pembayaran pengguna</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="pb-2 font-medium">ID</th>
                                    <th class="pb-2 font-medium">Jumlah</th>
                                    <th class="pb-2 font-medium">Metode</th>
                                    <th class="pb-2 font-medium">Status</th>
                                    <th class="pb-2 font-medium">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="payment in user.payments" :key="payment.id" class="border-b last:border-0">
                                    <td class="py-2 font-mono text-xs">#{{ payment.id }}</td>
                                    <td class="py-2">{{ formatCurrency(payment.amount) }}</td>
                                    <td class="py-2 text-muted-foreground">{{ payment.payment_method ?? '-' }}</td>
                                    <td class="py-2">
                                        <Badge :variant="statusColors[payment.status] ?? 'outline'">
                                            {{ payment.status }}
                                        </Badge>
                                    </td>
                                    <td class="py-2 text-muted-foreground">{{ formatDate(payment.created_at) }}</td>
                                </tr>
                                <tr v-if="!user.payments.length">
                                    <td colspan="5" class="py-4 text-center text-muted-foreground">Belum ada pembayaran</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
