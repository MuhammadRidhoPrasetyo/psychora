<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

type Props = {
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
};

defineProps<Props>();
</script>

<template>
    <div class="flex items-center justify-between px-2 py-4">
        <p class="text-sm text-muted-foreground">
            Showing {{ from ?? 0 }} to {{ to ?? 0 }} of {{ total }} results
        </p>
        <div class="flex items-center gap-1">
            <template v-for="(link, index) in links" :key="index">
                <Button
                    v-if="link.url"
                    as-child
                    :variant="link.active ? 'default' : 'outline'"
                    size="sm"
                >
                    <Link :href="link.url" preserve-scroll>
                        <ChevronLeft v-if="index === 0" class="h-4 w-4" />
                        <ChevronRight v-else-if="index === links.length - 1" class="h-4 w-4" />
                        <span v-else v-html="link.label" />
                    </Link>
                </Button>
                <Button
                    v-else
                    :variant="link.active ? 'default' : 'outline'"
                    size="sm"
                    disabled
                >
                    <ChevronLeft v-if="index === 0" class="h-4 w-4" />
                    <ChevronRight v-else-if="index === links.length - 1" class="h-4 w-4" />
                    <span v-else v-html="link.label" />
                </Button>
            </template>
        </div>
    </div>
</template>
