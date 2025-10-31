
<script setup lang="ts">
import { ref, computed, onMounted, defineEmits } from 'vue'
import type { Employee, EmployeeList } from '@/types/Employees'
import { router } from '@inertiajs/vue3';
import { debouncedWatch} from '@vueuse/core';
import { useToast } from '@/composables/useToast';
import { FileSpreadsheet, Search, Filter, ChevronDown, LayoutGrid, List,  EditIcon, ShieldBan } from 'lucide-vue-next';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';

const props = defineProps<{
    listEmployees: EmployeeList[],
    viewMode: 'list' | 'grid'
}>()

const emit = defineEmits<{
    selectedEmployees: []
}>()

const employees = ref(props.listEmployees)
const { showToast } = useToast();

interface Filters {
    department: string
    position: string
    status: string
    admissionDate: string
}

// Estado reativo
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
    const idx = selectedEmployees.value.indexOf(id);
    if (idx === -1) {
        selectedEmployees.value.push(id);
    } else {
        selectedEmployees.value.splice(idx, 1);
    }
    emit('selectedEmployees', selectedEmployees.value);
    console.log('Selected Employees:', selectedEmployees.value);
};

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

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('pt-BR')
}


/**
 * Busca e exibe um funcionário pelo CPF.
 *
 * @param {string} cpf - O CPF do funcionário.
 * @returns {Promise<void>}
 */
async function showEmployee(id: number) {
    router.get(`/funcionarios/${id}`, {
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
            // Check if the employee was found
        },
        onError: () => {
            alert('Error searching for employee. Check the CPF and try again.');
        }
    });

}

async function deactivateEmployee (cpf: string) {
    newEmployee.value = false;
    console.log('DEACTIVATE EMPLOYEE', cpf);
    router.post(`/funcionarios/deactivate`, {
        cpf: cpf
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            // Check if the employee was found
            loadEmployees();
        },
        onError: () => {

            alert('Error searching for employee. Check the CPF and try again.');
        }
    });

}

/**
 * Exclui um funcionário.
 * @returns {void}
 */
const deleteEmployee = () => {
    if (!employeeToDelete.value) return;

    router.delete(`/funcionarios/${form}`, {
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


console.log('Employees List:', employees.value);

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
</script>

<template>
    <section>
        <!-- Tabela de Funcionários -->
        <div v-if="viewMode === 'list'" class="relative overflow-x-auto shadow sm:rounded-lg">
            <table class="table-auto w-full border-collapse text-left">
                <thead class="bg-primary text-white uppercase  sticky top-0">
                <tr>
                    <th class="px-6 py-3">
                        <input
                            type="checkbox"
                            :checked="isAllSelected"
                            @change="toggleSelectAll"
                            class="rounded border-gray-300 accent-red-100 focus:ring-primary"
                        />
                    </th>
                    <th
                        v-for="column in columns"
                        :key="column.key"
                        @click="sortBy(column.key)"
                        class="px-6 py-3 text-xs font-medium tracking-wider cursor-pointer transition-colors"
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
                    <th class="px-3 py-3 text-xs font-medium tracking-wider">
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <tr
                    v-for="employee in paginatedEmployees"
                    :key="employee.id"
                    class="group bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                    @click="showEmployee(employee.id)"
                >
                    <td class="px-6 py-4">
                        <input
                            type="checkbox"
                            @click.stop
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
                </tr>
                </tbody>
            </table>
        </div>

        <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div
                v-for="employee in paginatedEmployees"
                :key="employee.id"
                class="default-box p-6 flex flex-col group hover:shadow-lg transition-shadow cursor-pointer"
                @click="showEmployee(employee.id)"
            >
                <div class="flex items-center mb-4">
                    <div class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center">
                      <span class="text-lg font-bold text-gray-800">
                        {{ employee.name.split(' ').map(n => n[0]).join('').substring(0, 2) }}
                      </span>
                    </div>
                    <div class="ml-4">
                        <div class="text-base font-semibold text-gray-900">{{ employee.name }}</div>
                        <div class="text-sm text-gray-500">{{ employee.cpf }}</div>
                    </div>
                </div>
                <div class="mb-2 text-sm text-gray-700">
                    <span class="font-medium">Função:</span> {{ employee.role }}
                </div>
                <div class="mb-2 text-sm text-gray-700">
                    <span class="font-medium">Departamento:</span> {{ employee.department[0] }}
                </div>
                <div class="mb-2 text-sm text-gray-700">
                    <span class="font-medium">Admissão:</span> {{ formatDate(employee.admission_date) }}
                </div>
                <div class="mb-2">
                    <span
                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                        :class="getStatusColor(employee.status)"
                    >
                      {{ getStatusText(employee.status) }}
                    </span>
                </div>
            </div>
        </div>
    </section>
    <!-- Paginação -->
    <div class="flex items-center justify-between default-box mt-6 p-4">
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
</template>
