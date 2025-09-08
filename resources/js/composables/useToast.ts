import { ref } from 'vue';

const toastMessage = ref<{ title: string, type: string, description: string  } | null>(null);

export interface ToastInternal {
    title: string;
    type: string; // success, error, info, warning
    description: string;
}

/**
 * Composable para exibir notificações do tipo toast.
 */
export function useToast() {
    /**
     * Exibe um toast com título, tipo e descrição.
     * @param title Titulo do toast
     * @param type Tipo do toast (success, error, info, warning)
     * @param description Descrição do toast
     */
    function showToast(title: string, type: string, description: string) {
        toastMessage.value = { title, type, description };
    }

    return { toastMessage, showToast };
}
