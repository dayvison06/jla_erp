<script setup lang="ts">
/**
 * Componente para gerenciamento de funcionários.
 *
 * Este componente permite listar, cadastrar, editar e excluir funcionários,
 * além de gerenciar seus dados pessoais, documentos, informações de contato,
 * dados bancários, informações contratuais, saúde, dependentes e anexos.
 */

// Importações de componentes e bibliotecas
import AppLayout from "@/layouts/AppLayout.vue";
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';
import ProgressBar from '@/components/ProgressBar.vue'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup, DropdownMenuItem,
    DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';
import { ref, reactive, onMounted } from 'vue'
import type { Employee, RoleHistory, Attachment, Dependent } from '@/types/Employees';
import { debouncedWatch} from '@vueuse/core';
import {
    PlusIcon,
    Cross,
    Banknote,
    FilePenLine,
    Users,
    SearchIcon,
    EditIcon,
    BookUser,
    TrashIcon,
    UploadIcon,
    FileIcon,
    File,
    EyeIcon,
    FileSpreadsheetIcon,
    ImageIcon,
    UploadCloudIcon,
    FileTextIcon,
    XIcon,
    UserIcon,
    FilterIcon,
    DownloadIcon,
    Paperclip,
    BriefcaseBusiness,
    MapPinHouse,
    ShieldBan,
} from 'lucide-vue-next';
import type { BreadcrumbItem } from "@/types";
import AttachmentDialog from '@/components/AttachmentDialog.vue';
import EmployeeCachedDialog from "@/components/EmployeeCachedDialog.vue";
import AttachmentsDisplay from '@/components/AttachmentsDisplay.vue';
import EmployeeManager from '@/components/employees/EmployeeManager.vue';

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
const showEmployeeForm = ref(false) // Controla a exibição do formulário de funcionário
const activeTab = ref('personal') // Aba ativa no formulário de funcionário
const searchQuery = ref('') // Termo de busca para funcionários
const statusFilter = ref('all') // Filtro de status dos funcionários
const showDeleteModal = ref(false) // Controla a exibição do modal de exclusão
const employeeToDelete = ref<Employee | null>(null) // Funcionário a ser excluído
const isDragging = ref(false) // Indica se um arquivo está sendo arrastado sobre a área de drop
const isLoading = ref(false) // Indica se os dados estão sendo carregados
console.log('OBESERVANDO NEW EMPLOYEE', newEmployee.value);

// Objeto reativo para um novo histórico de cargo
const newRoleHistory = reactive<RoleHistory>({
    id: 0,
    role: '',
    department: '',
    start_date: '',
    end_date: null,
    salary: '',
    reason: ''
})

// Objeto reativo para os dados do formulário do funcionário
const formData = reactive<Employee>({
    name: '',
    birth_date: '',
    gender: '',
    civil_state: '',
    nationality: '',
    birthplace: '',
    cnpj: '',
    cpf: '',
    rg: '',
    issuing_agency: '',
    issue_date: '',
    escolarity: '',
    voter_registration: '',
    military_certificate: '',
    mother_name: '',
    father_name: '',
    photo: null,
    status: 'active',

    ctps_number: '',
    ctps_series: '',
    ctps_state: '',
    pis_pasep: '',
    nit: '',
    cnh: '',
    cnh_category: '',
    cnh_expiry: '',
    professional_registration: '',

    postal_code: '',
    street: '',
    number: '',
    complement: '',
    district: '',
    city: '',
    state: '',
    phone: '',
    mobile: '',
    email: '',
    emergency_contact: '',
    emergency_phone: '',

    bank: '',
    agency: '',
    account: '',
    account_type: '',
    pix_key: '',

    role: '',
    department: '',
    contract_type: '',
    admission_date: '',
    termination_date: null,
    salary: '',
    work_schedule: '',
    benefits: [],

    last_exam_date: '',
    next_exam_date: '',
    aso_result: '',
    allergies: '',
    blood_type: '',
    accident_history: '',

    dependents: [],
    attachments: [],
})

console.log('ATTACHMENTS', formData.attachments);

// Dados de referência para o formulário
const tabs = [
    {id: 'personal', name: 'Dados Pessoais', icon: UserIcon},
    {id: 'documents', name: 'Documentos Trabalhistas', icon: BriefcaseBusiness},
    {id: 'contact', name: 'Endereço e Contato', icon: MapPinHouse},
    {id: 'bank', name: 'Dados Bancários', icon: Banknote},
    {id: 'contract', name: 'Informações Contratuais', icon: FilePenLine},
    {id: 'health', name: 'Saúde e Segurança', icon: Cross},
    {id: 'dependents', name: 'Dependentes', icon: Users},
    {id: 'attachments', name: 'Anexos', icon: Paperclip},
]

const states = [
    'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG',
    'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'
]

const banks = [
    {codigo: '001', nome: 'Banco do Brasil'},
    {codigo: '033', nome: 'Santander'},
    {codigo: '104', nome: 'Caixa Econômica Federal'},
    {codigo: '237', nome: 'Bradesco'},
    {codigo: '341', nome: 'Itaú'},
    {codigo: '260', nome: 'Nubank'},
    {codigo: '077', nome: 'Inter'},
    {codigo: '336', nome: 'C6 Bank'},
    {codigo: '212', nome: 'Banco Original'},
    {codigo: '655', nome: 'Votorantim'},
    {codigo: '756', nome: 'Sicoob'},
    {codigo: '748', nome: 'Sicredi'}
]

const departments = [
    'Administrativo',
    'Comercial',
    'Financeiro',
    'Marketing',
    'Operações',
    'Recursos Humanos',
    'TI',
    'Jurídico',
    'Logística',
    'Produção'
]

const cnhCategories = [
    'A', 'B', 'C', 'D', 'E', 'AB', 'AC', 'AD', 'AE'
]

const bloodTypes = [
    'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'
]
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

// Variavel reativa para armazenar o formulário em cache no localStorage
const cacheDialog = ref(false);

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

/**
 * Formata uma string de data para o formato dd/mm/aaaa.
 *
 * @param {string} dateString - A string de data.
 * @returns {string} A data formatada.
 */
const formatDate = (dateString: string) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
}

