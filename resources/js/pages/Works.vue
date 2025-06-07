<script setup lang="ts">
import AppLayout from "@/layouts/AppLayout.vue";
import {Head} from '@inertiajs/vue3';
import { ref, reactive, computed, onMounted } from 'vue'
import {
    PlusIcon,
    SearchIcon,
    DownloadIcon,
    EditIcon,
    TrashIcon,
    XIcon,
    ChevronUpIcon,
    ChevronDownIcon,
    PlusCircleIcon
} from 'lucide-vue-next'

// Tipos
interface Etapa {
    nome: string;
    dataInicio: string;
    dataPrevisaoTermino: string;
    dataConclusao: string;
    peso: number;
    custoPrevisto: string;
    descricao: string;
    concluida: boolean;
}

interface FuncionarioObra {
    id: number;
    nome: string;
    cargo: string;
    dataInicioObra: string;
    dataTerminoObra: string;
    custoDiario: string;
    adicionalFimSemana: number;
    adicionalNoturno: number;
    adicionalFeriados: number;
    observacoes: string;
}

interface Custo {
    categoria: string;
    descricao: string;
    valor: string;
    data: string;
    fornecedor: string;
    notaFiscal: string;
}

interface Work {
    id: number;
    nome: string;
    cliente: string;
    responsavelTecnico: string;
    dataInicio: string;
    dataPrevisaoTermino: string;
    dataConclusao: string;
    tipoObra: string;
    status: string;
    areaTotal: number;
    endereco: string;
    descricao: string;
    etapas: Etapa[];
    funcionarios: FuncionarioObra[];
    custos: Custo[];
}

// Estado
const showWorkForm = ref(false)
const activeTab = ref('general')
const searchQuery = ref('')
const works = ref<Work[]>([])
const currentWork = ref<Work | null>(null)
const showDeleteModal = ref(false)
const workToDelete = ref<Work | null>(null)
const showEmployeeSelector = ref(false)
const employeeSearchQuery = ref('')

// Dados de formulário
const formData = reactive<Work>({
    id: 0,
    nome: '',
    cliente: '',
    responsavelTecnico: '',
    dataInicio: '',
    dataPrevisaoTermino: '',
    dataConclusao: '',
    tipoObra: '',
    status: '',
    areaTotal: 0,
    endereco: '',
    descricao: '',
    etapas: [],
    funcionarios: [],
    custos: []
})

// Dados de referência
const tabs = [
    { id: 'general', name: 'Informações Gerais' },
    { id: 'stages', name: 'Etapas da Obra' },
    { id: 'employees', name: 'Funcionários' },
    { id: 'costs', name: 'Custos' },
    { id: 'reports', name: 'Relatórios' }
]

// Dados de exemplo para funcionários disponíveis
const availableEmployees = ref([
    {
        id: 1,
        nome: 'João Silva',
        cargo: 'Pedreiro',
        salario: 'R$ 3.000,00'
    },
    {
        id: 2,
        nome: 'Ana Oliveira',
        cargo: 'Engenheira Civil',
        salario: 'R$ 7.500,00'
    },
    {
        id: 3,
        nome: 'Carlos Santos',
        cargo: 'Eletricista',
        salario: 'R$ 3.200,00'
    },
    {
        id: 4,
        nome: 'Mariana Costa',
        cargo: 'Arquiteta',
        salario: 'R$ 6.800,00'
    },
    {
        id: 5,
        nome: 'Pedro Almeida',
        cargo: 'Encanador',
        salario: 'R$ 2.800,00'
    }
])

// Computed properties
const filteredWorks = computed(() => {
    if (!searchQuery.value) {
        return works.value
    }

    const query = searchQuery.value.toLowerCase()
    return works.value.filter(work =>
        work.nome.toLowerCase().includes(query) ||
        work.cliente.toLowerCase().includes(query) ||
        work.endereco.toLowerCase().includes(query)
    )
})

const filteredAvailableEmployees = computed(() => {
    // Filtra funcionários que já estão alocados na obra
    const allocatedIds = formData.funcionarios.map(f => f.id)
    let filtered = availableEmployees.value.filter(emp => !allocatedIds.includes(emp.id))

    // Aplica filtro de busca
    if (employeeSearchQuery.value) {
        const query = employeeSearchQuery.value.toLowerCase()
        filtered = filtered.filter(emp =>
            emp.nome.toLowerCase().includes(query) ||
            emp.cargo.toLowerCase().includes(query)
        )
    }

    return filtered
})

// Métodos
const getInitials = (name: string) => {
    if (!name) return ''
    return name
        .split(' ')
        .map(part => part.charAt(0))
        .join('')
        .substring(0, 2)
        .toUpperCase()
}

const formatDate = (dateString: string) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleDateString('pt-BR')
}

const formatCurrency = (value: number) => {
    return value.toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    })
}

const calculateProgress = (work: Work) => {
    if (!work.etapas || work.etapas.length === 0) return 0

    const totalWeight = work.etapas.reduce((sum, etapa) => sum + etapa.peso, 0)
    if (totalWeight === 0) return 0

    const completedWeight = work.etapas
        .filter(etapa => etapa.concluida)
        .reduce((sum, etapa) => sum + etapa.peso, 0)

    return Math.round((completedWeight / totalWeight) * 100)
}

const calculateFormProgress = () => {
    if (!formData.etapas || formData.etapas.length === 0) return 0

    const totalWeight = formData.etapas.reduce((sum, etapa) => sum + etapa.peso, 0)
    if (totalWeight === 0) return 0

    const completedWeight = formData.etapas
        .filter(etapa => etapa.concluida)
        .reduce((sum, etapa) => sum + etapa.peso, 0)

    return Math.round((completedWeight / totalWeight) * 100)
}

const calculateTotalCost = (work: Work) => {
    // Custo de funcionários
    const employeeCost = work.funcionarios.reduce((sum, funcionario) => {
        const custoDiario = parseFloat(funcionario.custoDiario.replace(/[^\d,]/g, '').replace(',', '.')) || 0
        const diasTrabalhados = calculateWorkingDays(funcionario.dataInicioObra, funcionario.dataTerminoObra || work.dataPrevisaoTermino)
        return sum + (custoDiario * diasTrabalhados)
    }, 0)

    // Custos adicionais
    const additionalCost = work.custos.reduce((sum, custo) => {
        const valor = parseFloat(custo.valor.replace(/[^\d,]/g, '').replace(',', '.')) || 0
        return sum + valor
    }, 0)

    return employeeCost + additionalCost
}

const calculateTotalCostForm = () => {
    // Custo de funcionários
    const employeeCost = calculateTotalEmployeeCost()

    // Custos adicionais
    const additionalCost = calculateTotalAdditionalCost()

    return employeeCost + additionalCost
}

