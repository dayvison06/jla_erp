<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { ref } from 'vue';

const appName = ref('Liber Helper');
</script>

<template>
    <div class="flex size-8 items-center justify-center rounded-md text-sidebar-primary-foreground">
        <AppLogoIcon class="size-8 text-foreground" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span  class="mb-0.5 truncate font-semibold leading-none">{{ appName || 'JLA Engenharia' }}</span>
    </div>
</template>
