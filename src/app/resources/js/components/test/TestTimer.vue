<script setup lang="ts">
import { Clock } from 'lucide-vue-next';

const props = defineProps<{
    formatted: string;
    isWarning: boolean;
    isDanger: boolean;
}>();
</script>

<template>
    <div
        class="flex items-center gap-2 rounded-lg px-4 py-2 font-mono text-lg font-bold transition-colors"
        :class="{
            'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400': isDanger,
            'bg-yellow-100 text-yellow-700 dark:bg-yellow-950 dark:text-yellow-400': isWarning && !isDanger,
            'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300': !isWarning && !isDanger,
        }"
    >
        <Clock class="size-5" :class="{ 'animate-pulse': isDanger }" />
        <span>{{ formatted }}</span>
    </div>
</template>