const calculateTotalEmployeeCost = () => {
    return formData.funcionarios.reduce((sum, funcionario) => {
        return sum + calculateFuncionarioCustoTotal(funcionario)
    }, 0)
}

const calculateAverageEmployeeCost = () => {
    if (formData.funcionarios.length === 0) return 0
    return calculateTotalEmployeeCost() / formData.funcionarios.length
}

const calculateTotalAdditionalCost = () => {
    return formData.custos.reduce((sum, custo) => {
        const valor = parseFloat(custo.valor.replace(/[^\d,]/g, '').replace(',', '.')) || 0
        return sum + valor
    }, 0)
}

const calculateEmployeeCostPercentage = () => {
    const totalCost = calculateTotalCostForm()
    if (totalCost === 0) return 0

    const employeeCost = calculateTotalEmployeeCost()
    return Math.round((employeeCost / totalCost) * 100)
}

const calculateAdditionalCostPercentage = () => {
    const totalCost = calculateTotalCostForm()
    if (totalCost === 0) return 0

    const additionalCost = calculateTotalAdditionalCost()
    return Math.round((additionalCost / totalCost) * 100)
}

const calculateCostByCategory = (category: string) => {
    return formData.custos
        .filter(custo => custo.categoria === category)
        .reduce((sum, custo) => {
            const valor = parseFloat(custo.valor.replace(/[^\d,]/g, '').replace(',', '.')) || 0
            return sum + valor
        }, 0)
}

const calculateOtherCosts = () => {
    return formData.custos
        .filter(custo => !['Material', 'Equipamento', 'Serviço Terceirizado'].includes(custo.categoria))
        .reduce((sum, custo) => {
            const valor = parseFloat(custo.valor.replace(/[^\d,]/g, '').replace(',', '.')) || 0
            return sum + valor
        }, 0)
}

const calculateCostPerSquareMeter = () => {
    if (!formData.areaTotal || formData.areaTotal === 0) return 0
    return calculateTotalCostForm() / formData.areaTotal
}

const calculateWorkingDays = (startDate: string, endDate: string) => {
    if (!startDate || !endDate) return 0

    const start = new Date(startDate)
    const end = new Date(endDate)

    // Diferença em milissegundos
    const diffTime = Math.abs(end.getTime() - start.getTime())

    // Converter para dias
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

    return diffDays + 1 // Incluir o dia inicial
}

const calculateFuncionarioCustoTotal = (funcionario: FuncionarioObra) => {
    const custoDiario = parseFloat(funcionario.custoDiario.replace(/[^\d,]/g, '').replace(',', '.')) || 0
    const diasTrabalhados = calculateWorkingDays(funcionario.dataInicioObra, funcionario.dataTerminoObra || formData.dataPrevisaoTermino)

    // Calcular dias de fim de semana e feriados (simplificado)
    const diasUteis = Math.round(diasTrabalhados * 5/7) // Aproximação de dias úteis
    const diasFimSemana = diasTrabalhados - diasUteis

    // Calcular custo base
    let custoTotal = custoDiario * diasUteis

    // Adicionar custo de fim de semana com adicional
    if (funcionario.adicionalFimSemana > 0) {
        const adicionalFimSemana = (funcionario.adicionalFimSemana / 100) + 1
        custoTotal += custoDiario * diasFimSemana * adicionalFimSemana
    } else {
        custoTotal += custoDiario * diasFimSemana
    }

    // Adicional noturno e feriados (simplificado)
    // Assumimos que 10% dos dias têm adicional noturno e 5% são feriados
    if (funcionario.adicionalNoturno > 0) {
        const diasNoturno = Math.round(diasTrabalhados * 0.1)
        const adicionalNoturno = (funcionario.adicionalNoturno / 100)
        custoTotal += custoDiario * diasNoturno * adicionalNoturno
    }

    if (funcionario.adicionalFeriados > 0) {
        const diasFeriados = Math.round(diasTrabalhados * 0.05)
        const adicionalFeriados = (funcionario.adicionalFeriados / 100) + 1
        custoTotal += custoDiario * diasFeriados * adicionalFeriados
    }

    return custoTotal
}

const getMonthsRange = () => {
    if (!formData.dataInicio || !formData.dataPrevisaoTermino) return []

    const start = new Date(formData.dataInicio)
    const end = new Date(formData.dataPrevisaoTermino)

    const months = []
    const currentDate = new Date(start)

    while (currentDate <= end) {
        months.push(currentDate.toLocaleDateString('pt-BR', { month: 'short', year: '2-digit' }))
        currentDate.setMonth(currentDate.getMonth() + 1)
    }

    return months
}

const getEtapaTimelineStyle = (etapa: Etapa) => {
    if (!formData.dataInicio || !formData.dataPrevisaoTermino || !etapa.dataInicio || !etapa.dataPrevisaoTermino) {
        return { left: '0%', width: '0%' }
    }

    const projectStart = new Date(formData.dataInicio).getTime()
    const projectEnd = new Date(formData.dataPrevisaoTermino).getTime()
    const projectDuration = projectEnd - projectStart

    const etapaStart = new Date(etapa.dataInicio).getTime()
    const etapaEnd = new Date(etapa.dataPrevisaoTermino).getTime()

    const startOffset = ((etapaStart - projectStart) / projectDuration) * 100
    const width = ((etapaEnd - etapaStart) / projectDuration) * 100

    return {
        left: `${startOffset}%`,
        width: `${width}%`
    }
}

const addEtapa = () => {
    formData.etapas.push({
        nome: '',
        dataInicio: '',
        dataPrevisaoTermino: '',
        dataConclusao: '',
        peso: 0,
        custoPrevisto: '',
        descricao: '',
        concluida: false
    })
}

const removeEtapa = (index: number) => {
    formData.etapas.splice(index, 1)
    normalizePesos()
}

const moveEtapa = (index: number, direction: number) => {
    const newIndex = index + direction
    if (newIndex < 0 || newIndex >= formData.etapas.length) return

    const etapa = formData.etapas[index]
    formData.etapas.splice(index, 1)
    formData.etapas.splice(newIndex, 0, etapa)
}

const normalizePesos = () => {
    // Garantir que a soma dos pesos seja 100%
    const totalPeso = formData.etapas.reduce((sum, etapa) => sum + etapa.peso, 0)

    if (totalPeso > 0 && totalPeso !== 100) {
        // Ajustar proporcionalmente
        formData.etapas.forEach(etapa => {
            etapa.peso = Math.round((etapa.peso / totalPeso) * 100)
        })

        // Ajustar diferenças de arredondamento
        const newTotal = formData.etapas.reduce((sum, etapa) => sum + etapa.peso, 0)
        if (newTotal !== 100) {
            const diff = 100 - newTotal
            // Adicionar a diferença à última etapa com peso > 0
            for (let i = formData.etapas.length - 1; i >= 0; i--) {
                if (formData.etapas[i].peso > 0) {
                    formData.etapas[i].peso += diff
                    break
                }
            }
        }
    }
}

