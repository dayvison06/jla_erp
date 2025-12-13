<script setup lang="ts">

import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { toast } from 'vue-sonner';

const postal_code = defineModel('postal_code');
const street = defineModel('street');
const number = defineModel('number');
const complement = defineModel('complement');
const district = defineModel('district');
const city = defineModel('city');
const state = defineModel('state');
const isReadonly = defineModel('read_only', {type: Boolean, default: false });

const cnhCategories = ['A', 'B', 'C', 'D', 'E', 'AB', 'AC', 'AD', 'AE'];

const states = [
    'AC',
    'AL',
    'AP',
    'AM',
    'BA',
    'CE',
    'DF',
    'ES',
    'GO',
    'MA',
    'MT',
    'MS',
    'MG',
    'PA',
    'PB',
    'PR',
    'PE',
    'PI',
    'RJ',
    'RN',
    'RS',
    'RO',
    'RR',
    'SC',
    'SP',
    'SE',
    'TO',
];

/**
 * Formata o CEP no input.
 *
 * @param {Event} e - O evento de input.
 * @returns {void}
 */
const formatCEP = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 8) {
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
    }

    postal_code.value = value;
    searchZipCode();
};


/**
 * Busca o endereço a partir do CEP.
 * @returns {Promise<void>}
 */
const searchZipCode = async () => {
    const cep = postal_code.value.replace(/\D/g, '');
    console.log(cep);
    if (cep.length !== 8) return;

    toast.promise(
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then((res) => res.json())
            .then((json) => {
                if (json.erro) {
                    throw new Error('CEP não encontrado.');
                }
                street.value = json.logradouro;
                district.value = json.bairro;
                city.value = json.localidade;
                state.value = json.uf;
            }),
        {
            position: 'top-center',
            loading: 'Buscando endereço...',
            success: 'Endereço encontrado.',
            error: 'Erro ao buscar CEP, preencha manualmente.',
        },
    );
};

</script>

<template>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- Campo: CEP -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">
                CEP * <span class="text-primary text-xs font-medium">(Preenchimento automático)</span></Label
            >
            <Input
                v-model="postal_code"
                type="text"
                class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                required
                @input="formatCEP"
                :readonly="isReadonly"
            />
        </div>

        <!-- Campo: Logradouro -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Logradouro *</Label>
            <Input
                v-model="street"
                type="text"
                class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                required
                :readonly="isReadonly"
            />
        </div>

        <!-- Campo: Número -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Número *</Label>
            <Input
                v-model="number"
                type="text"
                class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                required
                :readonly="isReadonly"
            />
        </div>

        <!-- Campo: Complemento -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Complemento</Label>
            <Input
                v-model="complement"
                type="text"
                class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                :readonly="isReadonly"
            />
        </div>

        <!-- Campo: Bairro -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Bairro *</Label>
            <Input
                v-model="district"
                type="text"
                class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                required
                :readonly="isReadonly"
            />
        </div>

        <!-- Campo: Cidade -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Cidade *</Label>
            <Input
                v-model="city"
                type="text"
                class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                required
                :readonly="isReadonly"
            />
        </div>

        <!-- Campo: Estado -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Estado *</Label>
            <Select v-model="state" :disabled="isReadonly">
                <SelectTrigger class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500">
                    <SelectValue placeholder="Selecione"/>
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        <SelectItem v-for="uf in states" :key="uf" :value="uf">{{ uf }}</SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>
        </div>
    </div>
</template>
