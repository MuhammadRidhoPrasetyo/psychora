<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login, register } from '@/routes';
import {
    BookOpen,
    Brain,
    CheckCircle,
    ChevronRight,
    ClipboardCheck,
    GraduationCap,
    LayoutGrid,
    Shield,
    Star,
    Timer,
    Trophy,
    Users,
    Zap,
} from 'lucide-vue-next';

interface Plan {
    id: string;
    name: string;
    slug: string;
    description: string | null;
    price: number;
    duration_days: number;
    features: Record<string, unknown> | null;
    is_active: boolean;
}

interface TestType {
    id: string;
    name: string;
    slug: string;
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
    plans: Plan[];
    programs: Program[];
}>();

const formatPrice = (price: number) => {
    if (price === 0) return 'Gratis';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
};

const features = [
    {
        icon: ClipboardCheck,
        title: 'Simulasi Tes Realistis',
        description: 'Latihan dengan format dan waktu persis seperti ujian sesungguhnya.',
    },
    {
        icon: Brain,
        title: 'Psikotes Lengkap',
        description: 'DISC, IST, Kraepelin, dan Papikostick dalam satu platform.',
    },
    {
        icon: Timer,
        title: 'Timer & Auto-Submit',
        description: 'Pengerjaan terhitung waktu dan otomatis submit saat habis.',
    },
    {
        icon: Trophy,
        title: 'Skor & Pembahasan',
        description: 'Langsung lihat skor, ranking, dan pembahasan detail per soal.',
    },
    {
        icon: LayoutGrid,
        title: 'Bank Soal Terupdate',
        description: 'Ribuan soal yang terus diperbarui sesuai perkembangan terbaru.',
    },
    {
        icon: Zap,
        title: 'Akses Fleksibel',
        description: 'Latihan kapan saja, di mana saja, dari perangkat apapun.',
    },
];

const faqs = [
    {
        question: 'Apa itu Psychora?',
        answer: 'Psychora adalah platform SaaS latihan tes seleksi CPNS, BUMN, dan psikotes kerja. Kami menyediakan simulasi tes dengan format realistis untuk membantu Anda mempersiapkan diri.',
    },
    {
        question: 'Tes apa saja yang tersedia?',
        answer: 'Kami menyediakan TWK, TIU, TKP untuk CPNS, serta DISC, IST, Kraepelin, dan Papikostick untuk BUMN dan psikotes kerja.',
    },
    {
        question: 'Apakah ada paket gratis?',
        answer: 'Ya! Kami menyediakan paket gratis dengan akses terbatas agar Anda bisa mencoba platform terlebih dahulu.',
    },
    {
        question: 'Bagaimana cara berlangganan?',
        answer: 'Daftar akun, pilih paket yang sesuai, lakukan pembayaran, dan admin akan mengaktifkan subscription Anda.',
    },
    {
        question: 'Apakah soal selalu diperbarui?',
        answer: 'Ya, tim kami secara berkala memperbarui bank soal agar selalu sesuai dengan materi terbaru seleksi CPNS dan BUMN.',
    },
];
</script>

