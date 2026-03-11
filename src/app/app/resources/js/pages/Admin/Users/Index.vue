<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import PaginationLinks from '@/components/PaginationLinks.vue';
import { Plus, Pencil, Eye, ToggleLeft, ToggleRight } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { User, PaginatedData } from '@/types/models';
import { ref } from 'vue';

type Props = {
    users: PaginatedData<User>;
    filters: { search?: string };
};

const props = defineProps<Props>();
const search = ref(props.filters.search ?? '');

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Users', href: '/admin/users' },
];

function doSearch() {
    router.get('/admin/users', { search: search.value }, { preserveState: true });
}

function toggleActive(user: User) {
    router.patch(`/admin/users/${user.id}/toggle-active`, {}, { preserveScroll: true });
}
</script>

<template>
    <Head title="Manage Users" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold tracking-tight">Users</h2>
                <Button as-child>
                    <Link href="/admin/users/create"><Plus class="mr-2 h-4 w-4" /> Add User</Link>
                </Button>
            </div>

            <div class="flex gap-2">
                <Input v-model="search" placeholder="Search users..." class="max-w-sm" @keyup.enter="doSearch" />
                <Button variant="outline" @click="doSearch">Search</Button>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Role</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Joined</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="user in users.data" :key="user.id">
                            <TableCell class="font-medium">{{ user.name }}</TableCell>
                            <TableCell>{{ user.email }}</TableCell>
                            <TableCell>
                                <Badge v-for="role in user.roles" :key="role.id" variant="outline" class="mr-1">{{ role.name }}</Badge>
                            </TableCell>
                            <TableCell>
                                <Badge :variant="user.is_active ? 'default' : 'destructive'">
                                    {{ user.is_active ? 'Active' : 'Inactive' }}
                                </Badge>
                            </TableCell>
                            <TableCell>{{ new Date(user.created_at).toLocaleDateString('id-ID') }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-1">
                                    <Button variant="ghost" size="icon" as-child>
                                        <Link :href="`/admin/users/${user.id}`"><Eye class="h-4 w-4" /></Link>
                                    </Button>
                                    <Button variant="ghost" size="icon" as-child>
                                        <Link :href="`/admin/users/${user.id}/edit`"><Pencil class="h-4 w-4" /></Link>
                                    </Button>
                                    <Button variant="ghost" size="icon" @click="toggleActive(user)">
                                        <ToggleRight v-if="user.is_active" class="h-4 w-4" />
                                        <ToggleLeft v-else class="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <PaginationLinks :links="users.links" :from="users.from" :to="users.to" :total="users.total" />
        </div>
    </AdminLayout>
</template>
