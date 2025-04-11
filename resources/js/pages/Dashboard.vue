<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { useNotification } from '@/composables/useNotification';
import { ref, computed, onMounted } from 'vue';
import {
    LayoutDashboard,
    Search,
    BarChart2,
    FileText,
    Calendar,
    CreditCard,
    Package,
    TrendingUp,
    TrendingDown,
    DollarSign,
    PlusCircle,
    Download,
    Sun,
    Moon,
    Clock,
    Edit,
    Trash2,
    Briefcase,
    Smartphone,
    Headphones,
    Monitor,
    Cpu,
    ShoppingCart,
    AlertCircle,
    CheckCircle,
    Laptop,
    Watch,
    Mouse,
} from 'lucide-vue-next';

// Theme state
const isDarkMode = ref(false);
const page = usePage<SharedData>();
const user = page.props.auth.user as User;

// Stats data
const stats = ref([
    {
        title: 'Total Revenue',
        value: '$24,345',
        icon: DollarSign,
        bgColor: 'bg-blue-100 dark:bg-blue-900',
        iconColor: 'text-blue-600 dark:text-blue-400',
        trend: {
            value: '+12%',
            icon: TrendingUp,
            bgColor: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400',
        },
        comparison: 'previous month',
    },
    {
        title: 'New Orders',
        value: '450',
        icon: ShoppingCart,
        bgColor: 'bg-emerald-100 dark:bg-emerald-900',
        iconColor: 'text-emerald-600 dark:text-emerald-400',
        trend: {
            value: '+4%',
            icon: TrendingUp,
            bgColor: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400',
        },
        comparison: 'previous week',
    },
    {
        title: 'Customers',
        value: '2,550',
        icon: ShoppingCart,
        bgColor: 'bg-purple-100 dark:bg-purple-900',
        iconColor: 'text-purple-600 dark:text-purple-400',
        trend: {
            value: '+8%',
            icon: TrendingUp,
            bgColor: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400',
        },
        comparison: 'previous month',
    },
    {
        title: 'Pending Invoices',
        value: '5',
        icon: FileText,
        bgColor: 'bg-amber-100 dark:bg-amber-900',
        iconColor: 'text-amber-600 dark:text-amber-400',
        trend: {
            value: '-2%',
            icon: TrendingDown,
            bgColor: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-400',
        },
        comparison: 'previous week',
    },
]);

// Chart period
const selectedPeriod = ref('month');

// Top products
const topProducts = ref([
    {
        name: 'Laptop Pro X1',
        category: 'Electronics',
        sales: '$12,500',
        percentage: 85,
        icon: Laptop,
    },
    {
        name: 'Wireless Earbuds',
        category: 'Audio',
        sales: '$8,200',
        percentage: 65,
        icon: Headphones,
    },
    {
        name: 'Smart Watch V3',
        category: 'Wearables',
        sales: '$6,700',
        percentage: 50,
        icon: Watch,
    },
    {
        name: 'Ultra HD Monitor',
        category: 'Electronics',
        sales: '$5,400',
        percentage: 40,
        icon: Monitor,
    },
    {
        name: 'Gaming Mouse',
        category: 'Accessories',
        sales: '$3,800',
        percentage: 30,
        icon: Mouse,
    },
]);

// Recent transactions
const recentTransactions = ref([
    {
        id: 'INV-1234',
        customer: 'John Smith',
        date: 'Today, 13:45',
        amount: '$1,250.00',
        status: 'Completed',
        statusClass: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400',
    },
    {
        id: 'INV-1233',
        customer: 'Alice Johnson',
        date: 'Today, 10:30',
        amount: '$890.50',
        status: 'Processing',
        statusClass: 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-400',
    },
    {
        id: 'INV-1232',
        customer: 'Robert Brown',
        date: 'Yesterday, 16:20',
        amount: '$1,890.00',
        status: 'Completed',
        statusClass: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400',
    },
    {
        id: 'INV-1231',
        customer: 'Emily Davis',
        date: 'Yesterday, 09:15',
        amount: '$450.25',
        status: 'Failed',
        statusClass: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-400',
    },
    {
        id: 'INV-1230',
        customer: 'Michael Wilson',
        date: '23 May, 2023',
        amount: '$2,500.00',
        status: 'Completed',
        statusClass: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400',
    },
]);

// Tasks
const tasks = ref([
    {
        title: 'Update product inventory',
        completed: false,
        priority: 'High',
        priorityClass: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-400',
        dueDate: 'Today',
        assignee: { name: 'John Doe', initials: 'JD' },
        timeLeft: '3h',
    },
    {
        title: 'Review new customer applications',
        completed: true,
        priority: 'Medium',
        priorityClass: 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-400',
        dueDate: 'Today',
        assignee: { name: 'Alice Smith', initials: 'AS' },
        timeLeft: '5h',
    },
    {
        title: 'Prepare monthly financial report',
        completed: false,
        priority: 'High',
        priorityClass: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-400',
        dueDate: 'Tomorrow',
        assignee: { name: 'Robert Johnson', initials: 'RJ' },
        timeLeft: '1d',
    },
    {
        title: 'Schedule meeting with suppliers',
        completed: false,
        priority: 'Low',
        priorityClass: 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-400',
        dueDate: 'Next week',
        assignee: { name: 'Emily Brown', initials: 'EB' },
        timeLeft: '5d',
    },
]);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const { adicionar } = useNotification();

