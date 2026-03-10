<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, Clock, FileText } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { TestSummary } from '@/types/test';

defineProps<{
    tests: TestSummary[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Daftar Tes', href: '/tests' },
];
</script>

<template>
    <Head title="Daftar Tes" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Tes Tersedia</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Pilih tes yang ingin Anda kerjakan.
                </p>
            </div>

            <div v-if="tests.length === 0" class="rounded-xl border border-gray-200 bg-white p-12 text-center dark:border-gray-700 dark:bg-gray-900">
                <BookOpen class="mx-auto size-12 text-gray-300 dark:text-gray-600" />
                <p class="mt-4 text-gray-500 dark:text-gray-400">Belum ada tes yang tersedia saat ini.</p>
            </div>

            <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="test in tests"
                    :key="test.id"
                    :href="`/tests/${test.slug}`"
                    class="group rounded-xl border border-gray-200 bg-white p-6 transition-all hover:border-indigo-300 hover:shadow-md dark:border-gray-700 dark:bg-gray-900 dark:hover:border-indigo-700"
                >
                    <div class="mb-3 flex items-start justify-between">
                        <span class="rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-400">
                            {{ test.test_type.name }}
                        </span>
                        <span
                            class="rounded-full px-2 py-0.5 text-xs font-medium"
                            :class="{
                                'bg-green-100 text-green-700 dark:bg-green-950 dark:text-green-400': test.visibility === 'free',
                                'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400': test.visibility === 'premium',
                            }"
                        >
                            {{ test.visibility === 'free' ? 'Gratis' : 'Premium' }}
                        </span>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-900 group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-400">
                        {{ test.title }}
                    </h3>
                    <p v-if="test.description" class="mb-4 line-clamp-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ test.description }}
                    </p>
                    <div class="flex items-center gap-4 text-xs text-gray-400 dark:text-gray-500">
                        <span v-if="test.duration_minutes" class="flex items-center gap-1">
                            <Clock class="size-3.5" />
                            {{ test.duration_minutes }} menit
                        </span>
                        <span v-if="test.total_questions" class="flex items-center gap-1">
                            <FileText class="size-3.5" />
                            {{ test.total_questions }} soal
                        </span>
                        <span class="flex items-center gap-1">
                            {{ test.program.name }}
                        </span>
                    </div>
                    <div v-if="test.user_attempts_count" class="mt-3 text-xs text-gray-400 dark:text-gray-500">
                        Sudah dikerjakan {{ test.user_attempts_count }}x
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
