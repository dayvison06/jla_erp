<script setup lang="ts">
import AppLayout from "@/layouts/AppLayout.vue";
import Cards from '@/pages/modules/employees/Cards.vue';
import ListEmployees from '@/pages/modules/employees/ListEmployees.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import ProgressBar from '@/components/ProgressBar.vue'
import { ref, } from 'vue'
import type { Employee } from '@/types/Employees';

import {
    PlusIcon,
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
        <main class="container mx-auto px-4 py-8">
            <!-- Barra de progresso para uploads e outras operações -->
            <ProgressBar :progress="progressbar" :visible="progressbar > 0" />

            <!-- Cabeçalho do módulo, visível apenas na listagem -->
            <header v-if="!showEmployeeForm" class="text-black mb-6 p-6 rounded-lg shadow">
                <div class="mx-auto flex items-start mb-4">
                    <!-- Título e descrição do módulo -->
                    <div class="flex flex-col flex-1 gap-4">
                        <div class="flex items-center gap-2">
                            <Users class="w-6 h-6"/>
                            <h1 class="text-2xl font-bold mb-2">Funcionários</h1>
                        </div>
                        <p class="text-gray-600 text-sm">
                            O módulo de funcionários permite cadastrar, editar e controlar colaboradores da empresa em um só lugar,
                            facilitando a gestão de dados, permissões e status de cada funcionário.
                        </p>
                    </div>
                </div>
                <!-- Ações do módulo -->
                <div class="flex items-center gap-3">
                    <!-- Botão para adicionar novo funcionário -->
                    <button
                        @click="router.get('/funcionarios/criar')"
                        class="flex items-center px-3 py-1.5 btn-primary text-white rounded-md "
                    >
                        <PlusIcon class="w-5 h-5 mr-2"/>
                        Novo Funcionário
                    </button>
                </div>
            </header>
            <Cards :all-employees="employees"
            />
            <!-- Seção de listagem de funcionários -->
            <ListEmployees :listEmployees="employees"
            />

        </main>
    </AppLayout>
</template>
