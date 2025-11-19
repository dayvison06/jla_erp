<script setup lang="ts">
import AppLayout from "@/layouts/AppLayout.vue";
import Cards from '@/pages/modules/employees/Cards.vue';
import ListEmployees from '@/pages/modules/employees/ListEmployees.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { debouncedWatch} from "@vueuse/core";
import { useToast } from '@/composables/useToast';
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
    Import,
    ChevronDown,
    TableProperties,
    LayoutGrid,
    Filter,
    FileSpreadsheet,
    List,
    CircleEllipsis,
    UserRoundX
} from 'lucide-vue-next';
import type { BreadcrumbItem } from "@/types";
import { Input } from '@/components/ui/input';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';
import FilterSidebar from '@/components/FilterSidebar.vue';

// Composables e serviços
const page = usePage()
const { showToast } = useToast();

// Estado do componente
const selectedEmployees = ref<number[]>([])
const employees = ref<Employee[]>(page.props.employees ?? []);
const viewMode = ref<'list' | 'grid'>('list');
const dialogImport = ref(false);
const importFile = ref<File | null>(null);
const searchQuery = ref('');
const filterMode = ref(false);
const itemsPerPage = ref(10);
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

/**
 * Busca funcionários com base nos filtros de busca e status.
 * @returns {void}
 */
function searchEmployees() {
    router.get('/funcionarios', {
        search: searchQuery.value,
        per_page: itemsPerPage.value, // Adiciona filtro para quantidade por página
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['employees'],
        onSuccess: (page) => {
            employees.value = page.props.employees.data;
            if (employees.value.length === 0) {
                showToast('Nenhum funcionário encontrado', 'warning', 'A busca não retornou resultados.');
                router.get('/funcionarios', {}, {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['employees'],
                });
            }
        }
    });
}

/**
 * Observa mudanças nos filtros de busca e status para atualizar a lista de funcionários.
 */
debouncedWatch([searchQuery], () => {
    console.log('Filtros alterados, buscando funcionários...');
    // Aqui poderia implementar lógica adicional se necessário
    searchEmployees()

}, { debounce: 800 });
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
                                <p class="text-sm text-gray-600">Formato XLSX (Excel) com colunas esperadas pelo sistema.</p>
                                <a href="/funcionarios/download/template" target="_blank" rel="noopener" download class="flex text-sm items-center text-secondary font-medium mt-2">
                                    <Import class="w-4 h-4 mr-2" />
                                    Baixar template
                                </a>
                            </div>
                        </div>

                        <!-- Área de upload -->
                        <div class="grid w-full max-w-sm items-center gap-1.5 mt-6">
                            <div class="flex gap-2 items-center">
                                <TableProperties class="w-4 h-4"/>
                                <Label for="xlsx" class="text-sm">Planilha</Label>
                            </div>
                            <Input type="file" class=" " @change="importFileUpload" />
                        </div>
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
                           <Button @click="dialogImport = !dialogImport" class="btn-primary-v2">
                               <Import class="w-4 h-4" />
                               Importar
                           </Button>
                            <Button
                                @click="router.get('/funcionarios/criar')"
                                class="btn btn-primary flex items-center"
                            >
                                <PlusCircle class="w-4 h-4 mr-2"/>
                                Novo Funcionário
                            </Button>
                        </div>

                    </div>
                </div>
            </header>
            <!-- Filters and Search -->
            <section class="rounded-t-lg p-2 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-primary w-4 h-4" />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Buscar por nome ou CPF..."
                                class="w-full pl-10 pr-4 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <Button class="btn-primary flex items-center gap-2" @click="filterMode = !filterMode">
                            <Filter class="w-4 h-4" />
                        </Button>
                        <FilterSidebar
                            v-if="filterMode"
                            @close="filterMode = false"
                            @update="updateTask"
                            @delete="deleteTask"
                            @duplicate="duplicateTask"
                        />
                        <Button
                            @click="exportSelected"
                            class="flex items-center gap-2 bg-gray-100 text-gray-700 rounded-lg border border-gray-300 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-200"
                            :disabled="selectedEmployees.length === 0"
                        >
                            <FileSpreadsheet class="w-4 h-4" />
                            Exportar
                        </Button>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button
                                    @click="exportSelected"
                                    class="flex items-center gap-2 btn-primary"
                                    :disabled="selectedEmployees.length === 0"
                                >
                                    <CircleEllipsis class="w-4 h-4" />
                                    Ações
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="start" class="min-w-24">
                                <DropdownMenuItem>
                                    <HardHat class="w-4 h-4" />
                                    Ficha de funcionário
                                </DropdownMenuItem>
                                <DropdownMenuItem>
                                    <UserRoundX class="w-4 h-4" />
                                    Desligar funcionário
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                    <div class="flex items-center gap-2 justify-end md:col-span-2">
                        <div class="border rounded-md p-0.5 mr-2 flex gap-1">
                            <Button
                                data-slot="button"
                                :class="[
                      'inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium transition-[color,box-shadow] disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*=\'size-\'])]:size-4 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive cursor-pointer rounded-md has-[>svg]:px-2.5 h-7 px-2',
                      viewMode === 'list' ? 'bg-primary text-primary-foreground shadow-xs hover:bg-primary/90' : 'hover:bg-accent hover:text-accent-foreground bg-gray-100 text-gray-700'
                    ]"
                                @click="viewMode = 'list'"
                            >
                                <List class="h-4 w-4" />
                            </Button>
                            <Button
                                data-slot="button"
                                :class="[
                      'inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium transition-[color,box-shadow] disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*=\'size-\'])]:size-4 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive cursor-pointer rounded-md has-[>svg]:px-2.5 h-7 px-2',
                      viewMode === 'grid' ? 'bg-primary text-primary-foreground shadow-xs hover:bg-primary/90' : 'hover:bg-accent hover:text-accent-foreground bg-gray-100 text-gray-700'
                    ]"
                                @click="viewMode = 'grid'"
                            >
                                <LayoutGrid class="h-4 w-4" />
                            </Button>
                        </div>
                        <label
                            data-slot="label"
                            class="font-medium select-none group-data-[disabled=true]:pointer-events-none group-data-[disabled=true]:opacity-50 peer-disabled:cursor-not-allowed peer-disabled:opacity-50 text-xs text-muted-foreground"
                        >
                            Por página:
                        </label>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button
                                    type="button"
                                    class="flex items-center justify-between rounded-md border bg-background"
                                >
                                    <span class="text-primary">{{ itemsPerPage }}</span>
                                    <ChevronDown class="ml-2 h-4 w-4 text-primary" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="min-w-24">
                                <DropdownMenuItem @click="itemsPerPage = 10; searchEmployees()">10</DropdownMenuItem>
                                <DropdownMenuItem @click="itemsPerPage = 25; searchEmployees()">25</DropdownMenuItem>
                                <DropdownMenuItem @click="itemsPerPage = 50; searchEmployees()">50</DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>
            </section>
            <!-- Seção de listagem de funcionários -->
            <ListEmployees :listEmployees="employees"
                           :viewMode="viewMode"
                           @update:selectedEmployees="selectedEmployees = $event"
             />

        </main>
    </AppLayout>
</template>
