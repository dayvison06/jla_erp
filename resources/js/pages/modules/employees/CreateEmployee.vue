<script setup lang="ts">
import {
    ArrowRight,
    PlusIcon,
    TrashIcon,
    UploadCloudIcon,
    UploadIcon,
    UserIcon,
    BriefcaseBusiness,
    MapPinHouse,
    FilePenLine,
    Paperclip,
    Users,
    Cross,
    Banknote, EditIcon, CornerDownLeft, Eye,
} from 'lucide-vue-next';
import AttachmentsDisplay from '@/components/AttachmentsDisplay.vue';
import { ref, reactive, onMounted, watch, isReadonly } from 'vue';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import EmployeeCachedDialog from '@/components/EmployeeCachedDialog.vue';
import { Employee, Attachment } from '@/types/Employees';
import { showToast } from '@/composables/useToast';
import {Button} from "@/components/ui/button";
import PersonalInformations from '@/components/forms/employees/PersonalInformations.vue';
import DocumentsInformations from '@/components/forms/employees/DocumentsInformations.vue';
import AddressInformations from '@/components/forms/employees/AddressInformations.vue';
import BankInformations from '@/components/forms/employees/BankInformations.vue';
import ContractInformations from '@/components/forms/employees/ContractInformations.vue';
import HealthInformations from '@/components/forms/employees/HealthInformations.vue';
import DependentInformations from '@/components/forms/employees/DependentInformations.vue';
import AttachmentInformations from '@/components/forms/employees/AttachmentInformations.vue';
import { formData } from '@/composables/useStoreEmployees';
import { debouncedWatch } from '@vueuse/core';

const cacheDialog = ref(false);
const cachedEmployeeData = ref<Employee | null>(null);
const breadcrumbs = [
    { title: 'Funcionários', href: '/funcionarios' },
    { title: 'Criar', href: '/funcionarios/create' },
];

/**
 * Salva o formulário atual no localStorage.
 * @returns {void}
 */
function setLocalCacheForm() {
    if (!formData.name) return;
    localStorage.setItem('cachedEmployee', JSON.stringify({ ...formData, attachments: [] }));
}

/**
 * Exibe o diálogo de cache se houver um formulário salvo no localStorage.
 * @returns {void}
 */
function loadLocalCacheFormDialog() {
    const cachedEmployee = localStorage.getItem('cachedEmployee');
    cachedEmployeeData.value = { ...JSON.parse(cachedEmployee || '{}') };
    if (!!cachedEmployee) {
        cacheDialog.value = true;
    }
}

/**
 * Carrega o formulário do localStorage e fecha o diálogo de cache.
 * @returns {void}
 */
function handleContinueForm() {
    const cachedEmployee = localStorage.getItem('cachedEmployee');
    Object.assign(formData, JSON.parse(cachedEmployee));
    cacheDialog.value = false;
}

function handleDestroyCacheForm() {
    localStorage.removeItem('cachedEmployee');
    cacheDialog.value = false;
}

/**
 * Cria um novo funcionário.
 * @returns {void}
 */
const progressbar = ref(0);
const createEmployee = () => {
    formData.salary = formData.salary.replace(/\D/g, '');

    router.post('/funcionarios', formData, {
        forceFormData: true,
        onProgress: (event) => {
            if (event?.lengthComputable) {
                progressbar.value = Math.round((event.loaded / event.total) * 100);
            }
        },
        onSuccess: () => {
            progressbar.value = 0;
            localStorage.removeItem('cachedEmployee');
        },
        onError: (error) => {
            const msgError = [];
            Object.keys(error).forEach((campo) => {
                msgError.push(`${error[campo]}`);
            });
            progressbar.value = 0;
            showToast('error', 'Falha', msgError[0]);
        },
    });
};

/**
 * Confirma a exclusão de um funcionário.
 *
 * @param {Employee} employee - O funcionário a ser excluído.
 * @returns {void}
 */
const confirmDelete = (employee: Employee) => {
    employeeToDelete.value = employee;
    showDeleteModal.value = true;
};

/**
 * Fecha o formulário de funcionário e reseta o estado.
 * @returns {void}
 */
function closeEmployeeForm() {
    setLocalCacheForm();
    router.get('/funcionarios');
}

onMounted(() => {
    loadLocalCacheFormDialog();
    formData.status = 'active';
    formData.admission_date = new Date().toLocaleDateString('pt-BR', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
    }).split('/').reverse().join('-');
});

debouncedWatch(() => formData, () => {
    setLocalCacheForm();
}, { deep: true, debounce: 5000 });
</script>

<template>
    <Head title="Criar" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="container mx-auto px-4 py-8">
            <!-- Diálogo para carregar dados do cache -->
            <EmployeeCachedDialog v-if="cacheDialog" :cachedEmployeeData="cachedEmployeeData" @continue="handleContinueForm()" @destroy="handleDestroyCacheForm()" />
            <header class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <h1 class="text-2xl font-bold flex items-center">
                        <UserIcon class="w-6 h-6 mr-2"/>
                        <p v-if="formData.name">
                            Cadastrando - <span class="text-gray-500">{{ formData.name }}</span>
                        </p>
                        <p v-else>
                            Cadastro de Funcionário
                        </p>
                    </h1>
                </div>
            </header>
            <Tabs default-value="personal">
                <TabsList class="mb-6 flex h-10 gap-2">
                    <TabsTrigger value="personal">
                        <UserIcon />
                        Dados Pessoais
                    </TabsTrigger>
                    <TabsTrigger value="documents">
                        <BriefcaseBusiness />
                        Documentos Trabalhistas
                    </TabsTrigger>
                    <TabsTrigger value="address">
                        <MapPinHouse />
                        Endereço
                    </TabsTrigger>
                    <TabsTrigger value="bank">
                        <Banknote />
                        Dados Bancários
                    </TabsTrigger>
                    <TabsTrigger value="contract">
                        <FilePenLine />
                        Informações Contratuais
                    </TabsTrigger>
                    <TabsTrigger value="health">
                        <Cross />
                        Saúde e Segurança
                    </TabsTrigger>
                    <TabsTrigger value="dependents">
                        <Users />
                        Dependentes
                    </TabsTrigger>
                    <TabsTrigger value="attachments">
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
                        />
                </TabsContent>
                <TabsContent value="health">
                    <HealthInformations
                        v-model:last_exam_date="formData.last_exam_date"
                        v-model:next_exam_date="formData.next_exam_date"
                        v-model:aso_result="formData.aso_result"
                        v-model:allergies="formData.allergies"
                        v-model:accident_history="formData.accident_history"
                    />
                </TabsContent>
                <TabsContent value="dependents">
                    <DependentInformations
                        v-model:dependents="formData.dependents"
                    />
                </TabsContent>
                <TabsContent value="attachments">
                    <AttachmentInformations
                        v-model:attachments="formData.attachments"
                    />
                </TabsContent>
            </Tabs>
            <!-- Botões de ação do formulário -->
            <div class="mt-8 flex justify-end space-x-4 pt-4">
                <!-- Botão para cancelar a operação -->
                <Button type="button" @click="closeEmployeeForm()" class="btn-primary-v2">Cancelar</Button>
                <!-- Botão para criar novo funcionário -->
                <Button type="button" @click="createEmployee" class="rounded-md bg-gray-900 px-4 py-2 text-white hover:bg-gray-700">Criar</Button>
            </div>
        </main>
    </AppLayout>
</template>

<style scoped></style>
