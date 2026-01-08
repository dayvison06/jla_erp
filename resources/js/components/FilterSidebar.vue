<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetDescription,
    SheetFooter,
} from '@/components/ui/sheet'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Label } from '@/components/ui/label'
import { Separator } from '@/components/ui/separator'

interface FilterOption {
    id: string | number
    label: string
    checked: boolean
}

interface Filter {
    id: string
    label: string
    options: FilterOption[]
    showAll: boolean
    maxVisible: number
}

const props = defineProps<{
    open: boolean
    activeFilters?: Record<string, string>
    jobRoles?: any[]
    departments?: any[]
}>()

const emit = defineEmits<{
    'update:open': [value: boolean]
    apply: [filters: Filter[]]
}>()

const isOpen = computed({
    get: () => props.open,
    set: (val) => emit('update:open', val)
})

const filters = ref<Filter[]>([
    {
        id: 'status',
        label: 'Status',
        maxVisible: 5,
        showAll: false,
        options: [
            { id: 'active', label: 'Ativo', checked: false },
            { id: 'inactive', label: 'Inativo', checked: false },
            { id: 'vacation', label: 'Em Férias', checked: false },
            { id: 'on_leave', label: 'Licença Médica', checked: false },
            { id: 'terminated', label: 'Desligado', checked: false },
        ],
    },
    {
        id: 'job_role',
        label: 'Cargo',
        maxVisible: 5,
        showAll: false,
        options: [],
    },
    {
        id: 'department',
        label: 'Departamento',
        maxVisible: 5,
        showAll: false,
        options: [],
    },
])

// Watch for changes in props to update options
watch(() => props.jobRoles, (newRoles) => {
    if (newRoles) {
        const roleFilter = filters.value.find(f => f.id === 'job_role')
        if (roleFilter) {
            roleFilter.options = newRoles.map((role: any) => ({
                id: role.id,
                label: role.name,
                checked: false
            }))
            applyInitialState()
        }
    }
}, { immediate: true })

watch(() => props.departments, (newDepts) => {
    if (newDepts) {
        const deptFilter = filters.value.find(f => f.id === 'department')
        if (deptFilter) {
            deptFilter.options = newDepts.map((dept: any) => ({
                id: dept.id,
                label: dept.name,
                checked: false
            }))
            applyInitialState()
        }
    }
}, { immediate: true })

watch(() => props.activeFilters, () => {
    applyInitialState()
}, { deep: true, immediate: true })

function applyInitialState() {
    if (!props.activeFilters) return

    filters.value.forEach(filter => {
        // Reset checks first
        // filter.options.forEach(opt => opt.checked = false)
        
        if (props.activeFilters && props.activeFilters[filter.id]) {
            const selectedIds = props.activeFilters[filter.id].split(',')
            filter.options.forEach(option => {
                option.checked = selectedIds.includes(option.id.toString())
            })
        } else {
             filter.options.forEach(option => {
                option.checked = false
            })
        }
    })
}

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

const hasSelectedFilters = computed(() => {
    return filters.value.some((filter) => filter.options.some((option) => option.checked))
})

const handleApply = () => {
    emit('apply', filters.value)
    isOpen.value = false
}

const clearFilters = () => {
    filters.value.forEach(filter => {
        filter.options.forEach(option => option.checked = false)
    })
    emit('apply', filters.value)
    isOpen.value = false
}
</script>

<template>
    <Sheet v-model:open="isOpen">
        <SheetContent class="w-full sm:max-w-md flex flex-col p-0 gap-0 border-l bg-background text-foreground">
            <SheetHeader class="px-6 py-4 border-b">
                <SheetTitle>Filtros</SheetTitle>
                <SheetDescription>
                    Selecione as opções abaixo para refinar os resultados.
                </SheetDescription>
            </SheetHeader>

            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-8">
                <div v-for="(filter, index) in filters" :key="filter.id">
                    <div class="flex items-center justify-between mb-4">
                        <Label class="text-base font-semibold">{{ filter.label }}</Label>
                    </div>

                    <!-- Options List -->
                    <div v-if="filter.options.length > 0" class="space-y-3">
                        <div
                            v-for="option in visibleOptions(filter)"
                            :key="option.id"
                            class="flex items-center space-x-2"
                        >
                            <Checkbox 
                                :id="`${filter.id}-${option.id}`" 
                                :checked="option.checked" 
                                @update:checked="(v) => option.checked = v"
                            />
                            <Label 
                                :for="`${filter.id}-${option.id}`" 
                                class="text-sm font-normal cursor-pointer leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                            >
                                {{ option.label }}
                            </Label>
                        </div>

                        <Button 
                            v-if="hasMoreOptions(filter)" 
                            variant="link" 
                            class="px-0 h-auto text-xs text-primary font-medium hover:no-underline"
                            @click="toggleShowAll(filter.id)"
                        >
                            {{ filter.showAll ? 'Mostrar menos' : `Mostrar mais (${filter.options.length - filter.maxVisible})` }}
                        </Button>
                    </div>

                     <!-- Empty State -->
                    <div v-else class="text-sm text-muted-foreground italic">
                        Nenhuma opção disponível
                    </div>
                    
                    <Separator class="mt-8" v-if="index < filters.length - 1" />
                </div>
            </div>

            <SheetFooter class="px-6 py-4 border-t bg-muted/20 sm:justify-between grid grid-cols-2 gap-3">
                <Button variant="outline" class="w-full" @click="clearFilters">
                    Limpar
                </Button>
                <Button class="w-full" @click="handleApply" :disabled="!hasSelectedFilters">
                    Aplicar
                </Button>
            </SheetFooter>
        </SheetContent>
    </Sheet>
</template>
