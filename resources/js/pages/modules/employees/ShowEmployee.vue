<script setup lang="ts">
import {
    ArrowRight,
    PlusIcon,
    TrashIcon,
    UploadCloudIcon,
    UploadIcon,
    UserIcon,
    BriefcaseBusiness,
    MapPinHouse,
    FilePenLine,
    Paperclip,
    Users,
    Cross,
    Banknote,
    CornerDownLeft,
    Eye,
    EditIcon,
    UserSearch,
    UserRoundPen,
    UserCog,
    EllipsisIcon
} from 'lucide-vue-next';
import AttachmentsDisplay from '@/components/AttachmentsDisplay.vue';
import { ref, reactive, onMounted } from 'vue';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Head, usePage, router } from '@inertiajs/vue3';
import type { Employee } from '@/types/Employees';
import AppLayout from '@/layouts/AppLayout.vue';
import { toast } from 'vue-sonner'
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
const page = usePage();
const employee: Employee = page.props.employee;
console.log('EMPLOYEE', employee);
const progressbar = ref(0);
const isDragging = ref(true);
const isReadonly = ref(true);
const breadcrumbs = [
    { title: 'Funcionários', href: '/funcionarios' },
    { title: 'Visualizar', href: `/funcionarios/${employee?.cpf}` }
];

