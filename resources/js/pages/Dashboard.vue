<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { useNotification } from '@/composables/useNotification';
import { ref } from 'vue';
import {
    LayoutDashboard,
    BarChart2,
    FileText,
    ShoppingCart,
    TrendingUp,
    TrendingDown,
    DollarSign,
    PlusCircle,
    Download,
    Clock,
    Edit,
    Trash2,
    Laptop,
    Headphones,
    Monitor,
    Watch,
    Mouse,
} from 'lucide-vue-next';

const isDarkMode = ref(false);
const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const stats = ref([
    {
        title: 'Receita Total',
        value: '$24,345',
        icon: DollarSign,
        bgColor: 'bg-blue-100 dark:bg-blue-900',
        iconColor: 'text-blue-600 dark:text-blue-400',
        trend: {
            value: '+12%',
            icon: TrendingUp,
            bgColor: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400',
        },
        comparison: 'mês anterior',
    },
    {
        title: 'Novos Pedidos',
        value: '450',
        icon: ShoppingCart,
        bgColor: 'bg-emerald-100 dark:bg-emerald-900',
        iconColor: 'text-emerald-600 dark:text-emerald-400',
        trend: {
            value: '+4%',
            icon: TrendingUp,
            bgColor: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400',
        },
        comparison: 'semana anterior',
    },
    {
        title: 'Clientes',
        value: '2,550',
        icon: ShoppingCart,
        bgColor: 'bg-purple-100 dark:bg-purple-900',
        iconColor: 'text-purple-600 dark:text-purple-400',
        trend: {
            value: '+8%',
            icon: TrendingUp,
            bgColor: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400',
        },
        comparison: 'mês anterior',
    },
    {
        title: 'Faturas Pendentes',
        value: '5',
        icon: FileText,
        bgColor: 'bg-amber-100 dark:bg-amber-900',
        iconColor: 'text-amber-600 dark:text-amber-400',
        trend: {
            value: '-2%',
            icon: TrendingDown,
            bgColor: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-400',
        },
        comparison: 'semana anterior',
    },
]);

const selectedPeriod = ref('month');

const topProducts = ref([
    { name: 'Laptop Pro X1', category: 'Eletrônicos', sales: '$12,500', percentage: 85, icon: Laptop },
    { name: 'Fones sem Fio', category: 'Áudio', sales: '$8,200', percentage: 65, icon: Headphones },
    { name: 'Smart Watch V3', category: 'Vestíveis', sales: '$6,700', percentage: 50, icon: Watch },
    { name: 'Monitor Ultra HD', category: 'Eletrônicos', sales: '$5,400', percentage: 40, icon: Monitor },
    { name: 'Mouse Gamer', category: 'Acessórios', sales: '$3,800', percentage: 30, icon: Mouse },
]);

