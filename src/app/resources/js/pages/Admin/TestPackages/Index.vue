<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import PaginationLinks from '@/components/PaginationLinks.vue';
import type { BreadcrumbItem } from '@/types';
import type { TestPackage, Program, PaginatedData } from '@/types/models';
import { ref } from 'vue';

const props = defineProps<{ packages: PaginatedData<TestPackage & { program?: { id: string; name: string }; items_count?: number }>; programs: Program[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin' },
    { title: 'Test Packages', href: '/admin/test-packages' },
];

const search = ref('');
const programFilter = ref('');

function filter() {
    router.get('/admin/test-packages', { search: search.value || undefined, program_id: programFilter.value || undefined }, { preserveState: true });
}
function destroy(id: string) {
    if (confirm('Delete this package?')) router.delete(`/admin/test-packages/${id}`);
}
</script>

<template>
    <Head title="Test Packages" />
    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="flex items-center justify-between mb-4">
            <div class="flex gap-2">
                <Input v-model="search" placeholder="Search..." class="w-48" @keyup.enter="filter" />
                <Select v-model="programFilter" @update:model-value="filter">
                    <SelectTrigger class="w-40"><SelectValue placeholder="All Programs" /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">All Programs</SelectItem>
                        <SelectItem v-for="p in programs" :key="p.id" :value="p.id">{{ p.name }}</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <Button as="a" href="/admin/test-packages/create">Create Package</Button>
        </div>
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>Name</TableHead>
                    <TableHead>Program</TableHead>
                    <TableHead>Items</TableHead>
                    <TableHead>Premium</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead class="w-32">Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="pkg in packages.data" :key="pkg.id">
                    <TableCell class="font-medium">{{ pkg.name }}</TableCell>
                    <TableCell>{{ pkg.program?.name }}</TableCell>
                    <TableCell>{{ pkg.items_count ?? 0 }}</TableCell>
                    <TableCell><Badge :variant="pkg.is_premium ? 'default' : 'outline'">{{ pkg.is_premium ? 'Premium' : 'Free' }}</Badge></TableCell>
                    <TableCell><Badge :variant="pkg.is_active ? 'default' : 'secondary'">{{ pkg.is_active ? 'Active' : 'Inactive' }}</Badge></TableCell>
                    <TableCell class="flex gap-1">
                        <Button variant="ghost" size="sm" as="a" :href="`/admin/test-packages/${pkg.id}`">View</Button>
                        <Button variant="ghost" size="sm" as="a" :href="`/admin/test-packages/${pkg.id}/edit`">Edit</Button>
                        <Button variant="ghost" size="sm" @click="destroy(pkg.id)">Delete</Button>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
        <PaginationLinks :links="packages.links" :from="packages.from" :to="packages.to" :total="packages.total" class="mt-4" />
    </AdminLayout>
</template>
