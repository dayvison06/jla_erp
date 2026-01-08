<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import type { BreadcrumbItemType } from '@/types';
import {
    CirclePlus,
    Search,
    MoreHorizontal,
    Pencil,
    Trash,
    Check,
    X
} from 'lucide-vue-next';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogFooter
} from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { ref, watch } from 'vue';
import { showToast } from '@/composables/useToast';

interface Benefit {
    id?: number;
    name: string;
    description: string | null;
    active: boolean;
}

const props = defineProps<{
    benefits: {
        data: Benefit[];
        links: any[];
        meta: any;
    };
    filters?: {
        search?: string;
    };
}>();

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Administrativo', href: '/administracao' },
    { title: 'Benefícios', href: '/administracao/beneficios' },
];

const dialogOpen = ref(false);
const isEditing = ref(false);
const search = ref(props.filters?.search || '');

const formBenefit = ref<Benefit>({
    name: '',
    description: '',
    active: true,
});

function resetFormBenefit() {
    formBenefit.value = {
        name: '',
        description: '',
        active: true,
    };
    isEditing.value = false;
}

function openCreateDialog() {
    resetFormBenefit();
    dialogOpen.value = true;
}

function openEditDialog(benefit: Benefit) {
    formBenefit.value = { ...benefit, active: !!benefit.active };
    isEditing.value = true;
    dialogOpen.value = true;
}

function submitBenefit() {
    if (isEditing.value && formBenefit.value.id) {
        router.put(route('admin.update_benefit', formBenefit.value.id), formBenefit.value as any, {
            onSuccess: () => {
                dialogOpen.value = false;
                resetFormBenefit();
                showToast('success', 'Benefício atualizado com sucesso!');
            },
            onError: (errors) => {
                showToast('error', 'Erro ao atualizar benefício.', Object.values(errors)[0]);
            }
        });
    } else {
        router.post(route('admin.store_benefit'), formBenefit.value as any, {
            onSuccess: () => {
                dialogOpen.value = false;
                resetFormBenefit();
                showToast('success', 'Benefício criado com sucesso!');
            },
            onError: (errors) => {
                showToast('error', 'Erro ao criar benefício.', Object.values(errors)[0]);
            }
        });
    }
}

function deleteBenefit(id: number) {
    if (confirm('Tem certeza que deseja excluir este benefício?')) {
        router.delete(route('admin.destroy_benefit', id), {
            onSuccess: () => {
               showToast('success', 'Benefício excluído com sucesso!');
            },
            onError: () => {
                showToast('error', 'Erro ao excluir benefício.');
            }
        });
    }
}

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            route('admin.benefits'),
            { search: value },
            { preserveState: true, replace: true }
        );
    }, 300);
});

</script>

<template>
    <Head title="Benefícios" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <main>
            <div class="bg-card relative overflow-hidden shadow-md sm:rounded-lg">
                <div class="flex flex-col items-center justify-between space-y-3 p-4 md:flex-row md:space-y-0 md:space-x-4">
                    <div class="w-full md:w-1/2">
                        <div class="flex items-center">
                            <label for="simple-search" class="sr-only">Buscar</label>
                            <div class="relative w-full">
                                <Search class="w-4 h-4 absolute left-2 top-1/2 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    v-model="search"
                                    type="text"
                                    class="pl-8"
                                    placeholder="Buscar por nome..."
                                />
                            </div>
                        </div>
                    </div>
                    <div
                        class="flex w-full flex-shrink-0 flex-col items-stretch justify-end space-y-2 md:w-auto md:flex-row md:items-center md:space-y-0 md:space-x-3"
                    >
                        <Button @click="openCreateDialog">
                            <CirclePlus class="h-4 w-4 mr-2" />
                            Novo Benefício
                        </Button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                        <thead class="bg-gray-50 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">Benefício</th>
                                <th scope="col" class="px-4 py-3">Descrição</th>
                                <th scope="col" class="px-4 py-3">Ativo</th>
                                <th scope="col" class="px-4 py-3 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="benefits.data.length > 0" v-for="benefit in benefits.data" :key="benefit.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ benefit.name }}</td>
                                <td class="px-4 py-3 truncate max-w-xs">{{ benefit.description }}</td>
                                <td class="px-4 py-3">
                                    <span v-if="benefit.active" class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Ativo</span>
                                    <span v-else class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">Inativo</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button variant="ghost" class="h-8 w-8 p-0">
                                                <span class="sr-only">Abrir menu</span>
                                                <MoreHorizontal class="h-4 w-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuLabel>Ações</DropdownMenuLabel>
                                            <DropdownMenuItem @click="openEditDialog(benefit)">
                                                <Pencil class="mr-2 h-4 w-4" />
                                                Editar
                                            </DropdownMenuItem>
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem @click="deleteBenefit(benefit.id!)" class="text-red-500 focus:text-red-500">
                                                <Trash class="mr-2 h-4 w-4" />
                                                Excluir
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    Nenhum benefício encontrado.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav
                    v-if="benefits.meta && benefits.meta.links"
                    class="flex flex-col items-start justify-between space-y-3 p-4 md:flex-row md:items-center md:space-y-0"
                >
                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                        Mostrando
                        <span class="font-semibold text-gray-900 dark:text-white">{{ benefits.meta.from }}</span>
                        -
                        <span class="font-semibold text-gray-900 dark:text-white">{{ benefits.meta.to }}</span>
                        de
                        <span class="font-semibold text-gray-900 dark:text-white">{{ benefits.meta.total }}</span>
                    </span>
                     <ul class="inline-flex items-stretch -space-x-px">
                         <li v-for="link in benefits.meta.links" :key="link.label">
                            <component
                                :is="link.url ? 'a' : 'span'"
                                :href="link.url"
                                @click.prevent="link.url && router.get(link.url, { search }, { preserveState: true })"
                                class="flex items-center justify-center px-3 py-2 text-sm leading-tight border"
                                :class="{
                                    'text-primary-600 bg-primary-50 border-primary-300 hover:bg-primary-100 hover:text-primary-700': link.active,
                                    'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white': !link.active,
                                    'cursor-not-allowed opacity-50': !link.url
                                }"
                                v-html="link.label"
                            />
                        </li>
                    </ul>
                </nav>
            </div>
        </main>

        <!-- Create/Edit Dialog -->
        <Dialog v-model:open="dialogOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>{{ isEditing ? 'Editar Benefício' : 'Novo Benefício' }}</DialogTitle>
                    <DialogDescription>
                        {{ isEditing ? 'Atualize as informações do benefício abaixo.' : 'Preencha as informações para criar um novo benefício.' }}
                    </DialogDescription>
                </DialogHeader>
                <div class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <Label for="name">Nome do Benefício</Label>
                        <Input id="name" v-model="formBenefit.name" placeholder="Ex: Vale Transporte" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="description">Descrição</Label>
                        <Textarea id="description" :model-value="formBenefit.description || ''" @update:model-value="(v) => formBenefit.description = v" placeholder="Descrição do benefício..." />
                    </div>
                    <div class="flex items-center space-x-2">
                         <Checkbox id="active" :checked="formBenefit.active" @update:checked="(v: boolean) => formBenefit.active = v" />
                        <Label for="active">Ativo</Label>
                    </div>
                </div>
                <DialogFooter>
                    <Button variant="secondary" @click="dialogOpen = false">Cancelar</Button>
                    <Button @click="submitBenefit">{{ isEditing ? 'Salvar Alterações' : 'Criar Benefício' }}</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
