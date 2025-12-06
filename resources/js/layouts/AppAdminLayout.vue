<script setup lang="ts">
import type { BreadcrumbItemType } from '@/types';
import Toaster from '@/components/ui/sonner/Sonner.vue'
import AppAdminSidebarLayout from '@/layouts/app/AppAdminSidebarLayout.vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

</script>

<template>
    <AppAdminSidebarLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppAdminSidebarLayout>
</template>
