<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import type { BreadcrumbItemType } from '@/types';
import {
    CirclePlus,
    Search,
    MoreHorizontal,
    Pencil,
    Trash
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
import { ref, watch } from 'vue';
import { showToast } from '@/composables/useToast';

interface Department {
    id?: number;
    name: string;
    description: string | null;
}

const props = defineProps<{
    departments: {
        data: Department[];
        links: any[];
        meta: any;
    };
    filters?: {
        search?: string;
    };
}>();

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Administrativo', href: '/administracao' },
    { title: 'Departamentos', href: '/administracao/departamentos' },
];

const dialogOpen = ref(false);
const isEditing = ref(false);
const search = ref(props.filters?.search || '');

const formDepartment = ref<Department>({
    name: '',
    description: '',
});

function resetFormDepartment() {
    formDepartment.value = {
        name: '',
        description: '',
    };
    isEditing.value = false;
}

function openCreateDialog() {
    resetFormDepartment();
    dialogOpen.value = true;
}

function openEditDialog(dept: Department) {
    formDepartment.value = { ...dept };
    isEditing.value = true;
    dialogOpen.value = true;
}

function submitDepartment() {
    if (isEditing.value && formDepartment.value.id) {
        router.put(route('admin.update_department', formDepartment.value.id), formDepartment.value, {
            onSuccess: () => {
                dialogOpen.value = false;
                resetFormDepartment();
                showToast('success', 'Departamento atualizado com sucesso!');
            },
            onError: (errors) => {
                showToast('error', 'Erro ao atualizar departamento.', Object.values(errors)[0]);
            }
        });
    } else {
        router.post(route('admin.store_department'), formDepartment.value, {
            onSuccess: () => {
                dialogOpen.value = false;
                resetFormDepartment();
                showToast('success', 'Departamento criado com sucesso!');
            },
            onError: (errors) => {
                showToast('error', 'Erro ao criar departamento.', Object.values(errors)[0]);
            }
        });
    }
}

function deleteDepartment(id: number) {
    if (confirm('Tem certeza que deseja excluir este departamento?')) {
        router.delete(route('admin.destroy_department', id), {
            onSuccess: () => {
               showToast('success', 'Departamento excluído com sucesso!');
            },
            onError: () => {
                showToast('error', 'Erro ao excluir departamento.');
            }
        });
    }
}

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            route('admin.departments'),
            { search: value },
            { preserveState: true, replace: true }
        );
    }, 300);
});

</script>

<template>
    <Head title="Departamentos" />
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
                            Novo Departamento
                        </Button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                        <thead class="bg-gray-50 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">Departamento</th>
                                <th scope="col" class="px-4 py-3">Descrição</th>
                                <th scope="col" class="px-4 py-3 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="departments.data.length > 0" v-for="dept in departments.data" :key="dept.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ dept.name }}</td>
                                <td class="px-4 py-3 truncate max-w-xs">{{ dept.description }}</td>
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
                                            <DropdownMenuItem @click="openEditDialog(dept)">
                                                <Pencil class="mr-2 h-4 w-4" />
                                                Editar
                                            </DropdownMenuItem>
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem @click="deleteDepartment(dept.id!)" class="text-red-500 focus:text-red-500">
                                                <Trash class="mr-2 h-4 w-4" />
                                                Excluir
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                    Nenhum departamento encontrado.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav
                    v-if="departments.meta && departments.meta.links"
                    class="flex flex-col items-start justify-between space-y-3 p-4 md:flex-row md:items-center md:space-y-0"
                >
                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                        Mostrando
                        <span class="font-semibold text-gray-900 dark:text-white">{{ departments.meta.from }}</span>
                        -
                        <span class="font-semibold text-gray-900 dark:text-white">{{ departments.meta.to }}</span>
                        de
                        <span class="font-semibold text-gray-900 dark:text-white">{{ departments.meta.total }}</span>
                    </span>
                     <ul class="inline-flex items-stretch -space-x-px">
                         <li v-for="link in departments.meta.links" :key="link.label">
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
                    <DialogTitle>{{ isEditing ? 'Editar Departamento' : 'Novo Departamento' }}</DialogTitle>
                    <DialogDescription>
                        {{ isEditing ? 'Atualize as informações do departamento abaixo.' : 'Preencha as informações para criar um novo departamento.' }}
                    </DialogDescription>
                </DialogHeader>
                <div class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <Label for="name">Nome do Departamento</Label>
                        <Input id="name" v-model="formDepartment.name" placeholder="Ex: Informática" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="description">Descrição</Label>
                        <Textarea id="description" :model-value="formDepartment.description || ''" @update:model-value="(v) => formDepartment.description = v" placeholder="Descrição do departamento..." />
                    </div>
                </div>
                <DialogFooter>
                    <Button variant="secondary" @click="dialogOpen = false">Cancelar</Button>
                    <Button @click="submitDepartment">{{ isEditing ? 'Salvar Alterações' : 'Criar Departamento' }}</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
