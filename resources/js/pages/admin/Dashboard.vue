<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import {
    MenuIcon,
    XIcon,
    UserCircleIcon,
    ChevronDownIcon,
    UserIcon,
    UsersIcon,
    ShieldCheckIcon,
    KeyIcon,
    PlusIcon,
    TrashIcon,
    PencilIcon,
    EyeIcon
} from 'lucide-vue-next';

// Métricas principais
const metrics = ref({
    totalUsers: 256,
    newUsers: 24,
    totalTeams: 15,
    newTeams: 3,
    totalRoles: 8,
    newRoles: 1,
    totalPermissions: 32,
    newPermissions: 5
});

// Dados para o gráfico de usuários por time
const usersByTeam = ref([
    { name: 'Desenvolvimento', users: 42 },
    { name: 'Marketing', users: 28 },
    { name: 'Suporte', users: 36 },
    { name: 'Vendas', users: 22 },
    { name: 'Recursos Humanos', users: 15 }
]);

// Cores para os times no gráfico
const teamColors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'];

// Calcular o máximo de usuários por time para o gráfico
const maxTeamUsers = computed(() => {
    return Math.max(...usersByTeam.value.map(team => team.users));
});

// Dados para o gráfico de atividade mensal
const monthlyActivity = ref([
    { name: 'Jan', count: 45 },
    { name: 'Fev', count: 52 },
    { name: 'Mar', count: 38 },
    { name: 'Abr', count: 65 },
    { name: 'Mai', count: 48 },
    { name: 'Jun', count: 70 }
]);

// Calcular o máximo de atividade mensal para o gráfico
const maxMonthlyActivity = computed(() => {
    return Math.max(...monthlyActivity.value.map(month => month.count));
});

// Atividades recentes
const recentActivities = ref([
    {
        type: 'create',
        description: 'Novo usuário criado',
        user: 'Admin',
        time: 'Agora mesmo'
    },
    {
        type: 'update',
        description: 'Permissão atualizada',
        user: 'Admin',
        time: '2 horas atrás'
    },
    {
        type: 'delete',
        description: 'Time removido',
        user: 'Admin',
        time: '5 horas atrás'
    },
    {
        type: 'view',
        description: 'Relatório visualizado',
        user: 'Admin',
        time: '1 dia atrás'
    }
]);

// Ícones para tipos de atividade
const activityTypeIcons = {
    create: PlusIcon,
    update: PencilIcon,
    delete: TrashIcon,
    view: EyeIcon
};

// Classes para tipos de atividade
const activityTypeClasses = {
    create: 'bg-green-100 text-green-600',
    update: 'bg-blue-100 text-blue-600',
    delete: 'bg-red-100 text-red-600',
    view: 'bg-yellow-100 text-yellow-600'
};

// Usuários recentes
const recentUsers = ref([
    {
        name: 'Ana Silva',
        email: 'ana@example.com',
        role: 'Desenvolvedora',
        team: 'Desenvolvimento',
        status: 'Ativo'
    },
    {
        name: 'Carlos Oliveira',
        email: 'carlos@example.com',
        role: 'Designer',
        team: 'Marketing',
        status: 'Ativo'
    },
    {
        name: 'Mariana Santos',
        email: 'mariana@example.com',
        role: 'Gerente',
        team: 'Vendas',
        status: 'Inativo'
    },
    {
        name: 'Roberto Lima',
        email: 'roberto@example.com',
        role: 'Analista',
        team: 'Suporte',
        status: 'Ativo'
    }
]);
</script>

