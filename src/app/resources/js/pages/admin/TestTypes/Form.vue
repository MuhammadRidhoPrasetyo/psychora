<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

type TestType = {
    id: number;
    name: string;
    engine_type: string;
    description: string | null;
};

type Props = {
    testType?: TestType;
    engineTypes: string[];
};

const props = defineProps<Props>();

const isEdit = !!props.testType;
const pageTitle = isEdit ? 'Edit Jenis Tes' : 'Tambah Jenis Tes';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Jenis Tes', href: '/admin/test-types' },
    { title: pageTitle, href: isEdit ? `/admin/test-types/${props.testType!.id}/edit` : '/admin/test-types/create' },
];

const form = useForm({
    name: props.testType?.name ?? '',
    engine_type: props.testType?.engine_type ?? '',
    description: props.testType?.description ?? '',
});

function submit() {
    if (isEdit) {
        form.put(`/admin/test-types/${props.testType!.id}`);
    } else {
        form.post('/admin/test-types');
    }
}
</script>

<template>
    <Head :title="pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center gap-2">
                <Link href="/admin/test-types">
                    <Button variant="ghost" size="sm">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <h2 class="text-xl font-semibold">{{ pageTitle }}</h2>
            </div>

            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>Detail Jenis Tes</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-2">
                            <Label for="name">Nama</Label>
                            <Input id="name" v-model="form.name" placeholder="Contoh: TWK, TIU, TKP" />
                            <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
                        </div>

                        <div class="grid gap-2">
                            <Label for="engine_type">Tipe Engine</Label>
                            <Select v-model="form.engine_type">
                                <SelectTrigger>
                                    <SelectValue placeholder="Pilih tipe engine" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="engine in engineTypes" :key="engine" :value="engine">
                                        {{ engine }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="form.errors.engine_type" class="text-sm text-destructive">{{ form.errors.engine_type }}</p>
                        </div>

                        <div class="grid gap-2">
                            <Label for="description">Deskripsi</Label>
                            <Input id="description" v-model="form.description" placeholder="Deskripsi singkat jenis tes" />
                            <p v-if="form.errors.description" class="text-sm text-destructive">{{ form.errors.description }}</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <Button type="submit" :disabled="form.processing">
                                {{ isEdit ? 'Simpan Perubahan' : 'Tambah Jenis Tes' }}
                            </Button>
                            <Link href="/admin/test-types">
                                <Button type="button" variant="outline">Batal</Button>
                            </Link>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