const recentTransactions = ref([
    { id: 'INV-1234', customer: 'John Smith', date: 'Hoje, 13:45', amount: '$1,250.00', status: 'Concluído', statusClass: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400' },
    { id: 'INV-1233', customer: 'Alice Johnson', date: 'Hoje, 10:30', amount: '$890.50', status: 'Processando', statusClass: 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-400' },
    { id: 'INV-1232', customer: 'Robert Brown', date: 'Ontem, 16:20', amount: '$1,890.00', status: 'Concluído', statusClass: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400' },
    { id: 'INV-1231', customer: 'Emily Davis', date: 'Ontem, 09:15', amount: '$450.25', status: 'Falhou', statusClass: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-400' },
    { id: 'INV-1230', customer: 'Michael Wilson', date: '23 Mai, 2023', amount: '$2,500.00', status: 'Concluído', statusClass: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400' },
]);

const tasks = ref([
    { title: 'Atualizar inventário de produtos', completed: false, priority: 'Alta', priorityClass: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-400', dueDate: 'Hoje', assignee: { name: 'John Doe', initials: 'JD' }, timeLeft: '3h' },
    { title: 'Revisar novos cadastros de clientes', completed: true, priority: 'Média', priorityClass: 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-400', dueDate: 'Hoje', assignee: { name: 'Alice Smith', initials: 'AS' }, timeLeft: '5h' },
    { title: 'Preparar relatório financeiro mensal', completed: false, priority: 'Alta', priorityClass: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-400', dueDate: 'Amanhã', assignee: { name: 'Robert Johnson', initials: 'RJ' }, timeLeft: '1d' },
    { title: 'Agendar reunião com fornecedores', completed: false, priority: 'Baixa', priorityClass: 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-400', dueDate: 'Próxima semana', assignee: { name: 'Emily Brown', initials: 'EB' }, timeLeft: '5d' },
]);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];

const { adicionar } = useNotification();

function testNotification() {
    adicionar({
        title: 'Teste de Notificação 🚀',
        icon: ShoppingCart,
        bgColor: 'bg-green-100',
        iconColor: 'text-green-600',
        time: 'Agora'
    });
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 bg-background text-foreground">
            <div class="flex flex-1 flex-col overflow-hidden">
                <main class="flex-1 overflow-auto p-4 md:p-6">
                    <div class="relative mb-6 overflow-hidden rounded-lg bg-gradient-to-r from-emerald-500 to-teal-500 shadow-md">
                        <div class="absolute top-0 right-0 opacity-10">
                            <LayoutDashboard size="180" class="text-white" />
                        </div>
                        <div class="relative z-10 p-6 md:p-8">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div>
                                    <h1 class="mb-2 text-2xl font-bold text-white md:text-3xl">Bem-vindo de volta, {{ user.name }}!</h1>
                                    <p class="text-emerald-50">Aqui está o que está acontecendo com o seu negócio hoje.</p>
                                </div>
                                <div class="mt-4 md:mt-0">
                                    <button
                                        @click="testNotification"
                                        class="flex items-center rounded-lg bg-white px-4 py-2 font-medium text-emerald-600 shadow-sm transition-colors hover:bg-emerald-50"
                                    >
                                        <Download size="16" class="mr-2" />
                                        Baixar Relatório
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                        <div
                            v-for="(stat, index) in stats"
                            :key="index"
                            class="rounded-lg border bg-card text-card-foreground p-6 shadow-sm"
                        >
                            <div class="mb-4 flex items-center justify-between">
                                <div class="rounded-full p-3" :class="stat.bgColor">
                                    <component :is="stat.icon" size="20" :class="stat.iconColor" />
                                </div>
                                <span class="rounded-full px-2 py-1 text-xs font-medium" :class="stat.trend.bgColor">
                  <component :is="stat.trend.icon" size="12" class="mr-1 inline" />
                  {{ stat.trend.value }}
                </span>
                            </div>
                            <h3 class="mb-1 text-sm font-medium text-muted-foreground">{{ stat.title }}</h3>
                            <p class="text-2xl font-bold">{{ stat.value }}</p>
                            <div class="mt-2 text-xs text-muted-foreground">
                                <span>vs. {{ stat.comparison }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
                        <div class="rounded-lg border bg-card p-6 shadow-sm lg:col-span-2">
                            <div class="mb-6 flex items-center justify-between">
                                <h2 class="text-lg font-medium">Visão Geral de Vendas</h2>
                                <div class="flex space-x-2">
                                    <button
                                        v-for="(period, index) in ['Dia', 'Semana', 'Mês', 'Ano']"
                                        :key="index"
                                        class="rounded-md px-3 py-1 text-xs transition-colors"
                                        :class="
                      selectedPeriod === period.toLowerCase()
                        ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300'
                        : 'text-muted-foreground hover:bg-muted'
                    "
                                        @click="selectedPeriod = period.toLowerCase()"
                                    >
                                        {{ period }}
                                    </button>
                                </div>
                            </div>
                            <div class="relative h-64">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="flex h-full w-full items-center justify-center rounded-lg bg-muted">
                                        <div class="text-center">
                                            <BarChart2 size="48" class="mx-auto text-muted-foreground" />
                                            <p class="mt-2 text-sm text-muted-foreground">Gráfico de vendas será exibido aqui</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border bg-card p-6 shadow-sm">
                            <div class="mb-6 flex items-center justify-between">
                                <h2 class="text-lg font-medium">Produtos em Destaque</h2>
                            </div>
                            <div class="space-y-5">
                                <div v-for="(product, index) in topProducts" :key="index" class="flex items-center">
                                    <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <component :is="product.icon" size="18" class="text-muted-foreground" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="truncate text-sm font-medium">{{ product.name }}</h4>
                                        <p class="text-xs text-muted-foreground">{{ product.category }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium">{{ product.sales }}</p>
                                        <div class="mt-1 h-1 w-16 rounded-full bg-muted">
                                            <div class="h-1 rounded-full bg-emerald-500" :style="{ width: product.percentage + '%' }"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </AppLayout>
</template>