<template>
    <Head title="Platform Latihan Tes CPNS, BUMN & Psikotes">
        <meta
            name="description"
            content="Psychora - Platform SaaS latihan tes seleksi CPNS, BUMN, dan psikotes kerja. Simulasi realistis, pembahasan lengkap, dan skor instan."
        />
    </Head>

    <div class="min-h-screen bg-white dark:bg-gray-950">
        <!-- Navbar -->
        <nav class="sticky top-0 z-50 border-b border-gray-100 bg-white/80 backdrop-blur-lg dark:border-gray-800 dark:bg-gray-950/80">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-2">
                    <div class="flex size-9 items-center justify-center rounded-lg bg-indigo-600 text-white">
                        <Brain class="size-5" />
                    </div>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">Psychora</span>
                </div>
                <div class="hidden items-center gap-8 md:flex">
                    <a href="#features" class="text-sm font-medium text-gray-600 transition hover:text-indigo-600 dark:text-gray-300">Fitur</a>
                    <a href="#programs" class="text-sm font-medium text-gray-600 transition hover:text-indigo-600 dark:text-gray-300">Program</a>
                    <a href="#pricing" class="text-sm font-medium text-gray-600 transition hover:text-indigo-600 dark:text-gray-300">Harga</a>
                    <a href="#faq" class="text-sm font-medium text-gray-600 transition hover:text-indigo-600 dark:text-gray-300">FAQ</a>
                </div>
                <div class="flex items-center gap-3">
                    <template v-if="$page.props.auth.user">
                        <Link
                            :href="dashboard()"
                            class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                        >
                            Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="text-sm font-medium text-gray-700 transition hover:text-indigo-600 dark:text-gray-300"
                        >
                            Masuk
                        </Link>
                        <Link
                            :href="register()"
                            class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                        >
                            Daftar Gratis
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-950 dark:via-gray-900 dark:to-indigo-950"></div>
            <div class="relative mx-auto max-w-7xl px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-4 py-1.5 text-sm font-medium text-indigo-700 dark:border-indigo-800 dark:bg-indigo-950 dark:text-indigo-300">
                        <Star class="size-4" />
                        Platform #1 Latihan Tes Seleksi
                    </div>
                    <h1 class="mb-6 text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl dark:text-white">
                        Persiapan Terbaik untuk
                        <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            CPNS &amp; BUMN
                        </span>
                    </h1>
                    <p class="mb-10 text-lg leading-relaxed text-gray-600 sm:text-xl dark:text-gray-400">
                        Latihan simulasi tes dengan format realistis. TWK, TIU, TKP, DISC, IST,
                        Kraepelin, Papikostick — semua dalam satu platform.
                    </p>
                    <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <Link
                            :href="register()"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-8 py-4 text-base font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:bg-indigo-500 hover:shadow-xl"
                        >
                            Mulai Latihan Gratis
                            <ChevronRight class="size-5" />
                        </Link>
                        <a
                            href="#programs"
                            class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-8 py-4 text-base font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-750"
                        >
                            Lihat Program
                        </a>
                    </div>
                    <div class="mt-12 flex items-center justify-center gap-8 text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-2">
                            <Users class="size-4 text-indigo-500" />
                            <span>1000+ Pengguna</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <BookOpen class="size-4 text-indigo-500" />
                            <span>5000+ Soal</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Shield class="size-4 text-indigo-500" />
                            <span>7 Jenis Tes</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="border-t border-gray-100 py-24 dark:border-gray-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto mb-16 max-w-2xl text-center">
                    <h2 class="mb-4 text-3xl font-bold text-gray-900 sm:text-4xl dark:text-white">
                        Semua yang Anda Butuhkan
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        Fitur lengkap untuk membantu Anda lolos seleksi CPNS dan BUMN.
                    </p>
                </div>
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="feature in features"
                        :key="feature.title"
                        class="group rounded-2xl border border-gray-100 bg-white p-8 transition hover:border-indigo-200 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900 dark:hover:border-indigo-800"
                    >
                        <div class="mb-4 inline-flex rounded-xl bg-indigo-50 p-3 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400">
                            <component :is="feature.icon" class="size-6" />
                        </div>
                        <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">
                            {{ feature.title }}
                        </h3>
                        <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                            {{ feature.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Programs Section -->
        <section id="programs" class="border-t border-gray-100 bg-gray-50 py-24 dark:border-gray-800 dark:bg-gray-900">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto mb-16 max-w-2xl text-center">
                    <h2 class="mb-4 text-3xl font-bold text-gray-900 sm:text-4xl dark:text-white">
                        Pilih Program Persiapan
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        Pilih jalur belajar sesuai target seleksi Anda.
                    </p>
                </div>
                <div class="grid gap-8 md:grid-cols-3">
                    <div
                        v-for="program in programs"
                        :key="program.id"
                        class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:shadow-lg dark:border-gray-700 dark:bg-gray-800"
                    >
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                            <div class="mb-2 inline-flex rounded-lg bg-white/20 p-2">
                                <GraduationCap class="size-6 text-white" />
                            </div>
                            <h3 class="text-xl font-bold text-white uppercase">
                                {{ program.name }}
                            </h3>
                            <p v-if="program.description" class="mt-1 text-sm text-indigo-100">
                                {{ program.description }}
                            </p>
                        </div>
                        <div class="p-6">
                            <h4 class="mb-3 text-sm font-semibold text-gray-500 uppercase dark:text-gray-400">
                                Jenis Tes
                            </h4>
                            <ul class="space-y-2">
                                <li
                                    v-for="testType in program.test_types"
                                    :key="testType.id"
                                    class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"
                                >
                                    <CheckCircle class="size-4 shrink-0 text-green-500" />
                                    <span>{{ testType.name }}</span>
                                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                        {{ testType.engine_type }}
                                    </span>
                                </li>
                            </ul>
                            <Link
                                :href="register()"
                                class="mt-6 block rounded-lg border border-indigo-600 px-4 py-2.5 text-center text-sm font-semibold text-indigo-600 transition hover:bg-indigo-600 hover:text-white dark:border-indigo-400 dark:text-indigo-400 dark:hover:bg-indigo-600 dark:hover:text-white"
                            >
                                Mulai Belajar
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="border-t border-gray-100 py-24 dark:border-gray-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto mb-16 max-w-2xl text-center">
                    <h2 class="mb-4 text-3xl font-bold text-gray-900 sm:text-4xl dark:text-white">
                        Pilih Paket Berlangganan
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        Mulai dari gratis, upgrade kapan saja sesuai kebutuhan.
                    </p>
                </div>
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="plan in plans"
                        :key="plan.id"
                        class="relative overflow-hidden rounded-2xl border bg-white p-8 transition hover:shadow-lg dark:bg-gray-900"
                        :class="[
                            plan.slug === 'all-access'
                                ? 'border-indigo-600 shadow-lg shadow-indigo-500/10 dark:border-indigo-500'
                                : 'border-gray-200 dark:border-gray-700',
                        ]"
                    >
                        <div
                            v-if="plan.slug === 'all-access'"
                            class="absolute right-0 top-0 rounded-bl-lg bg-indigo-600 px-3 py-1 text-xs font-semibold text-white"
                        >
                            Populer
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ plan.name }}
                        </h3>
                        <p v-if="plan.description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ plan.description }}
                        </p>
                        <div class="mt-6">
                            <span class="text-4xl font-extrabold text-gray-900 dark:text-white">
                                {{ formatPrice(plan.price) }}
                            </span>
                            <span v-if="plan.price > 0" class="text-sm text-gray-500 dark:text-gray-400">
                                / {{ plan.duration_days }} hari
                            </span>
                        </div>
                        <ul v-if="plan.features" class="mt-8 space-y-3">
                            <li
                                v-for="(value, key) in plan.features"
                                :key="String(key)"
                                class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400"
                            >
                                <CheckCircle class="mt-0.5 size-4 shrink-0 text-green-500" />
                                <span>{{ key }}: {{ value }}</span>
                            </li>
                        </ul>
                        <Link
                            :href="register()"
                            class="mt-8 block rounded-lg px-4 py-3 text-center text-sm font-semibold transition"
                            :class="[
                                plan.slug === 'all-access'
                                    ? 'bg-indigo-600 text-white shadow-sm hover:bg-indigo-500'
                                    : 'border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800',
                            ]"
                        >
                            {{ plan.price === 0 ? 'Daftar Gratis' : 'Pilih Paket' }}
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="border-t border-gray-100 bg-gray-50 py-24 dark:border-gray-800 dark:bg-gray-900">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="mb-16 text-center">
                    <h2 class="mb-4 text-3xl font-bold text-gray-900 sm:text-4xl dark:text-white">
                        Pertanyaan Umum
                    </h2>
                </div>
                <div class="space-y-4">
                    <details
                        v-for="(faq, index) in faqs"
                        :key="index"
                        class="group rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800"
                    >
                        <summary class="flex cursor-pointer items-center justify-between px-6 py-5 text-left text-base font-semibold text-gray-900 dark:text-white">
                            {{ faq.question }}
                            <ChevronRight class="size-5 shrink-0 text-gray-400 transition group-open:rotate-90" />
                        </summary>
                        <div class="px-6 pb-5 text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                            {{ faq.answer }}
                        </div>
                    </details>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="border-t border-gray-100 dark:border-gray-800">
            <div class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
                <div class="rounded-3xl bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-16 text-center shadow-2xl sm:px-16">
                    <h2 class="mb-4 text-3xl font-bold text-white sm:text-4xl">
                        Siap Lolos Seleksi?
                    </h2>
                    <p class="mx-auto mb-8 max-w-xl text-lg text-indigo-100">
                        Bergabung sekarang dan mulai persiapan tes CPNS &amp; BUMN Anda.
                        Gratis untuk memulai!
                    </p>
                    <Link
                        :href="register()"
                        class="inline-flex items-center gap-2 rounded-xl bg-white px-8 py-4 text-base font-semibold text-indigo-600 shadow-lg transition hover:bg-gray-100"
                    >
                        Daftar Sekarang
                        <ChevronRight class="size-5" />
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-gray-100 bg-gray-900 py-12 dark:border-gray-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                    <div class="flex items-center gap-2">
                        <div class="flex size-8 items-center justify-center rounded-lg bg-indigo-600 text-white">
                            <Brain class="size-4" />
                        </div>
                        <span class="text-lg font-bold text-white">Psychora</span>
                    </div>
                    <p class="text-sm text-gray-400">
                        &copy; {{ new Date().getFullYear() }} Psychora. Platform latihan tes CPNS, BUMN &amp; Psikotes.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
