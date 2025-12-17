<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { PlusIcon, TrashIcon } from "lucide-vue-next";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Checkbox } from "@/components/ui/checkbox";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";

const dependents = defineModel('dependents', { type: Array as () => Array<any>, default: () => [] });
const isReadonly = defineModel('read_only', { type: Boolean, default: false });

/**
 * Formata o CPF do dependente no input.
 *
 * @param {string | number} value - O valor do input.
 * @param {number} index - O índice do dependente.
 * @returns {void}
 */
const formatDependentCPF = (value: string | number, index: number) => {
    let cpf = String(value).replace(/\D/g, '');

    if (cpf.length <= 11) {
        cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
        cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
        cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    }

    dependents.value[index].cpf = cpf;
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

/**
 * Atualiza o array de finalidades (propósitos) do dependente com base no estado do checkbox
 */
const updatePurpose = (checked: boolean, purposeValue: string, dependent: any) => {
    if (!Array.isArray(dependent.purposes)) {
        dependent.purposes = [];
    }

    if (checked) {
        if (!dependent.purposes.includes(purposeValue)) {
            dependent.purposes.push(purposeValue);
        }
    } else {
        const idx = dependent.purposes.indexOf(purposeValue);
        if (idx !== -1) {
            dependent.purposes.splice(idx, 1);
        }
    }
}
</script>

<template>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium">Dependentes</h3>
        <!-- Botão para adicionar novo dependente -->
        <Button
            @click="addDependent"
            type="button"
            class="flex items-center"
            :disabled="isReadonly"
        >
            <PlusIcon class="h-4 w-4 mr-1" />
            Adicionar
        </Button>
    </div>

    <!-- Mensagem exibida quando não há dependentes -->
    <div v-if="dependents.length === 0" class="text-center py-8 text-muted-foreground bg-muted/50 rounded-lg border border-dashed">
        Nenhum dependente cadastrado
    </div>

    <!-- Formulário para cada dependente -->
    <div v-for="(dependent, index) in dependents" :key="index"
         class="border p-6 rounded-lg mb-6 bg-card text-card-foreground shadow-sm">
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h4 class="font-medium text-sm text-muted-foreground">Dependente #{{ index + 1 }}</h4>
            <!-- Botão para remover dependente -->
            <Button
                @click="removeDependent(index)"
                type="button"
                variant="ghost"
                class="text-destructive hover:text-destructive hover:bg-destructive/10 h-8 w-8 p-0"
                :disabled="isReadonly"
            >
                <TrashIcon class="h-4 w-4" />
            </Button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Campos do formulário do dependente -->
            <div class="space-y-2">
                <Label :for="`name-${index}`">Nome completo *</Label>
                <Input
                    :id="`name-${index}`"
                    v-model="dependent.name"
                    type="text"
                    required
                    :readonly="isReadonly"
                    placeholder="Nome do dependente"
                />
            </div>

            <div class="space-y-2">
                <Label :for="`birth_date-${index}`">Data de nascimento *</Label>
                <Input
                    :id="`birth_date-${index}`"
                    v-model="dependent.birth_date"
                    type="date"
                    required
                    :readonly="isReadonly"
                />
            </div>

            <div class="space-y-2">
                <Label :for="`cpf-${index}`">CPF *</Label>
                <Input
                    :id="`cpf-${index}`"
                    v-model="dependent.cpf"
                    type="text"
                    required
                    @input="(e) => formatDependentCPF((e.target as HTMLInputElement).value, index)"
                    :readonly="isReadonly"
                    placeholder="000.000.000-00"
                    maxlength="14"
                />
            </div>

            <div class="space-y-2">
                <Label>Grau de parentesco *</Label>
                <Select v-model="dependent.relationship" :disabled="isReadonly">
                    <SelectTrigger>
                        <SelectValue placeholder="Selecione" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="filho">Filho(a)</SelectItem>
                        <SelectItem value="conjuge">Cônjuge</SelectItem>
                        <SelectItem value="pais">Pais</SelectItem>
                        <SelectItem value="irmao">Irmão/Irmã</SelectItem>
                        <SelectItem value="outro">Outro</SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div class="space-y-2">
                <Label :for="`issuing_agency-${index}`">Orgão Emissor *</Label>
                <Input
                    :id="`issuing_agency-${index}`"
                    v-model="dependent.issuing_agency"
                    type="text"
                    required
                    :readonly="isReadonly"
                    placeholder="Ex: SSP/SP"
                />
            </div>

            <div class="space-y-2">
                <Label :for="`issue_date-${index}`">Data de Emissão *</Label>
                <Input
                    :id="`issue_date-${index}`"
                    v-model="dependent.issue_date"
                    type="date"
                    required
                    :readonly="isReadonly"
                />
            </div>

            <div class="space-y-2 md:col-span-2 lg:col-span-3">
                <Label class="mb-2 block">Finalidade *</Label>
                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center space-x-2 border rounded-md p-3 bg-background">
                        <Checkbox
                            :id="`purpose-income-${index}`"
                            :checked="dependent.purposes?.includes('income_tax')"
                            @update:checked="(checked) => updatePurpose(checked, 'income_tax', dependent)"
                            :disabled="isReadonly"
                        />
                        <Label :for="`purpose-income-${index}`" class="font-normal cursor-pointer">Imposto de Renda</Label>
                    </div>

                    <div class="flex items-center space-x-2 border rounded-md p-3 bg-background">
                        <Checkbox
                            :id="`purpose-health-${index}`"
                            :checked="dependent.purposes?.includes('health_plan')"
                            @update:checked="(checked) => updatePurpose(checked, 'health_plan', dependent)"
                            :disabled="isReadonly"
                        />
                        <Label :for="`purpose-health-${index}`" class="font-normal cursor-pointer">Plano de Saúde</Label>
                    </div>

                    <div class="flex items-center space-x-2 border rounded-md p-3 bg-background">
                        <Checkbox
                            :id="`purpose-dental-${index}`"
                            :checked="dependent.purposes?.includes('dental_plan')"
                            @update:checked="(checked) => updatePurpose(checked, 'dental_plan', dependent)"
                            :disabled="isReadonly"
                        />
                        <Label :for="`purpose-dental-${index}`" class="font-normal cursor-pointer">Plano Odontológico</Label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
