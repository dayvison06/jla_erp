// composables/useNotificacoes.ts
import { ref } from 'vue';
import type { Component } from 'vue';

export type Notificacao = {
    title: string;
    time?: string;
    icon?: Component;
    bgColor?: string;
    iconColor?: string;
    tipo?: 'sucesso' | 'erro' | 'info' | 'aviso';
};

const notifications = ref<Notificacao[]>([]);

export function useNotification() {

    /**
     * Adiciona uma nova notificação à lista de notificações.
     *
     * @param {Notificacao} n - Objeto contendo os dados da notificação.
     *   - `title` (string): Título da notificação.
     *   - `time` (string, opcional): Hora da notificação. Se não fornecido, será usado o horário atual.
     *   - `icon` (Component, opcional): Ícone da notificação.
     *   - `bgColor` (string, opcional): Cor de fundo da notificação. Padrão: 'bg-emerald-100'.
     *   - `iconColor` (string, opcional): Cor do ícone da notificação. Padrão: 'text-emerald-600'.
     *   - `tipo` ('sucesso' | 'erro' | 'info' | 'aviso', opcional): Tipo da notificação.
     */
    function adicionar(n: Notificacao) {
        const now = new Date();
        const timeString = n.time || now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });

        notifications.value.unshift({
            ...n,
            time: timeString,
            bgColor: n.bgColor || 'bg-emerald-100',
            iconColor: n.iconColor || 'text-emerald-600'
        });
    }

    /**
     * Limpa todas as notificações
     */
    function limpar() {
        notifications.value = [];
    }

    return {
        notifications,
        adicionar,
        limpar
    };
}
