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
    program?: Program;
    testTypes: TestType[];
};

const props = defineProps<Props>();

const isEdit = !!props.program;
const pageTitle = isEdit ? 'Edit Program' : 'Tambah Program';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Program', href: '/admin/programs' },
    { title: pageTitle, href: isEdit ? `/admin/programs/${props.program!.id}/edit` : '/admin/programs/create' },
];

const form = useForm({
    name: props.program?.name ?? '',
    description: props.program?.description ?? '',
    is_active: props.program?.is_active ?? true,
    test_type_ids: props.program?.test_types.map((tt) => tt.id) ?? [],
});

function submit() {
    if (isEdit) {
        form.put(`/admin/programs/${props.program!.id}`);
    } else {
        form.post('/admin/programs');
    }
}

function toggleTestType(id: number) {
    const index = form.test_type_ids.indexOf(id);
    if (index === -1) {
        form.test_type_ids.push(id);
    } else {
        form.test_type_ids.splice(index, 1);
    }
}
</script>

<template>
    <Head :title="pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center gap-2">
                <Link href="/admin/programs">
                    <Button variant="ghost" size="sm">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <h2 class="text-xl font-semibold">{{ pageTitle }}</h2>
            </div>

            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>Detail Program</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-2">
                            <Label for="name">Nama Program</Label>
                            <Input id="name" v-model="form.name" placeholder="Contoh: CPNS 2026" />
                            <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
                        </div>

                        <div class="grid gap-2">
                            <Label for="description">Deskripsi</Label>
                            <Input id="description" v-model="form.description" placeholder="Deskripsi singkat program" />
                            <p v-if="form.errors.description" class="text-sm text-destructive">{{ form.errors.description }}</p>
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
                            <Label>Jenis Tes</Label>
                            <div class="grid grid-cols-2 gap-3">
                                <div v-for="tt in testTypes" :key="tt.id" class="flex items-center gap-2">
                                    <Checkbox
                                        :id="`tt-${tt.id}`"
                                        :checked="form.test_type_ids.includes(tt.id)"
                                        @update:checked="toggleTestType(tt.id)"
                                    />
                                    <Label :for="`tt-${tt.id}`">{{ tt.name }}</Label>
                                </div>
                            </div>
                            <p v-if="!testTypes.length" class="text-sm text-muted-foreground">Belum ada jenis tes</p>
                            <p v-if="form.errors.test_type_ids" class="text-sm text-destructive">{{ form.errors.test_type_ids }}</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <Button type="submit" :disabled="form.processing">
                                {{ isEdit ? 'Simpan Perubahan' : 'Tambah Program' }}
                            </Button>
                            <Link href="/admin/programs">
                                <Button type="button" variant="outline">Batal</Button>
                            </Link>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
