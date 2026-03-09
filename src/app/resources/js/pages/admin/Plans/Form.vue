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

type Plan = {
    id: number;
    name: string;
    description: string | null;
    price: number;
    duration_days: number;
    is_active: boolean;
};

type Props = {
    plan?: Plan;
};

const props = defineProps<Props>();

const isEdit = !!props.plan;
const pageTitle = isEdit ? 'Edit Paket' : 'Tambah Paket';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Paket Langganan', href: '/admin/plans' },
    { title: pageTitle, href: isEdit ? `/admin/plans/${props.plan!.id}/edit` : '/admin/plans/create' },
];

const form = useForm({
    name: props.plan?.name ?? '',
    description: props.plan?.description ?? '',
    price: props.plan?.price ?? 0,
    duration_days: props.plan?.duration_days ?? 30,
    is_active: props.plan?.is_active ?? true,
});

function submit() {
    if (isEdit) {
        form.put(`/admin/plans/${props.plan!.id}`);
    } else {
        form.post('/admin/plans');
    }
}
</script>

<template>
    <Head :title="pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center gap-2">
                <Link href="/admin/plans">
                    <Button variant="ghost" size="sm">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <h2 class="text-xl font-semibold">{{ pageTitle }}</h2>
            </div>

            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>Detail Paket</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-2">
                            <Label for="name">Nama Paket</Label>
                            <Input id="name" v-model="form.name" placeholder="Contoh: Paket Premium" />
                            <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
                        </div>

                        <div class="grid gap-2">
                            <Label for="description">Deskripsi</Label>
                            <Input id="description" v-model="form.description" placeholder="Deskripsi singkat paket" />
                            <p v-if="form.errors.description" class="text-sm text-destructive">{{ form.errors.description }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="price">Harga (Rp)</Label>
                                <Input id="price" type="number" v-model.number="form.price" min="0" />
                                <p v-if="form.errors.price" class="text-sm text-destructive">{{ form.errors.price }}</p>
                            </div>

                            <div class="grid gap-2">
                                <Label for="duration_days">Durasi (Hari)</Label>
                                <Input id="duration_days" type="number" v-model.number="form.duration_days" min="1" />
                                <p v-if="form.errors.duration_days" class="text-sm text-destructive">{{ form.errors.duration_days }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <Checkbox
                                id="is_active"
                                :checked="form.is_active"
                                @update:checked="form.is_active = $event"
                            />
                            <Label for="is_active">Aktif</Label>
                        </div>

                        <div class="flex items-center gap-2">
                            <Button type="submit" :disabled="form.processing">
                                {{ isEdit ? 'Simpan Perubahan' : 'Tambah Paket' }}
                            </Button>
                            <Link href="/admin/plans">
                                <Button type="button" variant="outline">Batal</Button>
                            </Link>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
