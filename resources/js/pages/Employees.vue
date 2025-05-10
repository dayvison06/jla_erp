<script setup lang="ts">
import AppLayout from "@/layouts/AppLayout.vue";
import { Head } from '@inertiajs/vue3';
import { ref, reactive, computed, onMounted, watch } from 'vue'
import {
    PlusIcon,
    SearchIcon,
    EditIcon,
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
    CheckIcon,
    AlertCircleIcon,
    CalendarIcon,
    ClockIcon,
    BellIcon,
    UserPlusIcon,
    UserMinusIcon,
    BadgeCheckIcon
} from 'lucide-vue-next'
import type { BreadcrumbItem } from "@/types";

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Funcionários', href: '/funcionarios' },
];

// Tipos
interface Dependente {
    id: number;
    nome: string;
    dataNascimento: string;
    cpf: string;
    parentesco: string;
    finalidades: string[];
}

interface Documento {
    id: number;
    nome: string;
    tipo: string;
    dataEmissao: string;
    dataValidade: string | null;
    arquivo: File | null;
    url: string;
    status: 'valido' | 'pendente' | 'vencido';
}

interface Anexo {
    id: number;
    nome: string;
    tipo: string;
    tamanho: string;
    dataUpload: string;
    arquivo: File | null;
    url: string;
    categoria: string;
}

interface HistoricoCargo {
    id: number;
    cargo: string;
    departamento: string;
    dataInicio: string;
    dataFim: string | null;
    salario: string;
    motivo: string;
}

interface Employee {
    id: number;
    nome: string;
    dataNascimento: string;
    sexo: string;
    estadoCivil: string;
    nacionalidade: string;
    naturalidade: string;
    cpf: string;
    rg: string;
    orgaoEmissor: string;
    dataEmissao: string;
    tituloEleitor: string;
    reservista: string;
    nomeMae: string;
    nomePai: string;
    foto: string | null;
    status: 'ativo' | 'inativo' | 'ferias' | 'afastado' | 'desligado';

    // Documentos Trabalhistas
    numeroCtps: string;
    serieCtps: string;
    ufCtps: string;
    pisPasep: string;
    nit: string;
    cnh: string;
    categoriaCnh: string;
    validadeCnh: string;
    registroProfissional: string;

    // Endereço e Contato
    cep: string;
    logradouro: string;
    numero: string;
    complemento: string;
    bairro: string;
    cidade: string;
    estado: string;
    telefone: string;
    celular: string;
    email: string;
    contatoEmergencia: string;
    telefoneEmergencia: string;

    // Dados Bancários
    banco: string;
    agencia: string;
    conta: string;
    tipoConta: string;
    chavePix: string;

    // Informações Contratuais
    cargo: string;
    departamento: string;
    tipoVinculo: string;
    dataAdmissao: string;
    dataDesligamento: string | null;
    salario: string;
    jornadaTrabalho: string;
    beneficios: string[];
    historicosCargo: HistoricoCargo[];

    // Saúde e Segurança do Trabalho
    dataUltimoExame: string;
    dataProximoExame: string;
    resultadoAso: string;
    alergias: string;
    tipoSanguineo: string;
    historicoAcidentes: string;

    // Educação e Experiência
    escolaridade: string;
    cursos: string;
    certificacoes: string;
    experiencia: string;

    // Dependentes e Anexos
    dependentes: Dependente[];
    documentos: Documento[];
    anexos: Anexo[];
}

// Estado
const showEmployeeForm = ref(false)
const activeTab = ref('personal')
const searchQuery = ref('')
const statusFilter = ref('todos')
const departmentFilter = ref('todos')
const employees = ref<Employee[]>([])
const currentEmployee = ref<Employee | null>(null)
const showDeleteModal = ref(false)
const employeeToDelete = ref<Employee | null>(null)
const isDragging = ref(false)
const showDocumentModal = ref(false)
const currentDocument = ref<Documento | null>(null)
const showProfilePhotoModal = ref(false)
const profilePhotoPreview = ref<string | null>(null)
const showHistoryModal = ref(false)
const newCargoHistory = reactive<HistoricoCargo>({
    id: 0,
    cargo: '',
    departamento: '',
    dataInicio: '',
    dataFim: null,
    salario: '',
    motivo: ''
})

// Dados de formulário
const formData = reactive<Employee>({
    id: 0,
    nome: '',
    dataNascimento: '',
    sexo: '',
    estadoCivil: '',
    nacionalidade: '',
    naturalidade: '',
    cpf: '',
    rg: '',
    orgaoEmissor: '',
    dataEmissao: '',
    tituloEleitor: '',
    reservista: '',
    nomeMae: '',
    nomePai: '',
    foto: null,
    status: 'ativo',

    numeroCtps: '',
    serieCtps: '',
    ufCtps: '',
    pisPasep: '',
    nit: '',
    cnh: '',
    categoriaCnh: '',
    validadeCnh: '',
    registroProfissional: '',

    cep: '',
    logradouro: '',
    numero: '',
    complemento: '',
    bairro: '',
    cidade: '',
    estado: '',
    telefone: '',
    celular: '',
    email: '',
    contatoEmergencia: '',
    telefoneEmergencia: '',

    banco: '',
    agencia: '',
    conta: '',
    tipoConta: '',
    chavePix: '',

    cargo: '',
    departamento: '',
    tipoVinculo: '',
    dataAdmissao: '',
    dataDesligamento: null,
    salario: '',
    jornadaTrabalho: '',
    beneficios: [],
    historicosCargo: [],

    dataUltimoExame: '',
    dataProximoExame: '',
    resultadoAso: '',
    alergias: '',
    tipoSanguineo: '',
    historicoAcidentes: '',

    escolaridade: '',
    cursos: '',
    certificacoes: '',
    experiencia: '',

    dependentes: [],
    documentos: [],
    anexos: []
})

// Dados de referência
const tabs = [
    {id: 'personal', name: 'Dados Pessoais', icon: UserIcon},
    {id: 'documents', name: 'Documentos Trabalhistas', icon: FileTextIcon},
    {id: 'contact', name: 'Endereço e Contato', icon: FileIcon},
    {id: 'bank', name: 'Dados Bancários', icon: FileIcon},
    {id: 'contract', name: 'Informações Contratuais', icon: FileIcon},
    {id: 'health', name: 'Saúde e Segurança', icon: FileIcon},
    {id: 'dependents', name: 'Dependentes', icon: FileIcon},
    {id: 'attachments', name: 'Anexos', icon: FileIcon},
]

const estados = [
    'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG',
    'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'
]

