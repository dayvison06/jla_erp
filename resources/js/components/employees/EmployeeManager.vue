<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Gerenciamento de Funcionários</h2>
                <p class="text-sm text-gray-600 mt-1">{{ filteredEmployees.length }} funcionários encontrados</p>
            </div>
            <div class="flex gap-3 mt-4 sm:mt-0">
                <button
                    @click="exportSelected"
                    :disabled="selectedEmployees.length === 0"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
                >
                    Exportar Selecionados ({{ selectedEmployees.length }})
                </button>
            </div>
        </div>

        <!-- Busca Principal -->
        <div class="mb-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Buscar por nome ou CPF..."
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg"
                />
            </div>
        </div>

        <!-- Toggle Mais Filtros -->
        <div class="mb-4">
            <button
                @click="showAdvancedFilters = !showAdvancedFilters"
                class="flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium transition-colors"
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
            class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200 transition-all duration-300"
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
                    class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    Limpar Filtros
                </button>
                <button
                    @click="applyFilters"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
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
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
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
                                :class="sortDirection === 'asc' ? 'text-blue-600' : 'text-blue-600 rotate-180'"
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
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        />
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                  <span class="text-sm font-medium text-blue-800">
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
                        {{ employee.position }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ employee.department }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ formatDate(employee.admissionDate) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
              <span
                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                  :class="getStatusClass(employee.status)"
              >
                {{ getStatusLabel(employee.status) }}
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
                <span class="px-3 py-2 text-sm bg-blue-600 text-white rounded-lg">
          {{ currentPage }}
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

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

interface Employee {
    id: number
    name: string
    cpf: string
    position: string
    department: string
    admissionDate: string
    status: 'ativo' | 'inativo' | 'ferias' | 'licenca'
}

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

// Dados mockados
const employees = ref<Employee[]>([
    {
        id: 1,
        name: 'João Silva',
        cpf: '123.456.789-00',
        position: 'Desenvolvedor Frontend',
        department: 'Tecnologia',
        admissionDate: '2023-01-15',
        status: 'ativo'
    },
    {
        id: 2,
        name: 'Maria Santos',
        cpf: '987.654.321-00',
        position: 'Designer UX/UI',
        department: 'Design',
        admissionDate: '2023-02-20',
        status: 'ativo'
    },
    {
        id: 3,
        name: 'Pedro Oliveira',
        cpf: '456.789.123-00',
        position: 'Gerente de Projetos',
        department: 'Gestão',
        admissionDate: '2022-11-10',
        status: 'ferias'
    },
    {
        id: 4,
        name: 'Ana Costa',
        cpf: '789.123.456-00',
        position: 'Analista de Marketing',
        department: 'Marketing',
        admissionDate: '2023-03-05',
        status: 'ativo'
    },
    {
        id: 5,
        name: 'Carlos Ferreira',
        cpf: '321.654.987-00',
        position: 'Desenvolvedor Backend',
        department: 'Tecnologia',
        admissionDate: '2022-08-12',
        status: 'licenca'
    }
])

const columns = [
    { key: 'name', label: 'Nome' },
    { key: 'position', label: 'Cargo' },
    { key: 'department', label: 'Departamento' },
    { key: 'admissionDate', label: 'Data de Admissão' },
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

    // Busca principal por nome ou CPF
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(emp =>
            emp.name.toLowerCase().includes(query) ||
            emp.cpf.includes(query)
        )
    }

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

const getStatusClass = (status: string) => {
    const classes = {
        ativo: 'bg-green-100 text-green-800',
        inativo: 'bg-red-100 text-red-800',
        ferias: 'bg-yellow-100 text-yellow-800',
        licenca: 'bg-blue-100 text-blue-800'
    }
    return classes[status as keyof typeof classes] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status: string) => {
    const labels = {
        ativo: 'Ativo',
        inativo: 'Inativo',
        ferias: 'Férias',
        licenca: 'Licença'
    }
    return labels[status as keyof typeof labels] || status
}

// Fechar dropdown ao clicar fora
onMounted(() => {
    document.addEventListener('click', (e) => {
        if (!(e.target as Element).closest('.relative')) {
            openDropdown.value = null
        }
    })
})
</script>