const formatEtapaCustoPrevisto = (index: number) => {
    let value = formData.etapas[index].custoPrevisto.replace(/\D/g, '')

    if (value.length > 0) {
        value = (parseInt(value) / 100).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL',
            minimumFractionDigits: 2
        })
    } else {
        value = ''
    }

    formData.etapas[index].custoPrevisto = value
}

const formatFuncionarioCustoDiario = (index: number) => {
    let value = formData.funcionarios[index].custoDiario.replace(/\D/g, '')

    if (value.length > 0) {
        value = (parseInt(value) / 100).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL',
            minimumFractionDigits: 2
        })
    } else {
        value = ''
    }

    formData.funcionarios[index].custoDiario = value
}

const formatCustoValor = (index: number) => {
    let value = formData.custos[index].valor.replace(/\D/g, '')

    if (value.length > 0) {
        value = (parseInt(value) / 100).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL',
            minimumFractionDigits: 2
        })
    } else {
        value = ''
    }

    formData.custos[index].valor = value
}

const addCusto = () => {
    formData.custos.push({
        categoria: '',
        descricao: '',
        valor: '',
        data: '',
        fornecedor: '',
        notaFiscal: ''
    })
}

const removeCusto = (index: number) => {
    formData.custos.splice(index, 1)
}

const selectEmployee = (employee: any) => {
    formData.funcionarios.push({
        id: employee.id,
        nome: employee.nome,
        cargo: employee.cargo,
        dataInicioObra: formData.dataInicio || new Date().toISOString().split('T')[0],
        dataTerminoObra: '',
        custoDiario: '0',
        adicionalFimSemana: 50,
        adicionalNoturno: 20,
        adicionalFeriados: 100,
        observacoes: ''
    })

    showEmployeeSelector.value = false
}

const removeFuncionario = (index: number) => {
    formData.funcionarios.splice(index, 1)
}

const editWork = (work: Work) => {
    currentWork.value = { ...work }
    Object.assign(formData, JSON.parse(JSON.stringify(work)))
    showWorkForm.value = true
    activeTab.value = 'general'
}

const confirmDelete = (work: Work) => {
    workToDelete.value = work
    showDeleteModal.value = true
}

const deleteWork = () => {
    if (workToDelete.value) {
        works.value = works.value.filter(w => w.id !== workToDelete.value?.id)
        showDeleteModal.value = false
        workToDelete.value = null
    }
}

const saveWork = () => {
    // Aqui você implementaria a validação do formulário

    if (currentWork.value) {
        // Edição de obra existente
        const index = works.value.findIndex(w => w.id === currentWork.value?.id)
        if (index !== -1) {
            works.value[index] = { ...formData }
        }
    } else {
        // Nova obra
        const newWork = {
            ...formData,
            id: Date.now() // Gera um ID único baseado no timestamp
        }
        works.value.push(newWork)
    }

    showWorkForm.value = false
    resetForm()
}

const resetForm = () => {
    // Reseta o formulário para valores padrão
    Object.assign(formData, {
        id: 0,
        nome: '',
        cliente: '',
        responsavelTecnico: '',
        dataInicio: '',
        dataPrevisaoTermino: '',
        dataConclusao: '',
        tipoObra: '',
        status: '',
        areaTotal: 0,
        endereco: '',
        descricao: '',
        etapas: [],
        funcionarios: [],
        custos: []
    })

    currentWork.value = null
}

