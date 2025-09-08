<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';
import { File, ChartBarIcon, CogIcon, PlusIcon } from 'lucide-vue-next';
import { BreadcrumbItem, User } from '@/types';

const page = usePage()
const user: User = page.props.auth.user


interface QuickAction {
    title: string;
    description: string;
    icon: any;
    link: string;
}

// Ações rápidas
const quickActions = ref<QuickAction[]>([
    {
        title: 'Configurações',
        description: 'Ajustar suas preferências',
        icon: CogIcon,
        link: '/settings/profile'
    },
]);

// Métodos
const getGreeting = () => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Bom dia';
    if (hour < 18) return 'Boa tarde';
    return 'Boa noite';
};

const breadcrumbs: BreadcrumbItem = [
    { name: 'Home', href: '/' },
];
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="container mx-auto px-4 py-8">
            <!-- Header com saudação personalizada -->
            <header class="mb-8">
                <div class="flex items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
                            {{ getGreeting() }}, <small class=" font-normal text-3xl">{{ user.name }}</small>
                        </h1>
                    </div>
                </div>
            </header>
            <!-- Ações rápidas -->
            <section class="mb-10">
                <h2 class="mb-4 text-xl font-semibold text-gray-800 dark:text-white mb-6">Ações Rápidas</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <Link v-for="(action, index) in quickActions" :key="index" :href="action.link"
                         class="group flex cursor-pointer flex-col items-center rounded-xl bg-white p-6 text-center shadow-lg transition-all hover:shadow-md dark:bg-gray-800 dark:hover:bg-gray-750">
                            <div class="mb-4 rounded-full bg-gray-100 p-4 transition-all group-hover:bg-gray-200 dark:bg-gray-900 dark:group-hover:bg-gray-800">
                                <component :is="action.icon" class="h-6 w-6 text-gray-600 dark:text-gray-400" />
                            </div>
                            <h3 class="mb-2 font-medium text-gray-800 dark:text-white">{{ action.title }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ action.description }}</p>
                    </Link>
                </div>
            </section>
        </main>
    </AppLayout>
</template>
