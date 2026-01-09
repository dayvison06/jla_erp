<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import InfoTotalEmployees from '@/pages/modules/employees/InfoTotalEmployees.vue';
import ListEmployees from '@/pages/modules/employees/ListEmployees.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { debouncedWatch } from '@vueuse/core';
import { showToast } from '@/composables/useToast';
import ProgressBar from '@/components/ProgressBar.vue';
import { ref, computed } from 'vue';
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
    Search,
    Loader
} from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import { Input } from '@/components/ui/input';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import FilterSidebar from '@/components/FilterSidebar.vue';
import axios from 'axios';
import { Label } from '@/components/ui/label';

// Composables e serviços
const page = usePage();

// Estado do componente
const selectedEmployees = ref<number[]>([]);
const employees = computed(() => page.props.employees);
const viewMode = ref<'list' | 'grid'>('list');
const jobRoles = computed(() => page.props.job_roles);
const departments = computed(() => page.props.departments);
const dialogImport = ref(false);
const importFile = ref<File | null>(null);
const currentFilters = computed(() => page.props.filters as Record<string, any> || {});
const activeFilterCount = computed(() => {
    const keys = ['status', 'job_role', 'department'];
    let count = 0;
    keys.forEach(key => {
        if (currentFilters.value[key] && currentFilters.value[key].toString().trim() !== '') {
             const vals = currentFilters.value[key].toString().split(',');
             count += vals.length;
        }
    });
    return count;
});
const searchQuery = ref(currentFilters.value.query || '');
const searchResults = ref(false);
const listResults = ref<Employee[]>([]);
const filterMode = ref(false);
const itemsPerPage = ref(Number(currentFilters.value.per_page) || 10);
const loadingSearch = ref(false);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Administrativo', href: '/administrativo' },
    { title: 'Funcionários', href: '/funcionarios' },
];
const progressbar = ref(0);

const showEmployee = (id: number) => {
    router.visit(`/funcionarios/${id}`);
};

const exportSelected = () => {
    if (selectedEmployees.value.length === 0) return;
    // window.open(`/funcionarios/export?ids=${selectedEmployees.value.join(',')}`, '_blank');
    showToast('info', 'Funcionalidade de exportação em desenvolvimento.');
};


const importFileUpload = (event: Event) => {
    const input = event.target as HTMLInputElement;
    console.log('Input file changed:', input);
    if (input.files && input.files[0]) {
        importFile.value = input.files[0];
        console.log('Arquivo selecionado:', importFile.value);
    }
};

function paginateEmployees() {
    router.get('/funcionarios', {
        ...currentFilters.value,
        per_page: itemsPerPage.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
             // Success
        },
        onError: () => {
            showToast('error', 'Erro ao paginar funcionários.');
        }
    });
}

/**
 * Busca funcionários com base nos filtros de busca e status.
 * @returns {void}
 */
async function searchEmployees() {
    // Mantendo a busca "live" para o dropdown, independente dos filtros principais da lista
    loadingSearch.value = true;
    if(searchQuery.value.trim() === '') {
        searchResults.value = false;
        loadingSearch.value = false;
        return;
    }

    axios('/funcionarios/buscar', {
        params: {
            query: searchQuery.value,
        },
    })
        .then((response) => {
            listResults.value = response.data.data; // Paginate returns data inside data
            searchResults.value = true;
            loadingSearch.value = false;
        })
        .catch(() => {
             // Silent fail or toast
             loadingSearch.value = false;
        });
}

const handleApplyFilters = (filters: any[]) => {
    const params: Record<string, any> = {
        per_page: itemsPerPage.value,
        query: searchQuery.value,
    };

    filters.forEach(filter => {
        const checkedOptions = filter.options
            .filter((opt: any) => opt.checked)
            .map((opt: any) => opt.id);
        
        if (checkedOptions.length > 0) {
            params[filter.id] = checkedOptions.join(',');
        }
    });

    router.get('/funcionarios', params, {
        preserveState: true,
        preserveScroll: true,
    });
    filterMode.value = false;
}


const generateEmployeeReports = async () => {
    if (selectedEmployees.value.length === 0) return;

    try {
        const response = await axios.post('/funcionarios/ficha-funcionario', {
            employee_ids: selectedEmployees.value
        }, {
            responseType: 'blob'
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'ficha-funcionarios.pdf');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (error) {
        showToast('error', 'Erro ao gerar ficha de funcionários.');
        console.error(error);
    }
};
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
                                    class="text-foreground mt-2 flex items-center text-sm font-medium underline"
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
                            <Label
                                class="btn-primary mt-3 inline-flex cursor-pointer items-center rounded-md border border-transparent px-4 py-2 text-sm font-medium text-white hover:bg-primary/90 focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:outline-none"
                            >
                                <FileSpreadsheet class="mr-1 h-4 w-4" />
                                Enviar arquivo
                                <Input type="file" class="hidden" @change="importFileUpload" multiple />
                            </Label>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button
                            @click=""
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
                                        <Loader v-if="loadingSearch" class="text-foreground absolute animate-spin top-1/2 right-3 h-4 w-4 -translate-y-1/2 transform" />
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
                        <Button class="btn-primary flex items-center gap-2 relative" @click="filterMode = !filterMode">
                            <Filter class="h-4 w-4" />
                            <span v-if="activeFilterCount > 0" class="absolute -top-2 -right-2 z-50 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white shadow-sm ring-1 ring-white">
                                {{ activeFilterCount }}
                            </span>
                        </Button>
                        <FilterSidebar
                            :open="filterMode"
                            @update:open="filterMode = $event"
                            @apply="handleApplyFilters"
                            :active-filters="currentFilters"
                            :job-roles="jobRoles"
                            :departments="departments"
                        />
                        <Button
                            @click="exportSelected"
                            class="flex cursor-pointer items-center gap-2 rounded-lg border border-border bg-card text-foreground transition-colors hover:bg-muted disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="selectedEmployees.length === 0"
                        >
                            <FileSpreadsheet class="h-4 w-4" />
                            Exportar
                        </Button>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button
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
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                    <div class="flex items-center justify-end gap-2 md:col-span-2">
                        <div class="mr-2 flex gap-1 rounded-md p-0.5">
                            <Button
                                data-slot="button"
                                :class="[
                                    'focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive inline-flex h-7 cursor-pointer items-center justify-center gap-2 rounded-md px-2 text-sm font-medium whitespace-nowrap transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:pointer-events-none disabled:opacity-50 has-[>svg]:px-2.5 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*=\'size-\'])]:size-4',
                                    viewMode === 'list'
                                        ? 'bg-card text-foreground hover:bg-background/90 shadow-xs'
                                        : 'bg-card text-muted-foreground hover:bg-accent hover:text-accent-foreground',
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
                                        ? 'bg-card text-foreground hover:bg-background/90 shadow-xs'
                                        : 'bg-card text-muted-foreground hover:bg-accent hover:text-accent-foreground',
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
                                        paginateEmployees();
                                    "
                                    >10</DropdownMenuItem
                                >
                                <DropdownMenuItem
                                    @click="
                                        itemsPerPage = 25;
                                        paginateEmployees();
                                    "
                                    >25</DropdownMenuItem
                                >
                                <DropdownMenuItem
                                    @click="
                                        itemsPerPage = 50;
                                        paginateEmployees();
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
