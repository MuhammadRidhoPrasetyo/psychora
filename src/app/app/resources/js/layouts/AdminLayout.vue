<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    BarChart3, BookOpen, CreditCard, FileText, Home, Layers, LayoutGrid,
    ListChecks, Package, Settings, Shield, Users, Activity, ClipboardList,
    Brain, Calculator, PenTool, Puzzle,
} from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar, SidebarContent, SidebarFooter, SidebarHeader,
    SidebarMenu, SidebarMenuButton, SidebarMenuItem,
    SidebarGroup, SidebarGroupLabel, SidebarInset, SidebarProvider, SidebarTrigger,
} from '@/components/ui/sidebar';
import { Separator } from '@/components/ui/separator';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { BreadcrumbItem } from '@/types';
import {
    Breadcrumb, BreadcrumbItem as BreadcrumbItemUI, BreadcrumbLink,
    BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const { isCurrentUrl } = useCurrentUrl();

const mainNav = [
    { title: 'Dashboard', href: '/admin', icon: Home },
    { title: 'Users', href: '/admin/users', icon: Users },
    { title: 'Programs', href: '/admin/programs', icon: Layers },
    { title: 'Test Types', href: '/admin/test-types', icon: ListChecks },
];

const billingNav = [
    { title: 'Subscription Plans', href: '/admin/subscription-plans', icon: CreditCard },
    { title: 'Subscriptions', href: '/admin/subscriptions', icon: FileText },
    { title: 'Payments', href: '/admin/payments', icon: BarChart3 },
];

const testNav = [
    { title: 'Test Packages', href: '/admin/test-packages', icon: Package },
    { title: 'Tests', href: '/admin/tests', icon: ClipboardList },
    { title: 'Test Attempts', href: '/admin/test-attempts', icon: Activity },
];

const specializedNav = [
    { title: 'CPNS', href: '/admin/cpns/blueprints', icon: Shield },
    { title: 'DISC', href: '/admin/disc/forms', icon: PenTool },
    { title: 'IST', href: '/admin/ist/forms', icon: Brain },
    { title: 'Kraepelin', href: '/admin/kraepelin/forms', icon: Calculator },
    { title: 'Psychotest', href: '/admin/psychotest/aspects', icon: Puzzle },
];

const systemNav = [
    { title: 'Activity Logs', href: '/admin/activity-logs', icon: Activity },
];
</script>

<template>
    <SidebarProvider>
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" as-child>
                            <Link href="/admin">
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <SidebarGroup>
                    <SidebarGroupLabel>Main</SidebarGroupLabel>
                    <SidebarMenu>
                        <SidebarMenuItem v-for="item in mainNav" :key="item.title">
                            <SidebarMenuButton as-child :is-active="isCurrentUrl(item.href)" :tooltip="item.title">
                                <Link :href="item.href">
                                    <component :is="item.icon" />
                                    <span>{{ item.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroup>

                <SidebarGroup>
                    <SidebarGroupLabel>Billing</SidebarGroupLabel>
                    <SidebarMenu>
                        <SidebarMenuItem v-for="item in billingNav" :key="item.title">
                            <SidebarMenuButton as-child :is-active="isCurrentUrl(item.href)" :tooltip="item.title">
                                <Link :href="item.href">
                                    <component :is="item.icon" />
                                    <span>{{ item.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroup>

                <SidebarGroup>
                    <SidebarGroupLabel>Test Management</SidebarGroupLabel>
                    <SidebarMenu>
                        <SidebarMenuItem v-for="item in testNav" :key="item.title">
                            <SidebarMenuButton as-child :is-active="isCurrentUrl(item.href)" :tooltip="item.title">
                                <Link :href="item.href">
                                    <component :is="item.icon" />
                                    <span>{{ item.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroup>

                <SidebarGroup>
                    <SidebarGroupLabel>Specialized Modules</SidebarGroupLabel>
                    <SidebarMenu>
                        <SidebarMenuItem v-for="item in specializedNav" :key="item.title">
                            <SidebarMenuButton as-child :is-active="isCurrentUrl(item.href)" :tooltip="item.title">
                                <Link :href="item.href">
                                    <component :is="item.icon" />
                                    <span>{{ item.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroup>

                <SidebarGroup>
                    <SidebarGroupLabel>System</SidebarGroupLabel>
                    <SidebarMenu>
                        <SidebarMenuItem v-for="item in systemNav" :key="item.title">
                            <SidebarMenuButton as-child :is-active="isCurrentUrl(item.href)" :tooltip="item.title">
                                <Link :href="item.href">
                                    <component :is="item.icon" />
                                    <span>{{ item.title }}</span>
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

        <SidebarInset>
            <header class="flex h-16 shrink-0 items-center gap-2 border-b px-4">
                <SidebarTrigger class="-ml-1" />
                <Separator orientation="vertical" class="mr-2 h-4!" />
                <Breadcrumb v-if="breadcrumbs.length > 0">
                    <BreadcrumbList>
                        <template v-for="(item, index) in breadcrumbs" :key="index">
                            <BreadcrumbItemUI>
                                <BreadcrumbLink v-if="index < breadcrumbs.length - 1" as-child>
                                    <Link :href="item.href">{{ item.title }}</Link>
                                </BreadcrumbLink>
                                <BreadcrumbPage v-else>{{ item.title }}</BreadcrumbPage>
                            </BreadcrumbItemUI>
                            <BreadcrumbSeparator v-if="index < breadcrumbs.length - 1" />
                        </template>
                    </BreadcrumbList>
                </Breadcrumb>
            </header>

            <main class="flex-1 overflow-auto p-4">
                <slot />
            </main>
        </SidebarInset>
    </SidebarProvider>
</template>
