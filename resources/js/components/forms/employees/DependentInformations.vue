<script setup lang="ts">
import {formData} from "@/composables/useStoreEmployees";
import {Button} from "@/components/ui/button";
import {PlusIcon, TrashIcon} from "lucide-vue-next";
import {Input} from "@/components/ui/input";

const dependents = defineModel('dependents', {type: Array as () => Array<any>, default: () => []});
const isReadonly = defineModel('read_only', {type: Boolean, default: false});

/**
 * Formata o CPF do dependente no input.
 *
 * @param {Event} e - O evento de input.
 * @param {number} index - O índice do dependente.
 * @returns {void}
 */
const formatDependentCPF = (e: Event, index: number) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 11) {
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    }

    dependents.value[index].cpf = value;
}

/**
 * Adiciona um novo dependente ao formulário.
 * @returns {void}
 */
const addDependent = () => {
    dependents.value.push({
        name: '',
        relationship: '',
        birth_date: '',
        cpf: '',
        rg: '',
        issuing_agency: '',
        issue_date: '',
        purposes: [],
        civil_state: '',
    });
}

/**
 * Remove um dependente do formulário.
 *
 * @param {number} index - O índice do dependente a ser removido.
 * @returns {void}
 */
const removeDependent = (index: number) => {
    dependents.value.splice(index, 1);
}

</script>

<template>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium">Dependentes</h3>
        <!-- Botão para adicionar novo dependente -->
        <Button
            @click="addDependent"
            type="button"
            class="btn-primary flex items-center"
        >
            <PlusIcon class="h-4 w-4 mr-1"/>
            Adicionar
        </Button>
    </div>

    <!-- Mensagem exibida quando não há dependentes -->
    <div v-if="dependents.length === 0" class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
        Nenhum dependente cadastrado
    </div>

    <!-- Formulário para cada dependente -->
    <div v-for="(dependent, index) in dependents" :key="dependent.id"
         class="border p-4 rounded-md mb-4 bg-white shadow-sm">
        <div class="flex justify-end items-center mb-4">
            <!-- Botão para remover dependente -->
            <Button
                @click="removeDependent(index)"
                type="button"
                class="text-red-600 hover:text-red-800"
            >
                <TrashIcon class="h-5 w-5"/>
            </Button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Campos do formulário do dependente -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-foreground">Nome completo *</label>
                <Input
                    v-model="dependent.name"
                    type="text"
                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                    required
                    :readonly="isReadonly"
                />
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-foreground">Data de nascimento *</label>
                <Input
                    v-model="dependent.birth_date"
                    type="date"
                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                    required
                    :readonly="isReadonly"
                />
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-foreground">CPF *</label>
                <Input
                    v-model="dependent.cpf"
                    type="text"
                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                    required
                    @input="(e) => formatDependentCPF(e, index)"
                    :readonly="isReadonly"
                />
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-foreground">Grau de parentesco *</label>
                <select
                    v-model="dependent.relationship"
                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                    required
                    :disabled="isReadonly"
                >
                    <option value="">Selecione</option>
                    <option value="filho">Filho(a)</option>
                    <option value="conjuge">Cônjuge</option>
                    <option value="pais">Pais</option>
                    <option value="irmao">Irmão/Irmã</option>
                    <option value="outro">Outro</option>
                </select>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-foreground">Orgão Emissor *</label>
                <Input
                    v-model="dependent.issuing_agency"
                    type="text"
                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                    required
                    :readonly="isReadonly"
                />
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-foreground">Data de Emissão *</label>
                <Input
                    v-model="dependent.issue_date"
                    type="date"
                    class="w-full p-2 border rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                    required
                    :readonly="isReadonly"
                />
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-foreground">Finalidade *</label>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <Input
                            v-model="dependent.purposes"
                            type="checkbox"
                            value="income_tax"
                            class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                            :disabled="isReadonly"
                        />
                        <label class="ml-2 text-sm text-foreground">Imposto de Renda</label>
                    </div>
                    <div class="flex items-center">
                        <Input
                            v-model="dependent.purposes"
                            type="checkbox"
                            value="health_plan"
                            class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                            :disabled="isReadonly"
                        />
                        <label class="ml-2 text-sm text-foreground">Plano de Saúde</label>
                    </div>
                    <div class="flex items-center">
                        <Input
                            v-model="dependent.purposes"
                            type="checkbox"
                            value="dental_plan"
                            class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded"
                            :disabled="isReadonly"
                        />
                        <label class="ml-2 text-sm text-foreground">Plano Odontológico</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