// Carrega dados de exemplo
onMounted(() => {
    // Simulando dados de exemplo
    works.value = [
        {
            id: 1,
            nome: 'Edifício Residencial Aurora',
            cliente: 'Construtora Horizonte',
            responsavelTecnico: 'Eng. Roberto Mendes',
            dataInicio: '2023-03-15',
            dataPrevisaoTermino: '2024-06-30',
            dataConclusao: '',
            tipoObra: 'residencial',
            status: 'emAndamento',
            areaTotal: 2500,
            endereco: 'Av. Principal, 1500, Centro - São Paulo/SP',
            descricao: 'Construção de edifício residencial com 12 andares e 48 apartamentos.',
            etapas: [
                {
                    nome: 'Fundação',
                    dataInicio: '2023-03-15',
                    dataPrevisaoTermino: '2023-05-30',
                    dataConclusao: '2023-05-25',
                    peso: 20,
                    custoPrevisto: 'R$ 450.000,00',
                    descricao: 'Escavação e construção das fundações',
                    concluida: true
                },
                {
                    nome: 'Estrutura',
                    dataInicio: '2023-06-01',
                    dataPrevisaoTermino: '2023-10-30',
                    dataConclusao: '2023-11-10',
                    peso: 30,
                    custoPrevisto: 'R$ 850.000,00',
                    descricao: 'Construção da estrutura de concreto',
                    concluida: true
                },
                {
                    nome: 'Alvenaria',
                    dataInicio: '2023-11-15',
                    dataPrevisaoTermino: '2024-02-28',
                    dataConclusao: '',
                    peso: 20,
                    custoPrevisto: 'R$ 380.000,00',
                    descricao: 'Construção das paredes e divisórias',
                    concluida: false
                },
                {
                    nome: 'Instalações',
                    dataInicio: '2024-01-15',
                    dataPrevisaoTermino: '2024-04-30',
                    dataConclusao: '',
                    peso: 15,
                    custoPrevisto: 'R$ 320.000,00',
                    descricao: 'Instalações elétricas, hidráulicas e de gás',
                    concluida: false
                },
                {
                    nome: 'Acabamento',
                    dataInicio: '2024-03-15',
                    dataPrevisaoTermino: '2024-06-30',
                    dataConclusao: '',
                    peso: 15,
                    custoPrevisto: 'R$ 420.000,00',
                    descricao: 'Acabamentos, pintura e revestimentos',
                    concluida: false
                }
            ],
            funcionarios: [
                {
                    id: 1,
                    nome: 'João Silva',
                    cargo: 'Pedreiro',
                    dataInicioObra: '2023-03-15',
                    dataTerminoObra: '',
                    custoDiario: 'R$ 180,00',
                    adicionalFimSemana: 50,
                    adicionalNoturno: 20,
                    adicionalFeriados: 100,
                    observacoes: 'Responsável pela equipe de alvenaria'
                },
                {
                    id: 3,
                    nome: 'Carlos Santos',
                    cargo: 'Eletricista',
                    dataInicioObra: '2023-11-15',
                    dataTerminoObra: '',
                    custoDiario: 'R$ 200,00',
                    adicionalFimSemana: 50,
                    adicionalNoturno: 20,
                    adicionalFeriados: 100,
                    observacoes: ''
                }
            ],
            custos: [
                {
                    categoria: 'Material',
                    descricao: 'Cimento e Areia',
                    valor: 'R$ 120.000,00',
                    data: '2023-03-20',
                    fornecedor: 'Materiais Construção Ltda',
                    notaFiscal: '12345'
                },
                {
                    categoria: 'Equipamento',
                    descricao: 'Aluguel de Betoneira',
                    valor: 'R$ 15.000,00',
                    data: '2023-03-25',
                    fornecedor: 'Máquinas & Cia',
                    notaFiscal: '54321'
                },
                {
                    categoria: 'Serviço Terceirizado',
                    descricao: 'Projeto Estrutural',
                    valor: 'R$ 35.000,00',
                    data: '2023-02-10',
                    fornecedor: 'Engenharia Estrutural SA',
                    notaFiscal: '98765'
                }
            ]
        },
        {
            id: 2,
            nome: 'Centro Comercial Vitória',
            cliente: 'Empreendimentos Vitória Ltda',
            responsavelTecnico: 'Eng. Carla Sousa',
            dataInicio: '2023-05-10',
            dataPrevisaoTermino: '2024-08-15',
            dataConclusao: '',
            tipoObra: 'comercial',
            status: 'emAndamento',
            areaTotal: 3800,
            endereco: 'Rua Comercial, 500, Zona Sul - Rio de Janeiro/RJ',
            descricao: 'Construção de centro comercial com 3 pavimentos e 45 lojas.',
            etapas: [
                {
                    nome: 'Fundação',
                    dataInicio: '2023-05-10',
                    dataPrevisaoTermino: '2023-07-30',
                    dataConclusao: '2023-08-05',
                    peso: 15,
                    custoPrevisto: 'R$ 580.000,00',
                    descricao: 'Escavação e construção das fundações',
                    concluida: true
                },
                {
                    nome: 'Estrutura',
                    dataInicio: '2023-08-10',
                    dataPrevisaoTermino: '2024-01-15',
                    dataConclusao: '',
                    peso: 35,
                    custoPrevisto: 'R$ 1.200.000,00',
                    descricao: 'Construção da estrutura de concreto',
                    concluida: false
                },
                {
                    nome: 'Fechamento',
                    dataInicio: '2023-12-01',
                    dataPrevisaoTermino: '2024-04-30',
                    dataConclusao: '',
                    peso: 25,
                    custoPrevisto: 'R$ 850.000,00',
                    descricao: 'Fechamento externo e divisórias internas',
                    concluida: false
                },
                {
                    nome: 'Acabamento',
                    dataInicio: '2024-03-15',
                    dataPrevisaoTermino: '2024-08-15',
                    dataConclusao: '',
                    peso: 25,
                    custoPrevisto: 'R$ 920.000,00',
                    descricao: 'Acabamentos, instalações e equipamentos',
                    concluida: false
                }
            ],
            funcionarios: [
                {
                    id: 2,
                    nome: 'Ana Oliveira',
                    cargo: 'Engenheira Civil',
                    dataInicioObra: '2023-05-10',
                    dataTerminoObra: '',
                    custoDiario: 'R$ 450,00',
                    adicionalFimSemana: 50,
                    adicionalNoturno: 20,
                    adicionalFeriados: 100,
                    observacoes: 'Responsável técnica da obra'
                },
                {
                    id: 5,
                    nome: 'Pedro Almeida',
                    cargo: 'Encanador',
                    dataInicioObra: '2023-11-01',
                    dataTerminoObra: '',
                    custoDiario: 'R$ 190,00',
                    adicionalFimSemana: 50,
                    adicionalNoturno: 20,
                    adicionalFeriados: 100,
                    observacoes: ''
                }
            ],
            custos: [
                {
                    categoria: 'Material',
                    descricao: 'Aço e Concreto',
                    valor: 'R$ 350.000,00',
                    data: '2023-06-15',
                    fornecedor: 'Aço & Concreto SA',
                    notaFiscal: '78901'
                },
                {
                    categoria: 'Equipamento',
                    descricao: 'Aluguel de Guindastes',
                    valor: 'R$ 85.000,00',
                    data: '2023-08-10',
                    fornecedor: 'Guindastes Brasil',
                    notaFiscal: '45678'
                },
                {
                    categoria: 'Licenças e Taxas',
                    descricao: 'Alvará de Construção',
                    valor: 'R$ 45.000,00',
                    data: '2023-04-25',
                    fornecedor: 'Prefeitura Municipal',
                    notaFiscal: '123456'
                }
            ]
        },
        {
            id: 3,
            nome: 'Condomínio Jardim das Flores',
            cliente: 'Incorporadora Verde Ltda',
            responsavelTecnico: 'Eng. Paulo Ribeiro',
            dataInicio: '2023-07-05',
            dataPrevisaoTermino: '2025-01-20',
            dataConclusao: '',
            tipoObra: 'residencial',
            status: 'planejamento',
            areaTotal: 5200,
            endereco: 'Estrada das Flores, 300, Zona Leste - Belo Horizonte/MG',
            descricao: 'Construção de condomínio residencial com 8 blocos e 96 apartamentos.',
            etapas: [
                {
                    nome: 'Terraplanagem',
                    dataInicio: '2023-07-05',
                    dataPrevisaoTermino: '2023-08-30',
                    dataConclusao: '2023-08-25',
                    peso: 10,
                    custoPrevisto: 'R$ 280.000,00',
                    descricao: 'Preparação do terreno',
                    concluida: true
                },
                {
                    nome: 'Fundação',
                    dataInicio: '2023-09-01',
                    dataPrevisaoTermino: '2023-12-15',
                    dataConclusao: '',
                    peso: 15,
                    custoPrevisto: 'R$ 620.000,00',
                    descricao: 'Fundações dos blocos',
                    concluida: false
                }
            ],
            funcionarios: [],
            custos: [
                {
                    categoria: 'Material',
                    descricao: 'Material para terraplanagem',
                    valor: 'R$ 150.000,00',
                    data: '2023-07-10',
                    fornecedor: 'Terraplanagem Express',
                    notaFiscal: '34567'
                }
            ]
        }
    ]
})
</script>

