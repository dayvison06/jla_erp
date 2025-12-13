<script setup lang="ts">

import { formData } from '@/composables/useStoreEmployees';
import { Input } from '@/components/ui/input';

const last_exam_date = defineModel('last_exam_date');;
const next_exam_date = defineModel('next_exam_date');
const aso_result = defineModel('aso_result');
const allergies = defineModel('allergies');
const accident_history = defineModel('accident_history');
const isReadonly = defineModel('read_only', { type: Boolean, default: false });
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Campo: Data do último exame -->
        <div class="space-y-2">
            <label class="block text-sm font-medium text-foreground">Data do último exame
                admissional/periódico *</label>
            <Input
                v-model="last_exam_date"
                type="date"
                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                required
                :readonly="isReadonly"
            />
        </div>

        <!-- Campo: Data do próximo exame -->
        <div class="space-y-2">
            <label class="block text-sm font-medium text-foreground">Data do próximo exame *</label>
            <Input
                v-model="next_exam_date"
                type="date"
                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                required
                :readonly="isReadonly"
            />
        </div>

        <!-- Campo: Resultado do ASO -->
        <div class="space-y-2">
            <label class="block text-sm font-medium text-foreground">Resultado do ASO *</label>
            <select
                v-model="aso_result"
                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                required
                :disabled="isReadonly"
            >
                <option value="">Selecione</option>
                <option value="apto">Apto</option>
                <option value="aptoRestricoes">Apto com restrições</option>
                <option value="inapto">Inapto</option>
            </select>
        </div>

        <!-- Campo: Alergias ou restrições médicas -->
        <div class="col-span-1 md:col-span-2 lg:col-span-3 space-y-2">
            <label class="block text-sm font-medium text-foreground">Alergias ou restrições
                médicas</label>
            <textarea
                v-model="allergies"
                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500 min-h-[100px]"
                placeholder="Descreva alergias ou restrições médicas, se houver"
                :readonly="isReadonly"
            ></textarea>
        </div>

        <!-- Campo: Histórico de acidentes ou doenças ocupacionais -->
        <div class="col-span-1 md:col-span-2 lg:col-span-3 space-y-2">
            <label class="block text-sm font-medium text-foreground">Histórico de acidentes ou doenças
                ocupacionais</label>
            <textarea
                v-model="accident_history"
                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500 min-h-[100px]"
                placeholder="Descreva o histórico, se houver"
                :readonly="isReadonly"
            ></textarea>
        </div>
    </div>
</template>


