<script setup lang="ts">
import AppLayout from "@/layouts/AppLayout.vue";
import Cards from '@/pages/modules/employees/Cards.vue';
import ListEmployees from '@/pages/modules/employees/ListEmployees.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import ProgressBar from '@/components/ProgressBar.vue'
import { ref, } from 'vue'
import type { Employee } from '@/types/Employees';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button';

import {
    HardHat,
    PlusCircle,
    Import, UploadIcon
} from 'lucide-vue-next';
import type { BreadcrumbItem } from "@/types";
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from "@/components/ui/tooltip"

// Composables e serviços
const page = usePage()

// Estado do componente
const employees = ref<Employee[]>(page.props.employees?.data ?? []);
const dialogImport = ref(false);
const importFile = ref<File | null>(null);
console.log(employees.value)
// Breadcrumbs para navegação
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Funcionários', href: '/funcionarios' },
];

const importFileUpload = (event: Event) => {
    const input = event.target as HTMLInputElement;
    console.log('Input file changed:', input);
    if (input.files && input.files[0]) {
        importFile.value = input.files[0];
        console.log('Arquivo selecionado:', importFile.value);
    }
};

</script>

<template>
    <!-- Define o título da página -->
    <Head title='Funcionários'/>
    <!-- Layout principal da aplicação com breadcrumbs -->
    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="mx-auto w-full py-8">
            <!-- Barra de progresso para uploads e outras operações -->
            <ProgressBar :progress="progressbar" :visible="progressbar > 0" />
            <Dialog v-model:open="dialogImport">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Importar Funcionários</DialogTitle>
                        <DialogDescription>
                            Baixe o template CSV, preencha com os dados e faça o upload para importar os funcionários.
                        </DialogDescription>
                    </DialogHeader>

                    <div>
                        <!-- Área de download do template -->
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">Template de importação</p>
                                <p class="text-sm text-gray-600">Formato CSV com colunas esperadas pelo sistema.</p>
                            </div>
                            <a href="/funcionarios/template.csv" class="btn btn-outline flex items-center" download>
                                <Import class="w-4 h-4 mr-2" />
                                Baixar template
                            </a>
                        </div>

                        <!-- Área de upload -->
                        <label class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white btn-primary hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 cursor-pointer">
                            <UploadIcon class="h-4 w-4 mr-1" />
                            Selecionar arquivos
                            <input type="file" class="hidden" @change="importFileUpload" />
                        </label>
                    </div>

                    <DialogFooter>
                        <Button @click="router.post('/funcionarios/importar-csv', { importFile}, {forceFormData: true})" class="btn btn-primary flex items-center">
                            Enviar arquivo
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
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

                    <div class="flex justify-between items-center">
                        <Cards :all-employees="employees" />
                        <div class="flex gap-4">
                            <button @click="dialogImport = !dialogImport" class="btn btn-primary flex items-center">
                                <Import class="w-4 h-4 mr-2"/>
                                Importar
                            </button>
                            <button
                                @click="router.get('/funcionarios/create')"
                                class="btn btn-primary flex items-center"
                            >
                                <PlusCircle class="w-4 h-4 mr-2"/>
                                Novo Funcionário
                            </button>
                        </div>

                    </div>
                </div>
            </header>
            <!-- Seção de listagem de funcionários -->
            <ListEmployees :listEmployees="employees"
            />

        </main>
    </AppLayout>
</template>
