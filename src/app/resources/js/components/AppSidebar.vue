<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    LayoutGrid, Layers, CreditCard, History, Bookmark, ClipboardList,
    Shield, HelpCircle, Settings,
} from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarGroup,
    SidebarGroupLabel,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

const page = usePage();
const { isCurrentUrl } = useCurrentUrl();

const userRoles = computed(() => {
    const roles = (page.props.auth as any)?.user?.roles;
    return Array.isArray(roles) ? roles.map((r: any) => r.name) : [];
});

const isAdmin = computed(() =>
    userRoles.value.includes('super_admin') || userRoles.value.includes('admin'),
);

const mainNavItems: NavItem[] = [
    { title: 'Dashboard', href: '/dashboard', icon: LayoutGrid },
    { title: 'Program', href: '/programs', icon: Layers },
    { title: 'Paket Langganan', href: '/subscriptions/plans', icon: CreditCard },
];

const testNavItems: NavItem[] = [
    { title: 'Riwayat Tes', href: '/history', icon: History },
    { title: 'Bookmark', href: '/bookmarks', icon: Bookmark },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/dashboard">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />

            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel>Tes & Riwayat</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in testNavItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="isCurrentUrl(item.href)" :tooltip="item.title">
                            <Link :href="item.href">
                                <component :is="item.icon" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <SidebarGroup v-if="isAdmin" class="px-2 py-0">
                <SidebarGroupLabel>Administrasi</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton as-child :is-active="isCurrentUrl('/admin')" tooltip="Admin Panel">
                            <Link href="/admin">
                                <Shield />
                                <span>Admin Panel</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
