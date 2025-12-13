import { toast } from 'vue-sonner'

/**
 * Helper para exibição de mensagens toast
 * @param title - Título da mensagem
 * @param message - Mensagem detalhada
 * @param type - 'success' | 'error' | 'info'
 */
export function showToast(type: 'success' | 'error' | 'info', title: string ,message?: string) {
    console.log('Showing toast:', { type, title, message })
    toast[type](title, {
        duration: 3000,
        position: 'top-center',
        description: message
    })
}