/**
 * Formata o CPF no input.
 *
 * @param {Event} e - O evento de input.
 * @returns {void}
 */
const formatCPF = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 11) {
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    }

    formData.cpf = value;
}

/**
 * Formata o CPF do dependente no input.
 *
 * @param {Event} e - O evento de input.
 * @param {number} index - O índice do dependente.
 * @returns {void}
 */
const formatDependentCPF = (e: Event, index: number) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 11) {
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    }

    formData.dependents[index].cpf = value;
}

/**
 * Formata o CEP no input.
 *
 * @param {Event} e - O evento de input.
 * @returns {void}
 */
const formatCEP = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 8) {
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
    }

    formData.postal_code = value;
}

/**
 * Formata o telefone no input.
 *
 * @param {Event} e - O evento de input.
 * @returns {void}
 */
const formatPhone = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 10) {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
    }

    formData.phone = value;
}

/**
 * Formata o celular no input.
 *
 * @param {Event} e - O evento de input.
 * @returns {void}
 */
const formatMobile = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 11) {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
    }

    formData.mobile = value;
}

/**
 * Formata o telefone de emergência no input.
 *
 * @param {Event} e - O evento de input.
 * @returns {void}
 */
const formatEmergencyPhone = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 11) {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
    }

    formData.emergency_phone = value;
}

/**
 * Formata o salário no input.
 *
 * @param {Event} e - O evento de input.
 * @returns {void}
 */
const formatSalary = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length > 0) {
        value = (parseInt(value) / 100).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL',
            minimumFractionDigits: 2
        });
    } else {
        value = '';
    }

    formData.salary = value;
}

/**
 * Formata o salário do histórico de cargos no input.
 *
 * @param {Event} e - O evento de input.
 * @returns {void}
 */
const formatHistorySalary = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length > 0) {
        value = (parseInt(value) / 100).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL',
            minimumFractionDigits: 2
        });
    } else {
        value = '';
    }

    newRoleHistory.salary = value;
}

/**
 * Busca o endereço a partir do CEP.
 * @returns {Promise<void>}
 */
const searchZipCode = async () => {
    const cep = formData.postal_code.replace(/\D/g, '');

    if (cep.length !== 8) return;

    try {
        const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);

        const data = await response.json();

        if (data.erro) {
            showToast('Falha', 'error', 'CEP não encontrado, corrija ou preencha manualmente.');
            return;
        }

        formData.street = data.logradouro;
        formData.district = data.bairro;
        formData.city = data.localidade;
        formData.state = data.uf;
    } catch (error) {
        showToast('Buscar CEP', 'error', 'Falha ao buscar CEP, preencha manualmente.');
    }
}

/**
 * Adiciona um novo dependente ao formulário.
 * @returns {void}
 */
const addDependent = () => {
    formData.dependents.push({
        name: '',
        relationship: '',
        birth_date: '',
        cpf: '',
        rg: '',
        issuing_agency: '',
        issue_date: '',
        purposes: [],
        civil_state: '',
    });
}

/**
 * Remove um dependente do formulário.
 *
 * @param {number} index - O índice do dependente a ser removido.
 * @returns {void}
 */
const removeDependent = (index: number) => {
    formData.dependents.splice(index, 1);
}

/**
 * Busca e exibe um funcionário pelo CPF.
 *
 * @param {string} cpf - O CPF do funcionário.
 * @returns {Promise<void>}
 */
async function showEmployeeByCPF(cpf: string) {
    newEmployee.value = false;
    router.get(`funcionarios/${cpf}`, {
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
            // Check if the employee was found

            if (page.props.employee) {
                Object.assign(formData, page.props.employee);
                showEmployeeForm.value = true;
            } else {
                alert('Employee not found.');
            }
        },
        onError: () => {

            alert('Error searching for employee. Check the CPF and try again.');
        }
    });

}

/**
 * Confirma a exclusão de um funcionário.
 *
 * @param {Employee} employee - O funcionário a ser excluído.
 * @returns {void}
 */
const confirmDelete = (employee: Employee) => {
    employeeToDelete.value = employee;
    showDeleteModal.value = true;
}

/**
 * Remove um anexo do formulário.
 *
 * @param {number} id - O ID do anexo a ser removido.
 * @returns {void}
 */
const removeAttachment = (id: number) => {
    const index = formData.attachments.findIndex(attachment => attachment.id === id);
    if (index !== -1) {
        // Revoke object URL to prevent memory leaks
        if (formData.attachments[index].url) {
            URL.revokeObjectURL(formData.attachments[index].url);
        }
        formData.attachments.splice(index, 1);
    }
}

/**
 * Retorna os campos de texto que foram atualizados.
 *
 * @param {Employee} original - O funcionário original.
 * @param {Employee} updated - O funcionário atualizado.
 * @returns {Partial<Employee>} Os campos atualizados.
 */
const getUpdatedTextFields = (original: Employee, updated: Employee) => {
    const changes: Partial<Employee> = {};
    for (const key in updated) {

        if (updated[key] !== original[key] && typeof updated[key] !== 'object') {
            changes[key] = updated[key];
        }
    }
    return changes;
};

/**
 * Exclui um funcionário.
 * @returns {void}
 */
const deleteEmployee = () => {
    if (!employeeToDelete.value) return;

    router.delete(`/funcionarios/${employeeToDelete.value.cpf.replace(/\D/g, '')}`, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            loadEmployees();
            showToast('Sucesso', 'success', 'Funcionário excluído com sucesso.');
        },
        onError: () => {
            showToast('Falha', 'error', 'Falha ao excluir funcionário.');
        }
    });
}

/**
 * Salva as alterações de um funcionário existente.
 * @returns {void}
 */
const saveEmployee = () => {
    // validateAndPrepareFields()


    router.put(`/funcionarios/${formData.cpf.replace(/\D/g, '')}`, formData, {
        preserveState: true,
        preserveScroll: true,
        onProgress: (event) => {
            if (event?.lengthComputable) {
                progressbar.value = Math.round((event.loaded / event.total) * 100);
            }
        },
        onSuccess: (page) => {

            progressbar.value = 0;
            employee.value = page.props.employee;
        },
        onError: () => {
            progressbar.value = 0;

            alert('Error updating employee. Please check the form and try again.');
        }
    });

}

