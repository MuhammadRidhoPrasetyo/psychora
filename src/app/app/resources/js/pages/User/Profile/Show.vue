<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import type { BreadcrumbItem } from '@/types';
import type { User } from '@/types/models';

const props = defineProps<{ user: User }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Profile', href: '#' },
];

const form = useForm({
    birth_date: props.user.profile?.birth_date ?? '',
    gender: props.user.profile?.gender ?? '',
    phone: props.user.profile?.phone ?? '',
    province: props.user.profile?.province ?? '',
    city: props.user.profile?.city ?? '',
    education_level: props.user.profile?.education_level ?? '',
    target_program: props.user.profile?.target_program ?? '',
});

function submit() {
    form.put('/profile');
}
</script>

<template>
    <Head title="Profile" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>Account Information</CardTitle>
                    <CardDescription>Your basic account details.</CardDescription>
                </CardHeader>
                <CardContent class="space-y-2">
                    <div>
                        <Label>Name</Label>
                        <p class="text-sm font-medium">{{ user.name }}</p>
                    </div>
                    <div>
                        <Label>Email</Label>
                        <p class="text-sm font-medium">{{ user.email }}</p>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Profile Details</CardTitle>
                    <CardDescription>Update your personal information.</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="birth_date">Birth Date</Label>
                                <Input id="birth_date" type="date" v-model="form.birth_date" />
                                <InputError :message="form.errors.birth_date" />
                            </div>
                            <div class="space-y-2">
                                <Label for="gender">Gender</Label>
                                <Select v-model="form.gender">
                                    <SelectTrigger><SelectValue placeholder="Select gender" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="male">Male</SelectItem>
                                        <SelectItem value="female">Female</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.gender" />
                            </div>
                        </div>
                        <div class="space-y-2">
                            <Label for="phone">Phone</Label>
                            <Input id="phone" v-model="form.phone" placeholder="08xxxxxxxxxx" />
                            <InputError :message="form.errors.phone" />
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="province">Province</Label>
                                <Input id="province" v-model="form.province" />
                                <InputError :message="form.errors.province" />
                            </div>
                            <div class="space-y-2">
                                <Label for="city">City</Label>
                                <Input id="city" v-model="form.city" />
                                <InputError :message="form.errors.city" />
                            </div>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="education_level">Education Level</Label>
                                <Select v-model="form.education_level">
                                    <SelectTrigger><SelectValue placeholder="Select level" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="SMA">SMA/SMK</SelectItem>
                                        <SelectItem value="D3">D3</SelectItem>
                                        <SelectItem value="S1">S1</SelectItem>
                                        <SelectItem value="S2">S2</SelectItem>
                                        <SelectItem value="S3">S3</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.education_level" />
                            </div>
                            <div class="space-y-2">
                                <Label for="target_program">Target Program</Label>
                                <Input id="target_program" v-model="form.target_program" placeholder="e.g. CPNS, BUMN" />
                                <InputError :message="form.errors.target_program" />
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <Button type="submit" :disabled="form.processing">Save Changes</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
