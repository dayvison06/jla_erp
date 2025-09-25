
<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import type { Employee } from '@/types/Employees'
import { router } from '@inertiajs/vue3';
import { debouncedWatch} from '@vueuse/core';
import { useToast } from '@/composables/useToast';
import { UserSearch, FileSpreadsheet } from 'lucide-vue-next'

const props = defineProps<{
    listEmployees: Employee[]
}>()

const employees = ref<Employee[]>(props.listEmployees)
const { showToast } = useToast();

interface Filters {
    department: string
    position: string
    status: string
    admissionDate: string
}

// Estado reativo
const searchQuery = ref('')
const showAdvancedFilters = ref(false)
const selectedEmployees = ref<number[]>([])
const openDropdown = ref<number | null>(null)
const currentPage = ref(1)
const itemsPerPage = ref(10)
const sortColumn = ref<string>('name')
const sortDirection = ref<'asc' | 'desc'>('asc')

const filters = ref<Filters>({
    department: '',
    position: '',
    status: '',
    admissionDate: ''
})

const columns = [
    { key: 'name', label: 'Nome' },
    { key: 'civilState', label: 'Estado Civil' },
    { key: 'role', label: 'Função' },
    { key: 'admissionDate', label: 'Admissão' },
    { key: 'status', label: 'Status' }
]

// Computadas
const departments = computed(() => {
    return [...new Set(employees.value.map(emp => emp.department))].sort()
})

const positions = computed(() => {
    return [...new Set(employees.value.map(emp => emp.position))].sort()
})

const filteredEmployees = computed(() => {
    let filtered = employees.value

    // Filtros avançados
    if (filters.value.department) {
        filtered = filtered.filter(emp => emp.department === filters.value.department)
    }

    if (filters.value.position) {
        filtered = filtered.filter(emp => emp.position === filters.value.position)
    }

    if (filters.value.status) {
        filtered = filtered.filter(emp => emp.status === filters.value.status)
    }

    if (filters.value.admissionDate) {
        filtered = filtered.filter(emp => emp.admissionDate === filters.value.admissionDate)
    }

    // Ordenação
    filtered.sort((a, b) => {
        const aValue = a[sortColumn.value as keyof Employee]
        const bValue = b[sortColumn.value as keyof Employee]

        if (sortDirection.value === 'asc') {
            return aValue < bValue ? -1 : aValue > bValue ? 1 : 0
        } else {
            return aValue > bValue ? -1 : aValue < bValue ? 1 : 0
        }
    })

    return filtered
})

const totalPages = computed(() => {
    return Math.ceil(filteredEmployees.value.length / itemsPerPage.value)
})

const startIndex = computed(() => {
    return (currentPage.value - 1) * itemsPerPage.value
})

const endIndex = computed(() => {
    return startIndex.value + itemsPerPage.value
})

const paginatedEmployees = computed(() => {
    return filteredEmployees.value.slice(startIndex.value, endIndex.value)
})

const isAllSelected = computed(() => {
    return paginatedEmployees.value.length > 0 &&
        paginatedEmployees.value.every(emp => selectedEmployees.value.includes(emp.id))
})

// Métodos
const toggleEmployee = (id: number) => {
    const index = selectedEmployees.value.indexOf(id)
    if (index > -1) {
        selectedEmployees.value.splice(index, 1)
    } else {
        selectedEmployees.value.push(id)
    }
}

const toggleSelectAll = () => {
    if (isAllSelected.value) {
        selectedEmployees.value = selectedEmployees.value.filter(id =>
            !paginatedEmployees.value.some(emp => emp.id === id)
        )
    } else {
        paginatedEmployees.value.forEach(emp => {
            if (!selectedEmployees.value.includes(emp.id)) {
                selectedEmployees.value.push(emp.id)
            }
        })
    }
}

const sortBy = (column: string) => {
    if (sortColumn.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortColumn.value = column
        sortDirection.value = 'asc'
    }
}

