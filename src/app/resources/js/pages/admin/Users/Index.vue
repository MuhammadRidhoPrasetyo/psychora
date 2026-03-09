<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Search, Pencil, Trash2, Eye, Plus } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

type User = {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    is_active: boolean;
    roles: { id: number; name: string }[];
    created_at: string;
};

type PaginatedUsers = {
    data: User[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
    total: number;
};

type Props = {
    users: PaginatedUsers;
    filters: { search?: string };
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Pengguna', href: '/admin/users' },
];

const search = ref(props.filters.search ?? '');

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/admin/users', { search: value || undefined }, { preserveState: true, replace: true });
    }, 300);
});

function confirmDelete(user: User) {
    if (confirm(`Yakin ingin menghapus pengguna "${user.name}"?`)) {
        router.delete(`/admin/users/${user.id}`);
    }
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}

const roleColors: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    admin: 'destructive',
    user: 'secondary',
};
</script>

<template>
    <Head title="Kelola Pengguna" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>Daftar Pengguna</CardTitle>
                </CardHeader>
                <CardContent>
                    <!-- Search -->
                    <div class="mb-4 flex items-center gap-2">
                        <div class="relative flex-1">
                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                v-model="search"
                                placeholder="Cari nama atau email..."
                                class="pl-9"
                            />
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="pb-3 font-medium">Nama</th>
                                    <th class="pb-3 font-medium">Email</th>
                                    <th class="pb-3 font-medium">Role</th>
                                    <th class="pb-3 font-medium">Status</th>
                                    <th class="pb-3 font-medium">Bergabung</th>
                                    <th class="pb-3 font-medium text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users.data" :key="user.id" class="border-b last:border-0">
                                    <td class="py-3 font-medium">{{ user.name }}</td>
                                    <td class="py-3 text-muted-foreground">{{ user.email }}</td>
                                    <td class="py-3">
                                        <div class="flex gap-1">
                                            <Badge
                                                v-for="role in user.roles"
                                                :key="role.id"
                                                :variant="roleColors[role.name] ?? 'outline'"
                                            >
                                                {{ role.name }}
                                            </Badge>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <Badge :variant="user.is_active ? 'default' : 'destructive'">
                                            {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                                        </Badge>
                                    </td>
                                    <td class="py-3 text-muted-foreground">{{ formatDate(user.created_at) }}</td>
                                    <td class="py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <Link :href="`/admin/users/${user.id}`">
                                                <Button variant="ghost" size="sm">
                                                    <Eye class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Link :href="`/admin/users/${user.id}/edit`">
                                                <Button variant="ghost" size="sm">
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button variant="ghost" size="sm" @click="confirmDelete(user)">
                                                <Trash2 class="h-4 w-4 text-destructive" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!users.data.length">
                                    <td colspan="6" class="py-8 text-center text-muted-foreground">
                                        Tidak ada pengguna ditemukan
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="users.last_page > 1" class="mt-4 flex items-center justify-between">
                        <p class="text-sm text-muted-foreground">
                            Total {{ users.total }} pengguna
                        </p>
                        <div class="flex gap-1">
                            <template v-for="link in users.links" :key="link.label">
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
