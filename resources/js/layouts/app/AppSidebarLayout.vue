<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';
import Toaster from '@/components/ui/sonner/Sonner.vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar">
            <Toaster rich-colors />
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <div class="mx-auto w-full max-w-screen-2xl mt-8 md:px-6 lg:px-8">

                <slot />
            </div>
        </AppContent>
    </AppShell>
</template>
