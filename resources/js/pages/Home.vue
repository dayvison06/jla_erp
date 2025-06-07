<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';
import { File, ChartBarIcon, CogIcon, PlusIcon } from 'lucide-vue-next';
import { BreadcrumbItem } from '@/types';

// Tipos
interface User {
    id: string;
    name: string;
    avatar?: string;
    role: string;
    lastLogin: Date;
}

interface QuickAction {
    title: string;
    description: string;
    icon: any;
    link: string;
}

// Dados do usuário (normalmente viriam de uma API)
const user = ref<User>({
    id: '1',
    name: 'Carlos Silva',
    role: 'Administrador',
    lastLogin: new Date(Date.now() - 24 * 60 * 60 * 1000) // ontem
});

// Ações rápidas
const quickActions = ref<QuickAction[]>([
    {
        title: 'Novo Projeto',
        description: 'Criar um novo projeto no sistema',
        icon: PlusIcon,
        link: '/projects/new'
    },
    {
        title: 'Relatórios',
        description: 'Ver relatórios e análises',
        icon: ChartBarIcon,
        link: '/reports'
    },
    {
        title: 'Documentos',
        description: 'Acessar documentos importantes',
        icon: File,
        link: '/documents'
    },
    {
        title: 'Configurações',
        description: 'Ajustar suas preferências',
        icon: CogIcon,
        link: '/settings'
    }
]);

// Métodos
const getGreeting = () => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Bom dia!';
    if (hour < 18) return 'Boa tarde!';
    return 'Boa noite!';
};

const breadcrumbs: BreadcrumbItem = [
    { name: 'Home', href: '/' },
];
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="container mx-auto px-4 py-8">
                <!-- Header com saudação personalizada -->
                <header class="mb-8">
                    <div class="flex items-center">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
                                Bem-vindo, {{ user.name }}!
                            </h1>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                {{ getGreeting() }} É ótimo ver você novamente.
                            </p>
                        </div>
                    </div>
                </header>
                <!-- Ações rápidas -->
                <section class="mb-10">
                    <h2 class="mb-4 text-xl font-semibold text-gray-800 dark:text-white mb-6">Ações Rápidas</h2>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div v-for="(action, index) in quickActions" :key="index"
                             class="group flex cursor-pointer flex-col items-center rounded-xl bg-white p-6 text-center shadow-lg transition-all hover:shadow-md dark:bg-gray-800 dark:hover:bg-gray-750">
                            <div class="mb-4 rounded-full bg-blue-100 p-4 transition-all group-hover:bg-blue-200 dark:bg-blue-900 dark:group-hover:bg-blue-800">
                                <component :is="action.icon" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                            </div>
                            <h3 class="mb-2 font-medium text-gray-800 dark:text-white">{{ action.title }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ action.description }}</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
