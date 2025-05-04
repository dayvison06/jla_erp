<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    BarChart2,
    FileText,
    ShoppingCart,
    TrendingUp,
    TrendingDown,
    DollarSign,
    Laptop,
    Headphones,
    Monitor,
    Watch,
    Mouse,
} from 'lucide-vue-next';

const page = usePage<SharedData>();

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

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];

</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 bg-background text-foreground">
            <div class="flex flex-1 flex-col overflow-hidden">
                <main class="flex-1 overflow-auto p-4 md:p-6">
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
