<script setup lang="ts">
import { ref, computed } from 'vue'
import {
    Users,
    UserCheck,
    UserX,
    Building,
} from 'lucide-vue-next'

const props = defineProps<{ allEmployees: any[] }>()
const employees = ref(props.allEmployees || [])

// Computed properties para estatísticas
const totalEmployees = computed(() => employees.value.length)
const activeEmployees = computed(() => employees.value.filter(emp => emp.status === 'Ativo').length)
const inactiveEmployees = computed(() => employees.value.filter(emp => emp.status === 'Inativo').length)
</script>

<template>
    <div>
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total de Funcionários</p>
                        <p class="text-2xl font-bold text-gray-900">{{ totalEmployees }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <Users class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Ativos</p>
                        <p class="text-2xl font-bold text-green-600">{{ activeEmployees }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <UserCheck class="w-6 h-6 text-green-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Inativos</p>
                        <p class="text-2xl font-bold text-red-600">{{ inactiveEmployees }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <UserX class="w-6 h-6 text-red-600" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
