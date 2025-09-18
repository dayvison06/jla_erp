<script setup lang="ts">
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

const { showToast } = useToast();
const page = usePage()
const employees = ref<Employee[]>(page.props.employees?.data ?? []);
const employee = ref<Employee | null>(page.props.employee || null);
console.table('LISTANDO FUNCIONARIOS', employees.value);
onMounted( () => {
    // Se veio um funcionário para editar, carrega os dados no form
    if (employee.value) {
        Object.assign(formData, employee.value);
        showEmployeeForm.value = true;
        newEmployee.value = false;
        return;
    }

    loadEmployees();
})

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Funcionários', href: '/funcionarios' },
];

// Estados reativos
const newEmployee = ref(false)
const showEmployeeForm = ref(false)
const activeTab = ref('personal')
const searchQuery = ref('')
const statusFilter = ref('all')
const showDeleteModal = ref(false)
const employeeToDelete = ref<Employee | null>(null)
const isDragging = ref(false)
const showHistoryModal = ref(false)
const isLoading = ref(false)
const newRoleHistory = reactive<RoleHistory>({
    id: 0,
    role: '',
    department: '',
    start_date: '',
    end_date: null,
    salary: '',
    reason: ''
})

// Dados de formulário
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
    role_history: [],

    last_exam_date: '',
    next_exam_date: '',
    aso_result: '',
    allergies: '',
    blood_type: '',
    accident_history: '',

    dependents: [],
    attachments: [],
})

// Dados de referência
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
// Methods
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
console.log('CACHE DIALOG', cacheDialog.value);
function setLocalCacheForm () {
    localStorage.setItem('cachedEmployee', JSON.stringify({ ...formData, attachments: [] }));
}
function loadLocalCacheFormDialog () {
    console.log('CARREGANDO CACHE ');

    const cachedEmployee = localStorage.getItem('cachedEmployee');

    console.log('cachedEmployee', cachedEmployee);
    if (!!cachedEmployee) {
        cacheDialog.value = true;
        console.log('CACHE DIALOG', cacheDialog.value);
    }
    // cachedEmployee.value = localStorage.getItem('cachedEmployee');
    // Object.assign(formData, JSON.parse(cachedEmployee.value));

}

function handleContinueForm() {
    const cachedEmployee = localStorage.getItem('cachedEmployee');

    Object.assign(formData, JSON.parse(cachedEmployee));
    cacheDialog.value = false;
}
const formatDate = (dateString: string) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
}

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

const formatCEP = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 8) {
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
    }

    formData.postal_code = value;
}

const formatPhone = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 10) {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
    }

    formData.phone = value;
}

const formatMobile = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 11) {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
    }

    formData.mobile = value;
}

const formatEmergencyPhone = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 11) {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
    }

    formData.emergency_phone = value;
}

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

const removeDependent = (index: number) => {
    formData.dependents.splice(index, 1);
}

const removeRoleHistory = (id: number) => {
    formData.role_history = formData.role_history.filter(hist => hist.id !== id);
}

async function showEmployeeByCPF(cpf: string) {
    router.get(`funcionarios/${cpf}`, {
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
            // Check if the employee was found
            console.log('PAGE PROPS', page.props);
            if (page.props.employee) {
                Object.assign(formData, page.props.employee);
                showEmployeeForm.value = true;
            } else {
                alert('Employee not found.');
            }
        },
        onError: (error) => {
            console.error('Error searching for employee:', error);
            alert('Error searching for employee. Check the CPF and try again.');
        }
    });

}

const confirmDelete = (employee: Employee) => {
    employeeToDelete.value = employee;
    showDeleteModal.value = true;
}
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

const getUpdatedTextFields = (original: Employee, updated: Employee) => {
    const changes: Partial<Employee> = {};
    for (const key in updated) {

        if (updated[key] !== original[key] && typeof updated[key] !== 'object') {
            changes[key] = updated[key];
        }
    }
    return changes;
};

