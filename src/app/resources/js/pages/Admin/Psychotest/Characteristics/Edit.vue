<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import InputError from '@/components/InputError.vue';
import type { BreadcrumbItem } from '@/types';
import type { PsychotestAspect, PsychotestCharacteristic, PsychotestCharacteristicScore } from '@/types/models';
import { Plus, Trash2 } from 'lucide-vue-next';

const props = defineProps<{ aspect: PsychotestAspect; characteristic: PsychotestCharacteristic }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Psychotest', href: '#' },
    { title: props.aspect.name, href: `/admin/psychotest/aspects/${props.aspect.id}` },
    { title: 'Edit Characteristic', href: '#' },
];

const form = useForm({
    code: props.characteristic.code,
    name: props.characteristic.name,
    description: props.characteristic.description ?? '',
    sort_order: props.characteristic.sort_order,
    scores: (props.characteristic.scores ?? []).map(s => ({ ...s })) as PsychotestCharacteristicScore[],
});

function addScore() {
    form.scores.push({ id: '', psychotest_characteristic_id: props.characteristic.id, min_score: 0, max_score: 0, level: '', interpretation: '' });
}
function removeScore(i: number) {
    form.scores.splice(i, 1);
}

function submit() {
    form.put(`/admin/psychotest/aspects/${props.aspect.id}/characteristics/${props.characteristic.id}`);
}
</script>

<template>
    <Head title="Edit Characteristic" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <Card class="max-w-3xl">
            <CardHeader><CardTitle>Edit Characteristic</CardTitle></CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label>Code</Label>
                            <Input v-model="form.code" />
                            <InputError :message="form.errors.code" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Sort Order</Label>
                            <Input type="number" v-model.number="form.sort_order" />
                        </div>
                    </div>
                    <div class="grid gap-2">
                        <Label>Name</Label>
                        <Input v-model="form.name" />
                        <InputError :message="form.errors.name" />
                    </div>
                    <div class="grid gap-2">
                        <Label>Description</Label>
                        <Textarea v-model="form.description" rows="3" />
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <Label class="text-base">Score Levels</Label>
                            <Button type="button" variant="outline" size="sm" @click="addScore"><Plus class="mr-1 h-3 w-3" />Add Level</Button>
                        </div>
                        <Table v-if="form.scores.length">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Level</TableHead>
                                    <TableHead>Min</TableHead>
                                    <TableHead>Max</TableHead>
                                    <TableHead>Interpretation</TableHead>
                                    <TableHead class="w-12" />
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="(s, i) in form.scores" :key="i">
                                    <TableCell><Input v-model="s.level" class="w-24" /></TableCell>
                                    <TableCell><Input type="number" v-model.number="s.min_score" class="w-20" /></TableCell>
                                    <TableCell><Input type="number" v-model.number="s.max_score" class="w-20" /></TableCell>
                                    <TableCell><Input v-model="s.interpretation" /></TableCell>
                                    <TableCell>
                                        <Button type="button" variant="ghost" size="icon" @click="removeScore(i)"><Trash2 class="h-4 w-4" /></Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div class="flex gap-2">
                        <Button type="submit" :disabled="form.processing">Save Changes</Button>
                        <Button type="button" variant="outline" @click="$inertia.visit(`/admin/psychotest/aspects/${aspect.id}/characteristics`)">Cancel</Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </AdminLayout>
</template>