const clearFilters = () => {
    filters.value = {
        department: '',
        position: '',
        status: '',
        admissionDate: ''
    }
    searchQuery.value = ''
}

const applyFilters = () => {
    currentPage.value = 1
}

const toggleDropdown = (id: number) => {
    openDropdown.value = openDropdown.value === id ? null : id
}

const previousPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--
    }
}

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++
    }
}

const exportSelected = () => {
    const selectedData = employees.value.filter(emp => selectedEmployees.value.includes(emp.id))

    const csvContent = [
        ['Nome', 'CPF', 'Cargo', 'Departamento', 'Data de Admissão', 'Status'],
        ...selectedData.map(emp => [
            emp.name,
            emp.cpf,
            emp.position,
            emp.department,
            formatDate(emp.admissionDate),
            getStatusLabel(emp.status)
        ])
    ].map(row => row.join(',')).join('\n')

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
    const link = document.createElement('a')
    const url = URL.createObjectURL(blob)
    link.setAttribute('href', url)
    link.setAttribute('download', 'funcionarios_selecionados.csv')
    link.style.visibility = 'hidden'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
}

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('pt-BR')
}


/**
 * Busca funcionários com base nos filtros de busca e status.
 * @returns {void}
 */
function searchEmployees() {
    router.get('/funcionarios', {
        search: searchQuery.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['employees'],
        onSuccess: (page) => {
            employees.value = page.props.employees.data;
            if (employees.value.length === 0) {
                showToast('Nenhum funcionário encontrado', 'warning', 'A busca não retornou resultados.');
            }
        }

    });
}

/**
 * Retorna a cor do status do funcionário.
 *
 * @param {string} status - O status do funcionário.
 * @returns {string} A classe de cor do status.
 */
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

/**
 * Retorna o texto do status do funcionário.
 *
 * @param {string} status - O status do funcionário.
 * @returns {string} O texto do status.
 */
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

/**
 * Observa mudanças nos filtros de busca e status para atualizar a lista de funcionários.
 */
debouncedWatch([searchQuery], () => {
    // Aqui poderia implementar lógica adicional se necessário
    searchEmployees()

}, { debounce: 300 });

// Fechar dropdown ao clicar fora
onMounted(() => {
    document.addEventListener('click', (e) => {
        if (!(e.target as Element).closest('.relative')) {
            openDropdown.value = null
        }
    })
})
</script>