function testarNotificacao() {
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
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-1 flex-col overflow-hidden">
                <!-- Page Content -->
                <main class="flex-1 overflow-auto p-4 md:p-6">
                    <!-- Welcome Banner -->
                    <div class="relative mb-6 overflow-hidden rounded-lg bg-gradient-to-r from-emerald-500 to-teal-500 shadow-md">
                        <div class="absolute top-0 right-0 opacity-10">
                            <LayoutDashboard size="180" class="text-white" />
                        </div>
                        <div class="relative z-10 p-6 md:p-8">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div>
                                    <h1 class="mb-2 text-2xl font-bold text-white md:text-3xl">Welcome back, {{ user.name }}!</h1>
                                    <p class="text-emerald-50">Here's what's happening with your business today.</p>
                                </div>
                                <div class="mt-4 md:mt-0">
                                    <button
                                        @click="testarNotificacao"
                                        class="flex items-center rounded-lg bg-white px-4 py-2 font-medium text-emerald-600 shadow-sm transition-colors hover:bg-emerald-50"
                                    >
                                        <Download size="16" class="mr-2" />
                                        Download Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                        <div
                            v-for="(stat, index) in stats"
                            :key="index"
                            class="rounded-lg border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
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
                            <h3 class="mb-1 text-sm font-medium text-gray-500 dark:text-gray-400">{{ stat.title }}</h3>
                            <p class="text-2xl font-bold dark:text-white">{{ stat.value }}</p>
                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                <span>vs. {{ stat.comparison }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
                        <!-- Sales Chart -->
                        <div class="rounded-lg border border-gray-100 bg-white p-6 shadow-sm lg:col-span-2 dark:border-gray-700 dark:bg-gray-800">
                            <div class="mb-6 flex items-center justify-between">
                                <h2 class="text-lg font-medium dark:text-white">Sales Overview</h2>
                                <div class="flex space-x-2">
                                    <button
                                        v-for="(period, index) in ['Day', 'Week', 'Month', 'Year']"
                                        :key="index"
                                        class="rounded-md px-3 py-1 text-xs transition-colors"
                                        :class="
                                            selectedPeriod === period.toLowerCase()
                                                ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300'
                                                : 'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700'
                                        "
                                        @click="selectedPeriod = period.toLowerCase()"
                                    >
                                        {{ period }}
                                    </button>
                                </div>
                            </div>
                            <div class="relative h-64">
                                <!-- Placeholder for chart -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="flex h-full w-full items-center justify-center rounded-lg bg-gray-50 dark:bg-gray-700">
                                        <div class="text-center">
                                            <BarChart2 size="48" class="mx-auto text-gray-300 dark:text-gray-600" />
                                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Sales chart will render here</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top Products -->
                        <div class="rounded-lg border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="mb-6 flex items-center justify-between">
                                <h2 class="text-lg font-medium dark:text-white">Top Products</h2>
                                <button class="text-sm text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400">View All</button>
                            </div>
                            <div class="space-y-5">
                                <div v-for="(product, index) in topProducts" :key="index" class="flex items-center">
                                    <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700">
                                        <component :is="product.icon" size="18" class="text-gray-500 dark:text-gray-400" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="truncate text-sm font-medium dark:text-white">{{ product.name }}</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ product.category }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium dark:text-white">{{ product.sales }}</p>
                                        <div class="mt-1 h-1 w-16 rounded-full bg-gray-100 dark:bg-gray-700">
                                            <div class="h-1 rounded-full bg-emerald-500" :style="{ width: product.percentage + '%' }"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions & Tasks -->
                    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <!-- Recent Transactions -->
                        <div class="rounded-lg border border-gray-100 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-center justify-between border-b border-gray-100 p-6 dark:border-gray-700">
                                <h2 class="text-lg font-medium dark:text-white">Recent Transactions</h2>
                                <button class="text-sm text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400">View All</button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                                            <th class="px-6 py-3">ID</th>
                                            <th class="px-6 py-3">Customer</th>
                                            <th class="px-6 py-3">Date</th>
                                            <th class="px-6 py-3">Amount</th>
                                            <th class="px-6 py-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                        <tr
                                            v-for="(transaction, index) in recentTransactions"
                                            :key="index"
                                            class="hover:bg-gray-50 dark:hover:bg-gray-700"
                                        >
                                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap dark:text-white">
                                                {{ transaction.id }}
                                            </td>
                                            <td class="px-6 py-4 text-sm whitespace-nowrap dark:text-gray-300">
                                                {{ transaction.customer }}
                                            </td>
                                            <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                {{ transaction.date }}
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap dark:text-white">
                                                {{ transaction.amount }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="rounded-full px-2 py-1 text-xs font-medium" :class="transaction.statusClass">
                                                    {{ transaction.status }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="border-t border-gray-100 p-4 text-center dark:border-gray-700">
                                <button class="text-sm text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400">Load more</button>
                            </div>
                        </div>

                        <!-- Tasks -->
                        <div class="rounded-lg border border-gray-100 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-center justify-between border-b border-gray-100 p-6 dark:border-gray-700">
                                <h2 class="text-lg font-medium dark:text-white">Tasks</h2>
                                <button class="flex items-center text-sm text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400">
                                    <PlusCircle size="14" class="mr-1" />
                                    Add Task
                                </button>
                            </div>
                            <div class="p-6">
                                <div class="mb-4 flex items-center justify-between">
                                    <div class="flex space-x-2">
                                        <button
                                            v-for="(filter, index) in ['All', 'In Progress', 'Completed']"
                                            :key="index"
                                            class="rounded-md px-3 py-1 text-xs transition-colors"
                                            :class="
                                                selectedTaskFilter === filter.toLowerCase()
                                                    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300'
                                                    : 'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700'
                                            "
                                            @click="selectedTaskFilter = filter.toLowerCase()"
                                        >
                                            {{ filter }}
                                        </button>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ completedTasks }}/{{ totalTasks }} completed</div>
                                </div>

                                <div class="space-y-3">
                                    <div
                                        v-for="(task, index) in tasks"
                                        :key="index"
                                        class="rounded-lg border border-gray-100 p-3 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700"
                                    >
                                        <div class="flex items-center">
                                            <input
                                                type="checkbox"
                                                :checked="task.completed"
                                                @change="toggleTask(index)"
                                                class="h-4 w-4 rounded border-gray-300 text-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700"
                                            />
                                            <span
                                                class="ml-3 flex-1 text-sm dark:text-white"
                                                :class="{ 'text-gray-400 line-through dark:text-gray-500': task.completed }"
                                            >
                                                {{ task.title }}
                                            </span>
                                            <span class="rounded-full px-2 py-1 text-xs" :class="task.priorityClass">
                                                {{ task.priority }}
                                            </span>
                                        </div>
                                        <div class="mt-2 ml-7 flex items-center justify-between">
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Due {{ task.dueDate }}</div>
                                            <div class="flex items-center">
                                                <div
                                                    class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-xs text-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                                >
                                                    {{ task.assignee.initials }}
                                                </div>
                                                <Clock size="12" class="ml-2 text-gray-400" />
                                                <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">{{ task.timeLeft }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Status -->
                    <div class="mb-6 rounded-lg border border-gray-100 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center justify-between border-b border-gray-100 p-6 dark:border-gray-700">
                            <h2 class="text-lg font-medium dark:text-white">Inventory Status</h2>
                            <div class="flex space-x-2">
                                <button class="text-sm text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400">Export</button>
                                <button class="flex items-center rounded-md bg-emerald-500 px-3 py-1 text-sm text-white hover:bg-emerald-600">
                                    <PlusCircle size="14" class="mr-1" />
                                    Add Product
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                                        <th class="px-6 py-3">Product</th>
                                        <th class="px-6 py-3">Category</th>
                                        <th class="px-6 py-3">Stock</th>
                                        <th class="px-6 py-3">Price</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <tr v-for="(product, index) in inventoryProducts" :key="index" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700">
                                                    <component :is="product.icon" size="18" class="text-gray-500 dark:text-gray-400" />
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium dark:text-white">{{ product.name }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ product.id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap dark:text-gray-300">
                                            {{ product.category }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="mr-2 h-1.5 w-16 rounded-full bg-gray-100 dark:bg-gray-700">
                                                    <div
                                                        class="h-1.5 rounded-full"
                                                        :class="product.stockLevel.color"
                                                        :style="{ width: product.stockLevel.percentage + '%' }"
                                                    ></div>
                                                </div>
                                                <span class="text-sm dark:text-white">{{ product.stock }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium whitespace-nowrap dark:text-white">
                                            {{ product.price }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="rounded-full px-2 py-1 text-xs font-medium" :class="product.statusClass">
                                                {{ product.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                            <div class="flex space-x-2">
                                                <button class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                                    <Edit size="16" />
                                                </button>
                                                <button class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                                    <Trash2 size="16" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-100 p-4 dark:border-gray-700">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Showing 1 to 5 of 24 entries</div>
                            <div class="flex space-x-1">
                                <button
                                    class="rounded-md px-3 py-1 text-sm text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700"
                                >
                                    Previous
                                </button>
                                <button
                                    class="rounded-md bg-emerald-100 px-3 py-1 text-sm text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300"
                                >
                                    1
                                </button>
                                <button
                                    class="rounded-md px-3 py-1 text-sm text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700"
                                >
                                    2
                                </button>
                                <button
                                    class="rounded-md px-3 py-1 text-sm text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700"
                                >
                                    3
                                </button>
                                <button
                                    class="rounded-md px-3 py-1 text-sm text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700"
                                >
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </AppLayout>
</template>
