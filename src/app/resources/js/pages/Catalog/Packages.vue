<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Package } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface TestType {
    name: string;
    engine_type: string;
}

interface Test {
    id: string;
    title: string;
    test_type: TestType;
}

interface TestPackage {
    id: string;
    title: string;
    description: string | null;
    is_free: boolean;
    is_published: boolean;
    tests: Test[];
}

const props = defineProps<{
    packages: TestPackage[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Paket Tes', href: '/catalog/packages' },
];
</script>

<template>
    <Head title="Paket Tes" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Paket Tes</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Daftar paket tes yang tersedia untuk dikerjakan.</p>
            </div>

            <div v-if="packages.length === 0" class="rounded-xl border border-gray-200 bg-white p-12 text-center dark:border-gray-700 dark:bg-gray-900">
                <Package class="mx-auto mb-4 size-12 text-gray-300 dark:text-gray-600" />
                <p class="text-gray-500 dark:text-gray-400">Belum ada paket tes yang tersedia.</p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="pkg in packages" :key="pkg.id" class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-2">
                            <Package class="size-5 text-indigo-500" />
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ pkg.title }}</h3>
                        </div>
                        <span v-if="pkg.is_free" class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900 dark:text-green-300">Gratis</span>
                        <span v-else class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">Premium</span>
                    </div>
                    <p v-if="pkg.description" class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ pkg.description }}</p>
                    <div v-if="pkg.tests.length > 0" class="mt-4">
                        <p class="mb-2 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Tes Termasuk:</p>
                        <div class="flex flex-wrap gap-1">
                            <span v-for="test in pkg.tests" :key="test.id" class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                {{ test.title }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