// Objeto reativo para os dados do formulário do funcionário
const formData = reactive<Employee>({
    id: '',
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

onMounted(() => {
    if (employee) {
        Object.assign(formData, employee);
    }
    console.log('FORM DATA ON MOUNT', formData);
});

console.log('ATTACHMENTS', formData.attachments);

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
    console.log('File drop event:', event);
    isDragging.value = false;
    if (!event.dataTransfer?.files) return;
    Array.from(event.dataTransfer.files).forEach(addFile);
};

/**
 * Envia os anexos para o servidor.
 * @returns {void}
 */
function uploadAttachments() {

    router.post(`/funcionarios/upload/${formData.id}`, formData, {
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
    searchZipCode()
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
 * Busca o endereço a partir do CEP.
 * @returns {Promise<void>}
 */
const searchZipCode = async () => {
    const cep = formData.postal_code.replace(/\D/g, '');
    if (cep.length !== 8) return;

    toast.promise(
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(res => res.json())
            .then(json => {
                if (json.erro) {
                    throw new Error('CEP não encontrado.');
                }
                formData.street = json.logradouro;
                formData.district = json.bairro;
                formData.city = json.localidade;
                formData.state = json.uf;
            }),
        {
            position: 'top-center',
            loading: 'Buscando endereço...',
            success: 'Endereço encontrado.',
            error: 'Erro ao buscar CEP, preencha manualmente.'
        },
    );
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
 * Salva as alterações de um funcionário existente.
 * @returns {void}
 */
const saveEmployee = () => {
    router.put(`/funcionarios/${formData.id}`, formData, {
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
            isReadonly.value = true;
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
function returnPageEmployees() {
    router.get('/funcionarios');
}

</script>

<template>
    <Head title='Editar'/>
    <AppLayout :breadcrumbs="breadcrumbs">
        <main>
            <!-- Botão para cancelar a operação -->
            <Button
                type="button"
                @click="returnPageEmployees()"
                class="btn-primary-v2"
            >
                <CornerDownLeft class="w-4 h-4 mr-2"/>
                Voltar
            </Button>
            <div class="text-center">
                <span class="inline-flex px-2 py-1 text-xs font-semibold text-white rounded" :class="isReadonly ? 'bg-gray-400' : 'bg-primary'" :title="isReadonly ? 'Modo leitura' : 'Modo edição'" role="status" :aria-label="isReadonly ? 'Modo leitura' : 'Modo edição'">
                         <Eye v-if="isReadonly" class="w-4 h-4 mr-1" aria-hidden="true"/>
                         <EditIcon v-else class="w-4 h-4 mr-1" aria-hidden="true"/>
                         {{ isReadonly ? 'Modo leitura' : 'Modo edição' }}
             </span>
            </div>
            <header class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <h1 class="text-2xl font-bold flex items-center">
                        <UserSearch v-if="isReadonly" class="w-6 h-6 mr-2"/>
                        <UserRoundPen v-else class="w-6 h-6 mr-2"/>
                        {{ formData.name }}
                    </h1>
                    <Button
                        v-if="!isReadonly"
                        class="btn-primary-v2"
                    >
                        <EllipsisIcon class="w-4 h-4"/>
                    </Button>
                </div>
                <div class="flex items-center gap-2">
                    <Button
                        v-if="isReadonly"
                        type="button"
                        @click="isReadonly = !isReadonly"
                        class="btn-primary"
                    >
                        <EditIcon class="w-4 h-4 mr-2"/>
                        Alterar Dados
                    </Button>
                </div>
            </header>
            <Tabs default-value="personal">
                <TabsList class="flex gap-2 h-10 mb-6">
                    <TabsTrigger class="hover:text-secondary data-[state=active]:text-secondary" value="personal">
                        <UserIcon/>
                        Dados Pessoais
                    </TabsTrigger>
                    <TabsTrigger class="hover:text-secondary data-[state=active]:text-secondary" value="documents">
                        <BriefcaseBusiness/>
                        Documentos Trabalhistas
                    </TabsTrigger>
                    <TabsTrigger class="hover:text-secondary data-[state=active]:text-secondary" value="address">
                        <MapPinHouse/>
                        Endereço
                    </TabsTrigger>
                    <TabsTrigger class="hover:text-secondary data-[state=active]:text-secondary" value="bank">
                        <Banknote/>
                        Dados Bancários
                    </TabsTrigger>
                    <TabsTrigger class="hover:text-secondary data-[state=active]:text-secondary" value="contract">
                        <FilePenLine/>
                        Informações Contratuais
                    </TabsTrigger>
                    <TabsTrigger class="hover:text-secondary data-[state=active]:text-secondary" value="health">
                        <Cross/>
                        Saúde e Segurança
                    </TabsTrigger>
                    <TabsTrigger class="hover:text-secondary data-[state=active]:text-secondary" value="dependents">
                        <Users/>
                        Dependentes
                    </TabsTrigger>
                    <TabsTrigger class="hover:text-secondary data-[state=active]:text-secondary" value="attachments">
                        <Paperclip/>
                        Anexos
                    </TabsTrigger>
                </TabsList>
                <TabsContent value="personal">
                    <!-- IDENTIFICAÇÃO -->
                    <details class="group border rounded-md mb-4" open>
                        <summary
                            class="flex items-center justify-between cursor-pointer select-none p-4 font-bold text-gray-700"
                        >
                            <span>Informações Básicas</span>
                            <ArrowRight class="ml-2 text-gray-500 group-open:rotate-90 transition-all duration-100"/>
                        </summary>

                        <div class="p-4 pt-0">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Nome completo -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Nome completo *</label>
                                    <Input
                                        v-model="formData.name"
                                        type="text"
                                        required
                                        :readonly="isReadonly"
                                    />
                                </div>

                                <!-- E-mail -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">E-mail *</label>
                                    <Input
                                        v-model="formData.email"
                                        type="email"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        required
                                        :readonly="isReadonly"
                                    />
                                </div>

                                <!-- Data de nascimento -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Data de nascimento *</label>
                                    <Input
                                        v-model="formData.birth_date"
                                        type="date"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        required
                                        :readonly="isReadonly"
                                    />
                                </div>

                                <!-- Sexo/gênero -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Sexo/gênero *</label>
                                    <select
                                        v-model="formData.gender"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        required
                                        :disabled="isReadonly"
                                    >
                                        <option value="">Selecione</option>
                                        <option value="Masculino">Masculino</option>
                                        <option value="Feminino">Feminino</option>
                                        <option value="Outro">Outro</option>
                                    </select>
                                </div>

                                <!-- Estado civil -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Estado civil *</label>
                                    <select
                                        v-model="formData.civil_state"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        required
                                        :disabled="isReadonly"
                                    >
                                        <option value="">Selecione</option>
                                        <option value="Solteiro(a)">Solteiro(a)</option>
                                        <option value="Casado(a)">Casado(a)</option>
                                        <option value="Divorciado(a)">Divorciado(a)</option>
                                        <option value="Viúvo(a)">Viúvo(a)</option>
                                        <option value="União Estável">União Estável</option>
                                    </select>
                                </div>

                                <!-- Nacionalidade -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Nacionalidade *</label>
                                    <Input
                                        v-model="formData.nationality"
                                        type="text"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        required
                                        :readonly="isReadonly"
                                    />
                                </div>

                                <!-- Naturalidade -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Naturalidade *</label>
                                    <Input
                                        v-model="formData.birthplace"
                                        type="text"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        required
                                        :readonly="isReadonly"
                                    />
                                </div>
                            </div>
                        </div>
                    </details>

                    <!-- DOCUMENTOS -->
                    <details class="group border rounded-md mb-4">
                        <summary class="flex items-center justify-between cursor-pointer select-none p-4 font-bold text-gray-700">
                            <span>Documentos</span>
                            <ArrowRight class="ml-2 text-gray-500 group-open:rotate-90 transition-all duration-100"/>
                        </summary>

                        <div class="p-4 pt-0">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- CNPJ -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">CNPJ</label>
                                    <Input
                                        v-model="formData.cnpj"
                                        type="text"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        :readonly="isReadonly"
                                    />
                                </div>

                                <!-- CPF -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">CPF *</label>
                                    <Input
                                        v-model="formData.cpf"
                                        type="text"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        required
                                        @input="formatCPF"
                                        :readonly="isReadonly"
                                    />
                                </div>

                                <!-- RG -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">RG *</label>
                                    <Input
                                        v-model="formData.rg"
                                        type="text"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        required
                                        :readonly="isReadonly"
                                    />
                                </div>

                                <!-- Órgão emissor -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Órgão emissor *</label>
                                    <Input
                                        v-model="formData.issuing_agency"
                                        type="text"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        required
                                        :readonly="isReadonly"
                                    />
                                </div>

                                <!-- Data de emissão -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Data de emissão *</label>
                                    <Input
                                        v-model="formData.issue_date"
                                        type="date"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        required
                                        :readonly="isReadonly"
                                    />
                                </div>

                                <!-- Título de eleitor -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Título de eleitor *</label>
                                    <Input
                                        v-model="formData.voter_registration"
                                        type="text"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        required
                                        :readonly="isReadonly"
                                    />
                                </div>

                                <!-- Certidão de reservista -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Certidão de reservista</label>
                                    <Input
                                        v-model="formData.military_certificate"
                                        type="text"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        :readonly="isReadonly"
                                    />
                                    <p class="text-xs text-gray-500">Opcional para homens</p>
                                </div>
                            </div>
                        </div>
                    </details>

                    <!-- INFORMAÇÕES PESSOAIS -->
                    <details class="group border rounded-md mb-4">
                        <summary class="flex items-center justify-between cursor-pointer select-none p-4 font-bold text-gray-700">
                            <span>Informações Pessoais</span>
                            <ArrowRight class="ml-2 text-gray-500 group-open:rotate-90 transition-all duration-100"/>
                        </summary>

                        <div class="p-4 pt-0">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Nome da mãe -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Nome da mãe</label>
                                    <Input
                                        v-model="formData.mother_name"
                                        type="text"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        :readonly="isReadonly"
                                    />
                                </div>

                                <!-- Nome do pai -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Nome do pai</label>
                                    <Input
                                        v-model="formData.father_name"
                                        type="text"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        :readonly="isReadonly"
                                    />
                                </div>

                                <!-- Grau de escolaridade -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Grau de escolaridade *</label>
                                    <select
                                        v-model="formData.escolarity"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        required
                                        :disabled="isReadonly"
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

                                <!-- Tipo sanguíneo -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Tipo sanguíneo</label>
                                    <select
                                        v-model="formData.blood_type"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        :disabled="isReadonly"
                                    >
                                        <option value="">Selecione</option>
                                        <option v-for="type in bloodTypes" :key="type" :value="type">{{ type }}</option>
                                    </select>
                                </div>

                                <!-- Telefone -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Telefone *</label>
                                    <Input
                                        v-model="formData.phone"
                                        type="text"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        required
                                        @input="formatPhone"
                                        :readonly="isReadonly"
                                    />
                                </div>

                                <!-- Celular -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Celular</label>
                                    <Input
                                        v-model="formData.mobile"
                                        type="text"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        @input="formatMobile"
                                        :readonly="isReadonly"
                                    />
                                </div>

                                <!-- Contato de emergência -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Contato de emergência</label>
                                    <Input
                                        v-model="formData.emergency_contact"
                                        type="text"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        :readonly="isReadonly"
                                    />
                                </div>

                                <!-- Telefone de emergência -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Telefone de emergência</label>
                                    <Input
                                        v-model="formData.emergency_phone"
                                        type="text"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        @input="formatEmergencyPhone"
                                        :readonly="isReadonly"
                                    />
                                </div>
                            </div>
                        </div>
                    </details>
                </TabsContent>
                <TabsContent value="documents">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Campo: Número da Carteira de Trabalho -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Número da Carteira de Trabalho
                                *</label>
                            <Input
                                v-model="formData.ctps_number"
                                type="text"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                :readonly="isReadonly"
                            />
                        </div>

                        <!-- Campo: Série CTPS -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Série CTPS *</label>
                            <Input
                                v-model="formData.ctps_series"
                                type="text"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                :readonly="isReadonly"
                            />
                        </div>

                        <!-- Campo: UF CTPS -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">UF CTPS *</label>
                            <select
                                v-model="formData.ctps_state"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                :disabled="isReadonly"
                            >
                                <option value="">Selecione</option>
                                <option v-for="uf in states" :key="uf" :value="uf">{{ uf }}</option>
                            </select>
                        </div>

                        <!-- Campo: PIS/PASEP -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">PIS/PASEP *</label>
                            <Input
                                v-model="formData.pis_pasep"
                                type="text"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                :readonly="isReadonly"
                            />
                        </div>

                        <!-- Campo: Número do NIT -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Número do NIT</label>
                            <Input
                                v-model="formData.nit"
                                type="text"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                :readonly="isReadonly"
                            />
                            <p class="text-xs text-gray-500">Para autônomos ou contribuintes individuais</p>
                        </div>

                        <!-- Campo: CNH -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">CNH</label>
                            <Input
                                v-model="formData.cnh"
                                type="text"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                :readonly="isReadonly"
                            />
                        </div>

                        <!-- Campo: Categoria CNH -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Categoria CNH</label>
                            <select
                                v-model="formData.cnh_category"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                :disabled="isReadonly"
                            >
                                <option value="">Selecione</option>
                                <option v-for="cat in cnhCategories" :key="cat" :value="cat">{{ cat }}</option>
                            </select>
                        </div>

                        <!-- Campo: Validade CNH -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Validade CNH</label>
                            <Input
                                v-model="formData.cnh_expiry"
                                type="date"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                :readonly="isReadonly"
                            />
                        </div>

                        <!-- Campo: Registro profissional -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Registro profissional</label>
                            <Input
                                v-model="formData.professional_registration"
                                type="text"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                :readonly="isReadonly"
                            />
                            <p class="text-xs text-gray-500">Ex: CREA, CRM, OAB, etc.</p>
                        </div>
                    </div>
                </TabsContent>
                <TabsContent value="address">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Campo: CEP -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700"> CEP * <span class="text-xs font-medium text-primary">(Preenchimento automático)</span></label>
                            <Input
                                v-model="formData.postal_code"
                                type="text"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                @input="formatCEP"
                                :readonly="isReadonly"
                            />
                        </div>

                        <!-- Campo: Logradouro -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Logradouro *</label>
                            <Input
                                v-model="formData.street"
                                type="text"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                :readonly="isReadonly"
                            />
                        </div>

                        <!-- Campo: Número -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Número *</label>
                            <Input
                                v-model="formData.number"
                                type="text"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                :readonly="isReadonly"
                            />
                        </div>

                        <!-- Campo: Complemento -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Complemento</label>
                            <Input
                                v-model="formData.complement"
                                type="text"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                :readonly="isReadonly"
                            />
                        </div>

                        <!-- Campo: Bairro -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Bairro *</label>
                            <Input
                                v-model="formData.district"
                                type="text"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                :readonly="isReadonly"
                            />
                        </div>

                        <!-- Campo: Cidade -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Cidade *</label>
                            <Input
                                v-model="formData.city"
                                type="text"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                :readonly="isReadonly"
                            />
                        </div>

                        <!-- Campo: Estado -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Estado *</label>
                            <select
                                v-model="formData.state"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                :disabled="isReadonly"
                            >
                                <option value="">Selecione</option>
                                <option v-for="uf in states" :key="uf" :value="uf">{{ uf }}</option>
                            </select>
                        </div>
                    </div>
                </TabsContent>
                <TabsContent value="bank">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Campo: Banco -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Banco *</label>
                            <select
                                v-model="formData.bank"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                :disabled="isReadonly"
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
                            <Input
                                v-model="formData.agency"
                                type="text"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                :readonly="isReadonly"
                            />
                        </div>

                        <!-- Campo: Conta -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Conta *</label>
                            <Input
                                v-model="formData.account"
                                type="text"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                :readonly="isReadonly"
                            />
                        </div>

                        <!-- Campo: Tipo de conta -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Tipo de conta *</label>
                            <select
                                v-model="formData.account_type"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                :disabled="isReadonly"
                            >
                                <option value="">Selecione</option>
                                <option value="corrente">Conta Corrente</option>
                                <option value="poupanca">Conta Poupança</option>
                                <option value="salario">Conta Salário</option>
                            </select>
                        </div>

                        <!-- Campo: Tipo Chave PIX -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Tipo de Chave PIX</label>
                            <select
                                v-model="formData.pix_key_type"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                :disabled="isReadonly"
                            >
                                <option disabled value="">Selecione</option>
                                <option value="cpf">CPF</option>
                                <option value="email">E-mail</option>
                                <option value="phone">Telefone</option>
                                <option value="random">Chave Aleatória</option>
                            </select>
                            <p class="text-xs text-gray-500">CPF, e-mail, telefone ou chave aleatória</p>
                        </div>

                        <!-- Campo: Chave PIX -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Chave PIX</label>
                            <Input
                                v-model="formData.pix_key"
                                type="text"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                :readonly="isReadonly"
                            />
                            <p class="text-xs text-gray-500">CPF, e-mail, telefone ou chave aleatória</p>
                        </div>
                    </div>
                </TabsContent>
                <TabsContent value="contract">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Status -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Status *</label>
                            <select
                                v-model="formData.status"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                :disabled="isReadonly"
                            >
                                <option value="active">Ativo</option>
                                <option value="inactive">Inativo</option>
                                <option value="on_vacation">Em Férias</option>
                                <option value="on_leave">Afastado</option>
                                <option value="terminated">Desligado</option>
                            </select>
                        </div>

                        <!-- Campo: Cargo/função -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Cargo/função *</label>
                            <Input
                                v-model="formData.role"
                                type="text"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                :readonly="isReadonly"
                            />
                        </div>

                        <!-- Campo: Setor/departamento -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Setor/departamento *</label>
                            <select
                                v-model="formData.department"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                :disabled="isReadonly"
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
                                :disabled="isReadonly"
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
                            <Input
                                v-model="formData.admission_date"
                                type="date"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                :readonly="isReadonly"
                            />
                        </div>

                        <!-- Campo: Data de desligamento -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Data de desligamento</label>
                            <Input
                                v-model="formData.termination_date"
                                type="date"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                :disabled="formData.status !== 'desligado' || isReadonly"
                            />
                            <p class="text-xs text-gray-500">Aplicável apenas para funcionários desligados</p>
                        </div>

                        <!-- Campo: Salário -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Salário *</label>
                            <Input
                                v-model="formData.salary"
                                type="text"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                @input="formatSalary"
                                :readonly="isReadonly"
                            />
                        </div>

                        <!-- Campo: Jornada de trabalho -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Jornada de trabalho *</label>
                            <Input
                                v-model="formData.work_schedule"
                                type="text"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                placeholder="Ex: 44 horas semanais"
                                :readonly="isReadonly"
                            />
                        </div>

                        <!-- Campo: Benefícios -->
                        <div class="col-span-1 md:col-span-2 lg:col-span-3 space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Benefícios</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div class="flex items-center">
                                    <Input
                                        v-model="formData.benefits"
                                        type="checkbox"
                                        value="Vale Transporte"
                                        class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                                        :disabled="isReadonly"
                                    />
                                    <label class="ml-2 text-sm text-gray-700">Vale Transporte</label>
                                </div>
                                <div class="flex items-center">
                                    <Input
                                        v-model="formData.benefits"
                                        type="checkbox"
                                        value="valeRefeicao"
                                        class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                                        :disabled="isReadonly"
                                    />
                                    <label class="ml-2 text-sm text-gray-700">Vale Refeição</label>
                                </div>
                                <div class="flex items-center">
                                    <Input
                                        v-model="formData.benefits"
                                        type="checkbox"
                                        value="Plano de Saúde"
                                        class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                                        :disabled="isReadonly"
                                    />
                                    <label class="ml-2 text-sm text-gray-700">Plano de Saúde</label>
                                </div>
                                <div class="flex items-center">
                                    <Input
                                        v-model="formData.benefits"
                                        type="checkbox"
                                        value="Plano Odontológico"
                                        class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                                        :disabled="isReadonly"
                                    />
                                    <label class="ml-2 text-sm text-gray-700">Plano Odontológico</label>
                                </div>
                                <div class="flex items-center">
                                    <Input
                                        v-model="formData.benefits"
                                        type="checkbox"
                                        value="Seguro de Vida"
                                        class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                                        :disabled="isReadonly"
                                    />
                                    <label class="ml-2 text-sm text-gray-700">Seguro de Vida</label>
                                </div>
                                <div class="flex items-center">
                                    <Input
                                        v-model="formData.benefits"
                                        type="checkbox"
                                        value="Previdência Privada"
                                        class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                                        :disabled="isReadonly"
                                    />
                                    <label class="ml-2 text-sm text-gray-700">Previdência Privada</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </TabsContent>
                <TabsContent value="health">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Campo: Data do último exame -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Data do último exame
                                admissional/periódico *</label>
                            <Input
                                v-model="formData.last_exam_date"
                                type="date"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                :readonly="isReadonly"
                            />
                        </div>

                        <!-- Campo: Data do próximo exame -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Data do próximo exame *</label>
                            <Input
                                v-model="formData.next_exam_date"
                                type="date"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                :readonly="isReadonly"
                            />
                        </div>

                        <!-- Campo: Resultado do ASO -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Resultado do ASO *</label>
                            <select
                                v-model="formData.aso_result"
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                required
                                :disabled="isReadonly"
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
                                :readonly="isReadonly"
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
                                :readonly="isReadonly"
                            ></textarea>
                        </div>
                    </div>
                </TabsContent>
                <TabsContent value="dependents">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Dependentes</h3>
                        <!-- Botão para adicionar novo dependente -->
                        <Button
                            @click="addDependent"
                            type="button"
                            class="btn-primary flex items-center"
                        >
                            <PlusIcon class="h-4 w-4 mr-1"/>
                            Adicionar
                        </Button>
                    </div>

                    <!-- Mensagem exibida quando não há dependentes -->
                    <div v-if="formData.dependents.length === 0" class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
                        Nenhum dependente cadastrado
                    </div>

                    <!-- Formulário para cada dependente -->
                    <div v-for="(dependent, index) in formData.dependents" :key="dependent.id"
                         class="border p-4 rounded-md mb-4 bg-white shadow-sm">
                        <div class="flex justify-end items-center mb-4">
                            <!-- Botão para remover dependente -->
                            <Button
                                @click="removeDependent(index)"
                                type="button"
                                class="text-red-600 hover:text-red-800"
                            >
                                <TrashIcon class="h-5 w-5"/>
                            </Button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Campos do formulário do dependente -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Nome completo *</label>
                                <Input
                                    v-model="dependent.name"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                    :readonly="isReadonly"
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Data de nascimento *</label>
                                <Input
                                    v-model="dependent.birth_date"
                                    type="date"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                    :readonly="isReadonly"
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">CPF *</label>
                                <Input
                                    v-model="dependent.cpf"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                    @input="(e) => formatDependentCPF(e, index)"
                                    :readonly="isReadonly"
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Grau de parentesco *</label>
                                <select
                                    v-model="dependent.relationship"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                    :disabled="isReadonly"
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
                                <Input
                                    v-model="dependent.issuing_agency"
                                    type="text"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                    :readonly="isReadonly"
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Data de Emissão *</label>
                                <Input
                                    v-model="dependent.issue_date"
                                    type="date"
                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                    required
                                    :readonly="isReadonly"
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Finalidade *</label>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <Input
                                            v-model="dependent.purposes"
                                            type="checkbox"
                                            value="income_tax"
                                            class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                                            :disabled="isReadonly"
                                        />
                                        <label class="ml-2 text-sm text-gray-700">Imposto de Renda</label>
                                    </div>
                                    <div class="flex items-center">
                                        <Input
                                            v-model="dependent.purposes"
                                            type="checkbox"
                                            value="health_plan"
                                            class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                                            :disabled="isReadonly"
                                        />
                                        <label class="ml-2 text-sm text-gray-700">Plano de Saúde</label>
                                    </div>
                                    <div class="flex items-center">
                                        <Input
                                            v-model="dependent.purposes"
                                            type="checkbox"
                                            value="dental_plan"
                                            class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                                            :disabled="isReadonly"
                                        />
                                        <label class="ml-2 text-sm text-gray-700">Plano Odontológico</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </TabsContent>
                <TabsContent value="attachments">
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
                                <Input type="file" class="hidden" @change="handleFileUpload" multiple />
                            </label>
                        </div>
                    </div>

                    <!-- Tabela de anexos -->
                    <AttachmentsDisplay :allAttachments="formData.attachments"/>
                </TabsContent>
            </Tabs>
            <!-- Botões de ação do formulário -->
            <div class="flex justify-end space-x-4 mt-8 pt-4">
                <Button
                    v-if="!isReadonly"
                    type="button"
                    @click="isReadonly = true"
                    class="btn-primary-v2"
                >
                    Cancelar
                </Button>
                <!-- Botão para salvar funcionário existente -->
                <Button
                    v-if="!isReadonly"
                    type="button"
                    @click="saveEmployee"
                    class="btn-primary"
                >
                    Salvar
                </Button>
            </div>
        </main>
    </AppLayout>
</template>

<style scoped>

</style>
