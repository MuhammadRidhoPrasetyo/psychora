<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

type Role = {
    id: number;
    name: string;
};

type User = {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    is_active: boolean;
    roles: Role[];
};

type Props = {
    user: User;
    allRoles: Role[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Pengguna', href: '/admin/users' },
    { title: 'Edit', href: `/admin/users/${props.user.id}/edit` },
];

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone ?? '',
    is_active: props.user.is_active,
    roles: props.user.roles.map((r) => r.id),
});

function submit() {
    form.put(`/admin/users/${props.user.id}`);
}

function toggleRole(roleId: number) {
    const index = form.roles.indexOf(roleId);
    if (index === -1) {
        form.roles.push(roleId);
    } else {
        form.roles.splice(index, 1);
    }
}
</script>

<template>
    <Head :title="`Edit Pengguna - ${user.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center gap-2">
                <Link href="/admin/users">
                    <Button variant="ghost" size="sm">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <h2 class="text-xl font-semibold">Edit Pengguna</h2>
            </div>

            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>Informasi Pengguna</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-2">
                            <Label for="name">Nama</Label>
                            <Input id="name" v-model="form.name" />
                            <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Email</Label>
                            <Input id="email" type="email" v-model="form.email" />
                            <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
                        </div>

                        <div class="grid gap-2">
                            <Label for="phone">Telepon</Label>
                            <Input id="phone" v-model="form.phone" />
                            <p v-if="form.errors.phone" class="text-sm text-destructive">{{ form.errors.phone }}</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <Checkbox
                                id="is_active"
                                :checked="form.is_active"
                                @update:checked="form.is_active = $event"
                            />
                            <Label for="is_active">Aktif</Label>
                        </div>

                        <div class="grid gap-2">
                            <Label>Role</Label>
                            <div class="flex flex-wrap gap-4">
                                <div v-for="role in allRoles" :key="role.id" class="flex items-center gap-2">
                                    <Checkbox
                                        :id="`role-${role.id}`"
                                        :checked="form.roles.includes(role.id)"
                                        @update:checked="toggleRole(role.id)"
                                    />
                                    <Label :for="`role-${role.id}`">{{ role.name }}</Label>
                                </div>
                            </div>
                            <p v-if="form.errors.roles" class="text-sm text-destructive">{{ form.errors.roles }}</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <Button type="submit" :disabled="form.processing">
                                Simpan
                            </Button>
                            <Link href="/admin/users">
                                <Button type="button" variant="outline">Batal</Button>
                            </Link>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