/**
 * Fecha o formulário de funcionário e reseta o estado.
 * @returns {void}
 */
function closeEmployeeForm() {
    showEmployeeForm.value = false;
    resetForm();
    router.get('/funcionarios', {  }, { preserveState: true, preserveScroll: true });
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

/**
 * Valida e prepara os campos do formulário antes de salvar.
 * @returns {void}
 */
function validateAndPrepareFields() {
    // Basic validation of required fields
    // if (!formData.name || !formData.cpf || !formData.birth_date) {
    //     alert('Please fill in all required fields.');
    //     return false;
    // }
}

const progressbar = ref(0);

/**
 * Cria um novo funcionário.
 * @returns {void}
 */
const createEmployee = () => {
    validateAndPrepareFields();
    setLocalCacheForm();


    router.post('/funcionarios', formData, {
        forceFormData: true,
        onProgress: (event) => {
            if (event?.lengthComputable) {
                progressbar.value = Math.round((event.loaded / event.total) * 100);
            }
        },
        onSuccess: () => {
            progressbar.value = 0;
            resetForm();
            localStorage.removeItem('cachedEmployee');
            showEmployeeForm.value = false;
            loadEmployees();
        },
        onError: (error) => {
            const msgError = []
            Object.keys(error).forEach((campo) => {
                msgError.push(`${error[campo]}`);
            });
            progressbar.value = 0;
            showToast('Error', 'error', msgError[0]);
        }
    });
};

/**
 * Reseta o formulário para os valores padrão.
 * @returns {void}
 */
const resetForm = () => {
    // Reset the form to default values
    Object.assign(formData, {
        name: '',
        birth_date: '',
        gender: '',
        civil_state: '',
        nationality: '',
        birthplace: '',
        cnpj: '',
        cpf: '',
        rg: '',
        issuing_agency: '',
        issue_date: '',
        escolarity: '',
        voter_registration: '',
        military_certificate: '',
        mother_name: '',
        father_name: '',
        photo: null,
        status: 'active',

        ctps_number: '',
        ctps_series: '',
        ctps_state: '',
        pis_pasep: '',
        nit: '',
        cnh: '',
        cnh_category: '',
        cnh_expiry: '',
        professional_registration: '',

        postal_code: '',
        street: '',
        number: '',
        complement: '',
        district: '',
        city: '',
        state: '',
        phone: '',
        mobile: '',
        email: '',
        emergency_contact: '',
        emergency_phone: '',

        bank: '',
        agency: '',
        account: '',
        account_type: '',
        pix_key: '',

        role: '',
        department: '',
        contract_type: '',
        admission_date: '',
        termination_date: null,
        salary: '',
        work_schedule: '',
        benefits: [],

        last_exam_date: '',
        next_exam_date: '',
        aso_result: '',
        allergies: '',
        blood_type: '',
        accident_history: '',

        dependents: [],
        attachments: []
    });
}

/**
 * Adiciona um arquivo à lista de anexos.
 *
 * @param {File} file - O arquivo a ser adicionado.
 * @returns {void}
 */
const addFile = (file: File) => {

    // evita duplicados
    if (formData.attachments.some(a => a.name === file.name && a.size === fileSize)) {
        return;
    }

    const newAttachment: Attachment = {
        name: file.name,
        type: file.type || 'application/octet-stream',
        size: file.size,
        file: file,
        path: URL.createObjectURL(file),
        created_at: new Date().toISOString(),
    };

    formData.attachments.push(newAttachment);
    uploadAttachments();
};

/**
 * Manipula o upload de arquivos a partir do input.
 *
 * @param {Event} event - O evento de change.
 * @returns {void}
 */
const handleFileUpload = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files) {
        Array.from(input.files).forEach(addFile);
        input.value = '';
    }
};

/**
 * Manipula o drop de arquivos na área de drop.
 *
 * @param {DragEvent} event - O evento de drop.
 * @returns {void}
 */
const handleFileDrop = (event: DragEvent) => {
    isDragging.value = false;
    if (!event.dataTransfer?.files) return;
    Array.from(event.dataTransfer.files).forEach(addFile);
};

/**
 * Envia os anexos para o servidor.
 * @returns {void}
 */
function uploadAttachments() {

    console.log('Uploading attachments...', newEmployee.value);
    if (newEmployee.value) return;

    router.post(`/funcionarios/upload/${formData.cpf}`, formData, {
        forceFormData: true,
        onProgress: (event) => {
            if (event?.lengthComputable) {
                progressbar.value = Math.round((event.loaded / event.total) * 100);
            }
        },
        onSuccess: () => {
            progressbar.value = 0;
        },
        onError: () => {
            progressbar.value = 0;
        }
    });
}

// Observadores


/**
 * Observa mudanças no CEP para buscar o endereço automaticamente.
 */
debouncedWatch(
    () => formData.postal_code,
    (cep) => {
        if (cep && cep.replace(/\D/g, '').length === 8) {

            searchZipCode();
        }
    },
    { debounce: 500 }
);


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
            <EmployeeCachedDialog v-if="cacheDialog" @continue="handleContinueForm()" />

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
                        @click="newEmployee = true;loadLocalCacheFormDialog(); showEmployeeForm = true; resetForm();"
                        class="flex items-center px-3 py-1.5 btn-primary text-white rounded-md "
                    >
                        <PlusIcon class="w-5 h-5 mr-2"/>
                        Novo Funcionário
                    </button>
                </div>
            </header>
            <!-- Seção de listagem de funcionários -->
            <EmployeeManager v-if="!showEmployeeForm" :listEmployees="employees"/>
