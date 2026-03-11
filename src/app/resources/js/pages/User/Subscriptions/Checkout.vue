<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import type { BreadcrumbItem } from '@/types';
import type { SubscriptionPlan } from '@/types/models';
import { Check } from 'lucide-vue-next';

const props = defineProps<{ plan: SubscriptionPlan }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Plans', href: '/subscriptions/plans' },
    { title: 'Checkout', href: '#' },
];

const form = useForm({ payment_method: '' });

function formatPrice(price: number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(price);
}

function submit() {
    form.post(`/subscriptions/subscribe/${props.plan.id}`);
}
</script>

<template>
    <Head title="Checkout" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-xl mx-auto space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>{{ plan.name }}</CardTitle>
                    <CardDescription>{{ plan.description }}</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="text-center py-4 border rounded-lg">
                        <span class="text-3xl font-bold">{{ formatPrice(plan.price) }}</span>
                        <span class="text-muted-foreground"> / {{ plan.duration_days }} days</span>
                    </div>

                    <ul v-if="plan.entitlements?.length" class="space-y-2">
                        <li v-for="e in plan.entitlements" :key="e.id" class="flex items-center gap-2 text-sm">
                            <Check class="h-4 w-4 text-green-500" />
                            <span>{{ e.program?.name ?? 'All Programs' }}</span>
                        </li>
                    </ul>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Payment Method</CardTitle></CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid gap-2">
                            <Label>Choose Payment Method</Label>
                            <Select v-model="form.payment_method">
                                <SelectTrigger><SelectValue placeholder="Select payment method" /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="bank_transfer">Bank Transfer</SelectItem>
                                    <SelectItem value="ewallet">E-Wallet</SelectItem>
                                    <SelectItem value="qris">QRIS</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.payment_method" />
                        </div>
                        <Button type="submit" class="w-full" size="lg" :disabled="form.processing || !form.payment_method">
                            Pay {{ formatPrice(plan.price) }}
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
