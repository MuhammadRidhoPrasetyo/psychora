<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { login, register } from '@/routes';
import { dashboard } from '@/routes/user';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import {
    Brain,
    CheckCircle,
    ChevronRight,
    ClipboardList,
    GraduationCap,
    LayoutDashboard,
    LineChart,
    Shield,
    Star,
    Timer,
    Users,
    Zap,
} from 'lucide-vue-next';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

const features = [
    {
        icon: Brain,
        title: 'Tes Psikologi Lengkap',
        description: 'DISC, IST, Kraepelin, dan berbagai tes psikologi standar untuk seleksi CPNS & BUMN.',
    },
    {
        icon: ClipboardList,
        title: 'Latihan Soal CPNS',
        description: 'Ribuan soal TKD (TWK, TIU, TKP) dengan pembahasan lengkap dan update terbaru.',
    },
    {
        icon: LineChart,
        title: 'Analisis Hasil Detail',
        description: 'Laporan hasil tes komprehensif dengan grafik dan rekomendasi peningkatan.',
    },
    {
        icon: Timer,
        title: 'Simulasi Ujian Realistis',
        description: 'Waktu pengerjaan sesuai ujian sesungguhnya dengan sistem otomatis.',
    },
    {
        icon: LayoutDashboard,
        title: 'Dashboard Personal',
        description: 'Pantau perkembangan belajar dan riwayat tes dalam satu dashboard.',
    },
    {
        icon: Shield,
        title: 'Materi Terverifikasi',
        description: 'Soal dan materi disusun oleh tim ahli berpengalaman di bidang seleksi.',
    },
];

const programs = [
    {
        badge: 'CPNS',
        title: 'Paket Latihan CPNS',
        description: 'Persiapan lengkap seleksi CPNS dengan soal TKD (TWK, TIU, TKP) dan pembahasan.',
        features: ['Soal TWK, TIU, TKP', 'Try Out berkala', 'Pembahasan detail', 'Simulasi CAT BKN'],
    },
    {
        badge: 'BUMN',
        title: 'Paket Tes Psikologi BUMN',
        description: 'Latihan tes psikologi yang umum digunakan dalam seleksi rekrutmen BUMN.',
        features: ['Tes DISC Personality', 'Tes IST (Intelligenz Struktur Test)', 'Tes Kraepelin', 'Tes Psikologi Lainnya'],
    },
    {
        badge: 'Bundling',
        title: 'Paket Lengkap CPNS + BUMN',
        description: 'Akses semua fitur dalam satu paket hemat untuk persiapan CPNS dan BUMN.',
        features: ['Semua soal CPNS', 'Semua tes psikologi', 'Akses premium', 'Prioritas support'],
    },
];

const stats = [
    { value: '10.000+', label: 'Soal Latihan' },
    { value: '5.000+', label: 'Pengguna Aktif' },
    { value: '95%', label: 'Tingkat Kepuasan' },
    { value: '24/7', label: 'Akses Platform' },
];

const testimonials = [
    {
        name: 'Rina Wulandari',
        role: 'Lolos CPNS 2025',
        content: 'Psychora sangat membantu persiapan CPNS saya. Soal-soalnya mirip dengan ujian asli dan pembahasannya mudah dipahami.',
        rating: 5,
    },
    {
        name: 'Ahmad Fauzi',
        role: 'Lolos Seleksi BUMN',
        content: 'Tes psikologi di Psychora sangat lengkap. DISC dan IST-nya membantu saya memahami profil kepribadian sebelum interview.',
        rating: 5,
    },
    {
        name: 'Dewi Lestari',
        role: 'Lolos CPNS 2025',
        content: 'Dashboard analisis hasilnya keren banget! Bisa tahu kelemahan di mana dan fokus perbaiki dari situ.',
        rating: 5,
    },
];

