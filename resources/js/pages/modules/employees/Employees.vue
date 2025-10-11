<script setup lang="ts">
import AppLayout from "@/layouts/AppLayout.vue";
import CreateEmployee from '@/pages/modules/employees/CreateEmployee.vue';
import Cards from '@/pages/modules/employees/Cards.vue';
import ListEmployees from '@/pages/modules/employees/ListEmployees.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';
import ProgressBar from '@/components/ProgressBar.vue'
import { ref, reactive, onMounted } from 'vue'
import type { Employee, RoleHistory, Attachment, Dependent } from '@/types/Employees';
import { debouncedWatch} from '@vueuse/core';
import {
    PlusIcon,
    Cross,
    Banknote,
    FilePenLine,
    Users,
    TrashIcon,
    UploadIcon,
    File,
    UploadCloudIcon,
    XIcon,
    UserIcon,
    Paperclip,
    BriefcaseBusiness,
    MapPinHouse,
    ArrowRight
} from 'lucide-vue-next';
import type { BreadcrumbItem } from "@/types";
import EmployeeCachedDialog from "@/components/EmployeeCachedDialog.vue";
import AttachmentsDisplay from '@/components/AttachmentsDisplay.vue';
import { toast } from 'vue-sonner';

// Composables e serviços
const { showToast } = useToast();
const page = usePage()

// Estado do componente
const employees = ref<Employee[]>(page.props.employees?.data ?? []);
const employee = ref<Employee | null>(page.props.employee || null);

/**
 * Executa ao montar o componente.
 * Se um funcionário for passado como propriedade, carrega seus dados no formulário.
 * Caso contrário, carrega a lista de funcionários.
 */
onMounted( () => {
    if (employee.value) {
        Object.assign(formData, employee.value);
        showEmployeeForm.value = true;
        newEmployee.value = false;
        return;
    }
    loadEmployees();
})

// Breadcrumbs para navegação
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Funcionários', href: '/funcionarios' },
];

// Estados reativos para controle da UI
const newEmployee = ref(false) // Indica se o formulário é para um novo funcionário
const showDeleteModal = ref(false) // Controla a exibição do modal de exclusão
const employeeToDelete = ref<Employee | null>(null) // Funcionário a ser excluído
const isDragging = ref(false) // Indica se um arquivo está sendo arrastado sobre a área de drop
console.log('OBESERVANDO NEW EMPLOYEE', newEmployee.value);

// Métodos
/**
 * Retorna as iniciais de um nome.
 *
 * @param {string} name - O nome completo.
 * @returns {string} As iniciais do nome.
 */
const getInitials = (name: string) => {
    if (!name) return '';
    return name
        .split(' ')
        .map(part => part.charAt(0))
        .join('')
        .substring(0, 2)
        .toUpperCase();
}

/**
 * Salva o formulário atual no localStorage.
 * @returns {void}
 */
function setLocalCacheForm () {
    localStorage.setItem('cachedEmployee', JSON.stringify({ ...formData, attachments: [] }));
}

/**
 * Exibe o diálogo de cache se houver um formulário salvo no localStorage.
 * @returns {void}
 */
function loadLocalCacheFormDialog () {
    const cachedEmployee = localStorage.getItem('cachedEmployee');
    if (!!cachedEmployee) {
        cacheDialog.value = true;
    }
}

/**
 * Carrega o formulário do localStorage e fecha o diálogo de cache.
 * @returns {void}
 */
function handleContinueForm() {
    const cachedEmployee = localStorage.getItem('cachedEmployee');
    Object.assign(formData, JSON.parse(cachedEmployee));
    cacheDialog.value = false;
}

function handleDestroyCacheForm() {
    localStorage.removeItem('cachedEmployee');
    cacheDialog.value = false;
}

/**
 * Carrega a lista de funcionários.
 * @returns {void}
 */
function loadEmployees() {
    router.get('/funcionarios', {}, {
        only: ['employees'],
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
            employees.value = page.props.employees.data;
        }
    });
}

</script>

<template>
    <!-- Define o título da página -->
    <Head title='Funcionários'/>
    <!-- Layout principal da aplicação com breadcrumbs -->
    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="container mx-auto px-4 py-8">
            <!-- Barra de progresso para uploads e outras operações -->
            <ProgressBar :progress="progressbar" :visible="progressbar > 0" />
            <!-- Diálogo para carregar dados do cache -->
            <EmployeeCachedDialog v-if="cacheDialog" @continue="handleContinueForm()" @destroy="handleDestroyCacheForm()"/>

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
                        @click="newEmployeeButtonClick()"
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
