<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
import type { BreadcrumbItem } from '@/types';
import type { PsychotestForm, PsychotestAspect } from '@/types/models';
import { Plus, Trash2 } from 'lucide-vue-next';

const props = defineProps<{ form: PsychotestForm; aspects: PsychotestAspect[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Psychotest', href: '#' },
    { title: props.form.name, href: `/admin/psychotest/forms/${props.form.id}` },
    { title: 'New Question', href: '#' },
];

type OptionEntry = {
    label: string; statement: string; sort_order: number;
    mappings: { psychotest_aspect_id: string; psychotest_characteristic_id: string; weight: number }[];
};

const qForm = useForm({
    question_number: 1,
    is_active: true,
    options: [{ label: 'A', statement: '', sort_order: 1, mappings: [] }] as OptionEntry[],
});

function addOption() {
    const next = String.fromCharCode(65 + qForm.options.length);
    qForm.options.push({ label: next, statement: '', sort_order: qForm.options.length + 1, mappings: [] });
}
function removeOption(i: number) { qForm.options.splice(i, 1); }
function addMapping(opt: OptionEntry) {
    opt.mappings.push({ psychotest_aspect_id: '', psychotest_characteristic_id: '', weight: 1 });
}
function removeMapping(opt: OptionEntry, i: number) { opt.mappings.splice(i, 1); }

function getCharacteristics(aspectId: string) {
    return props.aspects.find(a => a.id === aspectId)?.characteristics ?? [];
}

function submit() {
    qForm.post(`/admin/psychotest/forms/${props.form.id}/questions`);
}
</script>

<template>
    <Head title="Create Question" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <Card class="max-w-4xl">
            <CardHeader><CardTitle>Create Question</CardTitle></CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="flex gap-4 items-end">
                        <div class="grid gap-2 w-32">
                            <Label>Number</Label>
                            <Input type="number" v-model.number="qForm.question_number" />
                            <InputError :message="qForm.errors.question_number" />
                        </div>
                        <div class="flex items-center gap-2">
                            <Switch :model-value="qForm.is_active" @update:model-value="qForm.is_active = $event" />
                            <Label>Active</Label>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <Label class="text-base">Options</Label>
                            <Button type="button" variant="outline" size="sm" @click="addOption"><Plus class="mr-1 h-3 w-3" />Add Option</Button>
                        </div>

                        <div v-for="(opt, oi) in qForm.options" :key="oi" class="border rounded-lg p-4 space-y-3">
                            <div class="flex gap-3 items-end">
                                <div class="grid gap-1 w-20">
                                    <Label class="text-xs">Label</Label>
                                    <Input v-model="opt.label" />
                                </div>
                                <div class="grid gap-1 flex-1">
                                    <Label class="text-xs">Statement</Label>
                                    <Input v-model="opt.statement" placeholder="Option statement" />
                                </div>
                                <div class="grid gap-1 w-20">
                                    <Label class="text-xs">Order</Label>
                                    <Input type="number" v-model.number="opt.sort_order" />
                                </div>
                                <Button type="button" variant="ghost" size="icon" @click="removeOption(oi)"><Trash2 class="h-4 w-4" /></Button>
                            </div>

                            <div class="ml-4 space-y-2">
                                <div class="flex items-center justify-between">
                                    <Label class="text-xs text-muted-foreground">Characteristic Mappings</Label>
                                    <Button type="button" variant="ghost" size="sm" @click="addMapping(opt)"><Plus class="mr-1 h-3 w-3" />Map</Button>
                                </div>
                                <div v-for="(m, mi) in opt.mappings" :key="mi" class="flex gap-2 items-end">
                                    <Select v-model="m.psychotest_aspect_id" @update:model-value="m.psychotest_characteristic_id = ''">
                                        <SelectTrigger class="w-36"><SelectValue placeholder="Aspect" /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="a in aspects" :key="a.id" :value="a.id">{{ a.code }}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <Select v-model="m.psychotest_characteristic_id">
                                        <SelectTrigger class="w-40"><SelectValue placeholder="Characteristic" /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="c in getCharacteristics(m.psychotest_aspect_id)" :key="c.id" :value="c.id">{{ c.code }}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <div class="grid gap-1 w-20">
                                        <Input type="number" v-model.number="m.weight" placeholder="Weight" />
                                    </div>
                                    <Button type="button" variant="ghost" size="icon" @click="removeMapping(opt, mi)"><Trash2 class="h-4 w-4" /></Button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <Button type="submit" :disabled="qForm.processing">Create</Button>
                        <Button type="button" variant="outline" @click="$inertia.visit(`/admin/psychotest/forms/${form.id}/questions`)">Cancel</Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </AdminLayout>
</template>