const saveEmployee = () => {
    // validateAndPrepareFields()
    console.table('CPF', formData.cpf, 'FORM DATA', formData);

    router.put(`/funcionarios/${formData.cpf.replace(/\D/g, '')}`, formData, {
        preserveState: true,
        preserveScroll: true,
        onProgress: (event) => {
            if (event?.lengthComputable) {
                progressbar.value = Math.round((event.loaded / event.total) * 100);
            }
        },
        onSuccess: (page) => {
            console.log('SUCESSO UPDATE', page);
            progressbar.value = 0;
            employee.value = page.props.employee;
        },
        onError: (errors) => {
            progressbar.value = 0;
            console.error('Error updating employee:', errors);
            alert('Error updating employee. Please check the form and try again.');
        }
    });
    console.log('FORM DATA UPDATE:', formData);
}

function closeEmployeeForm() {
    showEmployeeForm.value = false;
    resetForm();
    router.get('/funcionarios', {  }, { preserveState: true, preserveScroll: true });
}

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


function validateAndPrepareFields() {
    // Basic validation of required fields
    // if (!formData.name || !formData.cpf || !formData.birth_date) {
    //     alert('Please fill in all required fields.');
    //     return false;
    // }
}

const progressbar = ref(0);
const createEmployee = () => {
    validateAndPrepareFields();
    setLocalCacheForm();

    console.log('FORM DATA:', formData);
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
        role_history: [],

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

const formatFileSize = (size: number): string => {
    return size < 1024 * 1024
        ? `${(size / 1024).toFixed(2)} KB`
        : `${(size / 1024 / 1024).toFixed(2)} MB`;
};

const addFile = (file: File) => {
    console.log('Adding file:', URL.createObjectURL(file),);
    const fileSize = formatFileSize(file.size);

    // evita duplicados
    if (formData.attachments.some(a => a.name === file.name && a.size === fileSize)) {
        return;
    }

    const newAttachment: Attachment = {
        name: file.name,
        type: file.type || 'application/octet-stream',
        size: fileSize,
        file: file,
        path: URL.createObjectURL(file),
        created_at: new Date().toISOString(),
    };

    formData.attachments.push(newAttachment);
    uploadAttachments();
};

const handleFileUpload = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files) {
        Array.from(input.files).forEach(addFile);
        input.value = '';
    }
};

const handleFileDrop = (event: DragEvent) => {
    isDragging.value = false;
    if (!event.dataTransfer?.files) return;
    Array.from(event.dataTransfer.files).forEach(addFile);
};