<!--            <div v-if="!showEmployeeForm">-->
<!--                &lt;!&ndash; Controles de busca e filtro &ndash;&gt;-->
<!--                <div class="mb-6">-->
<!--                    <div class="flex flex-col md:flex-row md:items-center mb-4 gap-4">-->
<!--                        &lt;!&ndash; Campo de busca &ndash;&gt;-->
<!--                        <div class="flex-grow">-->
<!--                            <div class="relative flex flex-inline items-center">-->
<!--                                <input-->
<!--                                    v-model="searchQuery"-->
<!--                                    type="text"-->
<!--                                    placeholder="Buscar funcionário por nome ou CPF"-->
<!--                                    class="w-full pl-10 pr-4 py-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"-->
<!--                                />-->
<!--                                <SearchIcon class="absolute left-3 top-2.5 text-gray-400 w-5 h-5"/>-->
<!--                                <span class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center space-x-2" v-if="isLoading">-->
<!--                                    <small class="text-sm animate-pulse">Carregando</small>-->
<!--                                    <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-secondary" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">-->
<!--                                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>-->
<!--                                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>-->
<!--                                    </svg>-->
<!--                                </span>-->
<!--                            </div>-->
<!--                        </div>-->

<!--                        &lt;!&ndash; Filtros e ações &ndash;&gt;-->
<!--                        <div class="flex flex-col md:flex-row gap-2">-->
<!--                            &lt;!&ndash; Filtro de status &ndash;&gt;-->
<!--                            <div class="relative">-->
<!--                                <select-->
<!--                                    v-model="statusFilter"-->
<!--                                    class="pl-10 pr-4 py-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500 appearance-none"-->
<!--                                >-->
<!--                                    <option value="all">Todos os status</option>-->
<!--                                    <option value="active">Ativos</option>-->
<!--                                    <option value="inactive">Inativos</option>-->
<!--                                    <option value="vacation">Em férias</option>-->
<!--                                    <option value="leave">Afastados</option>-->
<!--                                    <option value="terminated">Desligados</option>-->
<!--                                </select>-->
<!--                                <FilterIcon class="absolute left-3 top-2.5 text-gray-400 w-5 h-5"/>-->
<!--                            </div>-->
<!--                            &lt;!&ndash; Botão para exportar dados &ndash;&gt;-->
<!--                            <button-->
<!--                                @click="exportData"-->
<!--                                class="flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200"-->
<!--                            >-->
<!--                                <DownloadIcon class="w-5 h-5 mr-2" />-->
<!--                                Exportar-->
<!--                            </button>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->

<!--                &lt;!&ndash; Tabela de funcionários &ndash;&gt;-->
<!--                <div class="overflow-x-auto bg-white rounded-lg shadow">-->
<!--                    <table class="min-w-full divide-y divide-gray-200">-->
<!--                        <thead class="bg-gray-50">-->
<!--                        <tr>-->
<!--                            <th scope="col"-->
<!--                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome-->
<!--                            </th>-->
<!--                            <th scope="col"-->
<!--                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo-->
<!--                            </th>-->
<!--                            <th scope="col"-->
<!--                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">-->
<!--                                Departamento-->
<!--                            </th>-->
<!--                            <th scope="col"-->
<!--                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data-->
<!--                                de Admissão-->
<!--                            </th>-->
<!--                            <th scope="col"-->
<!--                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">-->
<!--                                Status-->
<!--                            </th>-->
<!--                            <th scope="col"-->
<!--                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">-->
<!--                                Ações-->
<!--                            </th>-->
<!--                        </tr>-->
<!--                        </thead>-->
<!--                        <tbody class="bg-white divide-y divide-gray-200">-->
<!--                        &lt;!&ndash; Linha da tabela para cada funcionário &ndash;&gt;-->
<!--                        <tr v-for="employee in employees" :key="employee.cpf" class="hover:bg-gray-50">-->
<!--                            <td class="px-6 py-4 whitespace-nowrap">-->
<!--                                <div class="flex items-center">-->
<!--                                    <div class="flex-shrink-0 h-10 w-10">-->
<!--                                        &lt;!&ndash; Avatar com iniciais ou foto &ndash;&gt;-->
<!--                                        <div v-if="!employee.photo"-->
<!--                                             class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-800 font-semibold">-->
<!--                                            {{ getInitials(employee.name) }}-->
<!--                                        </div>-->
<!--                                        <img v-else :src="employee.photo" alt="" class="h-10 w-10 rounded-full object-cover">-->
<!--                                    </div>-->
<!--                                    <div class="ml-4">-->
<!--                                        <div class="text-sm font-medium text-gray-900">{{ employee.name }}</div>-->
<!--                                        <div class="text-sm text-gray-500">{{ employee.email }}</div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </td>-->
<!--                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ employee.role }}</td>-->
<!--                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ employee.department }}</td>-->
<!--                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-->
<!--                                {{ formatDate(employee.admission_date) }}-->
<!--                            </td>-->
<!--                            <td class="px-6 py-4 whitespace-nowrap">-->
<!--                                &lt;!&ndash; Status do funcionário com cor &ndash;&gt;-->
<!--                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getStatusColor(employee.status)">-->
<!--                                    {{ getStatusText(employee.status) }}-->
<!--                                </span>-->
<!--                            </td>-->
<!--                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">-->
<!--                                &lt;!&ndash; Menu de ações para cada funcionário &ndash;&gt;-->
<!--                                <DropdownMenu>-->
<!--                                    <DropdownMenuTrigger as-child>-->
<!--                                        <button class="cursor-pointer text-gray-600 hover:text-gray-900 hover:bg-gray-300 hover:rounded-lg mr-3">-->
<!--                                            <BookUser class="w-5 h-5"/>-->
<!--                                        </button>-->
<!--                                    </DropdownMenuTrigger>-->
<!--                                    <DropdownMenuContent-->
<!--                                        class="w-(&#45;&#45;reka-dropdown-menu-trigger-width) min-w-56 rounded-lg"-->
<!--                                        :side="isMobile ? 'bottom' : state === 'collapsed' ? 'left' : 'bottom'"-->
<!--                                        align="end"-->
<!--                                        :side-offset="4"-->
<!--                                    >-->
<!--                                        <DropdownMenuGroup>-->
<!--                                            <DropdownMenuItem :as-child="true">-->
<!--                                                <div class="block w-full" @click="showEmployeeByCPF(employee.cpf.replace(/\D/g, ''))" as="button">-->
<!--                                                    <EditIcon class="mr-2 h-4 w-4" />-->
<!--                                                    Editar-->
<!--                                                </div>-->
<!--                                            </DropdownMenuItem>-->
<!--                                            <DropdownMenuItem :as-child="true">-->
<!--                                                <div class="block w-full" @click="showEmployeeByCPF(employee.cpf.replace(/\D/g, ''))" as="button">-->
<!--                                                    <ShieldBan class="mr-2 h-4 w-4" />-->
<!--                                                    Desativar-->
<!--                                                </div>-->
<!--                                            </DropdownMenuItem>-->
<!--                                        </DropdownMenuGroup>-->
<!--                                    </DropdownMenuContent>-->
<!--                                </DropdownMenu>-->
<!--                            </td>-->
<!--                        </tr>-->
<!--                        &lt;!&ndash; Mensagem exibida quando não há funcionários &ndash;&gt;-->
<!--                        <tr v-if="employees.length === 0">-->
<!--                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">-->
<!--                                Nenhum funcionário encontrado-->
<!--                            </td>-->
<!--                        </tr>-->
<!--                        </tbody>-->
<!--                    </table>-->
<!--                </div>-->

