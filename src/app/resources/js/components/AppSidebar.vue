<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    CreditCard,
    FolderGit2,
    GraduationCap,
    LayoutGrid,
    ListChecks,
    Package,
    Settings,
    ShieldCheck,
    Users,
} from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const page = usePage();
const user = page.props.auth.user as any;
const roles = (user?.roles ?? []) as Array<{ name: string }>;
const isAdmin = roles.some((r: { name: string }) => r.name === 'super_admin' || r.name === 'admin');

const userNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Program',
        href: '/catalog/programs',
        icon: GraduationCap,
    },
    {
        title: 'Paket Tes',
        href: '/catalog/packages',
        icon: Package,
    },
    {
        title: 'Langganan',
        href: '/subscription/plans',
        icon: CreditCard,
    },
];

const adminNavItems: NavItem[] = [
    {
        title: 'Admin Dashboard',
        href: '/admin',
        icon: ShieldCheck,
    },
    {
        title: 'Pengguna',
        href: '/admin/users',
        icon: Users,
    },
    {
        title: 'Paket Langganan',
        href: '/admin/plans',
        icon: CreditCard,
    },
    {
        title: 'Program',
        href: '/admin/programs',
        icon: GraduationCap,
    },
    {
        title: 'Jenis Tes',
        href: '/admin/test-types',
        icon: ListChecks,
    },
    {
        title: 'Bank Tes',
        href: '/admin/tests',
        icon: BookOpen,
    },
    {
        title: 'Pembayaran',
        href: '/admin/payments',
        icon: CreditCard,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="userNavItems" />

            <template v-if="isAdmin">
                <SidebarGroup class="px-2 py-0">
                    <SidebarGroupLabel>Admin</SidebarGroupLabel>
                    <SidebarMenu>
                        <SidebarMenuItem v-for="item in adminNavItems" :key="item.title">
                            <SidebarMenuButton
                                as-child
                                :tooltip="item.title"
                            >
                                <Link :href="item.href">
                                    <component :is="item.icon" />
                                    <span>{{ item.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroup>
            </template>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