<template>
    <Head title="Painel Admin" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <!-- Título da página -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                <p class="text-gray-600">Bem-vindo ao painel de controle administrativo</p>
            </div>

            <!-- Cards de métricas -->
            <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Card de Usuários -->
                <div class="p-6 bg-white rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total de Usuários</p>
                            <p class="text-2xl font-bold text-gray-900">{{ metrics.totalUsers }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <UserIcon class="w-6 h-6 text-green-600" />
                        </div>
                    </div>
                    <div class="flex items-center mt-4">
                        <span class="text-sm font-medium text-green-600">+{{ metrics.newUsers }}</span>
                        <span class="ml-1 text-sm text-gray-500">desde o mês passado</span>
                    </div>
                </div>

                <!-- Card de Times -->
                <div class="p-6 bg-white rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total de Times</p>
                            <p class="text-2xl font-bold text-gray-900">{{ metrics.totalTeams }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <UsersIcon class="w-6 h-6 text-blue-600" />
                        </div>
                    </div>
                    <div class="flex items-center mt-4">
                        <span class="text-sm font-medium text-blue-600">+{{ metrics.newTeams }}</span>
                        <span class="ml-1 text-sm text-gray-500">desde o mês passado</span>
                    </div>
                </div>

                <!-- Card de Cargos -->
                <div class="p-6 bg-white rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total de Cargos</p>
                            <p class="text-2xl font-bold text-gray-900">{{ metrics.totalRoles }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <ShieldCheckIcon class="w-6 h-6 text-purple-600" />
                        </div>
                    </div>
                    <div class="flex items-center mt-4">
                        <span class="text-sm font-medium text-purple-600">+{{ metrics.newRoles }}</span>
                        <span class="ml-1 text-sm text-gray-500">desde o mês passado</span>
                    </div>
                </div>

                <!-- Card de Permissões -->
                <div class="p-6 bg-white rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total de Permissões</p>
                            <p class="text-2xl font-bold text-gray-900">{{ metrics.totalPermissions }}</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <KeyIcon class="w-6 h-6 text-yellow-600" />
                        </div>
                    </div>
                    <div class="flex items-center mt-4">
                        <span class="text-sm font-medium text-yellow-600">+{{ metrics.newPermissions }}</span>
                        <span class="ml-1 text-sm text-gray-500">desde o mês passado</span>
                    </div>
                </div>
            </div>

            <!-- Gráficos e Tabelas -->
            <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-2">
                <!-- Gráfico de Usuários por Time -->
                <div class="p-6 bg-white rounded-lg shadow">
                    <h2 class="mb-4 text-lg font-medium text-gray-800">Usuários por Time</h2>
                    <div class="h-64">
                        <!-- Aqui você pode integrar uma biblioteca de gráficos como Chart.js -->
                        <div class="flex flex-col h-full">
                            <div v-for="(team, index) in usersByTeam" :key="index" class="flex items-center mb-4">
                                <div class="w-full">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-700">{{ team.name }}</span>
                                        <span class="text-sm font-medium text-gray-700">{{ team.users }}</span>
                                    </div>
                                    <div class="w-full h-2 bg-gray-200 rounded-full">
                                        <div class="h-2 rounded-full"
                                             :style="{ width: `${(team.users / maxTeamUsers) * 100}%`, backgroundColor: teamColors[index % teamColors.length] }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Atividade Mensal -->
                <div class="p-6 bg-white rounded-lg shadow">
                    <h2 class="mb-4 text-lg font-medium text-gray-800">Atividade Mensal</h2>
                    <div class="h-64">
                        <!-- Aqui você pode integrar uma biblioteca de gráficos como Chart.js -->
                        <div class="flex items-end justify-between h-48">
                            <div v-for="(month, index) in monthlyActivity" :key="index" class="flex flex-col items-center">
                                <div class="w-10 bg-blue-500 rounded-t"
                                     :style="{ height: `${(month.count / maxMonthlyActivity) * 100}%` }"></div>
                                <span class="mt-2 text-xs text-gray-600">{{ month.name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção de Atividades Recentes e Usuários Recentes -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Atividades Recentes -->
                <div class="p-6 bg-white rounded-lg shadow">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-medium text-gray-800">Atividades Recentes</h2>
                        <button class="text-sm font-medium text-blue-600 hover:text-blue-800">Ver todas</button>
                    </div>
                    <div class="space-y-4">
                        <div v-for="(activity, index) in recentActivities" :key="index" class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full"
                                     :class="activityTypeClasses[activity.type]">
                                    <component :is="activityTypeIcons[activity.type]" class="w-4 h-4" />
                                </div>
                            </div>
                            <div class="flex-1 ml-4">
                                <p class="text-sm font-medium text-gray-900">{{ activity.description }}</p>
                                <p class="text-sm text-gray-500">{{ activity.user }} • {{ activity.time }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Usuários Recentes -->
                <div class="p-6 bg-white rounded-lg shadow">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-medium text-gray-800">Usuários Recentes</h2>
                        <button class="text-sm font-medium text-blue-600 hover:text-blue-800">Ver todos</button>
                    </div>
                    <div class="overflow-hidden">
                        <table class="min-w-full">
                            <thead>
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nome</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Cargo</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Time</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            <tr v-for="(user, index) in recentUsers" :key="index">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                                            <p class="text-sm text-gray-500">{{ user.email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm text-gray-900">{{ user.role }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm text-gray-900">{{ user.team }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full"
                        :class="user.status === 'Ativo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                    {{ user.status }}
                  </span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
