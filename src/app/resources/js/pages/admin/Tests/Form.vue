<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
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
};

type Test = {
    id: number;
    title: string;
    test_type_id: number;
    description: string | null;
    duration_minutes: number | null;
    is_published: boolean;
    passing_score: number | null;
};

type Props = {
    test?: Test;
    testTypes: TestType[];
};

const props = defineProps<Props>();

const isEdit = !!props.test;
const pageTitle = isEdit ? 'Edit Tes' : 'Tambah Tes';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Tes', href: '/admin/tests' },
    { title: pageTitle, href: isEdit ? `/admin/tests/${props.test!.id}/edit` : '/admin/tests/create' },
];

const form = useForm({
    title: props.test?.title ?? '',
    test_type_id: props.test?.test_type_id ? String(props.test.test_type_id) : '',
    description: props.test?.description ?? '',
    duration_minutes: props.test?.duration_minutes ?? null,
    passing_score: props.test?.passing_score ?? null,
    is_published: props.test?.is_published ?? false,
});

function submit() {
    const data = {
        ...form.data(),
        test_type_id: Number(form.test_type_id),
    };

    if (isEdit) {
        form.put(`/admin/tests/${props.test!.id}`);
    } else {
        form.post('/admin/tests');
    }
}
</script>

<template>
    <Head :title="pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center gap-2">
                <Link href="/admin/tests">
                    <Button variant="ghost" size="sm">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <h2 class="text-xl font-semibold">{{ pageTitle }}</h2>
            </div>

            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>Detail Tes</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-2">
                            <Label for="title">Judul Tes</Label>
                            <Input id="title" v-model="form.title" placeholder="Contoh: Tryout CPNS Batch 1" />
                            <p v-if="form.errors.title" class="text-sm text-destructive">{{ form.errors.title }}</p>
                        </div>

                        <div class="grid gap-2">
                            <Label for="test_type_id">Jenis Tes</Label>
                            <Select v-model="form.test_type_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Pilih jenis tes" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="tt in testTypes" :key="tt.id" :value="String(tt.id)">
                                        {{ tt.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="form.errors.test_type_id" class="text-sm text-destructive">{{ form.errors.test_type_id }}</p>
                        </div>

                        <div class="grid gap-2">
                            <Label for="description">Deskripsi</Label>
                            <Input id="description" v-model="form.description" placeholder="Deskripsi singkat tes" />
                            <p v-if="form.errors.description" class="text-sm text-destructive">{{ form.errors.description }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="duration_minutes">Durasi (Menit)</Label>
                                <Input id="duration_minutes" type="number" v-model.number="form.duration_minutes" min="1" placeholder="Kosongkan jika tidak ada batas" />
                                <p v-if="form.errors.duration_minutes" class="text-sm text-destructive">{{ form.errors.duration_minutes }}</p>
                            </div>

                            <div class="grid gap-2">
                                <Label for="passing_score">Passing Score</Label>
                                <Input id="passing_score" type="number" v-model.number="form.passing_score" min="0" placeholder="Opsional" />
                                <p v-if="form.errors.passing_score" class="text-sm text-destructive">{{ form.errors.passing_score }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <Checkbox
                                id="is_published"
                                :checked="form.is_published"
                                @update:checked="form.is_published = $event"
                            />
                            <Label for="is_published">Terbitkan</Label>
                        </div>

                        <div class="flex items-center gap-2">
                            <Button type="submit" :disabled="form.processing">
                                {{ isEdit ? 'Simpan Perubahan' : 'Tambah Tes' }}
                            </Button>
                            <Link href="/admin/tests">
                                <Button type="button" variant="outline">Batal</Button>
                            </Link>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
