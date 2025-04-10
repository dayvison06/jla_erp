<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import {
    LayoutDashboard, ChevronRight, Bell, Search,
    BarChart2, FileText, Calendar, CreditCard,
    Package, TrendingUp, TrendingDown, DollarSign, PlusCircle,
    Download, Sun, Moon, Clock, Edit, Trash2, Briefcase,
    Smartphone, Headphones, Monitor, Cpu, ShoppingCart,
    AlertCircle, CheckCircle, Laptop, Watch, Mouse
} from 'lucide-vue-next';

// Theme state
const isDarkMode = ref(false);
const toggleTheme = () => {
    isDarkMode.value = !isDarkMode.value;
    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
};

// Dropdown states
const isNotificationsOpen = ref(false);

// Notifications
const notifications = ref([
    {
        title: 'New order #1234 received',
        time: '5 minutes ago',
        icon: ShoppingCart,
        bgColor: 'bg-blue-100 dark:bg-blue-900',
        iconColor: 'text-blue-600 dark:text-blue-400'
    },
    {
        title: 'Payment for invoice #5678 received',
        time: '2 hours ago',
        icon: CreditCard,
        bgColor: 'bg-green-100 dark:bg-green-900',
        iconColor: 'text-green-600 dark:text-green-400'
    },
    {
        title: 'Low stock alert for Product X',
        time: '4 hours ago',
        icon: AlertCircle,
        bgColor: 'bg-amber-100 dark:bg-amber-900',
        iconColor: 'text-amber-600 dark:text-amber-400'
    },
    {
        title: 'New customer registered',
        time: '1 day ago',
        icon: ShoppingCart,
        bgColor: 'bg-purple-100 dark:bg-purple-900',
        iconColor: 'text-purple-600 dark:text-purple-400'
    },
    {
        title: 'System update completed',
        time: '2 days ago',
        icon: CheckCircle,
        bgColor: 'bg-emerald-100 dark:bg-emerald-900',
        iconColor: 'text-emerald-600 dark:text-emerald-400'
    }
]);

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
            bgColor: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400'
        },
        comparison: 'previous month'
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
            bgColor: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400'
        },
        comparison: 'previous week'
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
            bgColor: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400'
        },
        comparison: 'previous month'
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
            bgColor: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-400'
        },
        comparison: 'previous week'
    }
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
        icon: Laptop
    },
    {
        name: 'Wireless Earbuds',
        category: 'Audio',
        sales: '$8,200',
        percentage: 65,
        icon: Headphones
    },
    {
        name: 'Smart Watch V3',
        category: 'Wearables',
        sales: '$6,700',
        percentage: 50,
        icon: Watch
    },
    {
        name: 'Ultra HD Monitor',
        category: 'Electronics',
        sales: '$5,400',
        percentage: 40,
        icon: Monitor
    },
    {
        name: 'Gaming Mouse',
        category: 'Accessories',
        sales: '$3,800',
        percentage: 30,
        icon: Mouse
    }
]);

