<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import Toaster from '@/components/ui/sonner/Sonner.vue'

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});


</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Toaster />
        <slot />
    </AppLayout>
</template>
