<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3';
import {
    Search,
    Plus,
    Edit,
    Shield,
    Trash2,
    X,
    Eye
} from 'lucide-vue-next'
import AppAdminLayout from '@/layouts/AppAdminLayout.vue';

// Types
interface User {
    id: string
    name: string
    email: string
    status: 'active' | 'inactive'
    roleId: string
    teamId: string
    avatar?: string
    phone?: string
    permissions: string[]
}

interface Role {
    id: string
    name: string
    description: string
    permissions: string[]
}

interface Team {
    id: string
    name: string
    description: string
}

interface Permission {
    id: string
    name: string
}

interface PermissionGroup {
    name: string
    permissions: Permission[]
}

// State
const users = ref<User[]>([])
const roles = ref<Role[]>([])
const teams = ref<Team[]>([])
const searchQuery = ref('')

// Modals
const showRoleModal = ref(false)
const showRoleDetailsModal = ref(false)
const showRolePermissionsModal = ref(false)

// Tabs
const activeRoleDetailsTab = ref('users')

// Editing state
const editingRole = ref<Role>({
    id: '',
    name: '',
    description: '',
    permissions: []
})
const selectedRole = ref<Role | null>(null)
const selectedRolePermissions = ref<string[]>([])

// Abas de detalhes do cargo
const roleDetailsTabs = [
    { id: 'users', name: 'Usuários' },
    { id: 'permissions', name: 'Permissões' }
]

// Permission groups
const permissionGroups = ref<Record<string, PermissionGroup>>({
    users: {
        name: 'Usuários',
        permissions: [
            { id: 'users.view', name: 'Visualizar usuários' },
            { id: 'users.create', name: 'Criar usuários' },
            { id: 'users.edit', name: 'Editar usuários' },
            { id: 'users.delete', name: 'Excluir usuários' }
        ]
    },
    roles: {
        name: 'Cargos',
        permissions: [
            { id: 'roles.view', name: 'Visualizar cargos' },
            { id: 'roles.create', name: 'Criar cargos' },
            { id: 'roles.edit', name: 'Editar cargos' },
            { id: 'roles.delete', name: 'Excluir cargos' }
        ]
    },
    teams: {
        name: 'Times',
        permissions: [
            { id: 'teams.view', name: 'Visualizar times' },
            { id: 'teams.create', name: 'Criar times' },
            { id: 'teams.edit', name: 'Editar times' },
            { id: 'teams.delete', name: 'Excluir times' }
        ]
    },
    reports: {
        name: 'Relatórios',
        permissions: [
            { id: 'reports.view', name: 'Visualizar relatórios' },
            { id: 'reports.export', name: 'Exportar relatórios' }
        ]
    }
})

// Computed properties
const filteredRoles = computed(() => {
    if (!searchQuery.value) return roles.value

    const query = searchQuery.value.toLowerCase()
    return roles.value.filter(role =>
        role.name.toLowerCase().includes(query) ||
        role.description.toLowerCase().includes(query)
    )
})

// Methods
function getRoleUsers(roleId?: string): User[] {
    if (!roleId) return []
    return users.value.filter(user => user.roleId === roleId)
}

function getTeamName(teamId: string): string {
    const team = teams.value.find(t => t.id === teamId)
    return team ? team.name : 'Sem time'
}

function getPermissionName(permissionId: string): string {
    for (const groupKey in permissionGroups.value) {
        const permission = permissionGroups.value[groupKey].permissions.find(p => p.id === permissionId)
        if (permission) {
            return permission.name
        }
    }
    return permissionId
}

function isRoleInUse(roleId: string): boolean {
    return users.value.some(user => user.roleId === roleId)
}

function viewRoleDetails(role: Role) {
    selectedRole.value = { ...role }
    showRoleDetailsModal.value = true
}

function openRoleModal(role?: Role) {
    if (role) {
        editingRole.value = {
            ...role,
            permissions: [...role.permissions]
        }
    } else {
        editingRole.value = {
            id: '',
            name: '',
            description: '',
            permissions: []
        }
    }
    showRoleModal.value = true
}