const bancos = [
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

const departamentos = [
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

const categoriasCnh = [
    'A', 'B', 'C', 'D', 'E', 'AB', 'AC', 'AD', 'AE'
]

const tiposSanguineos = [
    'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'
]

const categoriasDocumento = [
    'Identificação',
    'Trabalhista',
    'Acadêmico',
    'Saúde',
    'Financeiro',
    'Outros'
]

// Computed properties
const filteredEmployees = computed(() => {
    let result = employees.value;

    // Aplicar filtro de pesquisa
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(employee =>
            employee.nome.toLowerCase().includes(query) ||
            employee.cpf.includes(query) ||
            employee.cargo.toLowerCase().includes(query) ||
            employee.departamento.toLowerCase().includes(query) ||
            employee.email.toLowerCase().includes(query)
        );
    }

    // Aplicar filtro de status
    if (statusFilter.value !== 'todos') {
        result = result.filter(employee => employee.status === statusFilter.value);
    }

    // Aplicar filtro de departamento
    if (departmentFilter.value !== 'todos') {
        result = result.filter(employee => employee.departamento === departmentFilter.value);
    }

    return result;
})

const uniqueDepartments = computed(() => {
    const departments = new Set<string>();
    employees.value.forEach(emp => {
        if (emp.departamento) {
            departments.add(emp.departamento);
        }
    });
    return Array.from(departments).sort();
})

const documentosVencidos = computed(() => {
    let count = 0;
    employees.value.forEach(emp => {
        count += emp.documentos.filter(doc => doc.status === 'vencido').length;
    });
    return count;
})

const proximosExames = computed(() => {
    const hoje = new Date();
    const trintaDias = new Date();
    trintaDias.setDate(hoje.getDate() + 30);

    return employees.value.filter(emp => {
        if (!emp.dataProximoExame) return false;
        const dataExame = new Date(emp.dataProximoExame);
        return dataExame >= hoje && dataExame <= trintaDias;
    });
})

// Métodos
const getInitials = (name: string) => {
    if (!name) return '';
    return name
        .split(' ')
        .map(part => part.charAt(0))
        .join('')
        .substring(0, 2)
        .toUpperCase();
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

const formatDependenteCPF = (e: Event, index: number) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 11) {
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    }

    formData.dependentes[index].cpf = value;
}

const formatCEP = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 8) {
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
    }

    formData.cep = value;
}

const formatTelefone = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 10) {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
    }

    formData.telefone = value;
}

const formatCelular = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 11) {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
    }

    formData.celular = value;
}

const formatTelefoneEmergencia = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 11) {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
    }

    formData.telefoneEmergencia = value;
}

const formatSalario = (e: Event) => {
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

    formData.salario = value;
}

const formatSalarioHistorico = (e: Event) => {
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

    newCargoHistory.salario = value;
}

const buscarCep = async () => {
    const cep = formData.cep.replace(/\D/g, '');

    if (cep.length !== 8) return;

    try {
        const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        const data = await response.json();

        if (!data.erro) {
            formData.logradouro = data.logradouro;
            formData.bairro = data.bairro;
            formData.cidade = data.localidade;
            formData.estado = data.uf;
        }
    } catch (error) {
        console.error('Erro ao buscar CEP:', error);
    }
}

const addDependente = () => {
    formData.dependentes.push({
        id: Date.now(),
        nome: '',
        dataNascimento: '',
        cpf: '',
        parentesco: '',
        finalidades: []
    });
}

const removeDependente = (index: number) => {
    formData.dependentes.splice(index, 1);
}

const addDocumento = () => {
    showDocumentModal.value = true;
    currentDocument.value = {
        id: Date.now(),
        nome: '',
        tipo: '',
        dataEmissao: '',
        dataValidade: null,
        arquivo: null,
        url: '',
        status: 'pendente'
    };
}

const saveDocumento = () => {
    if (!currentDocument.value) return;

    // Verificar status do documento baseado na data de validade
    if (currentDocument.value.dataValidade) {
        const hoje = new Date();
        const validade = new Date(currentDocument.value.dataValidade);

        if (validade < hoje) {
            currentDocument.value.status = 'vencido';
        } else {
            currentDocument.value.status = 'valido';
        }
    } else {
        currentDocument.value.status = 'valido';
    }

    // Se for um novo documento, adiciona à lista
    if (!formData.documentos.some(doc => doc.id === currentDocument.value?.id)) {
        formData.documentos.push(currentDocument.value);
    } else {
        // Se for edição, atualiza o documento existente
        const index = formData.documentos.findIndex(doc => doc.id === currentDocument.value?.id);
        if (index !== -1) {
            formData.documentos[index] = currentDocument.value;
        }
    }

    showDocumentModal.value = false;
    currentDocument.value = null;
}

const editDocumento = (documento: Documento) => {
    currentDocument.value = { ...documento };
    showDocumentModal.value = true;
}

const removeDocumento = (id: number) => {
    formData.documentos = formData.documentos.filter(doc => doc.id !== id);
}

const handleDocumentUpload = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (!input.files || !input.files.length || !currentDocument.value) return;

    const file = input.files[0];
    currentDocument.value.arquivo = file;
    currentDocument.value.nome = file.name;
    currentDocument.value.url = URL.createObjectURL(file);

    // Reset input
    input.value = '';
}

const addHistoricoCargo = () => {
    showHistoryModal.value = true;
    Object.assign(newCargoHistory, {
        id: Date.now(),
        cargo: formData.cargo,
        departamento: formData.departamento,
        dataInicio: new Date().toISOString().split('T')[0],
        dataFim: null,
        salario: formData.salario,
        motivo: 'Promoção'
    });
}

const saveHistoricoCargo = () => {
    formData.historicosCargo.push({...newCargoHistory});
    showHistoryModal.value = false;

    // Atualizar cargo atual se for uma promoção/alteração
    if (!newCargoHistory.dataFim) {
        formData.cargo = newCargoHistory.cargo;
        formData.departamento = newCargoHistory.departamento;
        formData.salario = newCargoHistory.salario;
    }

    // Resetar formulário
    Object.assign(newCargoHistory, {
        id: 0,
        cargo: '',
        departamento: '',
        dataInicio: '',
        dataFim: null,
        salario: '',
        motivo: ''
    });
}

const removeHistoricoCargo = (id: number) => {
    formData.historicosCargo = formData.historicosCargo.filter(hist => hist.id !== id);
}

const editEmployee = (employee: Employee) => {
    currentEmployee.value = {...employee};
    Object.assign(formData, JSON.parse(JSON.stringify(employee)));
    showEmployeeForm.value = true;
    activeTab.value = 'personal';
}

const confirmDelete = (employee: Employee) => {
    employeeToDelete.value = employee;
    showDeleteModal.value = true;
}

const deleteEmployee = () => {
    if (employeeToDelete.value) {
        employees.value = employees.value.filter(emp => emp.id !== employeeToDelete.value?.id);
        showDeleteModal.value = false;
        employeeToDelete.value = null;
    }
}

const saveEmployee = () => {
    // Validação básica
    if (!formData.nome || !formData.cpf || !formData.dataNascimento) {
        alert('Por favor, preencha todos os campos obrigatórios.');
        return;
    }

    if (currentEmployee.value) {
        // Edição de funcionário existente
        const index = employees.value.findIndex(emp => emp.id === currentEmployee.value?.id);
        if (index !== -1) {
            employees.value[index] = {...formData};
        }
    } else {
        // Novo funcionário
        const newEmployee = {
            ...formData,
            id: Date.now() // Gera um ID único baseado no timestamp
        };
        employees.value.push(newEmployee);
    }

    showEmployeeForm.value = false;
    resetForm();
}

