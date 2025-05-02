<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppAdminLayout from '@/layouts/AppAdminLayout.vue';
import { ref, computed, onMounted } from 'vue'
import {
    Search,
    Plus,
    Edit,
    Shield,
    Trash2,
    X,
    Eye
} from 'lucide-vue-next'

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
    permissions?: string[]
}

interface Permission {
    id: string
    name: string
    originalId?: string
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
const selectedGroup = ref('')

// Modals
const showPermissionModal = ref(false)
const showPermissionDetailsModal = ref(false)

// Tabs
const activePermissionDetailsTab = ref('roles')

// Editing state
const editingPermission = ref<Permission & { group?: string }>({
    id: '',
    name: '',
    group: ''
})
const selectedPermission = ref<Permission | null>(null)
const selectedPermissionGroup = ref('')

// Abas de detalhes da permissão
const permissionDetailsTabs = [
    { id: 'roles', name: 'Cargos' },
    { id: 'teams', name: 'Times' },
    { id: 'users', name: 'Usuários' }
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
const filteredPermissionGroups = computed(() => {
    const result: Record<string, PermissionGroup> = {}

    for (const [key, group] of Object.entries(permissionGroups.value)) {
        // Filter by selected group
        if (selectedGroup.value && key !== selectedGroup.value) {
            continue
        }

        // Check if any permission in this group matches the search query
        const hasMatchingPermissions = group.permissions.some(permission =>
            permission.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            permission.id.toLowerCase().includes(searchQuery.value.toLowerCase())
        )

        if (hasMatchingPermissions || !searchQuery.value) {
            result[key] = { ...group }
        }
    }

    return result
})

// Methods
function filteredPermissions(permissions: Permission[]) {
    if (!searchQuery.value) return permissions

    const query = searchQuery.value.toLowerCase()
    return permissions.filter(permission =>
        permission.name.toLowerCase().includes(query) ||
        permission.id.toLowerCase().includes(query)
    )
}

function getGroupName(groupKey: string): string {
    return permissionGroups.value[groupKey]?.name || groupKey
}

function getRolesWithPermission(permissionId?: string): Role[] {
    if (!permissionId) return []
    return roles.value.filter(role => role.permissions.includes(permissionId))
}

function getTeamsWithPermission(permissionId?: string): Team[] {
    if (!permissionId) return []
    return teams.value.filter(team => team.permissions?.includes(permissionId))
}

function getUsersWithRole(roleId: string): User[] {
    return users.value.filter(user => user.roleId === roleId)
}

function getUsersInTeam(teamId: string): User[] {
    return users.value.filter(user => user.teamId === teamId)
}

function getUsersWithPermission(permissionId?: string): User[] {
    if (!permissionId) return []

    const usersWithPermission: User[] = []
    const rolesWithPermission = getRolesWithPermission(permissionId)
    const teamsWithPermission = getTeamsWithPermission(permissionId)

    users.value.forEach(user => {
        // Check if user has permission directly
        if (user.permissions.includes(permissionId)) {
            usersWithPermission.push(user)
        }
        // Check if user's role has permission
        else if (rolesWithPermission.some(role => role.id === user.roleId)) {
            usersWithPermission.push(user)
        }
        // Check if user's team has permission
        else if (teamsWithPermission.some(team => team.id === user.teamId)) {
            usersWithPermission.push(user)
        }
    })

    return usersWithPermission
}

function getPermissionSource(user: User, permissionId?: string): string {
    if (!permissionId) return ''

    if (user.permissions.includes(permissionId)) {
        return 'Direta'
    }

    const userRole = roles.value.find(role => role.id === user.roleId)
    if (userRole && userRole.permissions.includes(permissionId)) {
        return `Via cargo: ${userRole.name}`
    }

    const userTeam = teams.value.find(team => team.id === user.teamId)
    if (userTeam && userTeam.permissions?.includes(permissionId)) {
        return `Via time: ${userTeam.name}`
    }

    return 'Desconhecido'
}

function isPermissionInUse(permissionId: string): boolean {
    // Check if any role uses this permission
    const roleUses = roles.value.some(role => role.permissions.includes(permissionId))

    // Check if any team uses this permission
    const teamUses = teams.value.some(team => team.permissions?.includes(permissionId))

    // Check if any user has this permission directly
    const userUses = users.value.some(user => user.permissions.includes(permissionId))

    return roleUses || teamUses || userUses
}

function viewPermissionDetails(permission: Permission, groupKey: string) {
    selectedPermission.value = { ...permission }
    selectedPermissionGroup.value = groupKey
    showPermissionDetailsModal.value = true
}

function openPermissionModal(permission?: Permission, groupKey?: string) {
    if (permission && groupKey) {
        editingPermission.value = {
            ...permission,
            originalId: permission.id,
            group: groupKey
        }
    } else {
        editingPermission.value = {
            id: '',
            name: '',
            group: Object.keys(permissionGroups.value)[0]
        }
    }
    showPermissionModal.value = true
}

function savePermission() {
    const permissionId = editingPermission.value.originalId || editingPermission.value.id
    const groupKey = editingPermission.value.group || ''

    if (editingPermission.value.originalId) {
        // Find and update existing permission
        for (const key in permissionGroups.value) {
            const index = permissionGroups.value[key].permissions.findIndex(p => p.id === permissionId)
            if (index !== -1) {
                // Remove from old group if group changed
                if (key !== groupKey) {
                    permissionGroups.value[key].permissions.splice(index, 1)
                } else {
                    // Update in same group
                    permissionGroups.value[key].permissions[index + 1] = {
                        id: permissionId,
                        name: editingPermission.value.name
                    }

                    permissionGroups.value[key].permissions[index] = {
                        id: permissionId,
                        name: editingPermission.value.name
                    }
                    // Update in same group
                    permissionGroups.value[key].permissions[index] = {
                        id: permissionId,
                        name: editingPermission.value.name
                    }
                }
            }
        }

        // Add to new group if group changed
        if (!permissionGroups.value[groupKey].permissions.some(p => p.id === permissionId)) {
            permissionGroups.value[groupKey].permissions.push({
                id: permissionId,
                name: editingPermission.value.name
            })
        }
    } else {
        // Add new permission
        permissionGroups.value[groupKey].permissions.push({
            id: editingPermission.value.id,
            name: editingPermission.value.name
        })
    }

    showPermissionModal.value = false
}

function deletePermission(permissionId: string, groupKey: string) {
    if (!isPermissionInUse(permissionId)) {
        const index = permissionGroups.value[groupKey].permissions.findIndex(p => p.id === permissionId)
        if (index !== -1) {
            permissionGroups.value[groupKey].permissions.splice(index, 1)
        }
    }
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
        {
            id: 'frontend',
            name: 'Frontend',
            description: 'Equipe de desenvolvimento frontend',
            permissions: ['users.view', 'teams.view']
        },
        {
            id: 'backend',
            name: 'Backend',
            description: 'Equipe de desenvolvimento backend',
            permissions: ['users.view', 'teams.view', 'reports.view']
        },
        {
            id: 'design',
            name: 'Design',
            description: 'Equipe de design e UX',
            permissions: ['users.view']
        },
        {
            id: 'qa',
            name: 'QA',
            description: 'Equipe de testes e qualidade',
            permissions: ['users.view', 'reports.view']
        }
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
    <Head :title="Permissões" />
    <AppAdminLayout :breadcrumbs="breadcrumbs">
        <div class="bg-gray-50 min-h-screen p-4 md:p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="border-b p-4 md:p-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <h1 class="text-2xl font-semibold text-gray-900">Gerenciamento de Permissões</h1>
                            <button
                                @click="openPermissionModal()"
                                class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors"
                            >
                                <Plus size="18" />
                                <span>Nova Permissão</span>
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
                                        placeholder="Buscar permissões..."
                                        class="pl-10 pr-4 py-2 w-full border rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary"
                                    />
                                </div>
                            </div>
                            <div class="md:w-64">
                                <select
                                    v-model="selectedGroup"
                                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary"
                                >
                                    <option value="">Todos os grupos</option>
                                    <option v-for="(group, key) in permissionGroups" :key="key" :value="key">
                                        {{ group.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Permissões -->
                <div v-for="(group, groupKey) in filteredPermissionGroups" :key="groupKey" class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="border-b p-4 md:p-6">
                        <h2 class="text-xl font-semibold text-gray-900">{{ group.name }}</h2>
                    </div>
                    <div class="p-4 md:p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nome
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cargos
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Times
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="permission in filteredPermissions(group.permissions)" :key="permission.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ permission.name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ permission.id }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                      <span
                          v-for="role in getRolesWithPermission(permission.id).slice(0, 2)"
                          :key="role.id"
                          class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800"
                      >
                        {{ role.name }}
                      </span>
                                            <span
                                                v-if="getRolesWithPermission(permission.id).length > 2"
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800"
                                            >
                        +{{ getRolesWithPermission(permission.id).length - 2 }} mais
                      </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                      <span
                          v-for="team in getTeamsWithPermission(permission.id).slice(0, 2)"
                          :key="team.id"
                          class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800"
                      >
                        {{ team.name }}
                      </span>
                                            <span
                                                v-if="getTeamsWithPermission(permission.id).length > 2"
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800"
                                            >
                        +{{ getTeamsWithPermission(permission.id).length - 2 }} mais
                      </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                @click="viewPermissionDetails(permission, groupKey)"
                                                class="text-primary hover:text-primary/80"
                                                title="Ver detalhes"
                                            >
                                                <Eye size="18" />
                                            </button>
                                            <button
                                                @click="openPermissionModal(permission, groupKey)"
                                                class="text-amber-600 hover:text-amber-500"
                                                title="Editar"
                                            >
                                                <Edit size="18" />
                                            </button>
                                            <button
                                                @click="deletePermission(permission.id, groupKey)"
                                                class="text-red-600 hover:text-red-500"
                                                :disabled="isPermissionInUse(permission.id)"
                                                :class="{ 'opacity-50 cursor-not-allowed': isPermissionInUse(permission.id) }"
                                                title="Excluir"
                                            >
                                                <Trash2 size="18" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredPermissions(group.permissions).length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Nenhuma permissão encontrada neste grupo
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Mensagem quando não há permissões -->
                <div v-if="Object.keys(filteredPermissionGroups).length === 0" class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <div class="flex flex-col items-center">
                        <Shield class="h-12 w-12 text-gray-400 mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma permissão encontrada</h3>
                        <p class="text-gray-500 max-w-md mx-auto mb-6">
                            Não foram encontradas permissões com os filtros atuais. Tente ajustar sua busca ou crie uma nova permissão.
                        </p>
                        <button
                            @click="openPermissionModal()"
                            class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors"
                        >
                            <Plus size="18" />
                            <span>Nova Permissão</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal de Detalhes da Permissão -->
            <Teleport to="body">
                <div v-if="showPermissionDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                        <div class="p-6 border-b sticky top-0 bg-white z-10">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    Detalhes da Permissão
                                </h2>
                                <button @click="showPermissionDetailsModal = false" class="text-gray-400 hover:text-gray-500">
                                    <X size="24" />
                                </button>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex flex-col md:flex-row gap-6">
                                <!-- Informações Básicas -->
                                <div class="md:w-1/3">
                                    <div class="flex flex-col p-4 border rounded-lg">
                                        <h3 class="text-lg font-medium">{{ selectedPermission?.name }}</h3>
                                        <p class="text-gray-500 mt-2">ID: {{ selectedPermission?.id }}</p>
                                        <p class="text-gray-500 mt-1">Grupo: {{ getGroupName(selectedPermissionGroup) }}</p>

                                        <div class="mt-4 pt-4 border-t">
                                            <h4 class="text-sm font-medium text-gray-700 mb-2">Estatísticas</h4>
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-gray-500">Cargos:</span>
                                                <span class="font-medium">{{ getRolesWithPermission(selectedPermission?.id).length }}</span>
                                            </div>
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-gray-500">Times:</span>
                                                <span class="font-medium">{{ getTeamsWithPermission(selectedPermission?.id).length }}</span>
                                            </div>
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-gray-500">Usuários com acesso:</span>
                                                <span class="font-medium">{{ getUsersWithPermission(selectedPermission?.id).length }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Abas de Detalhes -->
                                <div class="md:w-2/3">
                                    <div class="border-b mb-4">
                                        <div class="flex">
                                            <button
                                                v-for="tab in permissionDetailsTabs"
                                                :key="tab.id"
                                                @click="activePermissionDetailsTab = tab.id"
                                                class="px-4 py-2 text-sm font-medium transition-colors relative"
                                                :class="activePermissionDetailsTab === tab.id ? 'text-primary' : 'text-gray-500 hover:text-gray-700'"
                                            >
                                                {{ tab.name }}
                                                <div
                                                    v-if="activePermissionDetailsTab === tab.id"
                                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary"
                                                ></div>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Cargos -->
                                    <div v-if="activePermissionDetailsTab === 'roles'" class="space-y-4">
                                        <h4 class="font-medium text-gray-900">Cargos com esta Permissão</h4>

                                        <div class="space-y-2">
                                            <div v-if="getRolesWithPermission(selectedPermission?.id).length === 0" class="text-sm text-gray-500 italic">
                                                Nenhum cargo com esta permissão
                                            </div>
                                            <div
                                                v-for="role in getRolesWithPermission(selectedPermission?.id)"
                                                :key="role.id"
                                                class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50"
                                            >
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ role.name }}</div>
                                                    <div class="text-sm text-gray-500">{{ role.description }}</div>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ getUsersWithRole(role.id).length }} usuários
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Times -->
                                    <div v-if="activePermissionDetailsTab === 'teams'" class="space-y-4">
                                        <h4 class="font-medium text-gray-900">Times com esta Permissão</h4>

                                        <div class="space-y-2">
                                            <div v-if="getTeamsWithPermission(selectedPermission?.id).length === 0" class="text-sm text-gray-500 italic">
                                                Nenhum time com esta permissão
                                            </div>
                                            <div
                                                v-for="team in getTeamsWithPermission(selectedPermission?.id)"
                                                :key="team.id"
                                                class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50"
                                            >
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ team.name }}</div>
                                                    <div class="text-sm text-gray-500">{{ team.description }}</div>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ getUsersInTeam(team.id).length }} membros
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Usuários -->
                                    <div v-if="activePermissionDetailsTab === 'users'" class="space-y-4">
                                        <h4 class="font-medium text-gray-900">Usuários com esta Permissão</h4>

                                        <div class="space-y-2">
                                            <div v-if="getUsersWithPermission(selectedPermission?.id).length === 0" class="text-sm text-gray-500 italic">
                                                Nenhum usuário com esta permissão
                                            </div>
                                            <div
                                                v-for="user in getUsersWithPermission(selectedPermission?.id)"
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
                                                    {{ getPermissionSource(user, selectedPermission?.id) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                                <button
                                    @click="openPermissionModal(selectedPermission, selectedPermissionGroup)"
                                    class="px-4 py-2 border rounded-lg text-primary border-primary hover:bg-primary/5"
                                >
                                    Editar Permissão
                                </button>
                                <button
                                    @click="showPermissionDetailsModal = false"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
                                >
                                    Fechar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Teleport>

            <!-- Modal de Permissão (Cadastro/Edição) -->
            <Teleport to="body">
                <div v-if="showPermissionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                        <div class="p-6 border-b">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    {{ editingPermission.id ? 'Editar Permissão' : 'Nova Permissão' }}
                                </h2>
                                <button @click="showPermissionModal = false" class="text-gray-400 hover:text-gray-500">
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
                                            v-model="editingPermission.name"
                                            type="text"
                                            required
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-primary"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">ID da Permissão *</label>
                                        <input
                                            v-model="editingPermission.id"
                                            type="text"
                                            required
                                            :disabled="!!editingPermission.originalId"
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-primary"
                                            :class="{ 'bg-gray-100': !!editingPermission.originalId }"
                                        />
                                        <p v-if="!!editingPermission.originalId" class="text-xs text-gray-500 mt-1">
                                            O ID da permissão não pode ser alterado
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Grupo *</label>
                                        <select
                                            v-model="editingPermission.group"
                                            required
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-primary"
                                        >
                                            <option v-for="(group, key) in permissionGroups" :key="key" :value="key">
                                                {{ group.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="flex justify-end gap-2 mt-6">
                                    <button
                                        type="button"
                                        @click="showPermissionModal = false"
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