// Recent transactions
const recentTransactions = ref([
    {
        id: 'INV-1234',
        customer: 'John Smith',
        date: 'Today, 13:45',
        amount: '$1,250.00',
        status: 'Completed',
        statusClass: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400'
    },
    {
        id: 'INV-1233',
        customer: 'Alice Johnson',
        date: 'Today, 10:30',
        amount: '$890.50',
        status: 'Processing',
        statusClass: 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-400'
    },
    {
        id: 'INV-1232',
        customer: 'Robert Brown',
        date: 'Yesterday, 16:20',
        amount: '$1,890.00',
        status: 'Completed',
        statusClass: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400'
    },
    {
        id: 'INV-1231',
        customer: 'Emily Davis',
        date: 'Yesterday, 09:15',
        amount: '$450.25',
        status: 'Failed',
        statusClass: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-400'
    },
    {
        id: 'INV-1230',
        customer: 'Michael Wilson',
        date: '23 May, 2023',
        amount: '$2,500.00',
        status: 'Completed',
        statusClass: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400'
    }
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
        timeLeft: '3h'
    },
    {
        title: 'Review new customer applications',
        completed: true,
        priority: 'Medium',
        priorityClass: 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-400',
        dueDate: 'Today',
        assignee: { name: 'Alice Smith', initials: 'AS' },
        timeLeft: '5h'
    },
    {
        title: 'Prepare monthly financial report',
        completed: false,
        priority: 'High',
        priorityClass: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-400',
        dueDate: 'Tomorrow',
        assignee: { name: 'Robert Johnson', initials: 'RJ' },
        timeLeft: '1d'
    },
    {
        title: 'Schedule meeting with suppliers',
        completed: false,
        priority: 'Low',
        priorityClass: 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-400',
        dueDate: 'Next week',
        assignee: { name: 'Emily Brown', initials: 'EB' },
        timeLeft: '5d'
    }
]);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex-1 flex flex-col overflow-hidden bg-gray-50 dark:bg-gray-900">
                <!-- Header -->
                <header class="bg-white dark:bg-gray-800 shadow-sm z-10">
                    <div class="flex items-center justify-between p-4">
                        <div class="flex items-center">
                            <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Dashboard</h1>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="relative">
                                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size="16" />
                                <input
                                    type="text"
                                    placeholder="Search..."
                                    class="pl-10 pr-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:text-white w-48 md:w-64"
                                />
                            </div>

                            <div class="relative">
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

                            <button
                                @click="toggleTheme"
                                class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                <Sun v-if="isDarkMode" size="20" class="text-gray-600 dark:text-gray-300" />
                                <Moon v-else size="20" class="text-gray-600 dark:text-gray-300" />
                            </button>
                        </div>
                    </div>

                    <!-- Breadcrumb -->
                    <div class="px-4 py-2 flex items-center text-sm border-t border-gray-200 dark:border-gray-700">
                        <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-emerald-500 dark:hover:text-emerald-400">Home</a>
                        <ChevronRight size="14" class="mx-2 text-gray-400" />
                        <span class="text-gray-800 dark:text-white font-medium">Dashboard</span>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-auto p-4 md:p-6">
                    <!-- Welcome Banner -->
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-lg shadow-md mb-6 relative overflow-hidden">
                        <div class="absolute right-0 top-0 opacity-10">
                            <LayoutDashboard size="180" class="text-white" />
                        </div>
                        <div class="p-6 md:p-8 relative z-10">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div>
                                    <h1 class="text-white text-2xl md:text-3xl font-bold mb-2">Welcome back, Admin!</h1>
                                    <p class="text-emerald-50">Here's what's happening with your business today.</p>
                                </div>
                                <div class="mt-4 md:mt-0">
                                    <button class="bg-white text-emerald-600 px-4 py-2 rounded-lg shadow-sm hover:bg-emerald-50 transition-colors font-medium flex items-center">
                                        <Download size="16" class="mr-2" />
                                        Download Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                        <div
                            v-for="(stat, index) in stats"
                            :key="index"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700"
                        >
                            <div class="flex items-center justify-between mb-4">
                                <div class="rounded-full p-3" :class="stat.bgColor">
                                    <component :is="stat.icon" size="20" :class="stat.iconColor" />
                                </div>
                                <span
                                    class="text-xs font-medium px-2 py-1 rounded-full"
                                    :class="stat.trend.bgColor"
                                >
              <component :is="stat.trend.icon" size="12" class="inline mr-1" />
              {{ stat.trend.value }}
            </span>
                            </div>
                            <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">{{ stat.title }}</h3>
                            <p class="text-2xl font-bold dark:text-white">{{ stat.value }}</p>
                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                <span>vs. {{ stat.comparison }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        <!-- Sales Chart -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700 lg:col-span-2">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-lg font-medium dark:text-white">Sales Overview</h2>
                                <div class="flex space-x-2">
                                    <button
                                        v-for="(period, index) in ['Day', 'Week', 'Month', 'Year']"
                                        :key="index"
                                        class="text-xs px-3 py-1 rounded-md transition-colors"
                                        :class="selectedPeriod === period.toLowerCase() ?
                  'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300' :
                  'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700'"
                                        @click="selectedPeriod = period.toLowerCase()"
                                    >
                                        {{ period }}
                                    </button>
                                </div>
                            </div>
                            <div class="h-64 relative">
                                <!-- Placeholder for chart -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-full h-full bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                        <div class="text-center">
                                            <BarChart2 size="48" class="mx-auto text-gray-300 dark:text-gray-600" />
                                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Sales chart will render here</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top Products -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-lg font-medium dark:text-white">Top Products</h2>
                                <button class="text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 text-sm">
                                    View All
                                </button>
                            </div>
                            <div class="space-y-5">
                                <div
                                    v-for="(product, index) in topProducts"
                                    :key="index"
                                    class="flex items-center"
                                >
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center mr-3">
                                        <component :is="product.icon" size="18" class="text-gray-500 dark:text-gray-400" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium truncate dark:text-white">{{ product.name }}</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ product.category }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium dark:text-white">{{ product.sales }}</p>
                                        <div class="w-16 h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-1">
                                            <div
                                                class="h-1 rounded-full bg-emerald-500"
                                                :style="{ width: product.percentage + '%' }"
                                            ></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions & Tasks -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Recent Transactions -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                <h2 class="text-lg font-medium dark:text-white">Recent Transactions</h2>
                                <button class="text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 text-sm">
                                    View All
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                    <tr class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium dark:text-white">
                                            {{ transaction.id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm dark:text-gray-300">
                                            {{ transaction.customer }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ transaction.date }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium dark:text-white">
                                            {{ transaction.amount }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                    <span
                        class="px-2 py-1 text-xs font-medium rounded-full"
                        :class="transaction.statusClass"
                    >
                      {{ transaction.status }}
                    </span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-4 border-t border-gray-100 dark:border-gray-700 text-center">
                                <button class="text-sm text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400">
                                    Load more
                                </button>
                            </div>
                        </div>

                        <!-- Tasks -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                <h2 class="text-lg font-medium dark:text-white">Tasks</h2>
                                <button class="text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 text-sm flex items-center">
                                    <PlusCircle size="14" class="mr-1" />
                                    Add Task
                                </button>
                            </div>
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex space-x-2">
                                        <button
                                            v-for="(filter, index) in ['All', 'In Progress', 'Completed']"
                                            :key="index"
                                            class="text-xs px-3 py-1 rounded-md transition-colors"
                                            :class="selectedTaskFilter === filter.toLowerCase() ?
                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300' :
                    'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700'"
                                            @click="selectedTaskFilter = filter.toLowerCase()"
                                        >
                                            {{ filter }}
                                        </button>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ completedTasks }}/{{ totalTasks }} completed
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <div
                                        v-for="(task, index) in tasks"
                                        :key="index"
                                        class="p-3 border border-gray-100 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
                                    >
                                        <div class="flex items-center">
                                            <input
                                                type="checkbox"
                                                :checked="task.completed"
                                                @change="toggleTask(index)"
                                                class="w-4 h-4 text-emerald-500 rounded border-gray-300 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700"
                                            />
                                            <span
                                                class="ml-3 text-sm flex-1 dark:text-white"
                                                :class="{ 'line-through text-gray-400 dark:text-gray-500': task.completed }"
                                            >
                    {{ task.title }}
                  </span>
                                            <span
                                                class="text-xs px-2 py-1 rounded-full"
                                                :class="task.priorityClass"
                                            >
                    {{ task.priority }}
                  </span>
                                        </div>
                                        <div class="ml-7 mt-2 flex items-center justify-between">
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                Due {{ task.dueDate }}
                                            </div>
                                            <div class="flex items-center">
                                                <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs text-gray-600 dark:text-gray-300">
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
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 mb-6">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            <h2 class="text-lg font-medium dark:text-white">Inventory Status</h2>
                            <div class="flex space-x-2">
                                <button class="text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 text-sm">
                                    Export
                                </button>
                                <button class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1 rounded-md text-sm flex items-center">
                                    <PlusCircle size="14" class="mr-1" />
                                    Add Product
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <th class="px-6 py-3">Product</th>
                                    <th class="px-6 py-3">Category</th>
                                    <th class="px-6 py-3">Stock</th>
                                    <th class="px-6 py-3">Price</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr
                                    v-for="(product, index) in inventoryProducts"
                                    :key="index"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center mr-3">
                                                <component :is="product.icon" size="18" class="text-gray-500 dark:text-gray-400" />
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium dark:text-white">{{ product.name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ product.id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm dark:text-gray-300">
                                        {{ product.category }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-16 h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full mr-2">
                                                <div
                                                    class="h-1.5 rounded-full"
                                                    :class="product.stockLevel.color"
                                                    :style="{ width: product.stockLevel.percentage + '%' }"
                                                ></div>
                                            </div>
                                            <span class="text-sm dark:text-white">{{ product.stock }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium dark:text-white">
                                        {{ product.price }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                  <span
                      class="px-2 py-1 text-xs font-medium rounded-full"
                      :class="product.statusClass"
                  >
                    {{ product.status }}
                  </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
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
                        <div class="p-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Showing 1 to 5 of 24 entries
                            </div>
                            <div class="flex space-x-1">
                                <button class="px-3 py-1 rounded-md text-sm text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                                    Previous
                                </button>
                                <button class="px-3 py-1 rounded-md text-sm bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300">
                                    1
                                </button>
                                <button class="px-3 py-1 rounded-md text-sm text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                                    2
                                </button>
                                <button class="px-3 py-1 rounded-md text-sm text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                                    3
                                </button>
                                <button class="px-3 py-1 rounded-md text-sm text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
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
