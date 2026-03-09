<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle, GraduationCap } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface TestType {
    id: string;
    name: string;
    engine_type: string;
}

interface Program {
    id: string;
    name: string;
    slug: string;
    description: string | null;
    test_types: TestType[];
}

const props = defineProps<{
    programs: Program[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Program', href: '/catalog/programs' },
];
</script>

<template>
    <Head title="Program Latihan" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Program Latihan</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pilih program sesuai target seleksi Anda.</p>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="program in programs"
                    :key="program.id"
                    :href="`/catalog/programs/${program.id}`"
                    class="group overflow-hidden rounded-2xl border border-gray-200 bg-white transition hover:shadow-lg dark:border-gray-700 dark:bg-gray-900"
                >
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                        <GraduationCap class="mb-2 size-8 text-white" />
                        <h3 class="text-xl font-bold text-white uppercase">{{ program.name }}</h3>
                        <p v-if="program.description" class="mt-1 text-sm text-indigo-100">{{ program.description }}</p>
                    </div>
                    <div class="p-6">
                        <h4 class="mb-3 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Jenis Tes Tersedia</h4>
                        <ul class="space-y-2">
                            <li v-for="tt in program.test_types" :key="tt.id" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <CheckCircle class="size-4 shrink-0 text-green-500" />
                                {{ tt.name }}
                                <span class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-500 dark:bg-gray-800 dark:text-gray-400">{{ tt.engine_type }}</span>
                            </li>
                        </ul>
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
