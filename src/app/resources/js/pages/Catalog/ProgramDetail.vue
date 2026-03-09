<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle, Package } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface TestType {
    id: string;
    name: string;
    slug: string;
    engine_type: string;
    description: string | null;
}

interface TestPackage {
    id: string;
    title: string;
    description: string | null;
    is_free: boolean;
}

interface Program {
    id: string;
    name: string;
    slug: string;
    description: string | null;
    test_types: TestType[];
    test_packages: TestPackage[];
}

const props = defineProps<{
    program: Program;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Program', href: '/catalog/programs' },
    { title: props.program.name, href: '#' },
];
</script>

<template>
    <Head :title="program.name" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <div class="flex items-center gap-4">
                <Link href="/catalog/programs" class="rounded-lg border border-gray-200 p-2 transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                    <ArrowLeft class="size-5 text-gray-500" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 uppercase dark:text-white">{{ program.name }}</h1>
                    <p v-if="program.description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ program.description }}</p>
                </div>
            </div>

            <!-- Test Types -->
            <div>
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Jenis Tes</h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="tt in program.test_types" :key="tt.id" class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                        <div class="flex items-center gap-2">
                            <CheckCircle class="size-5 text-green-500" />
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ tt.name }}</h3>
                        </div>
                        <p v-if="tt.description" class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ tt.description }}</p>
                        <span class="mt-2 inline-block rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400">{{ tt.engine_type }}</span>
                    </div>
                </div>
            </div>

            <!-- Test Packages -->
            <div v-if="program.test_packages && program.test_packages.length > 0">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Paket Tes Tersedia</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div v-for="pkg in program.test_packages" :key="pkg.id" class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                        <div class="flex items-center gap-2">
                            <Package class="size-5 text-indigo-500" />
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ pkg.title }}</h3>
                            <span v-if="pkg.is_free" class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900 dark:text-green-300">Gratis</span>
                        </div>
                        <p v-if="pkg.description" class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ pkg.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
