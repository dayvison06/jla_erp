<script setup lang="ts">

import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const bank = defineModel('bank');
const agency = defineModel('agency');
const account = defineModel('account');
const account_type = defineModel('account_type');
const pix_key_type = defineModel('pix_key_type');
const pix_key = defineModel('pix_key');
const isReadonly = defineModel('read_only', {type: Boolean, default: false });

const banks = [
    { codigo: '001', nome: 'Banco do Brasil' },
    { codigo: '033', nome: 'Santander' },
    { codigo: '104', nome: 'Caixa Econômica Federal' },
    { codigo: '237', nome: 'Bradesco' },
    { codigo: '341', nome: 'Itaú' },
    { codigo: '260', nome: 'Nubank' },
    { codigo: '077', nome: 'Inter' },
    { codigo: '336', nome: 'C6 Bank' },
    { codigo: '212', nome: 'Banco Original' },
    { codigo: '655', nome: 'Votorantim' },
    { codigo: '756', nome: 'Sicoob' },
    { codigo: '748', nome: 'Sicredi' },
];

</script>

<template>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- Campo: Banco -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Banco *</Label>
            <Select v-model="bank" required :disabled="isReadonly">
                <SelectTrigger>
                    <SelectValue placeholder="Selecione um banco" />
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        <SelectItem
                            v-for="bank in banks"
                            :key="bank.codigo"
                            :value="bank.codigo"
                        >
                            {{ bank.codigo }} - {{ bank.nome }}
                        </SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>
        </div>

        <!-- Campo: Agência -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Agência *</Label>
            <Input
                v-model="agency"
                type="text"
                class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                required
                :readonly="isReadonly"
            />
        </div>

        <!-- Campo: Conta -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Conta *</Label>
            <Input
                v-model="account"
                type="text"
                class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                required
                :readonly="isReadonly"
            />
        </div>

        <!-- Campo: Tipo de conta -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Tipo de conta *</Label>
            <Select v-model="account_type" required :disabled="isReadonly">
                <SelectTrigger class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500">
                    <SelectValue placeholder="Selecione"/>
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        <SelectItem value="corrente">Conta Corrente</SelectItem>
                        <SelectItem value="poupanca">Conta Poupança</SelectItem>
                        <SelectItem value="salario">Conta Salário</SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>
        </div>

        <!-- Campo: Tipo Chave PIX -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Tipo de Chave PIX</Label>
            <Select v-model="pix_key_type" :disabled="isReadonly">
                <SelectTrigger class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500">
                    <SelectValue placeholder="Selecione"/>
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        <SelectItem value="cpf">CPF</SelectItem>
                        <SelectItem value="email">E-mail</SelectItem>
                        <SelectItem value="phone">Telefone</SelectItem>
                        <SelectItem value="random">Chave Aleatória</SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>
            <p class="text-xs text-gray-500">CPF, e-mail, telefone ou chave aleatória</p>
        </div>

        <!-- Campo: Chave PIX -->
        <div class="space-y-2">
            <Label class="block text-sm font-medium text-foreground">Chave PIX</Label>
            <Input
                v-model="pix_key"
                type="text"
                class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                :readonly="isReadonly"
            />
            <p class="text-xs text-gray-500">CPF, e-mail, telefone ou chave aleatória</p>
        </div>
    </div>
</template>
