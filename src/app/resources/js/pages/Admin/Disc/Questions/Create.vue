<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
import type { BreadcrumbItem } from '@/types';
import type { DiscForm } from '@/types/models';

const props = defineProps<{ form: DiscForm }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'DISC', href: '#' },
    { title: props.form.name, href: `/admin/disc/forms/${props.form.id}` },
    { title: 'Add Question', href: '#' },
];

const discCodes = ['D', 'I', 'S', 'C', 'star'];

const formData = useForm({
    number: 0,
    options: Array.from({ length: 4 }, () => ({
        option_text: '',
        scorings: [
            { response_type: 'most', disc_code: 'D', score_value: 0 },
            { response_type: 'least', disc_code: 'D', score_value: 0 },
        ],
    })),
});

function submit() {
    formData.post(`/admin/disc/forms/${props.form.id}/questions`);
}
</script>

<template>
    <Head title="Add DISC Question" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl">
            <Card>
                <CardHeader><CardTitle>Add DISC Question</CardTitle></CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid gap-2 w-32">
                            <Label>Number</Label>
                            <Input type="number" v-model.number="formData.number" required />
                            <InputError :message="formData.errors.number" />
                        </div>

                        <div v-for="(opt, i) in formData.options" :key="i" class="border rounded-lg p-4 space-y-3">
                            <div class="grid gap-2">
                                <Label>Option {{ i + 1 }} Text</Label>
                                <Input v-model="opt.option_text" required />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div v-for="(s, si) in opt.scorings" :key="si" class="space-y-2 border p-2 rounded">
                                    <Label class="text-xs font-medium">{{ s.response_type }} scoring</Label>
                                    <div class="flex gap-2">
                                        <Select v-model="s.disc_code">
                                            <SelectTrigger class="w-20"><SelectValue /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="c in discCodes" :key="c" :value="c">{{ c }}</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <Input type="number" v-model.number="s.score_value" class="w-20" placeholder="Score" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <Button type="submit" :disabled="formData.processing">Create</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
