<script lang="ts" setup>
import {toast, Toaster as Sonner, type ToasterProps} from 'vue-sonner'
import {usePage} from "@inertiajs/vue3";
import {watch} from "vue";
import { useToast, type ToastInternal } from '@/composables/useToast';

const { toastMessage }: ToastInternal = useToast();
const props = defineProps<ToasterProps>()
const page = usePage();

watch(
    () => page.props.session.message.notify,
    (hasNotify) => {
        if (hasNotify) {
            toast(hasNotify.title, {
                type: hasNotify.type,
                duration: 10000,
                description: hasNotify.message,
                position: 'top-center',
                closeButton: true,
            });
        }
    },
    {immediate: true}
);

watch(
    () => page.props.notify,
    (hasNotify) => {
        if (hasNotify) {
            toast(hasNotify.title, {
                type: hasNotify.type,
                duration: 10000,
                description: hasNotify.message,
                position: 'top-center',
            });
            console.log('INERTIA NOTIFY:', hasNotify)
        }
    },
    {immediate: true}
);


// Observa mudanças na mensagem de toast e exibe a notificação
watch(toastMessage, (newValue) => {
    if (toastMessage) {
        console.log('New Toast:', newValue)
        toast(newValue.title, {
            type: newValue.type,
            description: newValue.description,
            duration: 5000,
            position: 'top-center',
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