function openRolePermissionsModal(role?: Role) {
    if (role) {
        selectedRole.value = role
        selectedRolePermissions.value = [...role.permissions]
    }
    showRolePermissionsModal.value = true
}

function saveRole() {
    if (editingRole.value.id) {
        // Update existing role
        const index = roles.value.findIndex(r => r.id === editingRole.value.id)
        if (index !== -1) {
            roles.value[index] = { ...editingRole.value }
        }
    } else {
        // Add new role
        const newRole = {
            ...editingRole.value,
            id: generateId()
        }
        roles.value.push(newRole)
    }
    showRoleModal.value = false
}

function saveRolePermissions() {
    if (selectedRole.value) {
        const index = roles.value.findIndex(r => r.id === selectedRole.value?.id)
        if (index !== -1) {
            roles.value[index].permissions = [...selectedRolePermissions.value]
        }
    }
    showRolePermissionsModal.value = false
}

function deleteRole(roleId: string) {
    if (!isRoleInUse(roleId)) {
        roles.value = roles.value.filter(role => role.id !== roleId)
    }
}

function generateId(): string {
    return Math.random().toString(36).substring(2, 10)
}

// Initialize with sample data
onMounted(() => {
    // Sample roles
    roles.value = [
        {
            id: 'admin',
            name: 'Administrador',
            description: 'Acesso completo ao sistema',
            permissions: ['users.view', 'users.create', 'users.edit', 'users.delete', 'roles.view', 'roles.create', 'roles.edit', 'roles.delete', 'teams.view', 'teams.create', 'teams.edit', 'teams.delete', 'reports.view', 'reports.export']
        },
        {
            id: 'manager',
            name: 'Gerente',
            description: 'Gerencia times e projetos',
            permissions: ['users.view', 'users.create', 'users.edit', 'roles.view', 'teams.view', 'teams.create', 'teams.edit', 'reports.view', 'reports.export']
        },
        {
            id: 'developer',
            name: 'Desenvolvedor',
            description: 'Acesso a recursos de desenvolvimento',
            permissions: ['users.view', 'teams.view']
        },
        {
            id: 'analyst',
            name: 'Analista',
            description: 'Analisa dados e gera relatórios',
            permissions: ['users.view', 'reports.view', 'reports.export']
        },
        {
            id: 'designer',
            name: 'Designer',
            description: 'Responsável pelo design e UX',
            permissions: ['users.view']
        }
    ]

    // Sample teams
    teams.value = [
        { id: 'frontend', name: 'Frontend', description: 'Equipe de desenvolvimento frontend' },
        { id: 'backend', name: 'Backend', description: 'Equipe de desenvolvimento backend' },
        { id: 'design', name: 'Design', description: 'Equipe de design e UX' },
        { id: 'qa', name: 'QA', description: 'Equipe de testes e qualidade' }
    ]

    // Sample users
    users.value = [
        {
            id: 'user1',
            name: 'João Silva',
            email: 'joao.silva@exemplo.com',
            status: 'active',
            roleId: 'admin',
            teamId: 'frontend',
            avatar: '/placeholder.svg?height=40&width=40',
            phone: '(11) 98765-4321',
            permissions: ['users.view', 'users.create', 'users.edit', 'users.delete', 'roles.view', 'teams.view']
        },
        {
            id: 'user2',
            name: 'Maria Oliveira',
            email: 'maria.oliveira@exemplo.com',
            status: 'active',
            roleId: 'manager',
            teamId: 'backend',
            avatar: '/placeholder.svg?height=40&width=40',
            phone: '(11) 91234-5678',
            permissions: ['users.view', 'roles.view', 'teams.view']
        },
        {
            id: 'user3',
            name: 'Pedro Santos',
            email: 'pedro.santos@exemplo.com',
            status: 'inactive',
            roleId: 'developer',
            teamId: 'frontend',
            avatar: '/placeholder.svg?height=40&width=40',
            phone: '(11) 99876-5432',
            permissions: ['users.view']
        },
        {
            id: 'user4',
            name: 'Ana Costa',
            email: 'ana.costa@exemplo.com',
            status: 'active',
            roleId: 'analyst',
            teamId: 'qa',
            avatar: '/placeholder.svg?height=40&width=40',
            phone: '(11) 95678-1234',
            permissions: ['users.view', 'reports.view', 'reports.export']
        },
        {
            id: 'user5',
            name: 'Lucas Ferreira',
            email: 'lucas.ferreira@exemplo.com',
            status: 'active',
            roleId: 'developer',
            teamId: 'backend',
            avatar: '/placeholder.svg?height=40&width=40',
            phone: '(11) 92345-6789',
            permissions: ['users.view']
        },
        {
            id: 'user6',
            name: 'Carla Mendes',
            email: 'carla.mendes@exemplo.com',
            status: 'active',
            roleId: 'designer',
            teamId: 'design',
            avatar: '/placeholder.svg?height=40&width=40',
            phone: '(11) 93456-7890',
            permissions: ['users.view']
        },
        {
            id: 'user7',
            name: 'Roberto Almeida',
            email: 'roberto.almeida@exemplo.com',
            status: 'active',
            roleId: 'developer',
            teamId: '',
            avatar: '/placeholder.svg?height=40&width=40',
            phone: '(11) 94567-8901',
            permissions: ['users.view']
        }
    ]
})
</script>

