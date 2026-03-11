<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { BreadcrumbItem } from '@/types';
import type { SubscriptionPlan, Subscription } from '@/types/models';
import { Check } from 'lucide-vue-next';

const props = defineProps<{ plans: SubscriptionPlan[]; activeSubscription: Subscription | null }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Subscription Plans', href: '#' },
];

function formatPrice(price: number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(price);
}
</script>

<template>
    <Head title="Subscription Plans" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <div class="text-center">
                <h1 class="text-2xl font-bold">Choose Your Plan</h1>
                <p class="text-muted-foreground mt-1">Get access to all test programs and features</p>
            </div>

            <div v-if="activeSubscription" class="text-center">
                <Badge variant="default" class="text-sm">You have an active subscription</Badge>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
                <Card v-for="plan in plans" :key="plan.id" class="flex flex-col">
                    <CardHeader>
                        <CardTitle>{{ plan.name }}</CardTitle>
                        <CardDescription>{{ plan.description }}</CardDescription>
                        <div class="mt-2">
                            <span class="text-3xl font-bold">{{ formatPrice(plan.price) }}</span>
                            <span class="text-muted-foreground">/ {{ plan.duration_days }} days</span>
                        </div>
                    </CardHeader>
                    <CardContent class="flex-1">
                        <ul v-if="plan.entitlements?.length" class="space-y-2">
                            <li v-for="e in plan.entitlements" :key="e.id" class="flex items-center gap-2 text-sm">
                                <Check class="h-4 w-4 text-green-500 shrink-0" />
                                <span>{{ e.program?.name ?? 'All Programs' }}</span>
                            </li>
                        </ul>
                    </CardContent>
                    <CardFooter>
                        <Button class="w-full" :disabled="!!activeSubscription" @click="router.get(`/subscriptions/checkout/${plan.id}`)">
                            {{ activeSubscription ? 'Already Subscribed' : 'Subscribe' }}
                        </Button>
                    </CardFooter>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