const faqs = [
    {
        question: 'Apa itu Psychora?',
        answer: 'Psychora adalah platform latihan tes online untuk persiapan seleksi CPNS dan BUMN. Kami menyediakan soal-soal TKD, tes psikologi (DISC, IST, Kraepelin), dan analisis hasil yang komprehensif.',
    },
    {
        question: 'Jenis tes apa saja yang tersedia?',
        answer: 'Kami menyediakan tes TKD (TWK, TIU, TKP) untuk CPNS, serta tes psikologi seperti DISC, IST (Intelligenz Struktur Test), Kraepelin, dan berbagai tes psikologi lainnya untuk seleksi BUMN.',
    },
    {
        question: 'Bagaimana cara berlangganan?',
        answer: 'Cukup daftar akun gratis, pilih paket yang sesuai, lakukan pembayaran, dan langsung akses semua fitur premium. Kami menerima berbagai metode pembayaran.',
    },
    {
        question: 'Apakah soalnya diupdate?',
        answer: 'Ya, tim kami secara rutin memperbarui bank soal dan materi agar selalu relevan dengan format ujian terbaru.',
    },
];
</script>

<template>
    <Head title="Platform Latihan Tes CPNS & BUMN" />

    <div class="min-h-screen bg-background text-foreground">
        <!-- Navbar -->
        <header class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <Link href="/" class="flex items-center gap-2">
                    <div class="flex size-8 items-center justify-center rounded-md bg-primary text-primary-foreground">
                        <AppLogoIcon class="size-5 fill-current" />
                    </div>
                    <span class="text-lg font-bold">Psychora</span>
                </Link>

                <nav class="hidden items-center gap-6 text-sm md:flex">
                    <a href="#fitur" class="text-muted-foreground transition-colors hover:text-foreground">Fitur</a>
                    <a href="#program" class="text-muted-foreground transition-colors hover:text-foreground">Program</a>
                    <a href="#testimoni" class="text-muted-foreground transition-colors hover:text-foreground">Testimoni</a>
                    <a href="#faq" class="text-muted-foreground transition-colors hover:text-foreground">FAQ</a>
                </nav>

                <div class="flex items-center gap-3">
                    <template v-if="$page.props.auth.user">
                        <Button as-child>
                            <Link :href="dashboard()">
                                <LayoutDashboard class="size-4" />
                                Dashboard
                            </Link>
                        </Button>
                    </template>
                    <template v-else>
                        <Button variant="ghost" as-child>
                            <Link :href="login()">Masuk</Link>
                        </Button>
                        <Button v-if="canRegister" as-child>
                            <Link :href="register()">Daftar Gratis</Link>
                        </Button>
                    </template>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative overflow-hidden border-b">
            <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 sm:py-28 lg:px-8 lg:py-36">
                <div class="mx-auto max-w-3xl text-center">
                    <Badge variant="secondary" class="mb-4">
                        <Zap class="mr-1 size-3" />
                        Platform #1 Persiapan CPNS & BUMN
                    </Badge>
                    <h1 class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
                        Raih Karir Impian dengan
                        <span class="text-primary">Psychora</span>
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-muted-foreground">
                        Platform latihan tes online terlengkap untuk persiapan seleksi CPNS dan BUMN.
                        Soal TKD, tes psikologi DISC, IST, Kraepelin, dan analisis hasil yang komprehensif.
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-4">
                        <Button size="lg" as-child>
                            <Link :href="register()">
                                Mulai Latihan Gratis
                                <ChevronRight class="ml-1 size-4" />
                            </Link>
                        </Button>
                        <Button size="lg" variant="outline" as-child>
                            <a href="#program">Lihat Program</a>
                        </Button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="border-b bg-muted/50">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
                    <div v-for="stat in stats" :key="stat.label" class="text-center">
                        <div class="text-3xl font-bold text-primary">{{ stat.value }}</div>
                        <div class="mt-1 text-sm text-muted-foreground">{{ stat.label }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="fitur" class="border-b">
            <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <Badge variant="outline" class="mb-4">Fitur Unggulan</Badge>
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        Semua yang Kamu Butuhkan untuk Lolos Seleksi
                    </h2>
                    <p class="mt-4 text-lg text-muted-foreground">
                        Fitur lengkap dan terintegrasi untuk memaksimalkan persiapan tes kamu.
                    </p>
                </div>
                <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Card v-for="feature in features" :key="feature.title">
                        <CardHeader>
                            <div class="mb-2 flex size-10 items-center justify-center rounded-lg bg-primary/10">
                                <component :is="feature.icon" class="size-5 text-primary" />
                            </div>
                            <CardTitle class="text-lg">{{ feature.title }}</CardTitle>
                            <CardDescription>{{ feature.description }}</CardDescription>
                        </CardHeader>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Programs Section -->
        <section id="program" class="border-b bg-muted/50">
            <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <Badge variant="outline" class="mb-4">Program Latihan</Badge>
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        Pilih Paket Sesuai Kebutuhanmu
                    </h2>
                    <p class="mt-4 text-lg text-muted-foreground">
                        Berbagai pilihan paket yang dirancang untuk membantu kamu lolos seleksi.
                    </p>
                </div>
                <div class="mt-16 grid gap-8 lg:grid-cols-3">
                    <Card v-for="program in programs" :key="program.title" class="relative flex flex-col">
                        <CardHeader>
                            <Badge class="w-fit" :variant="program.badge === 'Bundling' ? 'default' : 'secondary'">
                                {{ program.badge }}
                            </Badge>
                            <CardTitle class="text-xl">{{ program.title }}</CardTitle>
                            <CardDescription>{{ program.description }}</CardDescription>
                        </CardHeader>
                        <CardContent class="flex-1">
                            <ul class="space-y-3">
                                <li v-for="item in program.features" :key="item" class="flex items-start gap-2">
                                    <CheckCircle class="mt-0.5 size-4 shrink-0 text-primary" />
                                    <span class="text-sm">{{ item }}</span>
                                </li>
                            </ul>
                        </CardContent>
                        <div class="p-6 pt-0">
                            <Button class="w-full" :variant="program.badge === 'Bundling' ? 'default' : 'outline'" as-child>
                                <Link :href="register()">
                                    Pilih Paket
                                    <ChevronRight class="ml-1 size-4" />
                                </Link>
                            </Button>
                        </div>
                    </Card>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="border-b">
            <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <Badge variant="outline" class="mb-4">Cara Kerja</Badge>
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        Mulai dalam 3 Langkah Mudah
                    </h2>
                </div>
                <div class="mt-16 grid gap-8 md:grid-cols-3">
                    <div class="text-center">
                        <div class="mx-auto flex size-14 items-center justify-center rounded-full bg-primary text-primary-foreground text-xl font-bold">
                            1
                        </div>
                        <h3 class="mt-4 text-lg font-semibold">Daftar Akun</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Buat akun gratis hanya dengan email. Proses cepat dan mudah.
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto flex size-14 items-center justify-center rounded-full bg-primary text-primary-foreground text-xl font-bold">
                            2
                        </div>
                        <h3 class="mt-4 text-lg font-semibold">Pilih Paket</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Pilih paket yang sesuai kebutuhan. CPNS, BUMN, atau bundling.
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="mx-auto flex size-14 items-center justify-center rounded-full bg-primary text-primary-foreground text-xl font-bold">
                            3
                        </div>
                        <h3 class="mt-4 text-lg font-semibold">Mulai Latihan</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Kerjakan tes, lihat hasil analisis, dan tingkatkan kemampuanmu.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section id="testimoni" class="border-b bg-muted/50">
            <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <Badge variant="outline" class="mb-4">Testimoni</Badge>
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        Dipercaya oleh Ribuan Peserta
                    </h2>
                    <p class="mt-4 text-lg text-muted-foreground">
                        Cerita sukses dari mereka yang telah menggunakan Psychora.
                    </p>
                </div>
                <div class="mt-16 grid gap-8 md:grid-cols-3">
                    <Card v-for="testimonial in testimonials" :key="testimonial.name">
                        <CardHeader>
                            <div class="flex gap-1">
                                <Star v-for="i in testimonial.rating" :key="i" class="size-4 fill-yellow-400 text-yellow-400" />
                            </div>
                            <CardDescription class="text-sm leading-relaxed text-foreground">
                                "{{ testimonial.content }}"
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="flex items-center gap-3">
                                <div class="flex size-10 items-center justify-center rounded-full bg-primary/10">
                                    <Users class="size-5 text-primary" />
                                </div>
                                <div>
                                    <div class="text-sm font-semibold">{{ testimonial.name }}</div>
                                    <div class="text-xs text-muted-foreground">{{ testimonial.role }}</div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="border-b">
            <div class="mx-auto max-w-3xl px-4 py-20 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <Badge variant="outline" class="mb-4">FAQ</Badge>
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        Pertanyaan yang Sering Diajukan
                    </h2>
                </div>
                <div class="mt-12 space-y-4">
                    <Card v-for="faq in faqs" :key="faq.question">
                        <CardHeader>
                            <CardTitle class="text-base">{{ faq.question }}</CardTitle>
                            <CardDescription>{{ faq.answer }}</CardDescription>
                        </CardHeader>
                    </Card>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="border-b">
            <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <GraduationCap class="mx-auto size-12 text-primary" />
                    <h2 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">
                        Siap Meraih Karir Impianmu?
                    </h2>
                    <p class="mt-4 text-lg text-muted-foreground">
                        Bergabung dengan ribuan peserta yang telah berhasil lolos seleksi CPNS dan BUMN bersama Psychora.
                    </p>
                    <div class="mt-8 flex items-center justify-center gap-4">
                        <Button size="lg" as-child>
                            <Link :href="register()">
                                Daftar Sekarang — Gratis
                                <ChevronRight class="ml-1 size-4" />
                            </Link>
                        </Button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-muted/50">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="grid gap-8 md:grid-cols-4">
                    <div class="md:col-span-2">
                        <div class="flex items-center gap-2">
                            <div class="flex size-8 items-center justify-center rounded-md bg-primary text-primary-foreground">
                                <AppLogoIcon class="size-5 fill-current" />
                            </div>
                            <span class="text-lg font-bold">Psychora</span>
                        </div>
                        <p class="mt-4 max-w-md text-sm text-muted-foreground">
                            Platform latihan tes online terlengkap untuk persiapan seleksi CPNS dan BUMN.
                            Raih karir impianmu bersama Psychora.
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold">Platform</h3>
                        <ul class="mt-4 space-y-2">
                            <li><a href="#fitur" class="text-sm text-muted-foreground hover:text-foreground">Fitur</a></li>
                            <li><a href="#program" class="text-sm text-muted-foreground hover:text-foreground">Program</a></li>
                            <li><a href="#testimoni" class="text-sm text-muted-foreground hover:text-foreground">Testimoni</a></li>
                            <li><a href="#faq" class="text-sm text-muted-foreground hover:text-foreground">FAQ</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold">Akun</h3>
                        <ul class="mt-4 space-y-2">
                            <li>
                                <Link :href="login()" class="text-sm text-muted-foreground hover:text-foreground">Masuk</Link>
                            </li>
                            <li>
                                <Link :href="register()" class="text-sm text-muted-foreground hover:text-foreground">Daftar</Link>
                            </li>
                        </ul>
                    </div>
                </div>
                <Separator class="my-8" />
                <div class="text-center text-sm text-muted-foreground">
                    &copy; {{ new Date().getFullYear() }} Psychora. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</template>
