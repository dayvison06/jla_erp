<script setup lang="ts">
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { onMounted, ref } from 'vue';
import axios from 'axios';

import Checkbox from '@/components/project/Checkbox.vue'

const status = defineModel('status');
const role = defineModel('role');
const contract_type = defineModel('contract_type');
const admission_date = defineModel('admission_date');
const termination_date = defineModel('termination_date');
const salary = defineModel('salary');
const benefits = defineModel('benefits', { type: Array, default: () => [] });
const isReadonly = defineModel('read_only', {type: Boolean, default: false });

const jobRoles = ref<Array<string>>([]);

/**
 * Formata o salário no input.
 *
 * @param {Event} e - O evento de input.
 * @returns {void}
 */
const formatSalary = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length > 0) {
        value = (parseInt(value) / 100).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL',
            minimumFractionDigits: 2,
        });
    } else {
        value = '';
    }

    salary.value = value;
};

/**
 * Obtém a lista de cargos disponíveis.
 * @returns {void}
 */
async function getListJobRoles() {
    axios.get('/administracao/cargos/lista', {
    }).then((response) => {
        // Supondo que a resposta seja um array de cargos
        jobRoles.value = response.data;
    }, (error) => {
    });
}

async function getListBenefits() {
    axios.get('/administracao/beneficios/lista', {
    }).then((response) => {
        // Supondo que a resposta seja um array de benefícios
        // Aqui você pode processar os benefícios conforme necessário
    }, (error) => {
    });
}

onMounted(() => {
    getListJobRoles();
    getListBenefits();
});

</script>

<template>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- Status -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Status *</Label>
            <Select v-model="status" required :disabled="isReadonly">
                <SelectTrigger class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500">
                    <SelectValue placeholder="Selecione"/>
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        <SelectItem value="active">Ativo</SelectItem>
                        <SelectItem value="inactive">Inativo</SelectItem>
                        <SelectItem value="on_vacation">Em Férias</SelectItem>
                        <SelectItem value="on_leave">Afastado</SelectItem>
                        <SelectItem value="terminated">Desligado</SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>
        </div>

        <!-- Campo: Cargo/função -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Cargo/função *</Label>
            <Select v-model="role" required :disabled="isReadonly">
                <SelectTrigger>
                    <SelectValue placeholder="Selecione uma função" />
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        <SelectLabel>Cargos</SelectLabel>
                        <SelectItem
                            v-for="role in jobRoles"
                            :key="role.id"
                            :value="role.name"
                            @select="salary = role.base_salary"
                        >
                            {{ role.name }}
                        </SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>
        </div>

        <!-- Campo: Tipo de vínculo -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Tipo de vínculo *</Label>
            <Select v-model="contract_type" required :disabled="isReadonly">
                <SelectTrigger class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500">
                    <SelectValue placeholder="Selecione"/>
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        <SelectItem value="clt">CLT</SelectItem>
                        <SelectItem value="estagio">Estágio</SelectItem>
                        <SelectItem value="pj">PJ</SelectItem>
                        <SelectItem value="autonomo">Autônomo</SelectItem>
                        <SelectItem value="temporario">Temporário</SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>
        </div>

        <!-- Campo: Data de admissão -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Data de admissão *</Label>
            <Input
                v-model="admission_date"
                type="date"
                class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                required
                :readonly="isReadonly"
            />
        </div>

        <!-- Campo: Data de desligamento -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Data de desligamento</Label>
            <Input
                v-model="termination_date"
                type="date"
                class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                :disabled="status !== 'desligado'"
                :readonly="isReadonly"
            />
            <p class="text-xs text-gray-500">Aplicável apenas para funcionários desligados</p>
        </div>

        <!-- Campo: Salário -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Salário *</Label>
            <Input
                v-model="salary"
                type="text"
                class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                required
                @input="formatSalary"
                :readonly="isReadonly"
            />
        </div>

        <!-- Campo: Benefícios -->
        <div class="col-span-1 space-y-2 md:col-span-2 lg:col-span-3">
            <Label class="block text-sm font-medium text-foreground">Benefícios</Label>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div class="flex items-center">
                    <Checkbox
                        v-model="benefits"
                        value="Transporte"
                        class="h-4 w-4 rounded"
                    />
                    <Label class="ml-2 text-sm text-foreground">Vale Transporte</Label>
                </div>
                <div class="flex items-center">
                    <Checkbox
                        v-model="benefits"
                        value="Refeicao"
                        class="h-4 w-4 rounded"
                    />
                    <Label class="ml-2 text-sm text-foreground">Vale Refeição</Label>
                </div>
                <div class="flex items-center">
                    <Checkbox
                        v-model="benefits"
                        value="Saude"
                        class="h-4 w-4 rounded"
                    />
                    <Label class="ml-2 text-sm text-foreground">Plano de Saúde</Label>
                </div>
                <div class="flex items-center">
                    <Checkbox
                        v-model="benefits"
                        value="Odontologico"
                        class="h-4 w-4 rounded"
                    />
                    <Label class="ml-2 text-sm text-foreground">Plano Odontológico</Label>
                </div>
                <div class="flex items-center">
                    <Checkbox
                        v-model="benefits"
                        value="Seguro"
                        class="h-4 w-4 rounded"
                    />
                    <Label class="ml-2 text-sm text-foreground">Seguro de Vida</Label>
                </div>
                <div class="flex items-center">
                    <Checkbox
                        v-model="benefits"
                        value="Previdencia"
                        class="h-4 w-4 rounded"
                    />
                    <Label class="ml-2 text-sm text-foreground">Previdência Privada</Label>
                </div>
            </div>
        </div>
    </div>
</template>