<template>
    <Head title="Obras"/>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 bg-background text-foreground">
            <div class="flex flex-1 flex-col overflow-hidden">
                <!-- Conteúdo principal -->
                <main class="flex-1 overflow-auto p-4 md:p-6">
                    <!-- Cabeçalho do módulo -->
                    <Header v-if="!showWorkForm" class="text-black mb-10">
                        <div class="mx-auto flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <h1 class="text-2xl font-bold">Obras em andamento</h1>
                            </div>
                            <button
                                @click="showWorkForm = true; currentWork = null"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md flex items-center"
                            >
                                <PlusIcon class="w-5 h-5 mr-2" />
                                Nova Obra
                            </button>
                        </div>
                    </Header>
                    <!-- Modo de listagem -->
                    <div v-if="!showWorkForm">
                        <div class="mb-6">
                            <div class="flex items-center mb-4">
                                <div class="flex-grow mx-4">
                                    <div class="relative">
                                        <input
                                            v-model="searchQuery"
                                            type="text"
                                            placeholder="Buscar obra..."
                                            class="w-full pl-10 pr-4 py-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        />
                                        <SearchIcon class="absolute left-3 top-2.5 text-gray-400 w-5 h-5" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cards de obras -->
                        <div v-if="filteredWorks.length === 0" class="text-center py-8 text-gray-500">
                            Nenhuma obra encontrada
                        </div>

                        <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            <div
                                v-for="work in filteredWorks"
                                :key="work.id"
                                class="bg-white border rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden group relative"
                            >
                                <!-- Card content -->
                                <div class="p-6 flex flex-col items-center">
                                    <!-- Círculo de progresso -->
                                    <div class="relative w-32 h-32 mb-4">
                                        <svg class="w-full h-full" viewBox="0 0 100 100">
                                            <!-- Círculo de fundo -->
                                            <circle
                                                cx="50"
                                                cy="50"
                                                r="45"
                                                fill="none"
                                                stroke="#e5e7eb"
                                                stroke-width="8"
                                            />

                                            <!-- Círculo de progresso -->
                                            <circle
                                                cx="50"
                                                cy="50"
                                                r="45"
                                                fill="none"
                                                stroke="#10b981"
                                                stroke-width="8"
                                                stroke-linecap="round"
                                                :stroke-dasharray="`${calculateProgress(work) * 2.83}, 283`"
                                                transform="rotate(-90 50 50)"
                                            />

                                            <!-- Texto de porcentagem -->
                                            <text
                                                x="50"
                                                y="50"
                                                font-size="20"
                                                text-anchor="middle"
                                                alignment-baseline="middle"
                                                fill="#374151"
                                                font-weight="bold"
                                            >
                                                {{ calculateProgress(work) }}%
                                            </text>
                                        </svg>
                                    </div>

                                    <!-- Informações da obra -->
                                    <h3 class="text-lg font-medium text-gray-900 text-center mb-1">{{ work.nome }}</h3>
                                    <p class="text-sm text-gray-500 text-center mb-2">{{ work.cliente }}</p>

                                    <div class="flex justify-between w-full text-sm text-gray-500">
                                        <div>Início: {{ formatDate(work.dataInicio) }}</div>
                                        <div>Término: {{ formatDate(work.dataPrevisaoTermino) }}</div>
                                    </div>

                                    <div class="mt-3 text-sm font-medium text-gray-700">
                                        {{ formatCurrency(calculateTotalCost(work)) }}
                                    </div>
                                </div>

                                <!-- Overlay com ações (aparece no hover) -->
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <div class="flex space-x-3">
                                        <button
                                            @click.stop="editWork(work)"
                                            class="p-2 bg-white rounded-full text-gray-600 hover:text-gray-800 shadow-md"
                                            title="Editar"
                                        >
                                            <EditIcon class="w-5 h-5" />
                                        </button>
                                        <button
                                            @click.stop="confirmDelete(work)"
                                            class="p-2 bg-white rounded-full text-red-600 hover:text-red-800 shadow-md"
                                            title="Excluir"
                                        >
                                            <TrashIcon class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>

                                <!-- Barra de status -->
                                <div
                                    class="h-1.5 w-full absolute bottom-0 left-0"
                                    :class="{
                    'bg-gray-500': work.status === 'emAndamento',
                    'bg-yellow-500': work.status === 'planejamento',
                    'bg-red-500': work.status === 'paralisada',
                    'bg-blue-500': work.status === 'concluida',
                    'bg-gray-500': work.status === 'cancelada'
                  }"
                                ></div>
                            </div>
                        </div>

                        <!-- Paginação -->
                        <div class="flex items-center justify-between mt-6">
                            <div class="text-sm text-gray-700">
                                Mostrando <span class="font-medium">{{ filteredWorks.length }}</span> de <span class="font-medium">{{ works.length }}</span> obras
                            </div>
                            <div class="flex space-x-2">
                                <button class="px-3 py-1 border rounded-md hover:bg-gray-50">Anterior</button>
                                <button class="px-3 py-1 border rounded-md bg-gray-50 text-gray-600 font-medium">1</button>
                                <button class="px-3 py-1 border rounded-md hover:bg-gray-50">Próxima</button>
                            </div>
                        </div>
                    </div>

                    <!-- Formulário de obra -->
                    <div v-else class="bg-white rounded-lg shadow-md">
                        <div class="border-b px-6 py-4 flex justify-between items-center">
                            <h2 class="text-xl font-semibold">{{ currentWork ? 'Editar Obra' : 'Nova Obra' }}</h2>
                            <button @click="showWorkForm = false" class="text-gray-500 hover:text-gray-700">
                                <XIcon class="w-6 h-6" />
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
                    'px-4 py-3 text-sm font-medium whitespace-nowrap',
                    activeTab === tab.id
                      ? 'border-b-2 border-gray-500 text-gray-600'
                      : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  ]"
                                >
                                    {{ tab.name }}
                                </button>
                            </div>
                        </div>

                        <!-- Conteúdo das abas -->
                        <div class="p-6">
                            <!-- 1. Informações Gerais -->
                            <div v-if="activeTab === 'general'" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Nome da Obra *</label>
                                        <input
                                            v-model="formData.nome"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Cliente *</label>
                                        <input
                                            v-model="formData.cliente"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Responsável Técnico *</label>
                                        <input
                                            v-model="formData.responsavelTecnico"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Data de Início *</label>
                                        <input
                                            v-model="formData.dataInicio"
                                            type="date"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Previsão de Término *</label>
                                        <input
                                            v-model="formData.dataPrevisaoTermino"
                                            type="date"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                            required
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Data de Conclusão</label>
                                        <input
                                            v-model="formData.dataConclusao"
                                            type="date"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Tipo de Obra *</label>
                                        <select
                                            v-model="formData.tipoObra"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                            required
                                        >
                                            <option value="">Selecione</option>
                                            <option value="residencial">Residencial</option>
                                            <option value="comercial">Comercial</option>
                                            <option value="industrial">Industrial</option>
                                            <option value="infraestrutura">Infraestrutura</option>
                                            <option value="reforma">Reforma</option>
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Status *</label>
                                        <select
                                            v-model="formData.status"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                            required
                                        >
                                            <option value="">Selecione</option>
                                            <option value="planejamento">Planejamento</option>
                                            <option value="emAndamento">Em Andamento</option>
                                            <option value="paralisada">Paralisada</option>
                                            <option value="concluida">Concluída</option>
                                            <option value="cancelada">Cancelada</option>
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Área Total (m²) *</label>
                                        <input
                                            v-model="formData.areaTotal"
                                            type="number"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                            required
                                        />
                                    </div>

                                    <div class="col-span-1 md:col-span-2 lg:col-span-3 space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Endereço *</label>
                                        <input
                                            v-model="formData.endereco"
                                            type="text"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                            required
                                            placeholder="Rua, número, bairro, cidade - UF"
                                        />
                                    </div>

                                    <div class="col-span-1 md:col-span-2 lg:col-span-3 space-y-2">
                                        <label class="block text-sm font-medium text-gray-700">Descrição</label>
                                        <textarea
                                            v-model="formData.descricao"
                                            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500 min-h-[100px]"
                                            placeholder="Descrição detalhada da obra"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Etapas da Obra -->
                            <div v-if="activeTab === 'stages'" class="space-y-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium">Etapas da Obra</h3>
                                    <button
                                        @click="addEtapa"
                                        type="button"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                    >
                                        <PlusIcon class="h-4 w-4 mr-1" />
                                        Adicionar Etapa
                                    </button>
                                </div>

                                <div v-if="formData.etapas.length === 0" class="text-center py-8 text-gray-500">
                                    Nenhuma etapa cadastrada
                                </div>

                                <div v-else class="space-y-4">
                                    <div
                                        v-for="(etapa, index) in formData.etapas"
                                        :key="index"
                                        class="border p-4 rounded-md"
                                        :class="{'bg-gray-50': etapa.concluida}"
                                    >
                                        <div class="flex justify-between items-center mb-4">
                                            <div class="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="etapa.concluida"
                                                    class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                                                />
                                                <h4 class="font-medium ml-2">Etapa {{ index + 1 }}</h4>
                                            </div>
                                            <div class="flex items-center">
                                                <button
                                                    @click="moveEtapa(index, -1)"
                                                    :disabled="index === 0"
                                                    :class="index === 0 ? 'text-gray-300 cursor-not-allowed' : 'text-gray-600 hover:text-gray-900'"
                                                    class="mr-2"
                                                    type="button"
                                                >
                                                    <ChevronUpIcon class="h-5 w-5" />
                                                </button>
                                                <button
                                                    @click="moveEtapa(index, 1)"
                                                    :disabled="index === formData.etapas.length - 1"
                                                    :class="index === formData.etapas.length - 1 ? 'text-gray-300 cursor-not-allowed' : 'text-gray-600 hover:text-gray-900'"
                                                    class="mr-2"
                                                    type="button"
                                                >
                                                    <ChevronDownIcon class="h-5 w-5" />
                                                </button>
                                                <button
                                                    @click="removeEtapa(index)"
                                                    type="button"
                                                    class="text-red-600 hover:text-red-800"
                                                >
                                                    <TrashIcon class="h-5 w-5" />
                                                </button>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Nome da Etapa *</label>
                                                <input
                                                    v-model="etapa.nome"
                                                    type="text"
                                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                                    required
                                                />
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Data de Início *</label>
                                                <input
                                                    v-model="etapa.dataInicio"
                                                    type="date"
                                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                                    required
                                                />
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Data de Término Prevista *</label>
                                                <input
                                                    v-model="etapa.dataPrevisaoTermino"
                                                    type="date"
                                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                                    required
                                                />
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Data de Conclusão</label>
                                                <input
                                                    v-model="etapa.dataConclusao"
                                                    type="date"
                                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                                />
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Peso (%) *</label>
                                                <input
                                                    v-model="etapa.peso"
                                                    type="number"
                                                    min="0"
                                                    max="100"
                                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                                    required
                                                    @change="normalizePesos"
                                                />
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Custo Previsto *</label>
                                                <input
                                                    v-model="etapa.custoPrevisto"
                                                    type="text"
                                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                                    required
                                                    @input="formatEtapaCustoPrevisto(index)"
                                                />
                                            </div>

                                            <div class="col-span-1 md:col-span-2 lg:col-span-3 space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Descrição</label>
                                                <textarea
                                                    v-model="etapa.descricao"
                                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                                    placeholder="Descrição detalhada da etapa"
                                                ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 p-4 bg-gray-50 rounded-md">
                                    <h4 class="font-medium mb-2">Progresso Total da Obra</h4>
                                    <div class="flex items-center">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div
                                                class="bg-gray-600 h-2.5 rounded-full"
                                                :style="{ width: `${calculateFormProgress()}%` }"
                                            ></div>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-700">{{ calculateFormProgress() }}%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Funcionários -->
                            <div v-if="activeTab === 'employees'" class="space-y-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium">Funcionários Alocados</h3>
                                    <button
                                        @click="showEmployeeSelector = true"
                                        type="button"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                    >
                                        <PlusIcon class="h-4 w-4 mr-1" />
                                        Adicionar Funcionário
                                    </button>
                                </div>

                                <div v-if="formData.funcionarios.length === 0" class="text-center py-8 text-gray-500">
                                    Nenhum funcionário alocado
                                </div>

                                <div v-else class="space-y-4">
                                    <div
                                        v-for="(funcionario, index) in formData.funcionarios"
                                        :key="funcionario.id"
                                        class="border p-4 rounded-md"
                                    >
                                        <div class="flex justify-between items-center mb-4">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-800 font-semibold">
                                                    {{ getInitials(funcionario.nome) }}
                                                </div>
                                                <div class="ml-3">
                                                    <h4 class="font-medium">{{ funcionario.nome }}</h4>
                                                    <p class="text-sm text-gray-500">{{ funcionario.cargo }}</p>
                                                </div>
                                            </div>
                                            <button
                                                @click="removeFuncionario(index)"
                                                type="button"
                                                class="text-red-600 hover:text-red-800"
                                            >
                                                <TrashIcon class="h-5 w-5" />
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Data de Início na Obra *</label>
                                                <input
                                                    v-model="funcionario.dataInicioObra"
                                                    type="date"
                                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                                    required
                                                />
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Data de Término na Obra</label>
                                                <input
                                                    v-model="funcionario.dataTerminoObra"
                                                    type="date"
                                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                                />
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Custo Diário *</label>
                                                <input
                                                    v-model="funcionario.custoDiario"
                                                    type="text"
                                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                                    required
                                                    @input="formatFuncionarioCustoDiario(index)"
                                                />
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Adicional Fim de Semana (%)</label>
                                                <input
                                                    v-model="funcionario.adicionalFimSemana"
                                                    type="number"
                                                    min="0"
                                                    max="100"
                                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                                />
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Adicional Noturno (%)</label>
                                                <input
                                                    v-model="funcionario.adicionalNoturno"
                                                    type="number"
                                                    min="0"
                                                    max="100"
                                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                                />
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Adicional Feriados (%)</label>
                                                <input
                                                    v-model="funcionario.adicionalFeriados"
                                                    type="number"
                                                    min="0"
                                                    max="100"
                                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                                />
                                            </div>

                                            <div class="col-span-1 md:col-span-2 lg:col-span-3 space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Observações</label>
                                                <textarea
                                                    v-model="funcionario.observacoes"
                                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                                    placeholder="Observações sobre o funcionário nesta obra"
                                                ></textarea>
                                            </div>
                                        </div>

                                        <div class="mt-4 p-3 bg-gray-50 rounded-md">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-medium">Custo Total Estimado:</span>
                                                <span class="font-medium text-gray-700">{{ formatCurrency(calculateFuncionarioCustoTotal(funcionario)) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 p-4 bg-gray-50 rounded-md">
                                    <h4 class="font-medium mb-2">Resumo de Custos com Funcionários</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="p-3 border rounded-md bg-white">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-medium">Custo Total com Funcionários:</span>
                                                <span class="font-medium text-gray-700">{{ formatCurrency(calculateTotalEmployeeCost()) }}</span>
                                            </div>
                                        </div>
                                        <div class="p-3 border rounded-md bg-white">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-medium">Média de Custo por Funcionário:</span>
                                                <span class="font-medium text-gray-700">{{ formatCurrency(calculateAverageEmployeeCost()) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. Custos -->
                            <div v-if="activeTab === 'costs'" class="space-y-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium">Custos Adicionais</h3>
                                    <button
                                        @click="addCusto"
                                        type="button"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                    >
                                        <PlusIcon class="h-4 w-4 mr-1" />
                                        Adicionar Custo
                                    </button>
                                </div>

                                <div v-if="formData.custos.length === 0" class="text-center py-8 text-gray-500">
                                    Nenhum custo adicional cadastrado
                                </div>

                                <div v-else class="space-y-4">
                                    <div
                                        v-for="(custo, index) in formData.custos"
                                        :key="index"
                                        class="border p-4 rounded-md"
                                    >
                                        <div class="flex justify-between items-center mb-4">
                                            <h4 class="font-medium">{{ custo.categoria }}</h4>
                                            <button
                                                @click="removeCusto(index)"
                                                type="button"
                                                class="text-red-600 hover:text-red-800"
                                            >
                                                <TrashIcon class="h-5 w-5" />
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Categoria *</label>
                                                <select
                                                    v-model="custo.categoria"
                                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                                    required
                                                >
                                                    <option value="">Selecione</option>
                                                    <option value="Material">Material</option>
                                                    <option value="Equipamento">Equipamento</option>
                                                    <option value="Serviço Terceirizado">Serviço Terceirizado</option>
                                                    <option value="Licenças e Taxas">Licenças e Taxas</option>
                                                    <option value="Transporte">Transporte</option>
                                                    <option value="Outros">Outros</option>
                                                </select>
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Descrição *</label>
                                                <input
                                                    v-model="custo.descricao"
                                                    type="text"
                                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                                    required
                                                />
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Valor *</label>
                                                <input
                                                    v-model="custo.valor"
                                                    type="text"
                                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                                    required
                                                    @input="formatCustoValor(index)"
                                                />
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Data *</label>
                                                <input
                                                    v-model="custo.data"
                                                    type="date"
                                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                                    required
                                                />
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Fornecedor</label>
                                                <input
                                                    v-model="custo.fornecedor"
                                                    type="text"
                                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                                />
                                            </div>

                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700">Nota Fiscal</label>
                                                <input
                                                    v-model="custo.notaFiscal"
                                                    type="text"
                                                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <h4 class="font-medium mb-4">Resumo de Custos</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <div class="p-4 border rounded-md bg-gray-50">
                                            <div class="text-sm text-gray-500 mb-1">Custo com Funcionários</div>
                                            <div class="text-lg font-medium text-gray-700">{{ formatCurrency(calculateTotalEmployeeCost()) }}</div>
                                        </div>

                                        <div class="p-4 border rounded-md bg-gray-50">
                                            <div class="text-sm text-gray-500 mb-1">Custos Adicionais</div>
                                            <div class="text-lg font-medium text-gray-700">{{ formatCurrency(calculateTotalAdditionalCost()) }}</div>
                                        </div>

                                        <div class="p-4 border rounded-md bg-gray-50">
                                            <div class="text-sm text-gray-500 mb-1">Custo Total da Obra</div>
                                            <div class="text-lg font-medium text-gray-700">{{ formatCurrency(calculateTotalCostForm()) }}</div>
                                        </div>
                                    </div>

                                    <div class="mt-6">
                                        <h5 class="font-medium mb-3">Distribuição de Custos</h5>
                                        <div class="h-10 w-full bg-gray-200 rounded-md overflow-hidden flex">
                                            <div
                                                class="h-full bg-gray-600 flex items-center justify-center text-xs text-white"
                                                :style="{ width: `${calculateEmployeeCostPercentage()}%` }"
                                                :title="`Funcionários: ${calculateEmployeeCostPercentage()}%`"
                                            >
                                                {{ calculateEmployeeCostPercentage() > 10 ? `${calculateEmployeeCostPercentage()}%` : '' }}
                                            </div>
                                            <div
                                                class="h-full bg-blue-500 flex items-center justify-center text-xs text-white"
                                                :style="{ width: `${calculateAdditionalCostPercentage()}%` }"
                                                :title="`Adicionais: ${calculateAdditionalCostPercentage()}%`"
                                            >
                                                {{ calculateAdditionalCostPercentage() > 10 ? `${calculateAdditionalCostPercentage()}%` : '' }}
                                            </div>
                                        </div>
                                        <div class="flex mt-2 text-sm">
                                            <div class="flex items-center mr-4">
                                                <div class="w-3 h-3 bg-gray-600 rounded-sm mr-1"></div>
                                                <span>Funcionários ({{ calculateEmployeeCostPercentage() }}%)</span>
                                            </div>
                                            <div class="flex items-center">
                                                <div class="w-3 h-3 bg-blue-500 rounded-sm mr-1"></div>
                                                <span>Custos Adicionais ({{ calculateAdditionalCostPercentage() }}%)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 5. Relatórios -->
                            <div v-if="activeTab === 'reports'" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="border rounded-lg p-4">
                                        <h3 class="text-lg font-medium mb-4">Progresso da Obra</h3>
                                        <div class="space-y-4">
                                            <div class="flex items-center">
                                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                    <div
                                                        class="bg-gray-600 h-2.5 rounded-full"
                                                        :style="{ width: `${calculateFormProgress()}%` }"
                                                    ></div>
                                                </div>
                                                <span class="ml-2 text-sm text-gray-700">{{ calculateFormProgress() }}%</span>
                                            </div>

                                            <div class="space-y-2">
                                                <div v-for="(etapa, index) in formData.etapas" :key="index" class="flex items-center justify-between text-sm">
                                                    <div class="flex items-center">
                                                        <div
                                                            class="w-3 h-3 rounded-full mr-2"
                                                            :class="etapa.concluida ? 'bg-gray-500' : 'bg-gray-300'"
                                                        ></div>
                                                        <span>{{ etapa.nome }}</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <span class="text-gray-500 mr-2">{{ etapa.peso }}%</span>
                                                        <span class="font-medium">{{ etapa.concluida ? '100%' : '0%' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border rounded-lg p-4">
                                        <h3 class="text-lg font-medium mb-4">Resumo Financeiro</h3>
                                        <div class="space-y-4">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="p-3 bg-gray-50 rounded-md">
                                                    <div class="text-sm text-gray-500">Custo Total</div>
                                                    <div class="text-lg font-medium">{{ formatCurrency(calculateTotalCostForm()) }}</div>
                                                </div>
                                                <div class="p-3 bg-gray-50 rounded-md">
                                                    <div class="text-sm text-gray-500">Custo por m²</div>
                                                    <div class="text-lg font-medium">{{ formatCurrency(calculateCostPerSquareMeter()) }}</div>
                                                </div>
                                            </div>

                                            <div class="space-y-2">
                                                <div class="flex justify-between text-sm">
                                                    <span>Funcionários:</span>
                                                    <span class="font-medium">{{ formatCurrency(calculateTotalEmployeeCost()) }}</span>
                                                </div>
                                                <div class="flex justify-between text-sm">
                                                    <span>Materiais:</span>
                                                    <span class="font-medium">{{ formatCurrency(calculateCostByCategory('Material')) }}</span>
                                                </div>
                                                <div class="flex justify-between text-sm">
                                                    <span>Equipamentos:</span>
                                                    <span class="font-medium">{{ formatCurrency(calculateCostByCategory('Equipamento')) }}</span>
                                                </div>
                                                <div class="flex justify-between text-sm">
                                                    <span>Serviços Terceirizados:</span>
                                                    <span class="font-medium">{{ formatCurrency(calculateCostByCategory('Serviço Terceirizado')) }}</span>
                                                </div>
                                                <div class="flex justify-between text-sm">
                                                    <span>Outros:</span>
                                                    <span class="font-medium">{{ formatCurrency(calculateOtherCosts()) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="border rounded-lg p-4">
                                    <h3 class="text-lg font-medium mb-4">Cronograma</h3>
                                    <div class="overflow-x-auto">
                                        <div class="min-w-[600px]">
                                            <div class="flex items-center mb-2">
                                                <div class="w-1/4 font-medium text-sm">Etapa</div>
                                                <div class="w-3/4 flex">
                                                    <div v-for="month in getMonthsRange()" :key="month" class="flex-1 text-center text-xs font-medium">
                                                        {{ month }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div v-for="(etapa, index) in formData.etapas" :key="index" class="flex items-center mb-3">
                                                <div class="w-1/4 text-sm truncate pr-2">{{ etapa.nome }}</div>
                                                <div class="w-3/4 flex items-center h-6 relative">
                                                    <div
                                                        class="absolute h-4 rounded-md"
                                                        :class="etapa.concluida ? 'bg-gray-500' : 'bg-gray-200'"
                                                        :style="getEtapaTimelineStyle(etapa)"
                                                    ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 flex justify-end">
                                    <button
                                        type="button"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                    >
                                        <DownloadIcon class="h-4 w-4 mr-1" />
                                        Exportar Relatório
                                    </button>
                                </div>
                            </div>

                            <!-- Botões de ação -->
                            <div class="flex justify-end space-x-4 mt-8 pt-4 border-t">
                                <button
                                    type="button"
                                    @click="showWorkForm = false"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="button"
                                    @click="saveWork"
                                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700"
                                >
                                    Salvar
                                </button>
                            </div>
                        </div>
                    </div>
                </main>

                <!-- Modal de seleção de funcionários -->
                <div v-if="showEmployeeSelector" class="fixed inset-0 bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg p-6 max-w-2xl w-full">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Selecionar Funcionários</h3>
                            <button @click="showEmployeeSelector = false" class="text-gray-500 hover:text-gray-700">
                                <XIcon class="w-6 h-6" />
                            </button>
                        </div>

                        <div class="mb-4">
                            <input
                                v-model="employeeSearchQuery"
                                type="text"
                                placeholder="Buscar funcionário..."
                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                            />
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            <div
                                v-for="employee in filteredAvailableEmployees"
                                :key="employee.id"
                                class="flex items-center justify-between p-3 border-b hover:bg-gray-50 cursor-pointer"
                                @click="selectEmployee(employee)"
                            >
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-800 font-semibold">
                                        {{ getInitials(employee.nome) }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="font-medium">{{ employee.nome }}</div>
                                        <div class="text-sm text-gray-500">{{ employee.cargo }}</div>
                                    </div>
                                </div>
                                <PlusCircleIcon class="h-5 w-5 text-gray-600" />
                            </div>

                            <div v-if="filteredAvailableEmployees.length === 0" class="py-4 text-center text-gray-500">
                                Nenhum funcionário encontrado
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button
                                @click="showEmployeeSelector = false"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                            >
                                Fechar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal de confirmação de exclusão -->
                <div v-if="showDeleteModal" class="fixed inset-0 bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-md w-full">
                    <h3 class="text-lg font-medium mb-4">Confirmar exclusão</h3>
                    <p class="mb-6">Tem certeza que deseja excluir a obra <span class="font-semibold">{{ workToDelete?.nome }}</span>? Esta ação não pode ser desfeita.</p>
                    <div class="flex justify-end space-x-4">
                        <button
                            @click="showDeleteModal = false"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                        >
                            Cancelar
                        </button>
                        <button
                            @click="deleteWork"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                        >
                            Excluir
                        </button>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </AppLayout>
</template>