function uploadAttachments() {

    console.log('newEmployee', newEmployee.value);
    // if (newEmployee) return;

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

const getFileIcon = (type: string) => {
    if (type.includes('pdf')) return FileTextIcon;
    if (type.includes('image')) return ImageIcon;
    if (type.includes('word') || type.includes('document')) return FileTextIcon;
    if (type.includes('excel') || type.includes('sheet')) return FileSpreadsheetIcon;
    if (type.includes('presentation') || type.includes('powerpoint')) return File;
    return FileIcon;
}

function searchEmployees() {
    isLoading.value = true
    router.get('/funcionarios', {
        search: searchQuery.value,
        status: statusFilter.value
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['employees'],
        onSuccess: (page) => {
            employees.value = page.props.employees.data;
            isLoading.value = false
            if (employees.value.length === 0) {
                showToast('Nenhum funcionário encontrado', 'warning', 'A busca não retornou resultados.');
            }
        }

    });
}

const getStatusColor = (status: string) => {
    switch (status) {
        case 'active':
            return 'bg-green-100 text-green-800';
        case 'inactive':
            return 'bg-gray-100 text-gray-800';
        case 'vacation':
            return 'bg-blue-100 text-blue-800';
        case 'leave':
            return 'bg-yellow-100 text-yellow-800';
        case 'terminated':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

const getStatusText = (status: string) => {
    switch (status) {
        case 'active':
            return 'Ativo';
        case 'inactive':
            return 'Inativo';
        case 'vacation':
            return 'Em Férias';
        case 'leave':
            return 'Afastado';
        case 'terminated':
            return 'Desligado';
        default:
            return 'Desconhecido';
    }
}

const exportData = () => {
    // Basic implementation of CSV export

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', 'employees.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Observar mudanças nos filtros para atualizar a lista
debouncedWatch([searchQuery, statusFilter], () => {
    // Aqui poderia implementar lógica adicional se necessário
    searchEmployees()

}, { debounce: 300 });

debouncedWatch(
    () => formData.postal_code,
    (cep) => {
        if (cep && cep.replace(/\D/g, '').length === 8) {
            console.log('WATCH CEP', cep);
            searchZipCode();
        }
    },
    { debounce: 500 }
);

console.log('SHOW EMPLOYEE:', employee);
</script>

<template>
    <Head title='Funcionários'/>
    <AppLayout :breadcrumbs="breadcrumbs" >
        <main class="container mx-auto px-4 py-8">
            <ProgressBar :progress="progressbar" :visible="progressbar > 0" />
            <EmployeeCachedDialog v-if="cacheDialog" @continue="handleContinueForm()" />
            <!-- Cabeçalho do módulo -->
            <header v-if="!showEmployeeForm" class="text-black mb-6 p-6 rounded-lg shadow">
                <div class="mx-auto flex items-start mb-4">
                    <!-- Título e descrição -->
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
                <!-- Ações -->
                <div class="flex items-center gap-3">
                    <button
                        @click="loadLocalCacheFormDialog(); showEmployeeForm = true; resetForm(); newEmployee = true;"
                        class="flex items-center px-3 py-1.5 btn-primary text-white rounded-md "
                    >
                        <PlusIcon class="w-5 h-5 mr-2"/>
                        Novo Funcionário
                    </button>

<!--                    <button-->
<!--                        @click=""-->
<!--                        class="flex items-center px-4 py-2 bg-secondary text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"-->
<!--                    >-->
<!--                        <FileUp class="w-5 h-5 mr-2"/>-->
<!--                        Importar Funcionários-->
<!--                    </button>-->
                </div>
            </header>
            <!-- Modo de listagem -->
            <div v-if="!showEmployeeForm">
                <div class="mb-6">
                    <div class="flex flex-col md:flex-row md:items-center mb-4 gap-4">
                        <div class="flex-grow">
                            <div class="relative flex flex-inline items-center">
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Buscar funcionário por nome ou CPF"
                                    class="w-full pl-10 pr-4 py-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                />
                                <SearchIcon class="absolute left-3 top-2.5 text-gray-400 w-5 h-5"/>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center space-x-2" v-if="isLoading">
                                    <small class="text-sm animate-pulse">Carregando</small>
                                    <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-secondary" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row gap-2">
                            <div class="relative">
                                <select
                                    v-model="statusFilter"
                                    class="pl-10 pr-4 py-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500 appearance-none"
                                >
                                    <option value="all">Todos os status</option>
                                    <option value="active">Ativos</option>
                                    <option value="inactive">Inativos</option>
                                    <option value="vacation">Em férias</option>
                                    <option value="leave">Afastados</option>
                                    <option value="terminated">Desligados</option>
                                </select>
                                <FilterIcon class="absolute left-3 top-2.5 text-gray-400 w-5 h-5"/>
                            </div>
                            <button
                                @click="exportData"
                                class="flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200"
                            >
                                <DownloadIcon class="w-5 h-5 mr-2" />
                                Exportar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabela de funcionários -->
                <div class="overflow-x-auto bg-white rounded-lg shadow">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Departamento
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data
                                de Admissão
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="employee in employees" :key="employee.cpf" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div v-if="!employee.photo"
                                             class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-800 font-semibold">
                                            {{ getInitials(employee.name) }}
                                        </div>
                                        <img v-else :src="employee.photo" alt="" class="h-10 w-10 rounded-full object-cover">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ employee.name }}</div>
                                        <div class="text-sm text-gray-500">{{ employee.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ employee.role }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ employee.department }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(employee.admission_date) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getStatusColor(employee.status)">
                                            {{ getStatusText(employee.status) }}
                                        </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">

                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <button class="cursor-pointer text-gray-600 hover:text-gray-900 hover:bg-gray-300 hover:rounded-lg mr-3">
                                            <BookUser class="w-5 h-5"/>
                                        </button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent
                                        class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg"
                                        :side="isMobile ? 'bottom' : state === 'collapsed' ? 'left' : 'bottom'"
                                        align="end"
                                        :side-offset="4"
                                    >
                                        <DropdownMenuGroup>
                                            <DropdownMenuItem :as-child="true">
                                                <div class="block w-full" @click="showEmployeeByCPF(employee.cpf.replace(/\D/g, ''))" as="button">
                                                    <EditIcon class="mr-2 h-4 w-4" />
                                                    Editar
                                                </div>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem :as-child="true">
                                                <div class="block w-full" @click="showEmployeeByCPF(employee.cpf.replace(/\D/g, ''))" as="button">
                                                    <ShieldBan class="mr-2 h-4 w-4" />
                                                    Desativar
                                                </div>
                                            </DropdownMenuItem>
                                        </DropdownMenuGroup>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </td>
                        </tr>
                        <tr v-if="employees.length === 0">
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Nenhum funcionário encontrado
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="flex items-center justify-between mt-4">
                    <div class="text-sm text-gray-700">
                        Mostrando <span class="font-medium">1</span> a <span class="font-medium">{{
                            employees.length
                        }}</span> de <span class="font-medium">{{ employees.length }}</span> resultados
                    </div>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 border rounded-md hover:bg-gray-50">Anterior</button>
                        <button class="px-3 py-1 border rounded-md bg-gray-50 text-gray-600 font-medium">1</button>
                        <button class="px-3 py-1 border rounded-md hover:bg-gray-50">Próxima</button>
                    </div>
                </div>
            </div>

            <!-- Formulário de funcionário -->
            <div v-else>
                <div class="flex justify-between items-center mb-5">
                    <h2 class="text-xl font-semibold">
                        <span v-if="!newEmployee">
                            Alterando dados de
                            <span class="bg-secondary px-2 py-0.5 rounded-lg text-white font-bold">{{ formData.name }}</span>
                        </span>
                        <span v-else>Novo Funcionário</span>
                    </h2>
                    <button @click="showEmployeeForm = false;" class="text-gray-500 hover:text-gray-700">
                        <XIcon class="w-6 h-6"/>
                    </button>
                </div>

                <!-- Abas -->
                <div>
                    <div class="flex overflow-x-auto mb-6">
                        <button
                            v-for="tab in tabs"
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="[
                                        'px-4 py-3 text-sm font-medium whitespace-nowrap flex items-center',
                                        activeTab === tab.id
                                            ? 'border-b-2 border-gray-600 bg-gray-50 rounded-t-lg text-gray-700'
                                            : 'text-gray-500 hover:text-gray-700 hover:border-gray-300 cursor-pointer'
                                    ]"
                        >
                            <component :is="tab.icon" class="w-4 h-4 mr-2" />
                            {{ tab.name }}
                        </button>
                    </div>
                </div>

                <!-- Conteúdo das abas -->
                <div class="p-4 bg-white">
                    <!-- 1. Dados Pessoais -->
                    <div v-if="activeTab === 'personal'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Nome completo *</label>
                                <input
                                    v-model="formData.name"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Data de nascimento *</label>
                                <input
                                    v-model="formData.birth_date"
                                    type="date"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

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

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Nacionalidade *</label>
                                <input
                                    v-model="formData.nationality"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Naturalidade *</label>
                                <input
                                    v-model="formData.birthplace"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">CNPJ</label>
                                <input
                                    v-model="formData.cnpj"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

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

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">RG *</label>
                                <input
                                    v-model="formData.rg"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Órgão emissor *</label>
                                <input
                                    v-model="formData.issuing_agency"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Data de emissão *</label>
                                <input
                                    v-model="formData.issue_date"
                                    type="date"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Título de eleitor *</label>
                                <input
                                    v-model="formData.voter_registration"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Certidão de reservista</label>
                                <input
                                    v-model="formData.military_certificate"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                />
                                <p class="text-xs text-gray-500">Opcional para homens</p>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Nome da mãe</label>
                                <input
                                    v-model="formData.mother_name"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Nome do pai</label>
                                <input
                                    v-model="formData.father_name"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                />
                            </div>

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

                    <!-- 2. Documentos Trabalhistas -->
                    <div v-if="activeTab === 'documents'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Série CTPS *</label>
                                <input
                                    v-model="formData.ctps_series"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

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

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">PIS/PASEP *</label>
                                <input
                                    v-model="formData.pis_pasep"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Número do NIT</label>
                                <input
                                    v-model="formData.nit"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                />
                                <p class="text-xs text-gray-500">Para autônomos ou contribuintes individuais</p>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">CNH</label>
                                <input
                                    v-model="formData.cnh"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                />
                            </div>

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

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Validade CNH</label>
                                <input
                                    v-model="formData.cnh_expiry"
                                    type="date"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                />
                            </div>

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

                    <!-- 3. Endereço e Contato -->
                    <div v-if="activeTab === 'contact'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Logradouro *</label>
                                <input
                                    v-model="formData.street"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Número *</label>
                                <input
                                    v-model="formData.number"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Complemento</label>
                                <input
                                    v-model="formData.complement"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Bairro *</label>
                                <input
                                    v-model="formData.district"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Cidade *</label>
                                <input
                                    v-model="formData.city"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

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

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Celular</label>
                                <input
                                    v-model="formData.mobile"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    @input="formatMobile"
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">E-mail *</label>
                                <input
                                    v-model="formData.email"
                                    type="email"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Contato de emergência</label>
                                <input
                                    v-model="formData.emergency_contact"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                />
                            </div>

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

                    <!-- 4. Dados Bancários -->
                    <div v-if="activeTab === 'bank'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Agência *</label>
                                <input
                                    v-model="formData.agency"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Conta *</label>
                                <input
                                    v-model="formData.account"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

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

                    <!-- 5. Informações Contratuais -->
                    <div v-if="activeTab === 'contract'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Cargo/função *</label>
                                <input
                                    v-model="formData.role"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

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

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Data de admissão *</label>
                                <input
                                    v-model="formData.admission_date"
                                    type="date"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

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

                        <!-- Histórico de cargos -->
<!--                        <div class="mt-8">-->
<!--                            <div class="flex justify-between items-center mb-4">-->
<!--                                <h3 class="text-lg font-medium">Histórico de Cargos</h3>-->
<!--                            </div>-->

<!--                            <div v-if="formData.role_history.length === 0" class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">-->
<!--                                Nenhum histórico de cargo cadastrado-->
<!--                            </div>-->

<!--                            <div v-else class="overflow-x-auto">-->
<!--                                <table class="min-w-full divide-y divide-gray-200">-->
<!--                                    <thead class="bg-gray-50">-->
<!--                                    <tr>-->
<!--                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">-->
<!--                                            Cargo-->
<!--                                        </th>-->
<!--                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">-->
<!--                                            Departamento-->
<!--                                        </th>-->
<!--                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">-->
<!--                                            Data Início-->
<!--                                        </th>-->
<!--                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">-->
<!--                                            Data Fim-->
<!--                                        </th>-->
<!--                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">-->
<!--                                            Salário-->
<!--                                        </th>-->
<!--                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">-->
<!--                                            Motivo-->
<!--                                        </th>-->
<!--                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">-->
<!--                                            Ações-->
<!--                                        </th>-->
<!--                                    </tr>-->
<!--                                    </thead>-->
<!--                                    <tbody class="bg-white divide-y divide-gray-200">-->
<!--                                    <tr v-for="hist in formData.role_history" :key="hist.id" class="hover:bg-gray-50">-->
<!--                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">-->
<!--                                            {{ hist.role }}-->
<!--                                        </td>-->
<!--                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-->
<!--                                            {{ hist.department }}-->
<!--                                        </td>-->
<!--                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-->
<!--                                            {{ formatDate(hist.start_date) }}-->
<!--                                        </td>-->
<!--                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-->
<!--                                            {{ hist.end_date ? formatDate(hist.end_date) : 'Atual' }}-->
<!--                                        </td>-->
<!--                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-->
<!--                                            {{ hist.salary }}-->
<!--                                        </td>-->
<!--                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-->
<!--                                            {{ hist.reason }}-->
<!--                                        </td>-->
<!--                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">-->
<!--                                            <button @click="removeRoleHistory(hist.id)" class="text-red-600 hover:text-red-900">-->
<!--                                                <TrashIcon class="w-5 h-5" />-->
<!--                                            </button>-->
<!--                                        </td>-->
<!--                                    </tr>-->
<!--                                    </tbody>-->
<!--                                </table>-->
<!--                            </div>-->
<!--                        </div>-->
                    </div>

                    <!-- 6. Saúde e Segurança do Trabalho -->
                    <div v-if="activeTab === 'health'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Data do próximo exame *</label>
                                <input
                                    v-model="formData.next_exam_date"
                                    type="date"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                />
                            </div>

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

                            <div class="col-span-1 md:col-span-2 lg:col-span-3 space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Alergias ou restrições
                                    médicas</label>
                                <textarea
                                    v-model="formData.allergies"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500 min-h-[100px]"
                                    placeholder="Descreva alergias ou restrições médicas, se houver"
                                ></textarea>
                            </div>

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

                    <!-- 7. Dependentes -->
                    <div v-if="activeTab === 'dependents'" class="space-y-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Dependentes</h3>
                            <button
                                @click="addDependent"
                                type="button"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                            >
                                <PlusIcon class="h-4 w-4 mr-1"/>
                                Adicionar
                            </button>
                        </div>

                        <div v-if="formData.dependents.length === 0" class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
                            Nenhum dependente cadastrado
                        </div>

                        <div v-for="(dependent, index) in formData.dependents" :key="dependent.id"
                             class="border p-4 rounded-md mb-4 bg-white shadow-sm">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-medium">Dependente #{{ index + 1 }}</h4>
                                <button
                                    @click="removeDependent(index)"
                                    type="button"
                                    class="text-red-600 hover:text-red-800"
                                >
                                    <TrashIcon class="h-5 w-5"/>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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

                    <!-- 8. Anexos -->
                    <div v-if="activeTab === 'attachments'" class="space-y-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Anexos e Documentos</h3>
                        </div>

                        <!-- Área de Drag and Drop -->
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
                                <label class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 cursor-pointer">
                                    <UploadIcon class="h-4 w-4 mr-1" />
                                    Selecionar arquivos
                                    <input type="file" class="hidden" @change="handleFileUpload" multiple />
                                </label>
                            </div>
                        </div>

                        <div v-if="formData.attachments.length === 0" class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
                            Nenhum anexo cadastrado
                        </div>

                        <div v-else class="border rounded-md overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arquivo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tamanho</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data de Upload</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="attachment in formData.attachments" :key="attachment.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center">
                                                <component :is="getFileIcon(attachment.type)" class="h-6 w-6 text-gray-500" />
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <a class="hover:underline" :href="attachment.path" title="Visualizar" target="_blank">{{ attachment.name }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ attachment.type }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ attachment.size }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(attachment.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right items-center text-sm font-medium">
                                        <div class="flex items-center">
                                            <AttachmentDialog :attachment="attachment" @remove="removeAttachment" />
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Prévia de imagens -->
                        <div v-if="formData.attachments.some(a => a.type.includes('image'))" class="mt-6">
                            <h4 class="text-md font-medium mb-3">Prévia de Imagens</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                <div
                                    v-for="attachment in formData.attachments.filter(a => a.type.includes('image'))"
                                    :key="`preview-${attachment.id}`"
                                    class="relative group"
                                >
                                    <img
                                        :src="attachment.path"
                                        :alt="attachment.name"
                                        class="h-40 w-full object-cover rounded-md border"
                                    />
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <a
                                            :href="attachment.path"
                                            target="_blank"
                                            class="p-2 bg-white rounded-full mx-1"
                                            title="Visualizar"
                                        >
                                            <EyeIcon class="w-5 h-5 text-gray-700" />
                                        </a>
                                        <button
                                            @click="removeAttachment(attachment.id)"
                                            class="p-2 bg-white rounded-full mx-1"
                                            title="Excluir"
                                        >
                                            <TrashIcon class="w-5 h-5 text-red-600" />
                                        </button>
                                    </div>
                                    <div class="mt-1 text-sm truncate">{{ attachment.name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de ação -->
                    <div class="flex justify-end space-x-4 mt-8 pt-4">
                        <button
                            type="button"
                            @click="closeEmployeeForm()"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                        >
                            Cancelar
                        </button>
                        <button
                            v-if="!newEmployee"
                            type="button"
                            @click="saveEmployee"
                            class="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-700"
                        >
                            Salvar
                        </button>
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
            </div>

            <!-- Modal de confirmação de exclusão -->
            <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-md w-full">
                    <h3 class="text-lg font-medium mb-4">Confirmar exclusão</h3>
                    <p class="mb-6">Tem certeza que deseja excluir o funcionário <span
                        class="font-semibold">{{ employeeToDelete?.name }}</span>? Esta ação não pode ser desfeita.</p>
                    <div class="flex justify-end space-x-4">
                        <button
                            @click="showDeleteModal = false"
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

            <!-- Modal de histórico de cargo -->
            <div v-if="showHistoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-lg w-full">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Adicionar Histórico de Cargo</h3>
                        <button @click="showHistoryModal = false" class="text-gray-500 hover:text-gray-700">
                            <XIcon class="w-6 h-6"/>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Cargo *</label>
                            <input
                                v-model="newRoleHistory.role"
                                type="text"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                            />
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Departamento *</label>
                            <select
                                v-model="newRoleHistory.department"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                            >
                                <option value="">Selecione</option>
                                <option v-for="dept in departamentos" :key="dept" :value="dept">{{ dept }}</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Data de início *</label>
                            <input
                                v-model="newCargoHistory.dataInicio"
                                type="date"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                            />
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Data de término</label>
                            <input
                                v-model="newCargoHistory.dataFim"
                                type="date"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                            />
                            <p class="text-xs text-gray-500">Deixe em branco se for o cargo atual</p>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Salário *</label>
                            <input
                                v-model="newRoleHistory.salary"
                                type="text"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                @input="formatHistorySalary"
                            />
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Motivo da alteração *</label>
                            <select
                                v-model="newCargoHistory.motivo"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                            >
                                <option value="">Selecione</option>
                                <option value="Contratação">Contratação</option>
                                <option value="Promoção">Promoção</option>
                                <option value="Transferência">Transferência</option>
                                <option value="Reajuste">Reajuste Salarial</option>
                                <option value="Desligamento">Desligamento</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-6 pt-4 border-t">
                        <button
                            type="button"
                            @click="showHistoryModal = false"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                        >
                            Cancelar
                        </button>
                        <button
                            type="button"
                            @click="saveHistoricoCargo"
                            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700"
                        >
                            Salvar
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </AppLayout>
</template>
