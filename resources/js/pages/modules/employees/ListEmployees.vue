
<script setup lang="ts">
import { ref, computed, defineEmits } from 'vue'
import type { Employee, EmployeeList } from '@/types/Employees'
import { router } from '@inertiajs/vue3';
import { showToast } from '@/composables/useToast';

const props = defineProps<{
    listEmployees: EmployeeList[],
    viewMode: 'list' | 'grid'
}>()

const emit = defineEmits<{
    selectedEmployees: []
}>()

const paginatedEmployees = ref(props.listEmployees)
console.log('Employees in ListEmployees.vue:', paginatedEmployees.value)

// Estado reativo
const selectedEmployees = ref<number[]>([])
const sortColumn = ref<string>('name')
const sortDirection = ref<'asc' | 'desc'>('asc')

const columns = [
    { key: 'name', label: 'Nome' },
    { key: 'civilState', label: 'Estado Civil' },
    { key: 'role', label: 'Função' },
    { key: 'admissionDate', label: 'Admissão' },
    { key: 'status', label: 'Status' }
]

const isAllSelected = computed(() => {
    return paginatedEmployees.value.data.length > 0 &&
        paginatedEmployees.value.data.every(emp => selectedEmployees.value.includes(emp.id))
})

// Métodos
const toggleEmployee = (id: number) => {
    const idx = selectedEmployees.value.indexOf(id);
    if (idx === -1) {
        selectedEmployees.value.push(id);
    } else {
        selectedEmployees.value.splice(idx, 1);
    }
    emit('update:selectedEmployees', selectedEmployees.value);
    console.log('Selected Employees:', selectedEmployees.value);
};

const toggleSelectAll = () => {
    if (isAllSelected.value) {
        selectedEmployees.value = selectedEmployees.value.filter(id =>
            !paginatedEmployees.value.data.some(emp => emp.id === id)
        )
    } else {
        paginatedEmployees.value.data.forEach(emp => {
            if (!selectedEmployees.value.includes(emp.id)) {
                selectedEmployees.value.push(emp.id)
            }
        })
    }
    emit('update:selectedEmployees', selectedEmployees.value);
}

const sortBy = (column: string) => {
    if (sortColumn.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortColumn.value = column
        sortDirection.value = 'asc'
    }
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
 * Busca e exibe um funcionário
 * @param {number} id - ID do funcionário a ser exibido.
 * @returns {Promise<void>}
 */
async function showEmployee(id: number) {
    router.get(`/funcionarios/${id}`, {
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
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

/**
 * Retorna a cor do status do funcionário.
 *
 * @param {string} status - O status do funcionário.
 * @returns {string} A classe de cor do status.
 */
const getStatusColor = (status: string) => {
    switch (status) {
        case 'active':
            return 'bg-green-700 text-white';
        case 'inactive':
            return 'bg-muted text-gray-800';
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
                <thead class="bg-card text-foreground uppercase sticky top-0 shadow-md">
                <tr>
                    <th class="px-6 py-3">
<!--                     Checkbox customizado-->
                        <div class="inline-flex items-center">
                            <label class="flex items-center cursor-pointer relative">
                                <input :checked="isAllSelected"
                                       @change="toggleSelectAll"
                                       type="checkbox" class="peer h-6 w-6 cursor-pointer transition-all appearance-none rounded-full shadow hover:shadow-md border-2 border-muted checked:bg-checked" />
                                <span class="absolute text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" stroke="currentColor" stroke-width="1">
                                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    </span>
                            </label>
                        </div>
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
                                :class="sortDirection === 'asc' ? 'text-foreground' : 'text-foreground rotate-180'"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody class="bg-card/40 text-sm text-foreground divide-y divide-gray-200">
                <tr
                    v-for="employee in paginatedEmployees.data"
                    :key="employee.id"
                    class="group border-b border-muted hover:bg-muted cursor-pointer"
                    @click="showEmployee(employee.id)"
                >
                    <td class="px-6 py-4">
                        <div class="inline-flex items-center" @click.stop>
                            <label class="flex items-center cursor-pointer relative">
                                <input
                                    @change="toggleEmployee(employee.id)"
                                    :checked="selectedEmployees.includes(employee.id)"
                                    type="checkbox" class="peer h-6 w-6 cursor-pointer transition-all appearance-none rounded-full shadow hover:shadow-md border-2 border-muted checked:bg-checked" />
                                <span class="absolute text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" stroke="currentColor" stroke-width="1">
                                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            </label>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                              <span class="text-sm font-medium text-gray-800">
                                {{ employee.name.split(' ').map(n => n[0]).join('').substring(0, 2) }}
                              </span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium">{{ employee.name }}</div>
                                <div class="text-sm text-muted-foreground">{{ employee.email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ employee.civil_state }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <div class="text-sm font-medium">{{ employee.role }}</div>
                            <div class="text-sm text-muted-foreground">{{ employee.contract_type }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
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
                v-for="employee in paginatedEmployees.data"
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
        <div class="text-sm text-foreground">
            <p v-if="paginatedEmployees.to === paginatedEmployees.total"> Mostrando {{ paginatedEmployees.from }} de {{ paginatedEmployees.total }} funcionário</p>
            <p v-else>Mostrando {{ paginatedEmployees.from }} a {{ paginatedEmployees.to }} de {{ paginatedEmployees.total }} funcionários</p>
        </div>
        <div class="flex gap-2">
            <Button
                @click="previousPage"
                :disabled="currentPage === 1"
                class="px-3 py-2 text-sm border border-muted rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors curs cursor-pointer"
            >
                Anterior
            </Button>
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
            <Button
                @click="nextPage"
                :disabled="currentPage === totalPages"
                class="px-3 py-2 text-sm border border-muted rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors cursor-pointer"
            >
                Próxima
            </Button>
        </div>
    </div>
</template>