<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">


        <!-- Header -->
        <!-- Busca Principal -->
        <div class="mb-4 flex gap-4 items-center">
            <div>
                <span class="text-xs font-semibold">Pesquisar</span>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <UserSearch class="w-5 h-5"/>
                    </div>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Buscar por nome ou CPF..."
                        class="pl-10 pr-4 py-1 border border-gray-300 rounded-lg text-base"
                    />
                </div>
                <div>
                    <p class="text-sm text-gray-600 mt-1">{{ filteredEmployees.length }} funcionários encontrados</p>
                </div>
            </div>
            <div class="flex py-1 gap-3 sm:mt-0">
                <button
                    @click="exportSelected"
                    class="bg-gray-100 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-200 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="selectedEmployees.length === 0"
                >
                    <div class="flex items-center gap-2">
                        <FileSpreadsheet class="w-5 h-5" /> Exportar
                    </div>
                </button>
            </div>
        </div>

        <!-- Toggle Mais Filtros -->
        <div class="mb-4">
            <button
                @click="showAdvancedFilters = !showAdvancedFilters"
                class="flex items-center gap-2 btn-link-secondary font-medium transition-colors"
            >
                <svg
                    class="h-4 w-4 transition-transform duration-200"
                    :class="{ 'rotate-180': showAdvancedFilters }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
                {{ showAdvancedFilters ? 'Ocultar filtros' : 'Mais filtros' }}
            </button>
        </div>

        <!-- Filtros Avançados (Expansível) -->
        <div
            v-show="showAdvancedFilters"
            class="mb-6 p-4 rounded-lg shadown-md transition-all duration-300"
        >
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Filtro por Departamento -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Departamento</label>
                    <select
                        v-model="filters.department"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Todos os departamentos</option>
                        <option v-for="dept in departments" :key="dept" :value="dept">{{ dept }}</option>
                    </select>
                </div>

                <!-- Filtro por Cargo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cargo</label>
                    <select
                        v-model="filters.position"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Todos os cargos</option>
                        <option v-for="pos in positions" :key="pos" :value="pos">{{ pos }}</option>
                    </select>
                </div>

                <!-- Filtro por Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select
                        v-model="filters.status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Todos os status</option>
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                        <option value="ferias">Férias</option>
                        <option value="licenca">Licença</option>
                    </select>
                </div>

                <!-- Filtro por Data de Admissão -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data de Admissão</label>
                    <input
                        v-model="filters.admissionDate"
                        type="date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                </div>
            </div>

            <!-- Botões de Ação dos Filtros -->
            <div class="flex gap-3 mt-4 pt-4 border-t border-gray-200">
                <button
                    @click="clearFilters"
                    class="px-2 py-1.5 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    Limpar Filtros
                </button>
                <button
                    @click="applyFilters"
                    class="px-2 py-1.5 btn-primary transition-colors"
                >
                    Aplicar Filtros
                </button>
            </div>
        </div>

        <!-- Tabela de Funcionários -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <input
                            type="checkbox"
                            :checked="isAllSelected"
                            @change="toggleSelectAll"
                            class="rounded border-gray-300 accent-primary focus:ring-primary"
                        />
                    </th>
                    <th
                        v-for="column in columns"
                        :key="column.key"
                        @click="sortBy(column.key)"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors"
                    >
                        <div class="flex items-center gap-2">
                            {{ column.label }}
                            <svg
                                v-if="sortColumn === column.key"
                                class="h-4 w-4"
                                :class="sortDirection === 'asc' ? 'text-gray-600' : 'text-gray-600 rotate-180'"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <tr
                    v-for="employee in paginatedEmployees"
                    :key="employee.id"
                    class="hover:bg-gray-50 transition-colors"
                >
                    <td class="px-6 py-4">
                        <input
                            type="checkbox"
                            :checked="selectedEmployees.includes(employee.id)"
                            @change="toggleEmployee(employee.id)"
                            class="rounded border-gray-300 accent-primary focus:ring-primary"
                        />
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                              <span class="text-sm font-medium text-gray-800">
                                {{ employee.name.split(' ').map(n => n[0]).join('').substring(0, 2) }}
                              </span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ employee.name }}</div>
                                <div class="text-sm text-gray-500">{{ employee.cpf }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ employee.civil_state }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ employee.role }}</div>
                            <div class="text-sm text-gray-600">{{ employee.contract_type }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ formatDate(employee.admission_date) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
              <span
                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                  :class="getStatusColor(employee.status)"
              >
                {{ getStatusText(employee.status) }}
              </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="relative">
                            <button
                                @click="toggleDropdown(employee.id)"
                                class="text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                </svg>
                            </button>
                            <div
                                v-if="openDropdown === employee.id"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200"
                            >
                                <div class="py-1">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Ver Detalhes</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Editar</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Desativar</a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <div class="flex items-center justify-between mt-6">
            <div class="text-sm text-gray-700">
                Mostrando {{ startIndex + 1 }} a {{ Math.min(endIndex, filteredEmployees.length) }} de {{ filteredEmployees.length }} funcionários
            </div>
            <div class="flex gap-2">
                <button
                    @click="previousPage"
                    :disabled="currentPage === 1"
                    class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    Anterior
                </button>
                <span
                    v-for="page in totalPages"
                    :key="page"
                    @click="currentPage = page"
                    :class="[
                        'px-3 py-2 text-sm rounded-lg cursor-pointer',
                        currentPage === page ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    ]"
                                >
                    {{ page }}
                </span>
                <button
                    @click="nextPage"
                    :disabled="currentPage === totalPages"
                    class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    Próxima
                </button>
            </div>
        </div>
    </div>
</template>
