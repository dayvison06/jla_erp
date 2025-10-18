<script setup lang="ts">
import AppLayout from "@/layouts/AppLayout.vue";
import Cards from '@/pages/modules/employees/Cards.vue';
import ListEmployees from '@/pages/modules/employees/ListEmployees.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import ProgressBar from '@/components/ProgressBar.vue'
import { ref, } from 'vue'
import type { Employee } from '@/types/Employees';

import {
    HardHat,
    Users,
} from 'lucide-vue-next';
import type { BreadcrumbItem } from "@/types";

// Composables e serviços
const page = usePage()

// Estado do componente
const employees = ref<Employee[]>(page.props.employees?.data ?? []);
console.log(employees.value)
// Breadcrumbs para navegação
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Funcionários', href: '/funcionarios' },
];

</script>

<template>
    <!-- Define o título da página -->
    <Head title='Funcionários'/>
    <!-- Layout principal da aplicação com breadcrumbs -->
    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="mx-auto w-full py-8">
            <!-- Barra de progresso para uploads e outras operações -->
            <ProgressBar :progress="progressbar" :visible="progressbar > 0" />

            <!-- Cabeçalho do módulo, visível apenas na listagem -->
            <header class="default-box mb-8">
                <div class="p-8">
                    <div class="flex items-start gap-6 mb-6">
                        <!-- Ícone com destaque -->
                        <div class="flex-shrink-0 w-12 h-12 bg-primary rounded-lg flex items-center justify-center shadow-md">
                            <HardHat class="w-6 h-6 text-white"/>
                        </div>

                        <!-- Título e descrição do módulo -->
                        <div class="flex-1 min-w-0">
                            <h1 class="text-2xl font-semibold text-gray-900 mb-2 tracking-tight">
                                Funcionários
                            </h1>
                            <p class="text-gray-600 text-base leading-relaxed">
                                Gerencie o cadastro completo de colaboradores, controle permissões e acompanhe o status de cada funcionário em uma plataforma centralizada e eficiente.
                            </p>
                        </div>
                    </div>
                    <!-- Separador sutil -->
                    <div class="border-t border-gray-100 mb-2"></div>

                    <Cards :all-employees="employees" />
                </div>
            </header>
            <!-- Seção de listagem de funcionários -->
            <ListEmployees :listEmployees="employees"
            />

        </main>
    </AppLayout>
</template>
