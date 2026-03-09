<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ArrowLeft, Pencil, Plus, Trash2, ExternalLink } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

type Section = {
    id: number;
    title: string;
    order: number;
    questions_count: number;
};

type Test = {
    id: number;
    title: string;
    description: string | null;
    test_type: { id: number; name: string; engine_type: string };
    duration_minutes: number | null;
    passing_score: number | null;
    is_published: boolean;
    sections: Section[];
    questions_count: number;
    created_at: string;
};

type Props = {
    test: Test;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Tes', href: '/admin/tests' },
    { title: props.test.title, href: `/admin/tests/${props.test.id}` },
];

const sectionForm = useForm({
    title: '',
    order: props.test.sections.length + 1,
});

function addSection() {
    sectionForm.post(`/admin/tests/${props.test.id}/sections`, {
        preserveScroll: true,
        onSuccess: () => {
            sectionForm.reset();
            sectionForm.order = props.test.sections.length + 1;
        },
    });
}

function deleteSection(section: Section) {
    if (confirm(`Yakin ingin menghapus seksi "${section.title}"?`)) {
        router.delete(`/admin/tests/${props.test.id}/sections/${section.id}`, {
            preserveScroll: true,
        });
    }
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}
</script>

<template>
    <Head :title="`Detail Tes - ${test.title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <Link href="/admin/tests">
                        <Button variant="ghost" size="sm">
                            <ArrowLeft class="h-4 w-4" />
                        </Button>
                    </Link>
                    <h2 class="text-xl font-semibold">Detail Tes</h2>
                </div>
                <Link :href="`/admin/tests/${test.id}/edit`">
                    <Button variant="outline" size="sm">
                        <Pencil class="mr-1 h-4 w-4" />
                        Edit
                    </Button>
                </Link>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <!-- Test Info -->
                <Card class="lg:col-span-1">
                    <CardHeader>
                        <CardTitle>Informasi Tes</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Judul</p>
                            <p class="font-medium">{{ test.title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Jenis Tes</p>
                            <Badge variant="secondary">{{ test.test_type.name }}</Badge>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Tipe Engine</p>
                            <Badge variant="outline">{{ test.test_type.engine_type }}</Badge>
                        </div>
                        <div v-if="test.description">
                            <p class="text-sm text-muted-foreground">Deskripsi</p>
                            <p class="text-sm">{{ test.description }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Durasi</p>
                            <p class="font-medium">{{ test.duration_minutes ? `${test.duration_minutes} menit` : 'Tidak ada batas' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Passing Score</p>
                            <p class="font-medium">{{ test.passing_score ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Total Soal</p>
                            <p class="font-medium">{{ test.questions_count }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Status</p>
                            <Badge :variant="test.is_published ? 'default' : 'outline'">
                                {{ test.is_published ? 'Terbit' : 'Draft' }}
                            </Badge>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Dibuat</p>
                            <p class="text-sm">{{ formatDate(test.created_at) }}</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Sections -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <CardTitle>Seksi Tes</CardTitle>
                        <CardDescription>Kelola seksi dan soal tes</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Add Section Form -->
                        <form @submit.prevent="addSection" class="flex items-end gap-2">
                            <div class="flex-1 grid gap-1">
                                <Label for="section_title">Judul Seksi Baru</Label>
                                <Input id="section_title" v-model="sectionForm.title" placeholder="Contoh: Bagian 1 - TWK" />
                            </div>
                            <div class="w-24 grid gap-1">
                                <Label for="section_order">Urutan</Label>
                                <Input id="section_order" type="number" v-model.number="sectionForm.order" min="1" />
                            </div>
                            <Button type="submit" :disabled="sectionForm.processing" size="sm">
                                <Plus class="mr-1 h-4 w-4" />
                                Tambah
                            </Button>
                        </form>
                        <p v-if="sectionForm.errors.title" class="text-sm text-destructive">{{ sectionForm.errors.title }}</p>

                        <Separator />

                        <!-- Sections List -->
                        <div v-if="test.sections.length" class="space-y-2">
                            <div
                                v-for="section in test.sections"
                                :key="section.id"
                                class="flex items-center justify-between rounded-lg border p-3"
                            >
                                <div>
                                    <p class="font-medium">{{ section.title }}</p>
                                    <p class="text-xs text-muted-foreground">
                                        Urutan: {{ section.order }} &middot; {{ section.questions_count }} soal
                                    </p>
                                </div>
                                <div class="flex items-center gap-1">
                                    <Link :href="`/admin/tests/${test.id}/sections/${section.id}/questions`">
                                        <Button variant="outline" size="sm">
                                            <ExternalLink class="mr-1 h-3 w-3" />
                                            Kelola Soal
                                        </Button>
                                    </Link>
                                    <Button variant="ghost" size="sm" @click="deleteSection(section)">
                                        <Trash2 class="h-4 w-4 text-destructive" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-center text-sm text-muted-foreground py-4">
                            Belum ada seksi. Tambahkan seksi pertama di atas.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
