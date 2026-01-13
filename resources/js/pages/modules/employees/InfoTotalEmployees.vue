<script setup lang="ts">
import { ref, computed } from 'vue'
import {
    Users,
    UserCheck,
    UserX,
    Building,
} from 'lucide-vue-next'

const props = defineProps<{ allEmployees: any }>()
const employees = computed(() => props.allEmployees.data || [])
console.log('Employees in Cards.vue:', employees.value)

// Computed properties para estatísticas
const totalEmployees = computed(() => employees.value.length)
const inactiveEmployees = computed(() => employees.value.filter(emp => emp.status === 'Inativo').length)
</script>

<template>
    <div>
        <!-- Stats Cards -->
        <div class="flex gap-2 text-muted-foreground">
            <div class="flex gap-2 items-center p-2">
                <Users class="w-5 h-5" />
                <p class="text-sm font-medium">Funcionários </p>
                <span class="text-xl font-semibold">{{ totalEmployees }}</span>
            </div>

            <div class="flex gap-2 items-center p-2">
                <UserX class="w-5 h-5" />
                <p class="text-sm font-medium">Inativos</p>
                <span class="text-xl font-semibold">{{ inactiveEmployees }}</span>
            </div>
        </div>
    </div>
</template>
