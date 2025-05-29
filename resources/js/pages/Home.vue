<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { BellIcon, UserIcon, File, ClockIcon, ChartBarIcon, CogIcon, UsersIcon, FolderIcon, PlusIcon, CheckIcon } from 'lucide-vue-next';
import { BreadcrumbItem } from '@/types';

// Tipos
interface User {
    id: string;
    name: string;
    avatar?: string;
    role: string;
    lastLogin: Date;
}

interface Stat {
    name: string;
    value: string | number;
    icon: any;
}

interface QuickAction {
    title: string;
    description: string;
    icon: any;
    link: string;
}

interface Activity {
    title: string;
    description: string;
    time: string;
    icon: any;
}

interface Announcement {
    title: string;
    content: string;
    date: string;
    isNew: boolean;
}

// Dados do usuário (normalmente viriam de uma API)
const user = ref<User>({
    id: '1',
    name: 'Carlos Silva',
    role: 'Administrador',
    lastLogin: new Date(Date.now() - 24 * 60 * 60 * 1000) // ontem
});

// Estatísticas
const stats = ref<Stat[]>([
    { name: 'Projetos Ativos', value: 12, icon: FolderIcon },
    { name: 'Tarefas Pendentes', value: 8, icon: ClockIcon },
    { name: 'Relatórios', value: 5, icon: ChartBarIcon },
    { name: 'Membros da Equipe', value: 24, icon: UsersIcon }
]);

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

// Atividades recentes
const recentActivities = ref<Activity[]>([
    {
        title: 'Projeto atualizado',
        description: 'Você atualizou o projeto "Redesign do Site"',
        time: '2h atrás',
        icon: CheckIcon
    },
    {
        title: 'Novo documento',
        description: 'Você adicionou "Relatório Q2" aos documentos',
        time: '5h atrás',
        icon: File
    },
    {
        title: 'Reunião agendada',
        description: 'Reunião de equipe agendada para amanhã às 10h',
        time: '1d atrás',
        icon: ClockIcon
    }
]);

// Anúncios
const announcements = ref<Announcement[]>([
    {
        title: 'Atualização do Sistema',
        content: 'Uma nova versão do sistema será lançada na próxima semana com melhorias de desempenho.',
        date: '22/05/2025',
        isNew: true
    },
    {
        title: 'Manutenção Programada',
        content: 'O sistema estará indisponível para manutenção no domingo, das 2h às 4h.',
        date: '18/05/2025',
        isNew: false
    }
]);

// Métodos
const getGreeting = () => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Bom dia!';
    if (hour < 18) return 'Boa tarde!';
    return 'Boa noite!';
};

const getInitials = (name: string) => {
    return name
        .split(' ')
        .map(part => part[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
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