const resetForm = () => {
    // Reseta o formulário para valores padrão
    Object.assign(formData, {
        id: 0,
        nome: '',
        dataNascimento: '',
        sexo: '',
        estadoCivil: '',
        nacionalidade: '',
        naturalidade: '',
        cpf: '',
        rg: '',
        orgaoEmissor: '',
        dataEmissao: '',
        tituloEleitor: '',
        reservista: '',
        nomeMae: '',
        nomePai: '',
        foto: null,
        status: 'ativo',

        numeroCtps: '',
        serieCtps: '',
        ufCtps: '',
        pisPasep: '',
        nit: '',
        cnh: '',
        categoriaCnh: '',
        validadeCnh: '',
        registroProfissional: '',

        cep: '',
        logradouro: '',
        numero: '',
        complemento: '',
        bairro: '',
        cidade: '',
        estado: '',
        telefone: '',
        celular: '',
        email: '',
        contatoEmergencia: '',
        telefoneEmergencia: '',

        banco: '',
        agencia: '',
        conta: '',
        tipoConta: '',
        chavePix: '',

        cargo: '',
        departamento: '',
        tipoVinculo: '',
        dataAdmissao: '',
        dataDesligamento: null,
        salario: '',
        jornadaTrabalho: '',
        beneficios: [],
        historicosCargo: [],

        dataUltimoExame: '',
        dataProximoExame: '',
        resultadoAso: '',
        alergias: '',
        tipoSanguineo: '',
        historicoAcidentes: '',

        escolaridade: '',
        cursos: '',
        certificacoes: '',
        experiencia: '',

        dependentes: [],
        documentos: [],
        anexos: []
    });

    currentEmployee.value = null;
}

const handleFileUpload = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
        const file = input.files[0];
        const fileSize = file.size / 1024 < 1024
            ? `${(file.size / 1024).toFixed(2)} KB`
            : `${(file.size / 1024 / 1024).toFixed(2)} MB`;

        const newAttachment = {
            id: Date.now(),
            nome: file.name,
            tipo: file.type,
            tamanho: fileSize,
            dataUpload: new Date().toISOString().split('T')[0],
            arquivo: file,
            url: URL.createObjectURL(file),
            categoria: 'Outros'
        };

        formData.anexos.push(newAttachment);

        // Reset input
        input.value = '';
    }
}

const handleFileDrop = (event: DragEvent) => {
    isDragging.value = false;

    if (!event.dataTransfer?.files) return;

    const files = Array.from(event.dataTransfer.files);

    files.forEach(file => {
        const fileSize = file.size / 1024 < 1024
            ? `${(file.size / 1024).toFixed(2)} KB`
            : `${(file.size / 1024 / 1024).toFixed(2)} MB`;

        const newAttachment = {
            id: Date.now() + Math.floor(Math.random() * 1000), // Garante IDs únicos mesmo com múltiplos arquivos
            nome: file.name,
            tipo: file.type || 'application/octet-stream',
            tamanho: fileSize,
            dataUpload: new Date().toISOString().split('T')[0],
            arquivo: file,
            url: URL.createObjectURL(file),
            categoria: 'Outros'
        };

        formData.anexos.push(newAttachment);
    });
}

const removeAnexo = (id: number) => {
    const index = formData.anexos.findIndex(anexo => anexo.id === id);
    if (index !== -1) {
        // Revoke object URL to prevent memory leaks
        if (formData.anexos[index].url) {
            URL.revokeObjectURL(formData.anexos[index].url);
        }
        formData.anexos.splice(index, 1);
    }
}

const getFileIcon = (tipo: string) => {
    if (tipo.includes('pdf')) return FileTextIcon;
    if (tipo.includes('image')) return ImageIcon;
    if (tipo.includes('word') || tipo.includes('document')) return FileTextIcon;
    if (tipo.includes('excel') || tipo.includes('sheet')) return FileSpreadsheetIcon;
    if (tipo.includes('presentation') || tipo.includes('powerpoint')) return File;
    return FileIcon;
}

const handleProfilePhotoUpload = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
        const file = input.files[0];

        // Verificar se é uma imagem
        if (!file.type.startsWith('image/')) {
            alert('Por favor, selecione apenas arquivos de imagem.');
            return;
        }

        profilePhotoPreview.value = URL.createObjectURL(file);
        showProfilePhotoModal.value = true;

        // Reset input
        input.value = '';
    }
}

const saveProfilePhoto = () => {
    formData.foto = profilePhotoPreview.value;
    showProfilePhotoModal.value = false;
}

const cancelProfilePhoto = () => {
    if (profilePhotoPreview.value) {
        URL.revokeObjectURL(profilePhotoPreview.value);
    }
    profilePhotoPreview.value = null;
    showProfilePhotoModal.value = false;
}

