<script setup lang="ts">
import { ref, computed } from 'vue'
import {
    Users,
    UserCheck,
    UserX,
    Building,
} from 'lucide-vue-next'

const props = defineProps<{ allEmployees: any[] }>()
const employees = ref(props.allEmployees.data || [])
console.log('Employees in Cards.vue:', employees.value)

// Computed properties para estatísticas
const totalEmployees = computed(() => employees.value.length)
const activeEmployees = computed(() => employees.value.filter(emp => emp.status === 'Ativo').length)
const inactiveEmployees = computed(() => employees.value.filter(emp => emp.status === 'Inativo').length)
</script>

<template>
    <div>
        <!-- Stats Cards -->
        <div class="flex gap-2">
            <div class="flex gap-2 items-center p-2">
                <Users class="w-5 h-5 text-gray-600" />
                <p class="text-sm font-medium text-gray-600">Funcionários </p>
                <span class="text-xl font-semibold text-gray-600">{{ totalEmployees }}</span>
            </div>
            <div class="flex gap-2 items-center p-2">
                <UserCheck class="w-5 h-5 text-gray-600" />
                <p class="text-sm font-medium text-gray-600">Ativos</p>
                <span class="text-xl font-semibold text-gray-600">{{ activeEmployees }}</span>
            </div>

            <div class="flex gap-2 items-center p-2">
                <UserX class="w-5 h-5 text-gray-600" />
                <p class="text-sm font-medium text-gray-600">Inativos</p>
                <span class="text-xl font-semibold text-gray-600">{{ inactiveEmployees }}</span>
            </div>
        </div>
    </div>
</template>
