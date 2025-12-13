<script setup lang="ts">

import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const ctps_number = defineModel('ctps_number');
const ctps_series = defineModel('ctps_series');
const ctps_state = defineModel('ctps_state');
const pis_pasep = defineModel('pis_pasep');
const nit = defineModel('nit');
const cnh = defineModel('cnh');
const cnh_category = defineModel('cnh_category');
const cnh_expiry = defineModel('cnh_expiry');
const professional_registration = defineModel('professional_registration');
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

</script>

<template>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- Campo: Número da Carteira de Trabalho -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Número da Carteira de Trabalho *</Label>
            <Input
                v-model="ctps_number"
                type="text"
                class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                required
                :readonly="isReadonly"
            />
        </div>

        <!-- Campo: Série CTPS -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Série CTPS *</Label>
            <Input
                v-model="ctps_series"
                type="text"
                class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                required
                :readonly="isReadonly"
            />
        </div>

        <!-- Campo: UF CTPS -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">UF CTPS *</Label>
            <Select v-model="ctps_state" :disabled="isReadonly">
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

        <!-- Campo: PIS/PASEP -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">PIS/PASEP *</Label>
            <Input
                v-model="pis_pasep"
                type="text"
                class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                required
                :readonly="isReadonly"
            />
        </div>

        <!-- Campo: Número do NIT -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Número do NIT</Label>
            <Input
                v-model="nit"
                type="text"
                class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                :readonly="isReadonly"
            />
            <p class="text-xs text-gray-500">Para autônomos ou contribuintes individuais</p>
        </div>

        <!-- Campo: CNH -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">CNH</Label>
            <Input
                v-model="cnh"
                type="text"
                class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                :readonly="isReadonly"
            />
        </div>

        <!-- Campo: Categoria CNH -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Categoria CNH</Label>
            <Select v-model="cnh_category" :disabled="isReadonly">
                <SelectTrigger class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500">
                    <SelectValue placeholder="Selecione"/>
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        <SelectItem v-for="cat in cnhCategories" :key="cat" :value="cat">{{ cat }}</SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>
        </div>

        <!-- Campo: Validade CNH -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Validade CNH</Label>
            <Input
                v-model="cnh_expiry"
                type="date"
                class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                :readonly="isReadonly"
            />
        </div>

        <!-- Campo: Registro profissional -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Registro profissional</Label>
            <Input
                v-model="professional_registration"
                type="text"
                class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                :readonly="isReadonly"
            />
            <p class="text-xs text-gray-500">Ex: CREA, CRM, OAB, etc.</p>
        </div>
    </div>
</template>
