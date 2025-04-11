<script setup lang="ts">
import { ref } from 'vue';
import { Bell, XIcon, CheckCircleIcon, InfoIcon, AlertCircleIcon, AlertTriangleIcon } from 'lucide-vue-next';

const notifications = defineModel<any[]>('notifications');

// Dropdown state
const isNotificationsOpen = ref(false);

// Ação de "marcar como lido"
function marcarComoLido() {
    // Aqui você pode limpar ou marcar os objetos como "lido"
    isNotificationsOpen.value = false;
}
</script>


<template>
    <div class="relative">
        <!-- Botão com badge -->
        <button
            @click="isNotificationsOpen = !isNotificationsOpen"
            class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 relative"
        >
            <Bell size="20" class="text-gray-600 dark:text-gray-300" />
            <span
                v-if="notifications?.length"
                class="absolute text-xs font-semibold text-white -top-1 -right-1 w-4 h-4 rounded-full bg-red-500"
            >{{ notifications.length }}</span>
        </button>

        <!-- Dropdown de notificações -->
        <div
            v-if="isNotificationsOpen"
            class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50"
        >
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="font-medium dark:text-white">Notificações</h3>
                <button
                    class="text-xs text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400"
                    @click="marcarComoLido()"
                >
                    Marcar todas como lidas
                </button>
            </div>

            <!-- Lista de notificações -->
            <div class="max-h-72 overflow-y-auto">
                <div
                    v-for="(notification, index) in notifications"
                    :key="index"
                    class="p-4 border-b border-gray-200 dark:border-gray-700 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                    <div class="flex">
                        <div
                            class="rounded-full p-2 mr-3 flex-shrink-0"
                            :class="notification.bgColor"
                        >
                            <component
                                :is="notification.icon"
                                size="16"
                                :class="notification.iconColor"
                            />
                        </div>
                        <div>
                            <p class="text-sm font-medium dark:text-white">{{ notification.title }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ notification.time }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rodapé do dropdown -->
            <div class="p-3 text-center border-t border-gray-200 dark:border-gray-700">
                <button class="text-sm text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400">
                    Ver todas as notificações
                </button>
            </div>
        </div>
    </div>
</template>

