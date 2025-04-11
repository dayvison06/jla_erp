<script setup lang="ts">
import { Bell } from 'lucide-vue-next';
</script>

<template>
    <div class="">
        <button
            @click="isNotificationsOpen = !isNotificationsOpen"
            class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 relative"
        >
            <Bell size="20" class="text-gray-600 dark:text-gray-300" />
            <span class="absolute top-0 right-0 w-2 h-2 rounded-full bg-red-500"></span>
        </button>

        <!-- Notifications Dropdown -->
        <div
            v-if="isNotificationsOpen"
            class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50"
        >
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="font-medium dark:text-white">Notifications</h3>
                <button class="text-xs text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400">
                    Mark all as read
                </button>
            </div>
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
            <div class="p-3 text-center border-t border-gray-200 dark:border-gray-700">
                <button class="text-sm text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400">
                    View all notifications
                </button>
            </div>
        </div>
    </div>
</template>
