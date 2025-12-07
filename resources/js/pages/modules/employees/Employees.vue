<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import InfoTotalEmployees from '@/pages/modules/employees/InfoTotalEmployees.vue';
import ListEmployees from '@/pages/modules/employees/ListEmployees.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { debouncedWatch } from '@vueuse/core';
import { showToast } from '@/composables/useToast';
import ProgressBar from '@/components/ProgressBar.vue';
import { ref } from 'vue';
import type { Employee } from '@/types/Employees';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
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
    UserRoundX,
    Search
} from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import { Input } from '@/components/ui/input';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import FilterSidebar from '@/components/FilterSidebar.vue';
import axios from 'axios';

// Composables e serviços
const page = usePage();

// Estado do componente
const selectedEmployees = ref<number[]>([]);
const employees = ref<Employee[]>(page.props.employees ?? []);
const viewMode = ref<'list' | 'grid'>('list');
const dialogImport = ref(false);
const importFile = ref<File | null>(null);
const searchQuery = ref('');
const searchResults = ref(false);
const listResults = ref(<Employee[]>[]);
const filterMode = ref(false);
const itemsPerPage = ref(10);
console.log(employees.value);
// Breadcrumbs para navegação
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Funcionários', href: '/funcionarios' }];

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
async function searchEmployees() {
    if(searchQuery.value.trim() === '') {
        employees.value = page.props.employees ?? [];
        searchResults.value = false;
        return;
    }

    axios('/funcionarios/buscar', {
        params: {
            query: searchQuery.value,
            per_page: itemsPerPage.value,
        },
    })
        .then((response) => {
            listResults.value = response.data;
            searchResults.value = true;
            console.log('Funcionários atualizados:', listResults.value);
            showToast('success', 'Funcionários buscados com sucesso!');
        })
        .catch((error) => {
            showToast('error', 'Erro ao buscar funcionários.');
        });
}

async function showEmployee(id: number) {
    router.get(`/funcionarios/${id}`, {
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            // Check if the employee was found
        },
        onError: () => {
            alert('Error searching for employee. Check the CPF and try again.');
        }
    });

}

async function generateEmployeeReports () {
    if (selectedEmployees.value.length === 0) {
        showToast('warning', 'Nenhum funcionário selecionado para gerar relatórios.');
        return;
    }

    try {
        const response = await axios.post('/funcionarios/ficha-funcionario', {
            employee_ids: selectedEmployees.value,
        }, {
            responseType: 'blob', // Importante para arquivos
        });

        // Cria um link para download do arquivo gerado
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'relatorios_funcionarios.zip'); // Nome do arquivo
        document.body.appendChild(link);
        link.click();
        showToast('success', 'Relatórios gerados com sucesso!');
    } catch (error) {
        console.error('Erro ao gerar relatórios:', error);
        showToast('error', 'Erro ao gerar relatórios dos funcionários.');
    }
}

/**
 * Observa mudanças nos filtros de busca e status para atualizar a lista de funcionários.
 */
debouncedWatch(
    [searchQuery],
    () => {
        console.log('Filtros alterados, buscando funcionários...');
        // Aqui poderia implementar lógica adicional se necessário
        searchEmployees();
    },
    { debounce: 800 },
);
</script>

