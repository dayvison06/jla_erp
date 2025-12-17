<script setup lang="ts">

import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import { ArrowRight } from 'lucide-vue-next';
import { Label } from '@/components/ui/label';
import { isReadonly } from 'vue';

const bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

const name = defineModel('name');
const email = defineModel('email');
const gender = defineModel('gender');
const birth_date = defineModel('birth_date');
const civil_state = defineModel('civil_state');
const nationality = defineModel('nationality');
const birthplace = defineModel('birthplace');
const cnpj = defineModel('cnpj');
const cpf = defineModel('cpf');
const rg = defineModel('rg');
const issuing_agency = defineModel('issuing_agency');
const issue_date = defineModel('issue_date');
const voter_registration = defineModel('voter_registration');
const military_certificate = defineModel('military_certificate');
const mother_name = defineModel('mother_name');
const father_name = defineModel('father_name');
const escolarity = defineModel('escolarity');
const blood_type = defineModel('blood_type');
const phone = defineModel('phone');
const mobile = defineModel('mobile');
const emergency_contact = defineModel('emergency_contact');
const emergency_phone = defineModel('emergency_phone');
const isReadonly = defineModel('read_only', { type: Boolean, default: false });

/**
 * Formata o telefone de emergência no input.
 *
 * @param {Event} e - O evento de input.
 * @returns {void}
 */
const formatEmergencyPhone = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 11) {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
    }

    emergency_phone.value = value;
};

/**
 * Formata o telefone no input.
 *
 * @param {Event} e - O evento de input.
 * @returns {void}
 */
const formatPhone = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 10) {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
    }

    phone.value = value;
};

/**
 * Formata o celular no input.
 *
 * @param {Event} e - O evento de input.
 * @returns {void}
 */
const formatMobile = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 11) {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
    }

    mobile.value = value;
};

/**
 * Formata o CPF no input.
 *
 * @param {Event} e - O evento de input.
 * @returns {void}
 */
const formatCPF = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 11) {
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    }

    cpf.value = value;
};

</script>

