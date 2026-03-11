<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Pencil } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { User } from '@/types/models';

type Props = { user: User };
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Users', href: '/admin/users' },
    { title: props.user.name, href: `/admin/users/${props.user.id}` },
];
</script>

<template>
    <Head :title="user.name" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold">{{ user.name }}</h2>
                <Button as-child variant="outline">
                    <Link :href="`/admin/users/${user.id}/edit`"><Pencil class="mr-2 h-4 w-4" /> Edit</Link>
                </Button>
            </div>
            <Card>
                <CardHeader><CardTitle>User Details</CardTitle></CardHeader>
                <CardContent class="space-y-3">
                    <div class="grid grid-cols-2 gap-2">
                        <span class="text-muted-foreground">Email</span>
                        <span>{{ user.email }}</span>
                        <span class="text-muted-foreground">Status</span>
                        <span><Badge :variant="user.is_active ? 'default' : 'destructive'">{{ user.is_active ? 'Active' : 'Inactive' }}</Badge></span>
                        <span class="text-muted-foreground">Role</span>
                        <span><Badge v-for="role in user.roles" :key="role.id" variant="outline">{{ role.name }}</Badge></span>
                        <span class="text-muted-foreground">Verified</span>
                        <span>{{ user.email_verified_at ? new Date(user.email_verified_at).toLocaleDateString('id-ID') : 'Not verified' }}</span>
                        <span class="text-muted-foreground">Joined</span>
                        <span>{{ new Date(user.created_at).toLocaleDateString('id-ID') }}</span>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
