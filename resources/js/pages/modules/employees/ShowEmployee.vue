<script setup lang="ts">
import {
    UserIcon,
    BriefcaseBusiness,
    MapPinHouse,
    FilePenLine,
    Paperclip,
    Users,
    Cross,
    Banknote,
    CornerDownLeft,
    Eye,
    EditIcon,
    UserSearch,
    UserRoundPen,
    UserCog,
    EllipsisIcon,
} from 'lucide-vue-next';
import { ref, reactive, onMounted } from 'vue';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Head, usePage, router } from '@inertiajs/vue3';
import type { Employee } from '@/types/Employees';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import PersonalInformations from '@/components/forms/employees/PersonalInformations.vue';
import BankInformations from '@/components/forms/employees/BankInformations.vue';
import DocumentsInformations from '@/components/forms/employees/DocumentsInformations.vue';
import AddressInformations from '@/components/forms/employees/AddressInformations.vue';
import ContractInformations from '@/components/forms/employees/ContractInformations.vue';
import HealthInformations from '@/components/forms/employees/HealthInformations.vue';
import DependentInformations from '@/components/forms/employees/DependentInformations.vue';
import AttachmentInformations from '@/components/forms/employees/AttachmentInformations.vue';
import { formData } from '@/composables/useStoreEmployees';
import axios from 'axios';

const page = usePage();
const employee: Employee = page.props.employee;
const progressbar = ref(0);
const isDragging = ref(true);
const isReadonly = ref(true);
const breadcrumbs = [
    { title: 'Funcionários', href: '/funcionarios' },
    { title: 'Visualizar', href: `/funcionarios/${employee?.cpf}` },
];
console.log('INERTIA DATA EMPLOYEE:', employee);
/**
 * Salva as alterações de um funcionário existente pegando somente os dados modificados.
 * @returns {void}
 */
const saveEmployee = () => {
    console.log('Form Data before save:', formData);
    const onlyModifiedData: Record<string, any> = {};
    for (const key in formData) {
        // Ignora campo vazio
        if (formData[key] === '') continue;
        if (formData[key] !== employee[key]) {
            onlyModifiedData[key] = formData[key];
        }
    }
    console.log('Modified Data:', onlyModifiedData);

    router.patch(`/funcionarios/${formData.id}`, formData, {
        preserveState: true,
        preserveScroll: true,
        onProgress: (event) => {
            if (event?.lengthComputable) {
                progressbar.value = Math.round((event.loaded / event.total) * 100);
            }
        },
        onSuccess: (page) => {
            progressbar.value = 0;
            employee.value = page.props.employee;
            isReadonly.value = true;
        },
        onError: () => {
            progressbar.value = 0;

            alert('Error updating employee. Please check the form and try again.');
        },
    });
};

function loadAttachments() {
 router.get(`/funcionarios/${formData.id}`, {}, {
     preserveState: true,
     preserveScroll: true,
     only: ['employee'],
     replace: false,
     onSuccess: (page) => {
         formData.attachments = page.props.employee.attachments;
     },
     onError: () => {
         alert('Erro ao carregar anexos.');
     },
 });
}

/**
 * Fecha o formulário de funcionário e reseta o estado.
 * @returns {void}
 */
function returnPageEmployees() {
    router.get('/funcionarios');
}

onMounted(() => {
    if (employee) {
        Object.assign(formData, employee);
    }
});
</script>

