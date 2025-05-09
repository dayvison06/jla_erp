<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppAdminLayout from '@/layouts/AppAdminLayout.vue'
import { ref, onMounted } from 'vue'
import { Plus, Edit, Trash2, X } from 'lucide-vue-next'

// Tipos
interface Permission {
    id: string
    name: string
    description: string
}

// Estado
const permissions = ref<Permission[]>([])
const showModal = ref(false)
const isEditing = ref(false)

// Permissão sendo editada/criada
const currentPermission = ref<Permission>({
    id: '',
    name: '',
    description: ''
})

// Métodos
function openModal(permission?: Permission) {
    if (permission) {
        // Editar permissão existente
        currentPermission.value = { ...permission }
        isEditing.value = true
    } else {
        // Nova permissão
        currentPermission.value = {
            id: '',
            name: '',
            description: ''
        }
        isEditing.value = false
    }
    showModal.value = true
}

function closeModal() {
    showModal.value = false
}

function savePermission() {
    if (isEditing.value) {
        // Atualizar permissão existente
        const index = permissions.value.findIndex(p => p.id === currentPermission.value.id)
        if (index !== -1) {
            permissions.value[index] = { ...currentPermission.value }
        }
    } else {
        // Adicionar nova permissão
        permissions.value.push({ ...currentPermission.value })
    }
    closeModal()
}

function deletePermission(id: string) {
    if (confirm('Tem certeza que deseja excluir esta permissão?')) {
        const index = permissions.value.findIndex(p => p.id === id)
        if (index !== -1) {
            permissions.value.splice(index, 1)
        }
    }
}

// Inicializar com dados de exemplo
onMounted(() => {
    permissions.value = [
        { id: '1', name: 'users_view', description: 'Permite visualizar a lista de usuários' },
        { id: '2', name: 'users_create', description: 'Permite criar novos usuários' },
        { id: '3', name: 'users_edit', description: 'Permite editar usuários existentes' },
        { id: '4', name: 'users_delete', description: 'Permite excluir usuários' },
        { id: '5', name: 'roles_view', description: 'Permite visualizar a lista de cargos' },
        { id: '6', name: 'roles_create', description: 'Permite criar novos cargos' },
        { id: '7', name: 'teams_view', description: 'Permite visualizar a lista de times' },
        { id: '8', name: 'reports_view', description: 'Permite visualizar relatórios' },
        { id: '9', name: 'reports_export', description: 'Permite exportar relatórios' },
    ]
})
</script>

<template>
    <Head title="Permissões" />
    <AppAdminLayout :breadcrumbs="breadcrumbs">
        <div class="bg-gray-50 min-h-screen p-4 md:p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Cabeçalho -->
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="p-4 md:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <h1 class="text-2xl font-semibold text-gray-900">Permissões</h1>
                        <button
                            @click="openModal()"
                            class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors"
                        >
                            <Plus size="18" />
                            <span>Nova Permissão</span>
                        </button>
                    </div>
                </div>

                <!-- Tabela de Permissões -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nome
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Descrição
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="permission in permissions" :key="permission.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ permission.id }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ permission.name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">{{ permission.description }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            @click="openModal(permission)"
                                            class="text-amber-600 hover:text-amber-500"
                                            title="Editar"
                                        >
                                            <Edit size="18" />
                                        </button>
                                        <button
                                            @click="deletePermission(permission.id)"
                                            class="text-red-600 hover:text-red-500"
                                            title="Excluir"
                                        >
                                            <Trash2 size="18" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="permissions.length === 0">
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Nenhuma permissão cadastrada
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal de Cadastro/Edição -->
            <Teleport to="body">
                <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                        <div class="p-6 border-b">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    {{ isEditing ? 'Editar Permissão' : 'Nova Permissão' }}
                                </h2>
                                <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
                                    <X size="24" />
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            <form @submit.prevent="savePermission">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Permissão *</label>
                                        <input
                                            v-model="currentPermission.name"
                                            type="text"
                                            required
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-primary"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                                        <textarea
                                            v-model="currentPermission.description"
                                            rows="3"
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-primary"
                                        ></textarea>
                                    </div>
                                </div>

                                <div class="flex justify-end gap-2 mt-6">
                                    <button
                                        type="button"
                                        @click="closeModal"
                                        class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50"
                                    >
                                        Cancelar
                                    </button>
                                    <button
                                        type="submit"
                                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90"
                                    >
                                        Salvar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </Teleport>
        </div>
    </AppAdminLayout>
</template>