<template>
    <!-- Define o título da página -->
    <Head title="Funcionários" />
    <!-- Layout principal da aplicação com breadcrumbs -->
    <AppLayout :breadcrumbs="breadcrumbs">
        <main>
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
                                <a
                                    href="/funcionarios/download/template"
                                    target="_blank"
                                    rel="noopener"
                                    download
                                    class="text-secondary mt-2 flex items-center text-sm font-medium"
                                >
                                    <Import class="mr-2 h-4 w-4" />
                                    Baixar template
                                </a>
                            </div>
                        </div>

                        <!-- Área de upload -->
                        <div class="mt-6 grid w-full max-w-sm items-center gap-1.5">
                            <div class="flex items-center gap-2">
                                <TableProperties class="h-4 w-4" />
                                <Label for="xlsx" class="text-sm">Planilha</Label>
                            </div>
                            <Input type="file" class=" " @change="importFileUpload" />
                        </div>
                    </div>

                    <DialogFooter>
                        <Button
                            @click="router.post('/funcionarios/importar-csv', { importFile }, { forceFormData: true })"
                            class="btn btn-primary flex items-center"
                        >
                            Enviar arquivo
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
            <!-- Cabeçalho do módulo, visível apenas na listagem -->
            <header class="default-box mb-8">
                <div class="p-8">
                    <div class="mb-6 flex items-start gap-6">
                        <!-- Ícone com destaque -->
                        <div class="icon-background flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg shadow-md">
                            <HardHat class="h-6 w-6 text-white" />
                        </div>

                        <!-- Título e descrição do módulo -->
                        <div class="min-w-0 flex-1">
                            <h1 class="mb-2 text-2xl font-semibold tracking-tight text-foreground">Funcionários</h1>
                            <p class="text-sm leading-relaxed text-muted-foreground">
                                Gerencie o cadastro completo de colaboradores, controle permissões e acompanhe o status de cada funcionário em uma
                                plataforma centralizada e eficiente.
                            </p>
                        </div>
                    </div>
                    <!-- Separador sutil -->
                    <div class="mb-2 border-t border-muted-foreground"></div>

                    <div class="flex items-center justify-between">
                        <InfoTotalEmployees :all-employees="employees" />
                        <div class="flex gap-4">
                            <Button @click="dialogImport = !dialogImport" class="btn-primary-v2">
                                <Import class="h-4 w-4" />
                                Importar
                            </Button>
                            <Button @click="router.get('/funcionarios/criar')" class="btn btn-primary flex items-center">
                                <PlusCircle class="mr-2 h-4 w-4" />
                                Novo Funcionário
                            </Button>
                        </div>
                    </div>
                </div>
            </header>
            <!-- Filters and Search -->
            <section class="mb-4 rounded-t-lg p-2">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <div class="relative">
                            <DropdownMenu v-model:open="searchResults">
                                <DropdownMenuTrigger disabled class="w-full">
                                    <div class="relative">
                                        <Search class="text-foreground absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform" />
                                        <Input v-model="searchQuery" type="text" placeholder="Buscar por nome ou CPF..." class="pl-8" />
                                    </div>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent class="w-full max-w-sm" align="start">
                                    <DropdownMenuLabel class="text-primary text-sm px-2">Encontrados</DropdownMenuLabel>
                                    <DropdownMenuItem
                                        class="cursor-pointer text-muted-foreground border-t border-muted mt-2"
                                        v-for="item in listResults.data"
                                        :key="item.id"
                                        @click="showEmployee(item.id)"
                                    >
                                        {{ item.name }}
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <Button class="btn-primary flex items-center gap-2" @click="filterMode = !filterMode">
                            <Filter class="h-4 w-4" />
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
                            class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-300 bg-gray-100 text-gray-700 transition-colors hover:bg-gray-200 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="selectedEmployees.length === 0"
                        >
                            <FileSpreadsheet class="h-4 w-4" />
                            Exportar
                        </Button>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button
                                    @click="exportSelected"
                                    class="btn-primary flex items-center gap-2"
                                    :disabled="selectedEmployees.length === 0"
                                >
                                    <CircleEllipsis class="h-4 w-4" />
                                    Ações
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="start" class="min-w-24">
                                <DropdownMenuItem @click="generateEmployeeReports">
                                    <HardHat class="h-4 w-4" />
                                    Ficha de funcionário
                                </DropdownMenuItem>
                                <DropdownMenuItem>
                                    <UserRoundX class="h-4 w-4" />
                                    Desligar funcionário
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                    <div class="flex items-center justify-end gap-2 md:col-span-2">
                        <div class="mr-2 flex gap-1 rounded-md border p-0.5">
                            <Button
                                data-slot="button"
                                :class="[
                                    'focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive inline-flex h-7 cursor-pointer items-center justify-center gap-2 rounded-md px-2 text-sm font-medium whitespace-nowrap transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:pointer-events-none disabled:opacity-50 has-[>svg]:px-2.5 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*=\'size-\'])]:size-4',
                                    viewMode === 'list'
                                        ? 'bg-primary text-primary-foreground hover:bg-primary/90 shadow-xs'
                                        : 'hover:bg-accent hover:text-accent-foreground bg-gray-100 text-gray-700',
                                ]"
                                @click="viewMode = 'list'"
                            >
                                <List class="h-4 w-4" />
                            </Button>
                            <Button
                                data-slot="button"
                                :class="[
                                    'focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive inline-flex h-7 cursor-pointer items-center justify-center gap-2 rounded-md px-2 text-sm font-medium whitespace-nowrap transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:pointer-events-none disabled:opacity-50 has-[>svg]:px-2.5 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*=\'size-\'])]:size-4',
                                    viewMode === 'grid'
                                        ? 'bg-primary text-primary-foreground hover:bg-primary/90 shadow-xs'
                                        : 'hover:bg-accent hover:text-accent-foreground bg-gray-100 text-gray-700',
                                ]"
                                @click="viewMode = 'grid'"
                            >
                                <LayoutGrid class="h-4 w-4" />
                            </Button>
                        </div>
                        <label
                            data-slot="label"
                            class="text-muted-foreground text-xs font-medium select-none group-data-[disabled=true]:pointer-events-none group-data-[disabled=true]:opacity-50 peer-disabled:cursor-not-allowed peer-disabled:opacity-50"
                        >
                            Por página:
                        </label>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button type="button" class="bg-background flex items-center justify-between rounded-md border">
                                    <span class="text-primary">{{ itemsPerPage }}</span>
                                    <ChevronDown class="text-primary ml-2 h-4 w-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="min-w-24">
                                <DropdownMenuItem
                                    @click="
                                        itemsPerPage = 10;
                                        searchEmployees();
                                    "
                                    >10</DropdownMenuItem
                                >
                                <DropdownMenuItem
                                    @click="
                                        itemsPerPage = 25;
                                        searchEmployees();
                                    "
                                    >25</DropdownMenuItem
                                >
                                <DropdownMenuItem
                                    @click="
                                        itemsPerPage = 50;
                                        searchEmployees();
                                    "
                                    >50</DropdownMenuItem
                                >
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>
            </section>
            <!-- Seção de listagem de funcionários -->
            <ListEmployees :listEmployees="employees" :viewMode="viewMode" @update:selectedEmployees="selectedEmployees = $event" />
        </main>
    </AppLayout>
</template>
