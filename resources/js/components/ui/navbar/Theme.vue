<script setup lang="ts">

import { Moon, Sun, Monitor } from 'lucide-vue-next';
import { useAppearance } from '@/composables/useAppearance';
const { appearance, updateAppearance } = useAppearance();

const tabs = [
    { value: 'light', Icon: Sun, label: 'Light' },
    { value: 'dark', Icon: Moon, label: 'Dark' },
    { value: 'system', Icon: Monitor, label: 'System' },
] as const;
</script>

<template>
    <button
        @click="updateAppearance(appearance === 'light' ? 'dark' : 'light')"
        class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"
    >
        <component :is="appearance === 'light' ? Moon : Sun" class="h-4 w-4" />
    </button>
</template>
