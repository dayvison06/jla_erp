<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import Toaster from '@/components/ui/sonner/Sonner.vue'
import { toast } from 'vue-sonner'
import { onMounted } from 'vue';
interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const unauthorized = page.props.session.message.unauthorized;

onMounted(() => {
    if (unauthorized) {
        toast('Acesso negado', {
            type: 'error',
            duration: 10000,
            description: unauthorized,
            position: 'top-center',
            classes: {
                title: 'text-base font-semibold',
                icon: 'text-red-500'
            },
            closeButton: true,
        });
    }
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Toaster />
        <slot />
    </AppLayout>
</template>