<template>
    <Head :title="Cargos" />
    <AppAdminLayout :breadcrumbs=breadcrumbs>
        <div class="bg-gray-50 min-h-screen p-4 md:p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="border-b p-4 md:p-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <h1 class="text-2xl font-semibold text-gray-900">Cargos</h1>
                            <button
                                @click="openRoleModal()"
                                class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors"
                            >
                                <Plus size="18" />
                                <span>Novo Cargo</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="p-4 md:p-6">
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <div class="relative">
                                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size="18" />
                                    <input
                                        v-model="searchQuery"
                                        type="text"
                                        placeholder="Buscar cargos..."
                                        class="pl-10 pr-4 py-2 w-full border rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Cargos -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nome
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Descrição
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Usuários
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Permissões
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="role in filteredRoles" :key="role.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ role.name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">{{ role.description }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex -space-x-2 overflow-hidden">
                                        <div
                                            v-for="(user, index) in getRoleUsers(role.id).slice(0, 3)"
                                            :key="user.id"
                                            class="inline-block h-8 w-8 rounded-full ring-2 ring-white"
                                            :title="user.name"
                                        >
                                            <img
                                                :src="user.avatar || '/placeholder.svg?height=32&width=32'"
                                                alt=""
                                                class="h-8 w-8 rounded-full bg-gray-200"
                                            />
                                        </div>
                                        <div
                                            v-if="getRoleUsers(role.id).length > 3"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-200 ring-2 ring-white"
                                        >
                                            <span class="text-xs font-medium">+{{ getRoleUsers(role.id).length - 3 }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                    <span
                        v-for="(permission, index) in role.permissions.slice(0, 2)"
                        :key="permission"
                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"
                    >
                      {{ getPermissionName(permission) }}
                    </span>
                                        <span
                                            v-if="role.permissions.length > 2"
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"
                                        >
                      +{{ role.permissions.length - 2 }} mais
                    </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            @click="viewRoleDetails(role)"
                                            class="text-primary hover:text-primary/80"
                                            title="Ver detalhes"
                                        >
                                            <Eye size="18" />
                                        </button>
                                        <button
                                            @click="openRoleModal(role)"
                                            class="text-amber-600 hover:text-amber-500"
                                            title="Editar"
                                        >
                                            <Edit size="18" />
                                        </button>
                                        <button
                                            @click="openRolePermissionsModal(role)"
                                            class="text-purple-600 hover:text-purple-500"
                                            title="Permissões"
                                        >
                                            <Shield size="18" />
                                        </button>
                                        <button
                                            @click="deleteRole(role.id)"
                                            class="text-red-600 hover:text-red-500"
                                            :disabled="isRoleInUse(role.id)"
                                            :class="{ 'opacity-50 cursor-not-allowed': isRoleInUse(role.id) }"
                                            title="Excluir"
                                        >
                                            <Trash2 size="18" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredRoles.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Nenhum cargo encontrado
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal de Detalhes do Cargo -->
            <Teleport to="body">
                <div v-if="showRoleDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                        <div class="p-6 border-b sticky top-0 bg-white z-10">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    Detalhes do Cargo
                                </h2>
                                <button @click="showRoleDetailsModal = false" class="text-gray-400 hover:text-gray-500">
                                    <X size="24" />
                                </button>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex flex-col md:flex-row gap-6">
                                <!-- Informações Básicas -->
                                <div class="md:w-1/3">
                                    <div class="flex flex-col p-4 border rounded-lg">
                                        <h3 class="text-lg font-medium">{{ selectedRole?.name }}</h3>
                                        <p class="text-gray-500 mt-2">{{ selectedRole?.description }}</p>

                                        <div class="mt-4 pt-4 border-t">
                                            <h4 class="text-sm font-medium text-gray-700 mb-2">Estatísticas</h4>
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-gray-500">Total de usuários:</span>
                                                <span class="font-medium">{{ getRoleUsers(selectedRole?.id).length }}</span>
                                            </div>
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-gray-500">Permissões:</span>
                                                <span class="font-medium">{{ selectedRole?.permissions?.length || 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Abas de Detalhes -->
                                <div class="md:w-2/3">
                                    <div class="border-b mb-4">
                                        <div class="flex">
                                            <button
                                                v-for="tab in roleDetailsTabs"
                                                :key="tab.id"
                                                @click="activeRoleDetailsTab = tab.id"
                                                class="px-4 py-2 text-sm font-medium transition-colors relative"
                                                :class="activeRoleDetailsTab === tab.id ? 'text-primary' : 'text-gray-500 hover:text-gray-700'"
                                            >
                                                {{ tab.name }}
                                                <div
                                                    v-if="activeRoleDetailsTab === tab.id"
                                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary"
                                                ></div>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Usuários -->
                                    <div v-if="activeRoleDetailsTab === 'users'" class="space-y-4">
                                        <div class="flex justify-between items-center">
                                            <h4 class="font-medium text-gray-900">Usuários com este Cargo</h4>
                                        </div>

                                        <div class="space-y-2">
                                            <div v-if="getRoleUsers(selectedRole?.id).length === 0" class="text-sm text-gray-500 italic">
                                                Nenhum usuário com este cargo
                                            </div>
                                            <div
                                                v-for="user in getRoleUsers(selectedRole?.id)"
                                                :key="user.id"
                                                class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50"
                                            >
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <img
                                                            :src="user.avatar || '/placeholder.svg?height=40&width=40'"
                                                            class="h-10 w-10 rounded-full bg-gray-200"
                                                            alt=""
                                                        />
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                                        <div class="text-sm text-gray-500">{{ user.email }}</div>
                                                    </div>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ getTeamName(user.teamId) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Permissões -->
                                    <div v-if="activeRoleDetailsTab === 'permissions'" class="space-y-4">
                                        <div class="flex justify-between items-center">
                                            <h4 class="font-medium text-gray-900">Permissões do Cargo</h4>
                                            <button
                                                @click="openRolePermissionsModal(selectedRole)"
                                                class="text-sm text-primary hover:text-primary/80 flex items-center gap-1"
                                            >
                                                <Edit size="14" />
                                                Gerenciar Permissões
                                            </button>
                                        </div>

                                        <div class="grid gap-4">
                                            <div v-if="!selectedRole?.permissions || selectedRole.permissions.length === 0" class="text-sm text-gray-500 italic">
                                                Nenhuma permissão atribuída a este cargo
                                            </div>
                                            <div v-else v-for="(group, groupKey) in permissionGroups" :key="groupKey" class="border rounded-lg p-4">
                                                <h5 class="font-medium text-gray-900 mb-3">{{ group.name }}</h5>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                    <div
                                                        v-for="permission in group.permissions"
                                                        :key="permission.id"
                                                        class="flex items-center"
                                                    >
                                                        <div
                                                            class="w-4 h-4 mr-2 rounded-sm"
                                                            :class="
                              selectedRole?.permissions?.includes(permission.id)
                                ? 'bg-primary'
                                : 'bg-gray-200'
                            "
                                                        ></div>
                                                        <span class="text-sm">{{ permission.name }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                                <button
                                    @click="openRoleModal(selectedRole)"
                                    class="px-4 py-2 border rounded-lg text-primary border-primary hover:bg-primary/5"
                                >
                                    Editar Cargo
                                </button>
                                <button
                                    @click="showRoleDetailsModal = false"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
                                >
                                    Fechar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Teleport>

            <!-- Modal de Cargo (Cadastro/Edição) -->
            <Teleport to="body">
                <div v-if="showRoleModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                        <div class="p-6 border-b">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    {{ editingRole.id ? 'Editar Cargo' : 'Novo Cargo' }}
                                </h2>
                                <button @click="showRoleModal = false" class="text-gray-400 hover:text-gray-500">
                                    <X size="24" />
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            <form @submit.prevent="saveRole">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Cargo *</label>
                                        <input
                                            v-model="editingRole.name"
                                            type="text"
                                            required
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-primary"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                                        <textarea
                                            v-model="editingRole.description"
                                            rows="3"
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-primary"
                                        ></textarea>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Permissões</label>
                                        <div class="border rounded-lg p-4 max-h-60 overflow-y-auto">
                                            <div v-for="(group, groupKey) in permissionGroups" :key="groupKey" class="mb-4">
                                                <h4 class="text-sm font-medium text-gray-900 mb-2">{{ group.name }}</h4>
                                                <div class="space-y-2">
                                                    <div
                                                        v-for="permission in group.permissions"
                                                        :key="permission.id"
                                                        class="flex items-center"
                                                    >
                                                        <input
                                                            :id="`role-permission-${permission.id}`"
                                                            v-model="editingRole.permissions"
                                                            :value="permission.id"
                                                            type="checkbox"
                                                            class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                                                        />
                                                        <label
                                                            :for="`role-permission-${permission.id}`"
                                                            class="ml-2 block text-sm text-gray-700"
                                                        >
                                                            {{ permission.name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end gap-2 mt-6">
                                    <button
                                        type="button"
                                        @click="showRoleModal = false"
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

            <!-- Modal de Permissões do Cargo -->
            <Teleport to="body">
                <div
                    v-if="showRolePermissionsModal"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                >
                    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                        <div class="p-6 border-b">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    Permissões - {{ selectedRole?.name }}
                                </h2>
                                <button @click="showRolePermissionsModal = false" class="text-gray-400 hover:text-gray-500">
                                    <X size="24" />
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div v-for="(group, groupKey) in permissionGroups" :key="groupKey" class="border rounded-lg p-4">
                                    <h3 class="font-medium text-gray-900 mb-3">{{ group.name }}</h3>
                                    <div class="space-y-3">
                                        <div
                                            v-for="permission in group.permissions"
                                            :key="permission.id"
                                            class="flex items-center"
                                        >
                                            <input
                                                :id="`role-permission-modal-${permission.id}`"
                                                v-model="selectedRolePermissions"
                                                :value="permission.id"
                                                type="checkbox"
                                                class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                                            />
                                            <label
                                                :for="`role-permission-modal-${permission.id}`"
                                                class="ml-2 block text-sm text-gray-700"
                                            >
                                                {{ permission.name }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-2 mt-6">
                                <button
                                    type="button"
                                    @click="showRolePermissionsModal = false"
                                    class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50"
                                >
                                    Cancelar
                                </button>
                                <button
                                    @click="saveRolePermissions"
                                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90"
                                >
                                    Salvar Permissões
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Teleport>
        </div>
    </AppAdminLayout>
</template>