<!--                &lt;!&ndash; Paginação da tabela &ndash;&gt;-->
<!--                <div class="flex items-center justify-between mt-4">-->
<!--                    <div class="text-sm text-gray-700">-->
<!--                        Mostrando <span class="font-medium">1</span> a <span class="font-medium">{{-->
<!--                            employees.length-->
<!--                        }}</span> de <span class="font-medium">{{ employees.length }}</span> resultados-->
<!--                    </div>-->
<!--                    <div class="flex space-x-2">-->
<!--                        <button class="px-3 py-1 border rounded-md hover:bg-gray-50">Anterior</button>-->
<!--                        <button class="px-3 py-1 border rounded-md bg-gray-50 text-gray-600 font-medium">1</button>-->
<!--                        <button class="px-3 py-1 border rounded-md hover:bg-gray-50">Próxima</button>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->

            <!-- Formulário de funcionário, visível ao criar ou editar -->
            <div v-else>
                <!-- Cabeçalho do formulário -->
                <div class="flex justify-between items-center mb-5">
                    <h2 class="text-2xl font-bold text-[#222931] mb-6 flex items-center gap-3">
                        <div class="w-1 h-8 bg-secondary rounded-full"></div>
                        <div>
                            <span v-if="!newEmployee" class="block">
                                Editando Funcionário
                                <span class="block text-xl font-normal text-secondary mt-1">{{ formData.name }}</span>
                            </span>
                            <span v-else class="block">Novo Funcionário</span>
                        </div>
                    </h2>
                    <!-- Botão para fechar o formulário -->
                    <button @click="showEmployeeForm = false; loadEmployees();" class="text-gray-500 hover:text-gray-700">
                        <XIcon class="w-6 h-6"/>
                    </button>
                </div>

                <!-- Abas de navegação do formulário -->
                <div>
                    <div class="flex flex-wrap overflow-x-auto gap-2 mb-6">
                        <button
                            v-for="tab in tabs"
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="[
                                        'px-4 py-3 text-sm font-medium whitespace-nowrap flex items-center',
                                        activeTab === tab.id
                                            ? 'border-b-2 border-secondary bg-gray-50 rounded-t-lg text-black'
                                            : 'text-gray-500 hover:text-gray-700 hover:border-gray-300 cursor-pointer'
                                    ]"
                        >
                            <component :is="tab.icon" class="w-4 h-4 mr-2" />
                            {{ tab.name }}
                        </button>
                    </div>
                </div>

                <!-- Conteúdo das abas do formulário -->
                <div class="p-4 bg-white">
                    <!-- Aba 1: Dados Pessoais -->
                    <div v-if="activeTab === 'personal'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Campo: Nome completo -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Nome completo *</label>
                                <input
                                    v-model="formData.name"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: Data de nascimento -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Data de nascimento *</label>
                                <input
                                    v-model="formData.birth_date"
                                    type="date"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: Sexo/gênero -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Sexo/gênero *</label>
                                <select
                                    v-model="formData.gender"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                >
                                    <option value="">Selecione</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Feminino">Feminino</option>
                                    <option value="Outro">Outro</option>
                                </select>
                            </div>

                            <!-- Campo: Estado civil -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Estado civil *</label>
                                <select
                                    v-model="formData.civil_state"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                >
                                    <option value="">Selecione</option>
                                    <option value="solteiro">Solteiro(a)</option>
                                    <option value="casado">Casado(a)</option>
                                    <option value="divorciado">Divorciado(a)</option>
                                    <option value="viuvo">Viúvo(a)</option>
                                    <option value="uniao">União Estável</option>
                                </select>
                            </div>

                            <!-- Campo: Nacionalidade -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Nacionalidade *</label>
                                <input
                                    v-model="formData.nationality"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: Naturalidade -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Naturalidade *</label>
                                <input
                                    v-model="formData.birthplace"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: CNPJ -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">CNPJ</label>
                                <input
                                    v-model="formData.cnpj"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: CPF -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">CPF *</label>
                                <input
                                    v-model="formData.cpf"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                    @input="formatCPF"
                                />
                            </div>

                            <!-- Campo: RG -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">RG *</label>
                                <input
                                    v-model="formData.rg"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: Órgão emissor -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Órgão emissor *</label>
                                <input
                                    v-model="formData.issuing_agency"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: Data de emissão -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Data de emissão *</label>
                                <input
                                    v-model="formData.issue_date"
                                    type="date"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: Título de eleitor -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Título de eleitor *</label>
                                <input
                                    v-model="formData.voter_registration"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: Certidão de reservista -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Certidão de reservista</label>
                                <input
                                    v-model="formData.military_certificate"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                />
                                <p class="text-xs text-gray-500">Opcional para homens</p>
                            </div>

                            <!-- Campo: Nome da mãe -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Nome da mãe</label>
                                <input
                                    v-model="formData.mother_name"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                />
                            </div>

                            <!-- Campo: Nome do pai -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Nome do pai</label>
                                <input
                                    v-model="formData.father_name"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                />
                            </div>

                            <!-- Campo: Status -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Status *</label>
                                <select
                                    v-model="formData.status"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                >
                                    <option value="ativo">Ativo</option>
                                    <option value="inativo">Inativo</option>
                                    <option value="ferias">Em Férias</option>
                                    <option value="afastado">Afastado</option>
                                    <option value="desligado">Desligado</option>
                                </select>
                            </div>

                            <!-- Campo: Grau de escolaridade -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Grau de escolaridade *</label>
                                <select
                                    v-model="formData.escolarity"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                >
                                    <option value="">Selecione</option>
                                    <option value="fundamental">Ensino Fundamental</option>
                                    <option value="medio">Ensino Médio</option>
                                    <option value="tecnico">Ensino Técnico</option>
                                    <option value="superior">Ensino Superior</option>
                                    <option value="posGraduacao">Pós-graduação</option>
                                    <option value="mestrado">Mestrado</option>
                                    <option value="doutorado">Doutorado</option>
                                </select>
                            </div>

                            <!-- Campo: Tipo sanguíneo -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Tipo sanguíneo</label>
                                <select
                                    v-model="formData.blood_type"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                >
                                    <option value="">Selecione</option>
                                    <option v-for="type in bloodTypes" :key="type" :value="type">{{ type }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Aba 2: Documentos Trabalhistas -->
                    <div v-if="activeTab === 'documents'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Campo: Número da Carteira de Trabalho -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Número da Carteira de Trabalho
                                    *</label>
                                <input
                                    v-model="formData.ctps_number"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: Série CTPS -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Série CTPS *</label>
                                <input
                                    v-model="formData.ctps_series"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: UF CTPS -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">UF CTPS *</label>
                                <select
                                    v-model="formData.ctps_state"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                >
                                    <option value="">Selecione</option>
                                    <option v-for="uf in states" :key="uf" :value="uf">{{ uf }}</option>
                                </select>
                            </div>

                            <!-- Campo: PIS/PASEP -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">PIS/PASEP *</label>
                                <input
                                    v-model="formData.pis_pasep"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: Número do NIT -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Número do NIT</label>
                                <input
                                    v-model="formData.nit"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                />
                                <p class="text-xs text-gray-500">Para autônomos ou contribuintes individuais</p>
                            </div>

                            <!-- Campo: CNH -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">CNH</label>
                                <input
                                    v-model="formData.cnh"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                />
                            </div>

                            <!-- Campo: Categoria CNH -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Categoria CNH</label>
                                <select
                                    v-model="formData.cnh_category"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                >
                                    <option value="">Selecione</option>
                                    <option v-for="cat in cnhCategories" :key="cat" :value="cat">{{ cat }}</option>
                                </select>
                            </div>

                            <!-- Campo: Validade CNH -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Validade CNH</label>
                                <input
                                    v-model="formData.cnh_expiry"
                                    type="date"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                />
                            </div>

                            <!-- Campo: Registro profissional -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Registro profissional</label>
                                <input
                                    v-model="formData.professional_registration"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                />
                                <p class="text-xs text-gray-500">Ex: CREA, CRM, OAB, etc.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Aba 3: Endereço e Contato -->
                    <div v-if="activeTab === 'contact'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Campo: CEP -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">CEP * <span class="text-xs font-medium text-gray-700">(Preenchimento automático)</span></label>
                                <input
                                    v-model="formData.postal_code"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                    @input="formatCEP"
                                />
                            </div>

                            <!-- Campo: Logradouro -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Logradouro *</label>
                                <input
                                    v-model="formData.street"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: Número -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Número *</label>
                                <input
                                    v-model="formData.number"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: Complemento -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Complemento</label>
                                <input
                                    v-model="formData.complement"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                />
                            </div>

                            <!-- Campo: Bairro -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Bairro *</label>
                                <input
                                    v-model="formData.district"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: Cidade -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Cidade *</label>
                                <input
                                    v-model="formData.city"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: Estado -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Estado *</label>
                                <select
                                    v-model="formData.state"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                >
                                    <option value="">Selecione</option>
                                    <option v-for="uf in states" :key="uf" :value="uf">{{ uf }}</option>
                                </select>
                            </div>

                            <!-- Campo: Telefone -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Telefone *</label>
                                <input
                                    v-model="formData.phone"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                    @input="formatPhone"
                                />
                            </div>

                            <!-- Campo: Celular -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Celular</label>
                                <input
                                    v-model="formData.mobile"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    @input="formatMobile"
                                />
                            </div>

                            <!-- Campo: E-mail -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">E-mail *</label>
                                <input
                                    v-model="formData.email"
                                    type="email"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: Contato de emergência -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Contato de emergência</label>
                                <input
                                    v-model="formData.emergency_contact"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                />
                            </div>

                            <!-- Campo: Telefone de emergência -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Telefone de emergência</label>
                                <input
                                    v-model="formData.emergency_phone"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    @input="formatEmergencyPhone"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Aba 4: Dados Bancários -->
                    <div v-if="activeTab === 'bank'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Campo: Banco -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Banco *</label>
                                <select
                                    v-model="formData.bank"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                >
                                    <option value="">Selecione</option>
                                    <option v-for="bank in banks" :key="bank.codigo" :value="bank.codigo">
                                        {{ bank.codigo }} - {{ bank.nome }}
                                    </option>
                                </select>
                            </div>

                            <!-- Campo: Agência -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Agência *</label>
                                <input
                                    v-model="formData.agency"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: Conta -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Conta *</label>
                                <input
                                    v-model="formData.account"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: Tipo de conta -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Tipo de conta *</label>
                                <select
                                    v-model="formData.account_type"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                >
                                    <option value="">Selecione</option>
                                    <option value="corrente">Conta Corrente</option>
                                    <option value="poupanca">Conta Poupança</option>
                                    <option value="salario">Conta Salário</option>
                                </select>
                            </div>

                            <!-- Campo: Chave PIX -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Chave PIX</label>
                                <input
                                    v-model="formData.pix_key"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                />
                                <p class="text-xs text-gray-500">CPF, e-mail, telefone ou chave aleatória</p>
                            </div>
                        </div>
                    </div>

                    <!-- Aba 5: Informações Contratuais -->
                    <div v-if="activeTab === 'contract'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Campo: Cargo/função -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Cargo/função *</label>
                                <input
                                    v-model="formData.role"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: Setor/departamento -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Setor/departamento *</label>
                                <select
                                    v-model="formData.department"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                >
                                    <option value="">Selecione</option>
                                    <option v-for="dept in departments" :key="dept" :value="dept">{{ dept }}</option>
                                    <option value="outro">Outro</option>
                                </select>
                            </div>

                            <!-- Campo: Tipo de vínculo -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Tipo de vínculo *</label>
                                <select
                                    v-model="formData.contract_type"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                >
                                    <option value="">Selecione</option>
                                    <option value="clt">CLT</option>
                                    <option value="estagio">Estágio</option>
                                    <option value="pj">PJ</option>
                                    <option value="autonomo">Autônomo</option>
                                    <option value="temporario">Temporário</option>
                                </select>
                            </div>

                            <!-- Campo: Data de admissão -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Data de admissão *</label>
                                <input
                                    v-model="formData.admission_date"
                                    type="date"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: Data de desligamento -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Data de desligamento</label>
                                <input
                                    v-model="formData.termination_date"
                                    type="date"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    :disabled="formData.status !== 'desligado'"
                                />
                                <p class="text-xs text-gray-500">Aplicável apenas para funcionários desligados</p>
                            </div>

                            <!-- Campo: Salário -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Salário *</label>
                                <input
                                    v-model="formData.salary"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                    @input="formatSalary"
                                />
                            </div>

                            <!-- Campo: Jornada de trabalho -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Jornada de trabalho *</label>
                                <input
                                    v-model="formData.work_schedule"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                    placeholder="Ex: 44 horas semanais"
                                />
                            </div>

                            <!-- Campo: Benefícios -->
                            <div class="col-span-1 md:col-span-2 lg:col-span-3 space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Benefícios</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div class="flex items-center">
                                        <input
                                            v-model="formData.benefits"
                                            type="checkbox"
                                            value="valeTransporte"
                                            class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                                        />
                                        <label class="ml-2 text-sm text-gray-700">Vale Transporte</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input
                                            v-model="formData.benefits"
                                            type="checkbox"
                                            value="valeRefeicao"
                                            class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                                        />
                                        <label class="ml-2 text-sm text-gray-700">Vale Refeição</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input
                                            v-model="formData.benefits"
                                            type="checkbox"
                                            value="planoSaude"
                                            class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                                        />
                                        <label class="ml-2 text-sm text-gray-700">Plano de Saúde</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input
                                            v-model="formData.benefits"
                                            type="checkbox"
                                            value="planoOdontologico"
                                            class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                                        />
                                        <label class="ml-2 text-sm text-gray-700">Plano Odontológico</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input
                                            v-model="formData.benefits"
                                            type="checkbox"
                                            value="seguroVida"
                                            class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                                        />
                                        <label class="ml-2 text-sm text-gray-700">Seguro de Vida</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input
                                            v-model="formData.benefits"
                                            type="checkbox"
                                            value="previdenciaPrivada"
                                            class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                                        />
                                        <label class="ml-2 text-sm text-gray-700">Previdência Privada</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Aba 6: Saúde e Segurança do Trabalho -->
                    <div v-if="activeTab === 'health'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Campo: Data do último exame -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Data do último exame
                                    admissional/periódico *</label>
                                <input
                                    v-model="formData.last_exam_date"
                                    type="date"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: Data do próximo exame -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Data do próximo exame *</label>
                                <input
                                    v-model="formData.next_exam_date"
                                    type="date"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <!-- Campo: Resultado do ASO -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Resultado do ASO *</label>
                                <select
                                    v-model="formData.aso_result"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                >
                                    <option value="">Selecione</option>
                                    <option value="apto">Apto</option>
                                    <option value="aptoRestricoes">Apto com restrições</option>
                                    <option value="inapto">Inapto</option>
                                </select>
                            </div>

                            <!-- Campo: Alergias ou restrições médicas -->
                            <div class="col-span-1 md:col-span-2 lg:col-span-3 space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Alergias ou restrições
                                    médicas</label>
                                <textarea
                                    v-model="formData.allergies"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500 min-h-[100px]"
                                    placeholder="Descreva alergias ou restrições médicas, se houver"
                                ></textarea>
                            </div>

                            <!-- Campo: Histórico de acidentes ou doenças ocupacionais -->
                            <div class="col-span-1 md:col-span-2 lg:col-span-3 space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Histórico de acidentes ou doenças
                                    ocupacionais</label>
                                <textarea
                                    v-model="formData.accident_history"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500 min-h-[100px]"
                                    placeholder="Descreva o histórico, se houver"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Aba 7: Dependentes -->
                    <div v-if="activeTab === 'dependents'" class="space-y-6">
                        <!-- Cabeçalho da seção de dependentes -->
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Dependentes</h3>
                            <!-- Botão para adicionar novo dependente -->
                            <button
                                @click="addDependent"
                                type="button"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                            >
                                <PlusIcon class="h-4 w-4 mr-1"/>
                                Adicionar
                            </button>
                        </div>

                        <!-- Mensagem exibida quando não há dependentes -->
                        <div v-if="formData.dependents.length === 0" class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
                            Nenhum dependente cadastrado
                        </div>

                        <!-- Formulário para cada dependente -->
                        <div v-for="(dependent, index) in formData.dependents" :key="dependent.id"
                             class="border p-4 rounded-md mb-4 bg-white shadow-sm">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-medium">Dependente #{{ index + 1 }}</h4>
                                <!-- Botão para remover dependente -->
                                <button
                                    @click="removeDependent(index)"
                                    type="button"
                                    class="text-red-600 hover:text-red-800"
                                >
                                    <TrashIcon class="h-5 w-5"/>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Campos do formulário do dependente -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Nome completo *</label>
                                    <input
                                        v-model="dependent.name"
                                        type="text"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        required
                                    />
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Data de nascimento *</label>
                                    <input
                                        v-model="dependent.birth_date"
                                        type="date"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        required
                                    />
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">CPF *</label>
                                    <input
                                        v-model="dependent.cpf"
                                        type="text"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        required
                                        @input="(e) => formatDependentCPF(e, index)"
                                    />
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Grau de parentesco *</label>
                                    <select
                                        v-model="dependent.relationship"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        required
                                    >
                                        <option value="">Selecione</option>
                                        <option value="filho">Filho(a)</option>
                                        <option value="conjuge">Cônjuge</option>
                                        <option value="pais">Pais</option>
                                        <option value="irmao">Irmão/Irmã</option>
                                        <option value="outro">Outro</option>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Orgão Emissor *</label>
                                    <input
                                        v-model="dependent.issuing_agency"
                                        type="text"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        required
                                    />
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Data de Emissão *</label>
                                    <input
                                        v-model="dependent.issue_date"
                                        type="date"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        required
                                    />
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Finalidade *</label>
                                    <div class="space-y-2">
                                        <div class="flex items-center">
                                            <input
                                                v-model="dependent.purposes"
                                                type="checkbox"
                                                value="income_tax"
                                                class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                                            />
                                            <label class="ml-2 text-sm text-gray-700">Imposto de Renda</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input
                                                v-model="dependent.purposes"
                                                type="checkbox"
                                                value="health_plan"
                                                class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                                            />
                                            <label class="ml-2 text-sm text-gray-700">Plano de Saúde</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input
                                                v-model="dependent.purposes"
                                                type="checkbox"
                                                value="dental_plan"
                                                class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                                            />
                                            <label class="ml-2 text-sm text-gray-700">Plano Odontológico</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Aba 8: Anexos -->
                    <div v-if="activeTab === 'attachments'" class="space-y-6">
                        <!-- Cabeçalho da seção de anexos -->
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Anexos e Documentos</h3>
                        </div>

                        <!-- Área para arrastar e soltar arquivos -->
                        <div
                            class="border-2 border-dashed rounded-lg p-8 text-center transition-all duration-200"
                            :class="isDragging ? 'border-gray-500 bg-gray-50' : 'border-gray-300 hover:border-gray-400'"
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="handleFileDrop"
                        >
                            <div class="flex flex-col items-center justify-center">
                                <UploadCloudIcon class="h-12 w-12 text-gray-400 mb-3" :class="{ 'text-gray-500': isDragging }" />
                                <p class="text-lg font-medium" :class="{ 'text-gray-600': isDragging }">
                                    Arraste e solte arquivos aqui
                                </p>
                                <p class="text-sm text-gray-500 mt-1">ou</p>
                                <!-- Botão para selecionar arquivos -->
                                <label class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white btn-primary hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 cursor-pointer">
                                    <UploadIcon class="h-4 w-4 mr-1" />
                                    Selecionar arquivos
                                    <input type="file" class="hidden" @change="handleFileUpload" multiple />
                                </label>
                            </div>
                        </div>

                        <!-- Tabela de anexos -->
                        <AttachmentsDisplay :allAttachments="formData.attachments"/>
<!--                        <div v-else class="border rounded-md overflow-x-auto">-->
<!--                            <table class="min-w-full divide-y divide-gray-200">-->
<!--                                <thead class="bg-gray-50">-->
<!--                                <tr>-->
<!--                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arquivo</th>-->
<!--                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>-->
<!--                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tamanho</th>-->
<!--                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data de Upload</th>-->
<!--                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>-->
<!--                                </tr>-->
<!--                                </thead>-->
<!--                                <tbody class="bg-white divide-y divide-gray-200">-->
<!--                                &lt;!&ndash; Linha da tabela para cada anexo &ndash;&gt;-->
<!--                                <tr v-for="attachment in formData.attachments" :key="attachment.id" class="hover:bg-gray-50">-->
<!--                                    <td class="px-6 py-4 whitespace-nowrap">-->
<!--                                        <div class="flex items-center">-->
<!--                                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center">-->
<!--                                                &lt;!&ndash; Ícone do tipo de arquivo &ndash;&gt;-->
<!--                                                <component :is="getFileIcon(attachment.type)" class="h-6 w-6 text-gray-500" />-->
<!--                                            </div>-->
<!--                                            <div class="ml-4">-->
<!--                                                <div class="text-sm font-medium text-gray-900">-->
<!--                                                    <a class="hover:underline" :href="attachment.path" title="Visualizar" target="_blank">{{ attachment.name }}</a>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </td>-->
<!--                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-->
<!--                                        {{ attachment.type }}-->
<!--                                    </td>-->
<!--                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-->
<!--                                        {{ attachment.size }}-->
<!--                                    </td>-->
<!--                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-->
<!--                                        {{ formatDate(attachment.created_at) }}-->
<!--                                    </td>-->
<!--                                    <td class="px-6 py-4 whitespace-nowrap text-right items-center text-sm font-medium">-->
<!--                                        &lt;!&ndash; Ações para cada anexo &ndash;&gt;-->
<!--                                        <div class="flex items-center">-->
<!--                                            <AttachmentDialog :attachment="attachment" @remove="removeAttachment" />-->
<!--                                        </div>-->
<!--                                    </td>-->
<!--                                </tr>-->
<!--                                </tbody>-->
<!--                            </table>-->
<!--                        </div>-->
                </div>
                </div>
                <!-- Modal de confirmação de exclusão -->
                <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-md w-full">
                    <h3 class="text-lg font-medium mb-4">Confirmar exclusão</h3>
                    <p class="mb-6">Tem certeza que deseja excluir o funcionário <span
                        class="font-semibold">{{ employeeToDelete?.name }}</span>? Esta ação não pode ser desfeita.</p>
                    <div class="flex justify-end space-x-4">
                        <button
                            @click="showDeleteModal = false, loadEmployees();"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                        >
                            Cancelar
                        </button>
                        <button
                            @click="deleteEmployee"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                        >
                            Excluir
                        </button>
                    </div>
                </div>
            </div>
                <!-- Botões de ação do formulário -->
                <div class="flex justify-end space-x-4 mt-8 pt-4">
                    <!-- Botão para cancelar a operação -->
                    <button
                        type="button"
                        @click="closeEmployeeForm()"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                    >
                        Cancelar
                    </button>
                    <!-- Botão para salvar funcionário existente -->
                    <button
                        v-if="!newEmployee"
                        type="button"
                        @click="saveEmployee"
                        class="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-700"
                    >
                        Salvar
                    </button>
                    <!-- Botão para criar novo funcionário -->
                    <button
                        v-else
                        type="button"
                        @click="createEmployee"
                        class="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-700"
                    >
                        Criar
                    </button>
                </div>
            </div>

        </main>
    </AppLayout>
</template>
