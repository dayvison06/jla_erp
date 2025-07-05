<script lang="ts" setup>
import {toast, Toaster as Sonner, type ToasterProps} from 'vue-sonner'
import {usePage} from "@inertiajs/vue3";
import {onMounted} from "vue";

const props = defineProps<ToasterProps>()

const page = usePage();

const hasNotify  = page.props.session.message.notify
console.log('hasNotify', hasNotify)

onMounted(() => {
    if (hasNotify) {
        toast(hasNotify.title, {
            type: hasNotify.type,
            duration: 10000,
            description: hasNotify.message,
            position: 'top-center',
            closeButton: true,
        });
    }
});
</script>

<template>
    <Sonner
        class="toaster group"
        v-bind="props"
        :toast-options="{
      classes: {
        toast: 'group toast group-[.toaster]:bg-background group-[.toaster]:text-foreground group-[.toaster]:border-border group-[.toaster]:shadow-lg',
        description: 'group-[.toast]:text-muted-foreground',
        actionButton:
          'group-[.toast]:bg-primary group-[.toast]:text-primary-foreground',
        cancelButton:
          'group-[.toast]:bg-muted group-[.toast]:text-muted-foreground',
      },
    }"
    />
</template>