<template>
    <Head title="Editar" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <section
            v-if="isReadonly"
            class="fixed left-1/2 top-4 z-50 w-[calc(100%-2rem)] max-w-3xl -translate-x-1/2 rounded-md bg-card px-4 py-3 text-foreground shadow-sm"
            role="status"
            aria-live="polite"
        >
            <div class="flex items-center justify-between gap-3">
                <div class="flex flex-col text-sm font-medium">
                    <div class="flex items-center gap-2">
                        <Eye class="h-4 w-4" />
                        <span class="font-semibold">
                        Modo leitura ativado.
                    </span>
                    </div>
                    <small class="text-xs">
                        Para editar as informações do funcionário, clique no botão ao lado.
                    </small>
                </div>
                <Button type="button" class="btn-primary" @click="isReadonly = false">
                    <EditIcon class="mr-2 h-4 w-4" />
                    Habilitar edição
                </Button>
            </div>
        </section>
        <main>
            <!-- Botão para cancelar a operação -->
            <Button type="button" @click="returnPageEmployees()" class="btn-primary-v2 mb-6">
                <CornerDownLeft class="mr-2 h-4 w-4" />
                Voltar
            </Button>
            <header class="mb-4 flex items-center">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <h1 class="flex items-center text-2xl font-bold">
                            <UserSearch v-if="isReadonly" class="mr-2 h-6 w-6" />
                            <UserRoundPen v-else class="mr-2 h-6 w-6" />
                            {{ formData.name }}
                        </h1>
                        <span
                            class="bg-card rounded px-2 py-1 text-xs font-semibold shadow-md"
                            :class="{
                                'text-white bg-green-700': formData.status === 'active',
                                'bg-red-100': formData.status === 'inactive',
                            }"
                            role="status"
                        >
                            {{ formData.status === 'active' ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                    <Button v-if="!isReadonly" class="btn-primary-v2">
                        <EllipsisIcon class="h-4 w-4" />
                    </Button>
                </div>
            </header>
            <Tabs default-value="personal">
                <TabsList class="mb-6 flex h-10 gap-2">
                    <TabsTrigger class="hover:text-secondary data-[state=active]:text-secondary" value="personal">
                        <UserIcon />
                        Dados Pessoais
                    </TabsTrigger>
                    <TabsTrigger class="hover:text-secondary data-[state=active]:text-secondary" value="documents">
                        <BriefcaseBusiness />
                        Documentos Trabalhistas
                    </TabsTrigger>
                    <TabsTrigger class="hover:text-secondary data-[state=active]:text-secondary" value="address">
                        <MapPinHouse />
                        Endereço
                    </TabsTrigger>
                    <TabsTrigger class="hover:text-secondary data-[state=active]:text-secondary" value="bank">
                        <Banknote />
                        Dados Bancários
                    </TabsTrigger>
                    <TabsTrigger class="hover:text-secondary data-[state=active]:text-secondary" value="contract">
                        <FilePenLine />
                        Informações Contratuais
                    </TabsTrigger>
                    <TabsTrigger class="hover:text-secondary data-[state=active]:text-secondary" value="health">
                        <Cross />
                        Saúde e Segurança
                    </TabsTrigger>
                    <TabsTrigger class="hover:text-secondary data-[state=active]:text-secondary" value="dependents">
                        <Users />
                        Dependentes
                    </TabsTrigger>
                    <TabsTrigger class="hover:text-secondary data-[state=active]:text-secondary" value="attachments">
                        <Paperclip />
                        Anexos
                    </TabsTrigger>
                </TabsList>
                <TabsContent value="personal">
                    <PersonalInformations
                        v-model:name="formData.name"
                        v-model:email="formData.email"
                        v-model:gender="formData.gender"
                        v-model:birth_date="formData.birth_date"
                        v-model:civil_state="formData.civil_state"
                        v-model:nationality="formData.nationality"
                        v-model:birthplace="formData.birthplace"
                        v-model:cnpj="formData.cnpj"
                        v-model:cpf="formData.cpf"
                        v-model:rg="formData.rg"
                        v-model:issuing_agency="formData.issuing_agency"
                        v-model:issue_date="formData.issue_date"
                        v-model:voter_registration="formData.voter_registration"
                        v-model:military_certificate="formData.military_certificate"
                        v-model:mother_name="formData.mother_name"
                        v-model:father_name="formData.father_name"
                        v-model:education_level="formData.education_level"
                        v-model:blood_type="formData.blood_type"
                        v-model:phone="formData.phone"
                        v-model:mobile="formData.mobile"
                        v-model:emergency_contact="formData.emergency_contact"
                        v-model:emergency_phone="formData.emergency_phone"
                        v-model:read_only="isReadonly"
                    />
                </TabsContent>
                <TabsContent value="documents">
                    <DocumentsInformations
                        v-model:ctps_number="formData.ctps_number"
                        v-model:ctps_series="formData.ctps_series"
                        v-model:ctps_state="formData.ctps_state"
                        v-model:pis_pasep="formData.pis_pasep"
                        v-model:nit="formData.nit"
                        v-model:cnh="formData.cnh"
                        v-model:cnh_category="formData.cnh_category"
                        v-model:cnh_expiry="formData.cnh_expiry"
                        v-model:professional_registration="formData.professional_registration"
                        v-model:read_only="isReadonly"
                    />
                </TabsContent>
                <TabsContent value="address">
                    <AddressInformations
                        v-model:postal_code="formData.postal_code"
                        v-model:street="formData.street"
                        v-model:number="formData.number"
                        v-model:complement="formData.complement"
                        v-model:district="formData.district"
                        v-model:city="formData.city"
                        v-model:state="formData.state"
                        v-model:read_only="isReadonly"
                    />
                </TabsContent>
                <TabsContent value="bank">
                    <BankInformations
                        v-model:bank="formData.bank"
                        v-model:agency="formData.agency"
                        v-model:account="formData.account"
                        v-model:account_type="formData.account_type"
                        v-model:pix_key_type="formData.pix_key_type"
                        v-model:pix_key="formData.pix_key"
                        v-model:read_only="isReadonly"
                    />
                </TabsContent>
                <TabsContent value="contract">
                    <ContractInformations
                        v-model:role="formData.role"
                        v-model:contract_type="formData.contract_type"
                        v-model:admission_date="formData.admission_date"
                        v-model:termination_date="formData.termination_date"
                        v-model:salary="formData.salary"
                        v-model:benefits="formData.benefits"
                        v-model:read_only="isReadonly"
                    />
                </TabsContent>
                <TabsContent value="health">
                    <HealthInformations
                        v-model:last_exam_date="formData.last_exam_date"
                        v-model:next_exam_date="formData.next_exam_date"
                        v-model:aso_result="formData.aso_result"
                        v-model:allergies="formData.allergies"
                        v-model:accident_history="formData.accident_history"
                        v-model:read_only="isReadonly"
                    />
                </TabsContent>
                <TabsContent value="dependents">
                    <DependentInformations v-model:dependents="formData.dependents" v-model:read_only="isReadonly" />
                </TabsContent>
                <TabsContent value="attachments">
                    <AttachmentInformations
                        v-model:attachments="formData.attachments"
                        :read-only="isReadonly"
                        :employee-id="formData.id"
                        @refreshAttachments="loadAttachments"
                    />
                </TabsContent>
            </Tabs>
            <!-- Botões de ação do formulário -->
            <div class="mt-8 flex justify-end space-x-4 pt-4">
                <Button v-if="!isReadonly" type="button" @click="isReadonly = true" class="btn-primary-v2"> Cancelar </Button>
                <!-- Botão para salvar funcionário existente -->
                <Button v-if="!isReadonly" type="button" @click="saveEmployee" class="btn-primary"> Salvar </Button>
            </div>
        </main>
    </AppLayout>
</template>

<style scoped></style>
