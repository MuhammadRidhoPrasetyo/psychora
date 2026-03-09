<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Plus, Pencil, Trash2, Eye, Search } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
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
    test_type: TestType;
    is_published: boolean;
    duration_minutes: number | null;
    questions_count: number;
    created_at: string;
};

type PaginatedTests = {
    data: Test[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
    total: number;
};

type Props = {
    tests: PaginatedTests;
    testTypes: TestType[];
    filters: { search?: string; test_type_id?: string };
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Tes', href: '/admin/tests' },
];

const search = ref(props.filters.search ?? '');
const typeFilter = ref(props.filters.test_type_id ?? 'all');

function applyFilters() {
    router.get('/admin/tests', {
        search: search.value || undefined,
        test_type_id: typeFilter.value !== 'all' ? typeFilter.value : undefined,
    }, { preserveState: true, replace: true });
}

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});

watch(typeFilter, () => {
    applyFilters();
});

function confirmDelete(test: Test) {
    if (confirm(`Yakin ingin menghapus tes "${test.title}"?`)) {
        router.delete(`/admin/tests/${test.id}`);
    }
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}
</script>

<template>
    <Head title="Kelola Tes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>Daftar Tes</CardTitle>
                    <Link href="/admin/tests/create">
                        <Button size="sm">
                            <Plus class="mr-1 h-4 w-4" />
                            Tambah Tes
                        </Button>
                    </Link>
                </CardHeader>
                <CardContent>
                    <!-- Filters -->
                    <div class="mb-4 flex flex-col gap-2 sm:flex-row">
                        <div class="relative flex-1">
                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <Input v-model="search" placeholder="Cari judul tes..." class="pl-9" />
                        </div>
                        <Select v-model="typeFilter">
                            <SelectTrigger class="w-full sm:w-48">
                                <SelectValue placeholder="Semua Jenis" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">Semua Jenis</SelectItem>
                                <SelectItem v-for="tt in testTypes" :key="tt.id" :value="String(tt.id)">
                                    {{ tt.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="pb-3 font-medium">Judul</th>
                                    <th class="pb-3 font-medium">Jenis Tes</th>
                                    <th class="pb-3 font-medium">Durasi</th>
                                    <th class="pb-3 font-medium">Soal</th>
                                    <th class="pb-3 font-medium">Status</th>
                                    <th class="pb-3 font-medium">Dibuat</th>
                                    <th class="pb-3 font-medium text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="test in tests.data" :key="test.id" class="border-b last:border-0">
                                    <td class="py-3 font-medium">{{ test.title }}</td>
                                    <td class="py-3">
                                        <Badge variant="secondary">{{ test.test_type.name }}</Badge>
                                    </td>
                                    <td class="py-3 text-muted-foreground">
                                        {{ test.duration_minutes ? `${test.duration_minutes} menit` : '-' }}
                                    </td>
                                    <td class="py-3">{{ test.questions_count }}</td>
                                    <td class="py-3">
                                        <Badge :variant="test.is_published ? 'default' : 'outline'">
                                            {{ test.is_published ? 'Terbit' : 'Draft' }}
                                        </Badge>
                                    </td>
                                    <td class="py-3 text-muted-foreground">{{ formatDate(test.created_at) }}</td>
                                    <td class="py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <Link :href="`/admin/tests/${test.id}`">
                                                <Button variant="ghost" size="sm">
                                                    <Eye class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Link :href="`/admin/tests/${test.id}/edit`">
                                                <Button variant="ghost" size="sm">
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button variant="ghost" size="sm" @click="confirmDelete(test)">
                                                <Trash2 class="h-4 w-4 text-destructive" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!tests.data.length">
                                    <td colspan="7" class="py-8 text-center text-muted-foreground">
                                        Tidak ada tes ditemukan
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="tests.last_page > 1" class="mt-4 flex items-center justify-between">
                        <p class="text-sm text-muted-foreground">Total {{ tests.total }} tes</p>
                        <div class="flex gap-1">
                            <template v-for="link in tests.links" :key="link.label">
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
