<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    BarChart2,
    FileCheck,
    Users,
    TrendingUp,
    TrendingDown,
    UserPlus,
    Award,
    Clock,
    Calendar,
    UserCheck,
} from 'lucide-vue-next';

const stats = ref([
    {
        title: 'Total de Funcionários',
        value: '245',
        icon: Users,
        bgColor: 'bg-emerald-100 dark:bg-emerald-900',
        iconColor: 'text-emerald-600 dark:text-emerald-400',
        trend: {
            value: '+5%',
            icon: TrendingUp,
            bgColor: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400',
        },
        comparison: 'trimestre anterior',
    },
    {
        title: 'Novas Contratações',
        value: '18',
        icon: UserPlus,
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
        title: 'Taxa de Retenção',
        value: '94%',
        icon: UserCheck,
        bgColor: 'bg-purple-100 dark:bg-purple-900',
        iconColor: 'text-purple-600 dark:text-purple-400',
        trend: {
            value: '+2%',
            icon: TrendingUp,
            bgColor: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400',
        },
        comparison: 'ano anterior',
    },
    {
        title: 'Avaliações Pendentes',
        value: '12',
        icon: FileCheck,
        bgColor: 'bg-amber-100 dark:bg-amber-900',
        iconColor: 'text-amber-600 dark:text-amber-400',
        trend: {
            value: '-3%',
            icon: TrendingDown,
            bgColor: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-400',
        },
        comparison: 'mês anterior',
    },
]);

const selectedPeriod = ref('month');

const topEmployees = ref([
    { name: 'Ana Silva', department: 'Desenvolvimento', metric: '98%', percentage: 98, icon: Award },
    { name: 'Carlos Mendes', department: 'Marketing', metric: '95%', percentage: 95, icon: Award },
    { name: 'Juliana Costa', department: 'Vendas', metric: '92%', percentage: 92, icon: Award },
    { name: 'Roberto Alves', department: 'Suporte', metric: '90%', percentage: 90, icon: Award },
    { name: 'Fernanda Lima', department: 'RH', metric: '88%', percentage: 88, icon: Award },
]);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];

</script>

<template>
    <Head title="Dashboard de Funcionários" />
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
                                <h2 class="text-lg font-medium">Presença e Produtividade</h2>
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
                                            <p class="mt-2 text-sm text-muted-foreground">Gráfico de produtividade será exibido aqui</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border bg-card p-6 shadow-sm">
                            <div class="mb-6 flex items-center justify-between">
                                <h2 class="text-lg font-medium">Funcionários em Destaque</h2>
                            </div>
                            <div class="space-y-5">
                                <div v-for="(employee, index) in topEmployees" :key="index" class="flex items-center">
                                    <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <component :is="employee.icon" size="18" class="text-muted-foreground" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="truncate text-sm font-medium">{{ employee.name }}</h4>
                                        <p class="text-xs text-muted-foreground">{{ employee.department }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium">{{ employee.metric }}</p>
                                        <div class="mt-1 h-1 w-16 rounded-full bg-muted">
                                            <div class="h-1 rounded-full bg-emerald-500" :style="{ width: employee.percentage + '%' }"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div class="rounded-lg border bg-card p-6 shadow-sm">
                            <div class="mb-6 flex items-center justify-between">
                                <h2 class="text-lg font-medium">Horas Trabalhadas por Departamento</h2>
                            </div>
                            <div class="relative h-64">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="flex h-full w-full items-center justify-center rounded-lg bg-muted">
                                        <div class="text-center">
                                            <Clock size="48" class="mx-auto text-muted-foreground" />
                                            <p class="mt-2 text-sm text-muted-foreground">Gráfico de horas trabalhadas será exibido aqui</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border bg-card p-6 shadow-sm">
                            <div class="mb-6 flex items-center justify-between">
                                <h2 class="text-lg font-medium">Próximas Avaliações</h2>
                            </div>
                            <div class="space-y-4">
                                <div v-for="i in 4" :key="i" class="flex items-center justify-between border-b pb-3 last:border-0">
                                    <div class="flex items-center">
                                        <div class="mr-3 h-8 w-8 rounded-full bg-muted"></div>
                                        <div>
                                            <p class="text-sm font-medium">Funcionário {{ i }}</p>
                                            <p class="text-xs text-muted-foreground">Avaliação de Desempenho</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <Calendar size="14" class="mr-1 text-muted-foreground" />
                                        <span class="text-xs">{{ new Date().toLocaleDateString() }}</span>
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