<template>
    <!-- IDENTIFICAÇÃO -->
    <details class="group mb-4 rounded-md border" open>
        <summary class="flex cursor-pointer items-center justify-between p-4 font-bold select-none">
            <span class="text-foreground">Informações Básicas</span>
            <ArrowRight class="ml-2 text-foreground transition-all duration-100 group-open:rotate-90" />
        </summary>

        <div class="p-4 bg-card">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Nome completo -->
                <div class="space-y-2">
                    <Label class="block text-sm font-semibold text-foreground">Nome completo *</Label>
                    <input
                        v-model="name"
                        type="text"
                        class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                        required
                        :readonly="isReadonly"
                    />
                </div>

                <!-- E-mail -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">E-mail *</Label>
                    <Input
                        v-model="email"
                        type="email"
                        class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                        required
                        :readonly="isReadonly"
                    />
                </div>

                <!-- Data de nascimento -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">Data de nascimento *</Label>
                    <Input
                        v-model="birth_date"
                        type="date"
                        max="3000-12-31"
                        class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                        required
                        :readonly="isReadonly"
                    />
                </div>

                <!-- Sexo/gênero -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">Sexo/gênero *</Label>
                    <Select v-model="gender" required :disabled="isReadonly">
                        <SelectTrigger class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500">
                            <SelectValue placeholder="Selecione"/>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem value="Masculino">Masculino</SelectItem>
                                <SelectItem value="Feminino">Feminino</SelectItem>
                                <SelectItem value="Outro">Outro</SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>

                <!-- Estado civil -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">Estado civil *</Label>
                    <Select v-model="civil_state" required :disabled="isReadonly">
                        <SelectTrigger class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500">
                            <SelectValue placeholder="Selecione"/>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem value="Solteiro(a)">Solteiro(a)</SelectItem>
                                <SelectItem value="Casado(a)">Casado(a)</SelectItem>
                                <SelectItem value="Divorciado(a)">Divorciado(a)</SelectItem>
                                <SelectItem value="Viúvo(a)">Viúvo(a)</SelectItem>
                                <SelectItem value="União Estável">União Estável</SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>

                <!-- Nacionalidade -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">Nacionalidade *</Label>
                    <Input
                        v-model="nationality"
                        type="text"
                        class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                        required
                        :readonly="isReadonly"
                    />
                </div>

                <!-- Naturalidade -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">Naturalidade *</Label>
                    <Input
                        v-model="birthplace"
                        type="text"
                        class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                        required
                        :readonly="isReadonly"
                    />
                </div>
            </div>
        </div>
    </details>

    <!-- DOCUMENTOS -->
    <details class="group mb-4 rounded-md border">
        <summary class="flex cursor-pointer items-center justify-between p-4 font-bold select-none">
            <span class="text-foreground">Documentos</span>
            <ArrowRight class="ml-2 text-foreground transition-all duration-100 group-open:rotate-90" />
        </summary>

        <div class="p-4 bg-card">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- CNPJ -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">CNPJ</Label>
                    <Input
                        v-model="cnpj"
                        type="text"
                        class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                        :readonly="isReadonly"
                    />
                </div>

                <!-- CPF -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">CPF *</Label>
                    <Input
                        v-model="cpf"
                        type="text"
                        class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                        required
                        @input="formatCPF"
                        :readonly="isReadonly"
                    />
                </div>

                <!-- RG -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">RG *</Label>
                    <Input
                        v-model="rg"
                        type="text"
                        class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                        required
                        :readonly="isReadonly"
                    />
                </div>

                <!-- Órgão emissor -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">Órgão emissor *</Label>
                    <Input
                        v-model="issuing_agency"
                        type="text"
                        class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                        required
                        :readonly="isReadonly"
                    />
                </div>

                <!-- Data de emissão -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">Data de emissão *</Label>
                    <Input
                        v-model="issue_date"
                        type="date"
                        class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                        required
                        :readonly="isReadonly"
                    />
                </div>

                <!-- Título de eleitor -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">Título de eleitor *</Label>
                    <Input
                        v-model="voter_registration"
                        type="text"
                        class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                        required
                        :readonly="isReadonly"
                    />
                </div>

                <!-- Certidão de reservista -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">Certidão de reservista</Label>
                    <Input
                        v-model="military_certificate"
                        type="text"
                        class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                        :readonly="isReadonly"
                    />
                    <p class="text-xs text-gray-500">Opcional para homens</p>
                </div>
            </div>
        </div>
    </details>

    <!-- INFORMAÇÕES PESSOAIS -->
    <details class="group mb-4 rounded-md border">
        <summary class="flex cursor-pointer items-center justify-between p-4 font-bold select-none">
            <span class="text-foreground">Informações Pessoais</span>
            <ArrowRight class="ml-2 text-foreground transition-all duration-100 group-open:rotate-90" />
        </summary>

        <div class="p-4 bg-card">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Nome da mãe -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">Nome da mãe</Label>
                    <Input
                        v-model="mother_name"
                        type="text"
                        class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                        :readonly="isReadonly"
                    />
                </div>

                <!-- Nome do pai -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">Nome do pai</Label>
                    <Input
                        v-model="father_name"
                        type="text"
                        class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                        :readonly="isReadonly"
                    />
                </div>

                <!-- Grau de escolaridade -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">Grau de escolaridade *</Label>
                    <Select v-model="escolarity" :disabled="isReadonly">
                        <SelectTrigger class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500">
                            <SelectValue placeholder="Selecione"/>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem value="fundamental">Ensino Fundamental</SelectItem>
                                <SelectItem value="medio">Ensino Médio</SelectItem>
                                <SelectItem value="tecnico">Ensino Técnico</SelectItem>
                                <SelectItem value="superior">Ensino Superior</SelectItem>
                                <SelectItem value="posGraduacao">Pós-graduação</SelectItem>
                                <SelectItem value="mestrado">Mestrado</SelectItem>
                                <SelectItem value="doutorado">Doutorado</SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>

                <!-- Tipo sanguíneo -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">Tipo sanguíneo</Label>
                    <Select v-model="blood_type" :disabled="isReadonly">
                        <SelectTrigger class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500">
                            <SelectValue placeholder="Selecione"/>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem v-for="type in bloodTypes" :key="type" :value="type">{{ type }}</SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>

                <!-- Telefone -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">Telefone *</Label>
                    <Input
                        v-model="phone"
                        type="text"
                        class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                        required
                        @input="formatPhone"
                        :readonly="isReadonly"
                    />
                </div>

                <!-- Celular -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">Celular</Label>
                    <Input
                        v-model="mobile"
                        type="text"
                        class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                        @input="formatMobile"
                        :readonly="isReadonly"
                    />
                </div>

                <!-- Contato de emergência -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">Contato de emergência</Label>
                    <Input
                        v-model="emergency_contact"
                        type="text"
                        class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                        :readonly="isReadonly"
                    />
                </div>

                <!-- Telefone de emergência -->
                <div class="space-y-2">
                    <Label class="block text-sm font-medium text-foreground">Telefone de emergência</Label>
                    <Input
                        v-model="emergency_phone"
                        type="text"
                        class="w-full rounded-md border p-2 focus:border-gray-500 focus:ring-2 focus:ring-gray-500"
                        @input="formatEmergencyPhone"
                        :readonly="isReadonly"
                    />
                </div>
            </div>
        </div>
    </details>
</template>