const getStatusColor = (status: string) => {
    switch (status) {
        case 'ativo':
            return 'bg-green-100 text-green-800';
        case 'inativo':
            return 'bg-gray-100 text-gray-800';
        case 'ferias':
            return 'bg-blue-100 text-blue-800';
        case 'afastado':
            return 'bg-yellow-100 text-yellow-800';
        case 'desligado':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

const getStatusText = (status: string) => {
    switch (status) {
        case 'ativo':
            return 'Ativo';
        case 'inativo':
            return 'Inativo';
        case 'ferias':
            return 'Em Férias';
        case 'afastado':
            return 'Afastado';
        case 'desligado':
            return 'Desligado';
        default:
            return 'Desconhecido';
    }
}

const getDocumentStatusColor = (status: string) => {
    switch (status) {
        case 'valido':
            return 'bg-green-100 text-green-800';
        case 'pendente':
            return 'bg-yellow-100 text-yellow-800';
        case 'vencido':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

const getDocumentStatusText = (status: string) => {
    switch (status) {
        case 'valido':
            return 'Válido';
        case 'pendente':
            return 'Pendente';
        case 'vencido':
            return 'Vencido';
        default:
            return 'Desconhecido';
    }
}

const exportarDados = () => {
    // Implementação básica de exportação para CSV
    let csv = 'Nome,CPF,Cargo,Departamento,Data de Admissão,Status\n';

    filteredEmployees.value.forEach(emp => {
        csv += `"${emp.nome}","${emp.cpf}","${emp.cargo}","${emp.departamento}","${formatDate(emp.dataAdmissao)}","${getStatusText(emp.status)}"\n`;
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', 'funcionarios.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Verificar documentos vencidos e próximos a vencer
const verificarDocumentos = () => {
    const hoje = new Date();

    employees.value.forEach(emp => {
        emp.documentos.forEach(doc => {
            if (doc.dataValidade) {
                const dataValidade = new Date(doc.dataValidade);

                if (dataValidade < hoje) {
                    doc.status = 'vencido';
                } else {
                    // Verificar se está próximo de vencer (30 dias)
                    const trintaDias = new Date();
                    trintaDias.setDate(hoje.getDate() + 30);

                    if (dataValidade <= trintaDias) {
                        // Aqui poderia implementar notificações
                        console.log(`Documento ${doc.nome} do funcionário ${emp.nome} vencerá em ${formatDate(doc.dataValidade)}`);
                    }
                }
            }
        });
    });
}

// Carrega dados de exemplo
onMounted(() => {
    // Simulando dados de exemplo
    employees.value = [
        {
            id: 1,
            nome: 'João Silva',
            dataNascimento: '1985-05-15',
            sexo: 'M',
            estadoCivil: 'casado',
            nacionalidade: 'Brasileira',
            naturalidade: 'São Paulo',
            cpf: '123.456.789-00',
            rg: '12.345.678-9',
            orgaoEmissor: 'SSP',
            dataEmissao: '2010-01-10',
            tituloEleitor: '123456789012',
            reservista: '12345678',
            nomeMae: 'Maria Silva',
            nomePai: 'José Silva',
            foto: null,
            status: 'ativo',

            numeroCtps: '12345',
            serieCtps: '123',
            ufCtps: 'SP',
            pisPasep: '12345678901',
            nit: '',
            cnh: '12345678901',
            categoriaCnh: 'B',
            validadeCnh: '2025-01-10',
            registroProfissional: '',

            cep: '01234-567',
            logradouro: 'Rua das Flores',
            numero: '123',
            complemento: 'Apto 45',
            bairro: 'Centro',
            cidade: 'São Paulo',
            estado: 'SP',
            telefone: '(11) 3456-7890',
            celular: '(11) 98765-4321',
            email: 'joao.silva@email.com',
            contatoEmergencia: 'Maria Silva',
            telefoneEmergencia: '(11) 99876-5432',

            banco: '001',
            agencia: '1234',
            conta: '12345-6',
            tipoConta: 'corrente',
            chavePix: 'joao.silva@email.com',

            cargo: 'Analista de Sistemas',
            departamento: 'TI',
            tipoVinculo: 'clt',
            dataAdmissao: '2020-01-15',
            dataDesligamento: null,
            salario: 'R$ 5.000,00',
            jornadaTrabalho: '44 horas semanais',
            beneficios: ['valeTransporte', 'valeRefeicao', 'planoSaude'],
            historicosCargo: [
                {
                    id: 101,
                    cargo: 'Desenvolvedor',
                    departamento: 'TI',
                    dataInicio: '2020-01-15',
                    dataFim: '2021-06-30',
                    salario: 'R$ 4.000,00',
                    motivo: 'Contratação'
                },
                {
                    id: 102,
                    cargo: 'Analista de Sistemas',
                    departamento: 'TI',
                    dataInicio: '2021-07-01',
                    dataFim: null,
                    salario: 'R$ 5.000,00',
                    motivo: 'Promoção'
                }
            ],

            dataUltimoExame: '2023-01-10',
            dataProximoExame: '2024-01-10',
            resultadoAso: 'apto',
            alergias: 'Nenhuma',
            tipoSanguineo: 'O+',
            historicoAcidentes: '',

            escolaridade: 'superior',
            cursos: 'Análise e Desenvolvimento de Sistemas',
            certificacoes: 'AWS Certified Developer',
            experiencia: 'Desenvolvedor Full Stack na Empresa XYZ (2015-2020)',

            dependentes: [
                {
                    id: 201,
                    nome: 'Maria Silva',
                    dataNascimento: '2015-03-20',
                    cpf: '987.654.321-00',
                    parentesco: 'filho',
                    finalidades: ['ir', 'planoSaude']
                }
            ],
            documentos: [
                {
                    id: 301,
                    nome: 'Carteira de Trabalho',
                    tipo: 'Trabalhista',
                    dataEmissao: '2010-01-10',
                    dataValidade: null,
                    arquivo: null,
                    url: '',
                    status: 'valido'
                },
                {
                    id: 302,
                    nome: 'CNH',
                    tipo: 'Identificação',
                    dataEmissao: '2020-01-10',
                    dataValidade: '2025-01-10',
                    arquivo: null,
                    url: '',
                    status: 'valido'
                }
            ],
            anexos: []
        },
        {
            id: 2,
            nome: 'Ana Oliveira',
            dataNascimento: '1990-08-22',
            sexo: 'F',
            estadoCivil: 'solteiro',
            nacionalidade: 'Brasileira',
            naturalidade: 'Rio de Janeiro',
            cpf: '987.654.321-00',
            rg: '98.765.432-1',
            orgaoEmissor: 'SSP',
            dataEmissao: '2012-05-20',
            tituloEleitor: '987654321098',
            reservista: '',
            nomeMae: 'Carla Oliveira',
            nomePai: 'Roberto Oliveira',
            foto: null,
            status: 'ativo',

            numeroCtps: '54321',
            serieCtps: '321',
            ufCtps: 'RJ',
            pisPasep: '98765432109',
            nit: '',
            cnh: '98765432109',
            categoriaCnh: 'B',
            validadeCnh: '2023-05-20',
            registroProfissional: '',

            cep: '98765-432',
            logradouro: 'Avenida Brasil',
            numero: '456',
            complemento: 'Bloco B',
            bairro: 'Copacabana',
            cidade: 'Rio de Janeiro',
            estado: 'RJ',
            telefone: '(21) 2345-6789',
            celular: '(21) 98765-4321',
            email: 'ana.oliveira@email.com',
            contatoEmergencia: 'Roberto Oliveira',
            telefoneEmergencia: '(21) 99876-5432',

            banco: '341',
            agencia: '4321',
            conta: '54321-0',
            tipoConta: 'corrente',
            chavePix: '987.654.321-00',

            cargo: 'Designer UX/UI',
            departamento: 'Design',
            tipoVinculo: 'clt',
            dataAdmissao: '2021-03-10',
            dataDesligamento: null,
            salario: 'R$ 4.500,00',
            jornadaTrabalho: '40 horas semanais',
            beneficios: ['valeTransporte', 'valeRefeicao', 'planoSaude', 'planoOdontologico'],
            historicosCargo: [
                {
                    id: 103,
                    cargo: 'Designer UX/UI',
                    departamento: 'Design',
                    dataInicio: '2021-03-10',
                    dataFim: null,
                    salario: 'R$ 4.500,00',
                    motivo: 'Contratação'
                }
            ],

            dataUltimoExame: '2023-02-15',
            dataProximoExame: '2024-02-15',
            resultadoAso: 'apto',
            alergias: 'Poeira',
            tipoSanguineo: 'A+',
            historicoAcidentes: '',

            escolaridade: 'superior',
            cursos: 'Design Gráfico',
            certificacoes: 'Adobe Certified Expert',
            experiencia: 'Designer na Agência ABC (2018-2021)',

            dependentes: [],
            documentos: [
                {
                    id: 303,
                    nome: 'Carteira de Trabalho',
                    tipo: 'Trabalhista',
                    dataEmissao: '2012-05-20',
                    dataValidade: null,
                    arquivo: null,
                    url: '',
                    status: 'valido'
                },
                {
                    id: 304,
                    nome: 'CNH',
                    tipo: 'Identificação',
                    dataEmissao: '2018-05-20',
                    dataValidade: '2023-05-20',
                    arquivo: null,
                    url: '',
                    status: 'vencido'
                }
            ],
            anexos: []
        },
        {
            id: 3,
            nome: 'Carlos Mendes',
            dataNascimento: '1988-11-10',
            sexo: 'M',
            estadoCivil: 'casado',
            nacionalidade: 'Brasileira',
            naturalidade: 'Belo Horizonte',
            cpf: '456.789.123-00',
            rg: '45.678.912-3',
            orgaoEmissor: 'SSP',
            dataEmissao: '2008-07-15',
            tituloEleitor: '456789123456',
            reservista: '87654321',
            nomeMae: 'Lucia Mendes',
            nomePai: 'Antonio Mendes',
            foto: null,
            status: 'ferias',

            numeroCtps: '67890',
            serieCtps: '456',
            ufCtps: 'MG',
            pisPasep: '45678912345',
            nit: '',
            cnh: '45678912345',
            categoriaCnh: 'AB',
            validadeCnh: '2026-07-15',
            registroProfissional: 'CRM 12345',

            cep: '30123-456',
            logradouro: 'Rua dos Médicos',
            numero: '789',
            complemento: 'Sala 101',
            bairro: 'Savassi',
            cidade: 'Belo Horizonte',
            estado: 'MG',
            telefone: '(31) 3456-7890',
            celular: '(31) 98765-4321',
            email: 'carlos.mendes@email.com',
            contatoEmergencia: 'Lucia Mendes',
            telefoneEmergencia: '(31) 99876-5432',

            banco: '001',
            agencia: '5678',
            conta: '98765-4',
            tipoConta: 'corrente',
            chavePix: 'carlos.mendes@email.com',

            cargo: 'Médico',
            departamento: 'Saúde',
            tipoVinculo: 'clt',
            dataAdmissao: '2019-05-20',
            dataDesligamento: null,
            salario: 'R$ 12.000,00',
            jornadaTrabalho: '30 horas semanais',
            beneficios: ['valeTransporte', 'valeRefeicao', 'planoSaude', 'planoOdontologico', 'seguroVida'],
            historicosCargo: [
                {
                    id: 104,
                    cargo: 'Médico',
                    departamento: 'Saúde',
                    dataInicio: '2019-05-20',
                    dataFim: null,
                    salario: 'R$ 12.000,00',
                    motivo: 'Contratação'
                }
            ],

            dataUltimoExame: '2023-03-20',
            dataProximoExame: '2024-03-20',
            resultadoAso: 'apto',
            alergias: 'Nenhuma',
            tipoSanguineo: 'B+',
            historicoAcidentes: '',

            escolaridade: 'superior',
            cursos: 'Medicina',
            certificacoes: 'Especialização em Clínica Médica',
            experiencia: 'Médico no Hospital XYZ (2015-2019)',

            dependentes: [
                {
                    id: 202,
                    nome: 'Pedro Mendes',
                    dataNascimento: '2018-04-12',
                    cpf: '123.456.789-01',
                    parentesco: 'filho',
                    finalidades: ['ir', 'planoSaude']
                },
                {
                    id: 203,
                    nome: 'Julia Mendes',
                    dataNascimento: '1990-06-25',
                    cpf: '234.567.890-12',
                    parentesco: 'conjuge',
                    finalidades: ['ir', 'planoSaude']
                }
            ],
            documentos: [
                {
                    id: 305,
                    nome: 'Carteira de Trabalho',
                    tipo: 'Trabalhista',
                    dataEmissao: '2008-07-15',
                    dataValidade: null,
                    arquivo: null,
                    url: '',
                    status: 'valido'
                },
                {
                    id: 306,
                    nome: 'CRM',
                    tipo: 'Profissional',
                    dataEmissao: '2015-01-10',
                    dataValidade: '2025-01-10',
                    arquivo: null,
                    url: '',
                    status: 'valido'
                }
            ],
            anexos: []
        }
    ];

    // Verificar documentos vencidos
    verificarDocumentos();
});

// Observar mudanças nos filtros para atualizar a lista
watch([searchQuery, statusFilter, departmentFilter], () => {
    // Aqui poderia implementar lógica adicional se necessário
});
</script>

<template>
    <Head title='Funcionários'/>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 bg-background text-foreground">
            <div class="flex flex-1 flex-col overflow-hidden">
                <main class="flex-1 overflow-auto p-4 md:p-6">
                    <!-- Cabeçalho do módulo -->
                    <header v-if="!showEmployeeForm" class="text-black mb-6">
                        <div class="mx-auto flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <h1 class="text-2xl font-bold">Funcionários</h1>
                            </div>
                            <button
                                @click="showEmployeeForm = true; currentEmployee = null"
                                class="bg-emerald-600 cursor-pointer hover:bg-emerald-700 text-white px-4 py-2 rounded-md flex items-center"
                            >
                                <PlusIcon class="w-5 h-5 mr-2"/>
                                Novo Funcionário
                            </button>
                        </div>
                    </header>

                    <!-- Alertas e notificações -->
                    <div v-if="!showEmployeeForm && (documentosVencidos > 0 || proximosExames.length > 0)" class="mb-6">
                        <div v-if="documentosVencidos > 0" class="bg-red-50 border-l-4 border-red-500 p-4 mb-3">
                            <div class="flex items-center">
                                <AlertCircleIcon class="h-5 w-5 text-red-500 mr-2" />
                                <p class="text-sm text-red-700">
                                    <span class="font-bold">Atenção:</span> Existem {{ documentosVencidos }} documentos vencidos que precisam de atenção.
                                </p>
                            </div>
                        </div>

                        <div v-if="proximosExames.length > 0" class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
                            <div class="flex items-center">
                                <BellIcon class="h-5 w-5 text-yellow-500 mr-2" />
                                <p class="text-sm text-yellow-700">
                                    <span class="font-bold">Lembrete:</span> {{ proximosExames.length }} funcionários têm exames médicos agendados nos próximos 30 dias.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Modo de listagem -->
                    <div v-if="!showEmployeeForm">
                        <div class="mb-6">
                            <div class="flex flex-col md:flex-row md:items-center mb-4 gap-4">
                                <div class="flex-grow">
                                    <div class="relative">
                                        <input
                                            v-model="searchQuery"
                                            type="text"
                                            placeholder="Buscar funcionário por nome, CPF, cargo..."
                                            class="w-full pl-10 pr-4 py-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        />
                                        <SearchIcon class="absolute left-3 top-2.5 text-gray-400 w-5 h-5"/>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row gap-2">
                                    <div class="relative">
                                        <select
                                            v-model="statusFilter"
                                            class="pl-10 pr-4 py-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 appearance-none"
                                        >
                                            <option value="todos">Todos os status</option>
                                            <option value="ativo">Ativos</option>
                                            <option value="inativo">Inativos</option>
                                            <option value="ferias">Em férias</option>
                                            <option value="afastado">Afastados</option>
                                            <option value="desligado">Desligados</option>
                                        </select>
                                        <FilterIcon class="absolute left-3 top-2.5 text-gray-400 w-5 h-5"/>
                                    </div>

                                    <div class="relative">
                                        <select
                                            v-model="departmentFilter"
                                            class="pl-10 pr-4 py-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 appearance-none"
                                        >
                                            <option value="todos">Todos os departamentos</option>
                                            <option v-for="dept in uniqueDepartments" :key="dept" :value="dept">{{ dept }}</option>
                                        </select>
                                        <FilterIcon class="absolute left-3 top-2.5 text-gray-400 w-5 h-5"/>
                                    </div>

                                    <button
                                        @click="exportarDados"
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
                                <tr v-for="employee in filteredEmployees" :key="employee.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div v-if="!employee.foto"
                                                     class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-800 font-semibold">
                                                    {{ getInitials(employee.nome) }}
                                                </div>
                                                <img v-else :src="employee.foto" alt="" class="h-10 w-10 rounded-full object-cover">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ employee.nome }}</div>
                                                <div class="text-sm text-gray-500">{{ employee.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ employee.cargo }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ employee.departamento }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(employee.dataAdmissao) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getStatusColor(employee.status)">
                                            {{ getStatusText(employee.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="editEmployee(employee)"
                                                class="text-emerald-600 hover:text-emerald-900 mr-3">
                                            <EditIcon class="w-5 h-5"/>
                                        </button>
                                        <button @click="confirmDelete(employee)" class="text-red-600 hover:text-red-900">
                                            <TrashIcon class="w-5 h-5"/>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="filteredEmployees.length === 0">
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
                                    filteredEmployees.length
                                }}</span> de <span class="font-medium">{{ employees.length }}</span> resultados
                            </div>
                            <div class="flex space-x-2">
                                <button class="px-3 py-1 border rounded-md hover:bg-gray-50">Anterior</button>
                                <button class="px-3 py-1 border rounded-md bg-emerald-50 text-emerald-600 font-medium">1</button>
                                <button class="px-3 py-1 border rounded-md hover:bg-gray-50">Próxima</button>
                            </div>
                        </div>
                    </div>

                    <!-- Formulário de funcionário -->
                    <div v-else>
                        <div class="flex justify-between items-center mb-5">
                            <h2 class="text-xl font-semibold">{{ currentEmployee ? `Alterando dados de ${ currentEmployee.nome }` : 'Novo Funcionário' }}</h2>
                            <button @click="showEmployeeForm = false" class="text-gray-500 hover:text-gray-700">
                                <XIcon class="w-6 h-6"/>
                            </button>
                        </div>

                        <!-- Abas -->
                        <div class="border-b">
                            <div class="flex overflow-x-auto">
                                <button
                                    v-for="tab in tabs"
                                    :key="tab.id"
                                    @click="activeTab = tab.id"
                                    :class="[
                                        'px-4 py-3 text-sm font-medium whitespace-nowrap flex items-center',
                                        activeTab === tab.id
                                            ? 'border-b-2 border-emerald-600 bg-emerald-50 rounded-t text-emerald-700'
                                            : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                    ]"
                                >
                                    <component :is="tab.icon" class="w-4 h-4 mr-2" />
                                    {{ tab.name }}
                                </button>
                            </div>
                        </div>

                        <!-- Conteúdo das abas -->
                        <div class="p-6 bg-white rounded-b-lg shadow">
                            <!-- 1. Dados Pessoais -->
                            <div v-if="activeTab === 'personal'" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Nome completo *</label>
                                        <input
                                            v-model="formData.nome"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Data de nascimento *</label>
                                        <input
                                            v-model="formData.dataNascimento"
                                            type="date"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Sexo/gênero *</label>
                                        <select
                                            v-model="formData.sexo"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        >
                                            <option value="">Selecione</option>
                                            <option value="M">Masculino</option>
                                            <option value="F">Feminino</option>
                                            <option value="O">Outro</option>
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Estado civil *</label>
                                        <select
                                            v-model="formData.estadoCivil"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
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
                                            v-model="formData.nacionalidade"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Naturalidade *</label>
                                        <input
                                            v-model="formData.naturalidade"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">CPF *</label>
                                        <input
                                            v-model="formData.cpf"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                            @input="formatCPF"
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">RG *</label>
                                        <input
                                            v-model="formData.rg"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Órgão emissor *</label>
                                        <input
                                            v-model="formData.orgaoEmissor"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Data de emissão *</label>
                                        <input
                                            v-model="formData.dataEmissao"
                                            type="date"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Título de eleitor *</label>
                                        <input
                                            v-model="formData.tituloEleitor"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Certidão de reservista</label>
                                        <input
                                            v-model="formData.reservista"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        />
                                        <p class="text-xs text-gray-500">Opcional para homens</p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Nome da mãe</label>
                                        <input
                                            v-model="formData.nomeMae"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Nome do pai</label>
                                        <input
                                            v-model="formData.nomePai"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Status *</label>
                                        <select
                                            v-model="formData.status"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
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
                                            v-model="formData.escolaridade"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
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
                                            v-model="formData.tipoSanguineo"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        >
                                            <option value="">Selecione</option>
                                            <option v-for="tipo in tiposSanguineos" :key="tipo" :value="tipo">{{ tipo }}</option>
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
                                            v-model="formData.numeroCtps"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Série CTPS *</label>
                                        <input
                                            v-model="formData.serieCtps"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">UF CTPS *</label>
                                        <select
                                            v-model="formData.ufCtps"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        >
                                            <option value="">Selecione</option>
                                            <option v-for="uf in estados" :key="uf" :value="uf">{{ uf }}</option>
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">PIS/PASEP *</label>
                                        <input
                                            v-model="formData.pisPasep"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Número do NIT</label>
                                        <input
                                            v-model="formData.nit"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        />
                                        <p class="text-xs text-gray-500">Para autônomos ou contribuintes individuais</p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">CNH</label>
                                        <input
                                            v-model="formData.cnh"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Categoria CNH</label>
                                        <select
                                            v-model="formData.categoriaCnh"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        >
                                            <option value="">Selecione</option>
                                            <option v-for="cat in categoriasCnh" :key="cat" :value="cat">{{ cat }}</option>
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Validade CNH</label>
                                        <input
                                            v-model="formData.validadeCnh"
                                            type="date"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Registro profissional</label>
                                        <input
                                            v-model="formData.registroProfissional"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        />
                                        <p class="text-xs text-gray-500">Ex: CREA, CRM, OAB, etc.</p>
                                    </div>
                                </div>

                                <!-- Documentos anexados -->
                                <div class="mt-8">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-medium">Documentos</h3>
                                        <button
                                            @click="addDocumento"
                                            type="button"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                                        >
                                            <PlusIcon class="h-4 w-4 mr-1"/>
                                            Adicionar Documento
                                        </button>
                                    </div>

                                    <div v-if="formData.documentos.length === 0" class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
                                        Nenhum documento cadastrado
                                    </div>

                                    <div v-else class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Documento
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Tipo
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Data de Emissão
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Validade
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Ações
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="doc in formData.documentos" :key="doc.id" class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <FileTextIcon class="h-5 w-5 text-gray-400 mr-2" />
                                                        <span class="text-sm font-medium text-gray-900">{{ doc.nome }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ doc.tipo }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ formatDate(doc.dataEmissao) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ doc.dataValidade ? formatDate(doc.dataValidade) : 'Não expira' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getDocumentStatusColor(doc.status)">
                                                            {{ getDocumentStatusText(doc.status) }}
                                                        </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <button @click="editDocumento(doc)" class="text-emerald-600 hover:text-emerald-900 mr-3">
                                                        <EditIcon class="w-5 h-5" />
                                                    </button>
                                                    <button @click="removeDocumento(doc.id)" class="text-red-600 hover:text-red-900">
                                                        <TrashIcon class="w-5 h-5" />
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Endereço e Contato -->
                            <div v-if="activeTab === 'contact'" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">CEP *</label>
                                        <input
                                            v-model="formData.cep"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                            @input="formatCEP"
                                            @blur="buscarCep"
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Logradouro *</label>
                                        <input
                                            v-model="formData.logradouro"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Número *</label>
                                        <input
                                            v-model="formData.numero"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Complemento</label>
                                        <input
                                            v-model="formData.complemento"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Bairro *</label>
                                        <input
                                            v-model="formData.bairro"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Cidade *</label>
                                        <input
                                            v-model="formData.cidade"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Estado *</label>
                                        <select
                                            v-model="formData.estado"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        >
                                            <option value="">Selecione</option>
                                            <option v-for="uf in estados" :key="uf" :value="uf">{{ uf }}</option>
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Telefone *</label>
                                        <input
                                            v-model="formData.telefone"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                            @input="formatTelefone"
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Celular</label>
                                        <input
                                            v-model="formData.celular"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            @input="formatCelular"
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">E-mail *</label>
                                        <input
                                            v-model="formData.email"
                                            type="email"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Contato de emergência</label>
                                        <input
                                            v-model="formData.contatoEmergencia"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Telefone de emergência</label>
                                        <input
                                            v-model="formData.telefoneEmergencia"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            @input="formatTelefoneEmergencia"
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
                                            v-model="formData.banco"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        >
                                            <option value="">Selecione</option>
                                            <option v-for="banco in bancos" :key="banco.codigo" :value="banco.codigo">
                                                {{ banco.codigo }} - {{ banco.nome }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Agência *</label>
                                        <input
                                            v-model="formData.agencia"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Conta *</label>
                                        <input
                                            v-model="formData.conta"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Tipo de conta *</label>
                                        <select
                                            v-model="formData.tipoConta"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
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
                                            v-model="formData.chavePix"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
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
                                            v-model="formData.cargo"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Setor/departamento *</label>
                                        <select
                                            v-model="formData.departamento"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        >
                                            <option value="">Selecione</option>
                                            <option v-for="dept in departamentos" :key="dept" :value="dept">{{ dept }}</option>
                                            <option value="outro">Outro</option>
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Tipo de vínculo *</label>
                                        <select
                                            v-model="formData.tipoVinculo"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
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
                                            v-model="formData.dataAdmissao"
                                            type="date"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Data de desligamento</label>
                                        <input
                                            v-model="formData.dataDesligamento"
                                            type="date"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            :disabled="formData.status !== 'desligado'"
                                        />
                                        <p class="text-xs text-gray-500">Aplicável apenas para funcionários desligados</p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Salário *</label>
                                        <input
                                            v-model="formData.salario"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                            @input="formatSalario"
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Jornada de trabalho *</label>
                                        <input
                                            v-model="formData.jornadaTrabalho"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                            placeholder="Ex: 44 horas semanais"
                                        />
                                    </div>

                                    <div class="col-span-1 md:col-span-2 lg:col-span-3 space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Benefícios</label>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            <div class="flex items-center">
                                                <input
                                                    v-model="formData.beneficios"
                                                    type="checkbox"
                                                    value="valeTransporte"
                                                    class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                                                />
                                                <label class="ml-2 text-sm text-gray-700">Vale Transporte</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input
                                                    v-model="formData.beneficios"
                                                    type="checkbox"
                                                    value="valeRefeicao"
                                                    class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                                                />
                                                <label class="ml-2 text-sm text-gray-700">Vale Refeição</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input
                                                    v-model="formData.beneficios"
                                                    type="checkbox"
                                                    value="planoSaude"
                                                    class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                                                />
                                                <label class="ml-2 text-sm text-gray-700">Plano de Saúde</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input
                                                    v-model="formData.beneficios"
                                                    type="checkbox"
                                                    value="planoOdontologico"
                                                    class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                                                />
                                                <label class="ml-2 text-sm text-gray-700">Plano Odontológico</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input
                                                    v-model="formData.beneficios"
                                                    type="checkbox"
                                                    value="seguroVida"
                                                    class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                                                />
                                                <label class="ml-2 text-sm text-gray-700">Seguro de Vida</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input
                                                    v-model="formData.beneficios"
                                                    type="checkbox"
                                                    value="previdenciaPrivada"
                                                    class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                                                />
                                                <label class="ml-2 text-sm text-gray-700">Previdência Privada</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Histórico de cargos -->
                                <div class="mt-8">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-medium">Histórico de Cargos</h3>
                                        <button
                                            @click="addHistoricoCargo"
                                            type="button"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                                        >
                                            <PlusIcon class="h-4 w-4 mr-1"/>
                                            Adicionar Histórico
                                        </button>
                                    </div>

                                    <div v-if="formData.historicosCargo.length === 0" class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
                                        Nenhum histórico de cargo cadastrado
                                    </div>

                                    <div v-else class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Cargo
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Departamento
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Data Início
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Data Fim
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Salário
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Motivo
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Ações
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="hist in formData.historicosCargo" :key="hist.id" class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ hist.cargo }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ hist.departamento }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ formatDate(hist.dataInicio) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ hist.dataFim ? formatDate(hist.dataFim) : 'Atual' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ hist.salario }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ hist.motivo }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <button @click="removeHistoricoCargo(hist.id)" class="text-red-600 hover:text-red-900">
                                                        <TrashIcon class="w-5 h-5" />
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- 6. Saúde e Segurança do Trabalho -->
                            <div v-if="activeTab === 'health'" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Data do último exame
                                            admissional/periódico *</label>
                                        <input
                                            v-model="formData.dataUltimoExame"
                                            type="date"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Data do próximo exame *</label>
                                        <input
                                            v-model="formData.dataProximoExame"
                                            type="date"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Resultado do ASO *</label>
                                        <select
                                            v-model="formData.resultadoAso"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
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
                                            v-model="formData.alergias"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 min-h-[100px]"
                                            placeholder="Descreva alergias ou restrições médicas, se houver"
                                        ></textarea>
                                    </div>

                                    <div class="col-span-1 md:col-span-2 lg:col-span-3 space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Histórico de acidentes ou doenças
                                            ocupacionais</label>
                                        <textarea
                                            v-model="formData.historicoAcidentes"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 min-h-[100px]"
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
                                        @click="addDependente"
                                        type="button"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                                    >
                                        <PlusIcon class="h-4 w-4 mr-1"/>
                                        Adicionar
                                    </button>
                                </div>

                                <div v-if="formData.dependentes.length === 0" class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
                                    Nenhum dependente cadastrado
                                </div>

                                <div v-for="(dependente, index) in formData.dependentes" :key="dependente.id"
                                     class="border p-4 rounded-md mb-4 bg-white shadow-sm">
                                    <div class="flex justify-between items-center mb-4">
                                        <h4 class="font-medium">Dependente #{{ index + 1 }}</h4>
                                        <button
                                            @click="removeDependente(index)"
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
                                                v-model="dependente.nome"
                                                type="text"
                                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                                required
                                            />
                                        </div>

                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-700">Data de nascimento *</label>
                                            <input
                                                v-model="dependente.dataNascimento"
                                                type="date"
                                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                                required
                                            />
                                        </div>

                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-700">CPF *</label>
                                            <input
                                                v-model="dependente.cpf"
                                                type="text"
                                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                                required
                                                @input="(e) => formatDependenteCPF(e, index)"
                                            />
                                        </div>

                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-700">Grau de parentesco *</label>
                                            <select
                                                v-model="dependente.parentesco"
                                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
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
                                            <label class="block text-sm font-medium text-gray-700">Finalidade *</label>
                                            <div class="space-y-2">
                                                <div class="flex items-center">
                                                    <input
                                                        v-model="dependente.finalidades"
                                                        type="checkbox"
                                                        value="ir"
                                                        class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                                                    />
                                                    <label class="ml-2 text-sm text-gray-700">Imposto de Renda</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input
                                                        v-model="dependente.finalidades"
                                                        type="checkbox"
                                                        value="planoSaude"
                                                        class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                                                    />
                                                    <label class="ml-2 text-sm text-gray-700">Plano de Saúde</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input
                                                        v-model="dependente.finalidades"
                                                        type="checkbox"
                                                        value="planoOdontologico"
                                                        class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
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
                                    <label
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 cursor-pointer"
                                    >
                                        <UploadIcon class="h-4 w-4 mr-1" />
                                        Adicionar Arquivo
                                        <input
                                            type="file"
                                            class="hidden"
                                            @change="handleFileUpload"
                                        />
                                    </label>
                                </div>

                                <!-- Área de Drag and Drop -->
                                <div
                                    class="border-2 border-dashed rounded-lg p-8 text-center transition-all duration-200"
                                    :class="isDragging ? 'border-emerald-500 bg-emerald-50' : 'border-gray-300 hover:border-gray-400'"
                                    @dragover.prevent="isDragging = true"
                                    @dragleave.prevent="isDragging = false"
                                    @drop.prevent="handleFileDrop"
                                >
                                    <div class="flex flex-col items-center justify-center">
                                        <UploadCloudIcon class="h-12 w-12 text-gray-400 mb-3" :class="{ 'text-emerald-500': isDragging }" />
                                        <p class="text-lg font-medium" :class="{ 'text-emerald-600': isDragging }">
                                            Arraste e solte arquivos aqui
                                        </p>
                                        <p class="text-sm text-gray-500 mt-1">ou</p>
                                        <label class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 cursor-pointer">
                                            <UploadIcon class="h-4 w-4 mr-1" />
                                            Selecionar arquivos
                                            <input type="file" class="hidden" @change="handleFileUpload" multiple />
                                        </label>
                                    </div>
                                </div>

                                <div v-if="formData.anexos.length === 0" class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
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
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="anexo in formData.anexos" :key="anexo.id" class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center">
                                                        <component :is="getFileIcon(anexo.tipo)" class="h-6 w-6 text-gray-500" />
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ anexo.nome }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ anexo.tipo }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ anexo.tamanho }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ formatDate(anexo.dataUpload) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <select
                                                    v-model="anexo.categoria"
                                                    class="p-1 text-sm border rounded-md focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                                                >
                                                    <option v-for="cat in categoriasDocumento" :key="cat" :value="cat">{{ cat }}</option>
                                                </select>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a
                                                    :href="anexo.url"
                                                    target="_blank"
                                                    class="text-emerald-600 hover:text-emerald-900 mr-3"
                                                    title="Visualizar"
                                                >
                                                    <EyeIcon class="w-5 h-5" />
                                                </a>
                                                <button
                                                    @click="removeAnexo(anexo.id)"
                                                    class="text-red-600 hover:text-red-900"
                                                    title="Excluir"
                                                >
                                                    <TrashIcon class="w-5 h-5" />
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Prévia de imagens -->
                                <div v-if="formData.anexos.some(a => a.tipo.includes('image'))" class="mt-6">
                                    <h4 class="text-md font-medium mb-3">Prévia de Imagens</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                        <div
                                            v-for="anexo in formData.anexos.filter(a => a.tipo.includes('image'))"
                                            :key="`preview-${anexo.id}`"
                                            class="relative group"
                                        >
                                            <img
                                                :src="anexo.url"
                                                :alt="anexo.nome"
                                                class="h-40 w-full object-cover rounded-md border"
                                            />
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                                <a
                                                    :href="anexo.url"
                                                    target="_blank"
                                                    class="p-2 bg-white rounded-full mx-1"
                                                    title="Visualizar"
                                                >
                                                    <EyeIcon class="w-5 h-5 text-gray-700" />
                                                </a>
                                                <button
                                                    @click="removeAnexo(anexo.id)"
                                                    class="p-2 bg-white rounded-full mx-1"
                                                    title="Excluir"
                                                >
                                                    <TrashIcon class="w-5 h-5 text-red-600" />
                                                </button>
                                            </div>
                                            <div class="mt-1 text-sm truncate">{{ anexo.nome }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botões de ação -->
                            <div class="flex justify-end space-x-4 mt-8 pt-4 border-t">
                                <button
                                    type="button"
                                    @click="showEmployeeForm = false"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="button"
                                    @click="saveEmployee"
                                    class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700"
                                >
                                    Salvar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal de confirmação de exclusão -->
                    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                        <div class="bg-white rounded-lg p-6 max-w-md w-full">
                            <h3 class="text-lg font-medium mb-4">Confirmar exclusão</h3>
                            <p class="mb-6">Tem certeza que deseja excluir o funcionário <span
                                class="font-semibold">{{ employeeToDelete?.nome }}</span>? Esta ação não pode ser desfeita.</p>
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

                    <!-- Modal de documento -->
                    <div v-if="showDocumentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                        <div class="bg-white rounded-lg p-6 max-w-lg w-full">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium">{{ currentDocument?.id ? 'Editar Documento' : 'Novo Documento' }}</h3>
                                <button @click="showDocumentModal = false" class="text-gray-500 hover:text-gray-700">
                                    <XIcon class="w-6 h-6"/>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Nome do documento *</label>
                                    <input
                                        v-model="currentDocument.nome"
                                        type="text"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        required
                                    />
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Tipo de documento *</label>
                                    <select
                                        v-model="currentDocument.tipo"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        required
                                    >
                                        <option value="">Selecione</option>
                                        <option v-for="cat in categoriasDocumento" :key="cat" :value="cat">{{ cat }}</option>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Data de emissão *</label>
                                    <input
                                        v-model="currentDocument.dataEmissao"
                                        type="date"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        required
                                    />
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Data de validade</label>
                                    <input
                                        v-model="currentDocument.dataValidade"
                                        type="date"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    />
                                    <p class="text-xs text-gray-500">Deixe em branco se o documento não tiver data de validade</p>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Arquivo</label>
                                    <div class="flex items-center">
                                        <label class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 cursor-pointer">
                                            <UploadIcon class="h-4 w-4 mr-1" />
                                            Selecionar arquivo
                                            <input type="file" class="hidden" @change="handleDocumentUpload" />
                                        </label>
                                        <span v-if="currentDocument.nome" class="ml-2 text-sm text-gray-500">
                                            {{ currentDocument.nome }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-4 mt-6 pt-4 border-t">
                                <button
                                    type="button"
                                    @click="showDocumentModal = false"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="button"
                                    @click="saveDocumento"
                                    class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700"
                                >
                                    Salvar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal de foto de perfil -->
                    <div v-if="showProfilePhotoModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                        <div class="bg-white rounded-lg p-6 max-w-md w-full">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium">Foto de Perfil</h3>
                                <button @click="cancelProfilePhoto" class="text-gray-500 hover:text-gray-700">
                                    <XIcon class="w-6 h-6"/>
                                </button>
                            </div>

                            <div class="flex justify-center mb-6">
                                <img v-if="profilePhotoPreview" :src="profilePhotoPreview" alt="Prévia da foto" class="h-48 w-48 rounded-full object-cover" />
                            </div>

                            <div class="flex justify-end space-x-4 mt-6 pt-4 border-t">
                                <button
                                    type="button"
                                    @click="cancelProfilePhoto"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="button"
                                    @click="saveProfilePhoto"
                                    class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700"
                                >
                                    Salvar
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
                                        v-model="newCargoHistory.cargo"
                                        type="text"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        required
                                    />
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Departamento *</label>
                                    <select
                                        v-model="newCargoHistory.departamento"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
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
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        required
                                    />
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Data de término</label>
                                    <input
                                        v-model="newCargoHistory.dataFim"
                                        type="date"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    />
                                    <p class="text-xs text-gray-500">Deixe em branco se for o cargo atual</p>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Salário *</label>
                                    <input
                                        v-model="newCargoHistory.salario"
                                        type="text"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        required
                                        @input="formatSalarioHistorico"
                                    />
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Motivo da alteração *</label>
                                    <select
                                        v-model="newCargoHistory.motivo"
                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
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
                                    class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700"
                                >
                                    Salvar
                                </button>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </AppLayout>
</template>
