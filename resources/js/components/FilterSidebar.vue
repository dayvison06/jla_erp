<script setup lang="ts">
import { ref, computed } from 'vue'
import { X, ChevronDown } from 'lucide-vue-next'

interface FilterOption {
    id: string
    label: string
    checked: boolean
}

interface Filter {
    id: string
    label: string
    operator: string
    options: FilterOption[]
    showAll: boolean
    maxVisible: number
}

const emit = defineEmits<{
    close: []
    apply: [filters: Filter[]]
}>()

const filters = ref<Filter[]>([
    {
        id: 'reporting_manager',
        label: 'Reporting manager',
        operator: 'contains',
        maxVisible: 5,
        showAll: false,
        options: [
            { id: '1', label: 'No manager assigned', checked: false },
            { id: '2', label: 'Me', checked: false },
            { id: '3', label: 'Carolina Cabette Fonseca', checked: false },
            { id: '4', label: 'Financial LiberFly', checked: false },
            { id: '5', label: 'Jakeline Pompeu de Almeida', checked: false },
            { id: '6', label: 'João Silva', checked: false },
            { id: '7', label: 'Maria Santos', checked: false },
        ],
    },
    {
        id: 'role',
        label: 'Role',
        operator: 'contains',
        maxVisible: 5,
        showAll: false,
        options: [
            { id: '1', label: 'Account Admin', checked: false },
            { id: '2', label: 'Administrator', checked: false },
            { id: '3', label: 'Agent', checked: false },
            { id: '4', label: 'Lead converter', checked: false },
            { id: '5', label: 'Legal Ops.', checked: false },
            { id: '6', label: 'Sales Manager', checked: false },
        ],
    },
    {
        id: 'territories',
        label: 'Territories',
        operator: 'contains',
        maxVisible: 5,
        showAll: false,
        options: [
            { id: '1', label: 'No territory assigned', checked: false },
            { id: '2', label: 'Atendimento', checked: false },
        ],
    },
    {
        id: 'teams',
        label: 'Teams',
        operator: 'contains',
        maxVisible: 5,
        showAll: false,
        options: [
            { id: '1', label: 'Agendamento Closers', checked: false },
            { id: '2', label: 'Atendimento', checked: false },
            { id: '3', label: 'Closers', checked: false },
            { id: '4', label: 'Comercial', checked: false },
            { id: '5', label: 'Financeiro', checked: false },
            { id: '6', label: 'Marketing', checked: false },
            { id: '7', label: 'Suporte', checked: false },
        ],
    },
    {
        id: 'last_login',
        label: 'Last login',
        operator: 'any time',
        maxVisible: 5,
        showAll: false,
        options: [],
    },
])

const operatorOptions = ['contains', 'does not contain', 'is', 'is not', 'any time']

const visibleOptions = (filter: Filter) => {
    if (filter.showAll || filter.options.length <= filter.maxVisible) {
        return filter.options
    }
    return filter.options.slice(0, filter.maxVisible)
}

const hasMoreOptions = (filter: Filter) => {
    return filter.options.length > filter.maxVisible
}

const toggleShowAll = (filterId: string) => {
    const filter = filters.value.find((f) => f.id === filterId)
    if (filter) {
        filter.showAll = !filter.showAll
    }
}

const toggleOption = (filterId: string, optionId: string) => {
    const filter = filters.value.find((f) => f.id === filterId)
    if (filter) {
        const option = filter.options.find((o) => o.id === optionId)
        if (option) {
            option.checked = !option.checked
        }
    }
}

const hasSelectedFilters = computed(() => {
    return filters.value.some((filter) => filter.options.some((option) => option.checked))
})

const handleApply = () => {
    emit('apply', filters.value)
}
</script>

<template>
    <div class="fixed inset-y-0 right-0 w-full sm:w-96 bg-white dark:bg-gray-800 shadow-2xl border-l border-gray-200 dark:border-gray-700 z-50 flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Filters</h2>
            <button
                @click="$emit('close')"
                class="h-8 w-8 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md flex items-center justify-center transition-colors"
                aria-label="Close filters"
            >
                <X class="h-5 w-5 text-gray-500 dark:text-gray-400" />
            </button>
        </div>

        <!-- Filter Body -->
        <div class="flex-1 overflow-y-auto px-4 py-4">
            <div class="space-y-6">
                <div v-for="filter in filters" :key="filter.id" class="filter-section">
                    <!-- Filter Header -->
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ filter.label }}
                        </label>
                        <div class="relative">
                            <select
                                v-model="filter.operator"
                                class="text-xs text-gray-600 dark:text-gray-400 bg-transparent border-none pr-6 pl-2 py-1 cursor-pointer hover:text-gray-900 dark:hover:text-gray-200 focus:outline-none appearance-none"
                            >
                                <option v-for="op in operatorOptions" :key="op" :value="op">
                                    {{ op }}
                                </option>
                            </select>
                            <ChevronDown class="h-3 w-3 text-gray-500 absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none" />
                        </div>
                    </div>

                    <!-- Filter Options -->
                    <div v-if="filter.options.length > 0" class="space-y-1.5">
                        <label
                            v-for="option in visibleOptions(filter)"
                            :key="option.id"
                            class="flex items-center gap-2.5 py-1.5 px-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors group"
                        >
                            <div class="relative flex items-center justify-center">
                                <input
                                    type="checkbox"
                                    :checked="option.checked"
                                    @change="toggleOption(filter.id, option.id)"
                                    class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer"
                                />
                            </div>
                            <span class="text-sm text-gray-700 dark:text-gray-300 flex-1 truncate" :title="option.label">
                {{ option.label }}
              </span>
                        </label>

                        <!-- Show More Link -->
                        <button
                            v-if="hasMoreOptions(filter)"
                            @click="toggleShowAll(filter.id)"
                            class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium py-1 px-2"
                        >
                            {{ filter.showAll ? 'Show less' : 'Show more' }}
                        </button>
                    </div>

                    <!-- Empty State for filters without options -->
                    <div v-else class="text-xs text-gray-500 dark:text-gray-400 italic py-2">
                        No options available
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 flex items-center justify-end">
            <button
                @click="handleApply"
                :disabled="!hasSelectedFilters"
                :class="[
          'px-6 py-2 rounded-md text-sm font-medium transition-colors',
          hasSelectedFilters
            ? 'btn-primary'
            : 'btn-disabled',
        ]"
            >
                Aplicar
            </button>
        </div>
    </div>
</template>
