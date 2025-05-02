<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import {
    Search,
    UserPlus,
    Edit,
    Shield,
    Ban,
    CheckCircle,
    ChevronLeft,
    ChevronRight,
    X,
    Plus,
    Trash2
} from 'lucide-vue-next'
import AppAdminLayout from '@/layouts/AppAdminLayout.vue';
import { Head } from '@inertiajs/vue3';

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
const activeTab = ref('users')
const currentPage = ref(1)
const itemsPerPage = ref(10)
const filters = ref({
    role: '',
    team: '',
    status: ''
})

// Modals
const showUserModal = ref(false)
const showPermissionsModal = ref(false)
const showRoleModal = ref(false)
const showTeamModal = ref(false)

// Editing state
const editingUser = ref<User>({
    id: '',
    name: '',
    email: '',
    status: 'active',
    roleId: '',
    teamId: '',
    permissions: []
})
const editingRole = ref<Role>({
    id: '',
    name: '',
    description: ''
})
const editingTeam = ref<Team>({
    id: '',
    name: '',
    description: ''
})
const selectedUser = ref<User | null>(null)
const selectedUserPermissions = ref<string[]>([])

// Tabs
const tabs = [
    { id: 'users', name: 'Usuários' },
    { id: 'roles', name: 'Cargos' },
    { id: 'teams', name: 'Times' }
]

// Table headers
const userTableHeaders = [
    { key: 'name', label: 'Nome', class: 'w-1/4' },
    { key: 'status', label: 'Status', class: 'w-1/6' },
    { key: 'role', label: 'Cargo', class: 'w-1/6' },
    { key: 'team', label: 'Time', class: 'w-1/6' }
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
const filteredUsers = computed(() => {
    let result = [...users.value]

    // Apply search
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        result = result.filter(
            user =>
                user.name.toLowerCase().includes(query) ||
                user.email.toLowerCase().includes(query)
        )
    }

    // Apply filters
    if (filters.value.role) {
        result = result.filter(user => user.roleId === filters.value.role)
    }
    if (filters.value.team) {
        result = result.filter(user => user.teamId === filters.value.team)
    }
    if (filters.value.status) {
        result = result.filter(user => user.status === filters.value.status)
    }

    return result
})

const paginatedUsers = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    const end = start + itemsPerPage.value
    return filteredUsers.value.slice(start, end)
})

const totalPages = computed(() => {
    return Math.ceil(filteredUsers.value.length / itemsPerPage.value) || 1
})

const paginationStart = computed(() => {
    return (currentPage.value - 1) * itemsPerPage.value + 1
})

const paginationEnd = computed(() => {
    return Math.min(currentPage.value * itemsPerPage.value, filteredUsers.value.length)
})

// Methods
function resetFilters() {
    filters.value = {
        role: '',
        team: '',
        status: ''
    }
    searchQuery.value = ''
}

function getRoleName(roleId: string): string {
    const role = roles.value.find(r => r.id === roleId)
    return role ? role.name : 'Não atribuído'
}

function getTeamName(teamId: string): string {
    const team = teams.value.find(t => t.id === teamId)
    return team ? team.name : 'Não atribuído'
}

function getUsersInTeam(teamId: string): number {
    return users.value.filter(user => user.teamId === teamId).length
}

function isRoleInUse(roleId: string): boolean {
    return users.value.some(user => user.roleId === roleId)
}

function isTeamInUse(teamId: string): boolean {
    return users.value.some(user => user.teamId === teamId)
}

function openUserModal(user?: User) {
    if (user) {
        editingUser.value = { ...user }
    } else {
        editingUser.value = {
            id: '',
            name: '',
            email: '',
            status: 'active',
            roleId: roles.value.length > 0 ? roles.value[0].id : '',
            teamId: teams.value.length > 0 ? teams.value[0].id : '',
            permissions: []
        }
    }
    showUserModal.value = true
}

function openPermissionsModal(user: User) {
    selectedUser.value = user
    selectedUserPermissions.value = [...user.permissions]
    showPermissionsModal.value = true
}

function openRoleModal(role?: Role) {
    if (role) {
        editingRole.value = { ...role }
    } else {
        editingRole.value = {
            id: '',
            name: '',
            description: ''
        }
    }
    showRoleModal.value = true
}

function openTeamModal(team?: Team) {
    if (team) {
        editingTeam.value = { ...team }
    } else {
        editingTeam.value = {
            id: '',
            name: '',
            description: ''
        }
    }
    showTeamModal.value = true
}

function saveUser() {
    if (editingUser.value.id) {
        // Update existing user
        const index = users.value.findIndex(u => u.id === editingUser.value.id)
        if (index !== -1) {
            users.value[index] = { ...editingUser.value }
        }
    } else {
        // Add new user
        const newUser = {
            ...editingUser.value,
            id: generateId(),
            permissions: []
        }
        users.value.push(newUser)
    }
    showUserModal.value = false
}

function savePermissions() {
    if (selectedUser.value) {
        const index = users.value.findIndex(u => u.id === selectedUser.value?.id)
        if (index !== -1) {
            users.value[index].permissions = [...selectedUserPermissions.value]
        }
    }
    showPermissionsModal.value = false
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

function saveTeam() {
    if (editingTeam.value.id) {
        // Update existing team
        const index = teams.value.findIndex(t => t.id === editingTeam.value.id)
        if (index !== -1) {
            teams.value[index] = { ...editingTeam.value }
        }
    } else {
        // Add new team
        const newTeam = {
            ...editingTeam.value,
            id: generateId()
        }
        teams.value.push(newTeam)
    }
    showTeamModal.value = false
}

function toggleUserStatus(user: User) {
    const index = users.value.findIndex(u => u.id === user.id)
    if (index !== -1) {
        users.value[index].status = user.status === 'active' ? 'inactive' : 'active'
    }
}

function deleteRole(roleId: string) {
    if (!isRoleInUse(roleId)) {
        roles.value = roles.value.filter(role => role.id !== roleId)
    }
}

function deleteTeam(teamId: string) {
    if (!isTeamInUse(teamId)) {
        teams.value = teams.value.filter(team => team.id !== teamId)
    }
}

function generateId(): string {
    return Math.random().toString(36).substring(2, 10)
}

// Initialize with sample data
onMounted(() => {
    // Sample roles
    roles.value = [
        { id: 'admin', name: 'Administrador', description: 'Acesso completo ao sistema' },
        { id: 'manager', name: 'Gerente', description: 'Gerencia times e projetos' },
        { id: 'developer', name: 'Desenvolvedor', description: 'Acesso a recursos de desenvolvimento' },
        { id: 'analyst', name: 'Analista', description: 'Analisa dados e gera relatórios' }
    ]

    // Sample teams
    teams.value = [
        { id: 'frontend', name: 'Frontend', description: 'Equipe de desenvolvimento frontend' },
        { id: 'backend', name: 'Backend', description: 'Equipe de desenvolvimento backend' },
        { id: 'design', name: 'Design', description: 'Equipe de design e UX' },
        { id: 'qa', name: 'QA', description: 'Equipe de testes e qualidade' }
    ]

    const avatarGenUrl = 'https://cdn-icons-png.flaticon.com/512/4792/4792929.png'
    // Sample users
    users.value = [
        {
            id: 'user1',
            name: 'João Silva',
            email: 'joao.silva@exemplo.com',
            status: 'active',
            roleId: 'admin',
            teamId: 'frontend',
            avatar: avatarGenUrl,
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
            avatar: avatarGenUrl,
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
            avatar: avatarGenUrl,
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
            avatar: avatarGenUrl,
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
            avatar: avatarGenUrl,
            phone: '(11) 92345-6789',
            permissions: ['users.view']
        }
    ]
})
</script>

<template>
    <Head title="Painel Admin" />
    <AppAdminLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen p-4 md:p-6">
            <div class="max-w-7xl mx-auto bg-white rounded-lg shadow-sm">
                <!-- Header -->
                <div class="border-b p-4 md:p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <h1 class="text-2xl font-semibold text-gray-900">Gerenciamento de Usuários</h1>
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1 md:min-w-[300px]">
                                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size="18" />
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Buscar usuários..."
                                    class="pl-10 pr-4 py-2 w-full border rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary"
                                />
                            </div>
                            <button
                                @click="openUserModal()"
                                class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors"
                            >
                                <UserPlus size="18" />
                                <span class="hidden md:inline">Novo Usuário</span>
                            </button>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="flex mt-6 border-b">
                        <button
                            v-for="tab in tabs"
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            class="px-4 py-2 text-sm font-medium transition-colors relative"
                            :class="activeTab === tab.id ? 'text-primary' : 'text-gray-500 hover:text-gray-700'"
                        >
                            {{ tab.name }}
                            <div
                                v-if="activeTab === tab.id"
                                class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary"
                            >
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div v-if="activeTab === 'users'" class="p-4 md:p-6">
                    <!-- Filters -->
                    <div class="flex flex-col md:flex-row gap-3 mb-6">
                        <div class="flex-1">
                            <label class="text-sm font-medium text-gray-700 mb-1 block">Cargo</label>
                            <select
                                v-model="filters.role"
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary"
                            >
                                <option value="">Todos os cargos</option>
                                <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="text-sm font-medium text-gray-700 mb-1 block">Time</label>
                            <select
                                v-model="filters.team"
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary"
                            >
                                <option value="">Todos os times</option>
                                <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="text-sm font-medium text-gray-700 mb-1 block">Status</label>
                            <select
                                v-model="filters.status"
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary"
                            >
                                <option value="">Todos</option>
                                <option value="active">Ativo</option>
                                <option value="inactive">Inativo</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button
                                @click="resetFilters"
                                class="px-4 py-2 border rounded-lg text-sm text-gray-700 hover:bg-gray-50"
                            >
                                Limpar Filtros
                            </button>
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th
                                    v-for="header in tableHeaders"
                                    :key="header.key"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    :class="header.class"
                                >
                                    {{ header.label }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                      :class="
                      user.status === 'active'
                        ? 'bg-green-100 text-green-800'
                        : 'bg-gray-100 text-gray-800'
                    "
                  >
                    {{ user.status === 'active' ? 'Ativo' : 'Inativo' }}
                  </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ getRoleName(user.roleId) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ getTeamName(user.teamId) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            @click="openUserModal(user)"
                                            class="text-primary hover:text-primary/80"
                                            title="Editar"
                                        >
                                            <Edit size="18" />
                                        </button>
                                        <button
                                            @click="openPermissionsModal(user)"
                                            class="text-amber-600 hover:text-amber-500"
                                            title="Permissões"
                                        >
                                            <Shield size="18" />
                                        </button>
                                        <button
                                            @click="toggleUserStatus(user)"
                                            :class="
                        user.status === 'active'
                          ? 'text-red-600 hover:text-red-500'
                          : 'text-green-600 hover:text-green-500'
                      "
                                            :title="user.status === 'active' ? 'Desativar' : 'Ativar'"
                                        >
                                            <component :is="user.status === 'active' ? Ban : CheckCircle" size="18" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredUsers.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Nenhum usuário encontrado
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex items-center justify-between border-t border-gray-200 px-4 py-3 sm:px-6 mt-4">
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Mostrando <span class="font-medium">{{ paginationStart }}</span> a
                                    <span class="font-medium">{{ paginationEnd }}</span> de
                                    <span class="font-medium">{{ filteredUsers.length }}</span> resultados
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <button
                                        @click="currentPage = Math.max(1, currentPage - 1)"
                                        :disabled="currentPage === 1"
                                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                        :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
                                    >
                                        <span class="sr-only">Anterior</span>
                                        <ChevronLeft class="h-5 w-5" aria-hidden="true" />
                                    </button>
                                    <button
                                        v-for="page in totalPages"
                                        :key="page"
                                        @click="currentPage = page"
                                        :class="[
                    currentPage === page
                      ? 'z-10 bg-primary border-primary text-white'
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                  ]"
                                    >
                                        {{ page }}
                                    </button>
                                    <button
                                        @click="currentPage = Math.min(totalPages, currentPage + 1)"
                                        :disabled="currentPage === totalPages"
                                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                        :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }"
                                    >
                                        <span class="sr-only">Próximo</span>
                                        <ChevronRight class="h-5 w-5" aria-hidden="true" />
                                    </button>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Roles Tab -->
                <div v-if="activeTab === 'roles'" class="p-4 md:p-6">
                    <div class="flex justify-between mb-6">
                        <h2 class="text-lg font-medium text-gray-900">Cargos</h2>
                        <button
                            @click="openRoleModal()"
                            class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors"
                        >
                            <Plus size="18" />
                            <span>Novo Cargo</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            v-for="role in roles"
                            :key="role.id"
                            class="border rounded-lg p-4 hover:shadow-md transition-shadow"
                        >
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ role.name }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ role.description }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <button @click="openRoleModal(role)" class="text-primary hover:text-primary/80">
                                        <Edit size="18" />
                                    </button>
                                    <button
                                        @click="deleteRole(role.id)"
                                        class="text-red-600 hover:text-red-500"
                                        :disabled="isRoleInUse(role.id)"
                                        :class="{ 'opacity-50 cursor-not-allowed': isRoleInUse(role.id) }"
                                    >
                                        <Trash2 size="18" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Teams Tab -->
                <div v-if="activeTab === 'teams'" class="p-4 md:p-6">
                    <div class="flex justify-between mb-6">
                        <h2 class="text-lg font-medium text-gray-900">Times</h2>
                        <button
                            @click="openTeamModal()"
                            class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors"
                        >
                            <Plus size="18" />
                            <span>Novo Time</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            v-for="team in teams"
                            :key="team.id"
                            class="border rounded-lg p-4 hover:shadow-md transition-shadow"
                        >
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ team.name }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ team.description }}</p>
                                    <p class="text-xs text-gray-400 mt-2">
                                        {{ getUsersInTeam(team.id) }} membros
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <button @click="openTeamModal(team)" class="text-primary hover:text-primary/80">
                                        <Edit size="18" />
                                    </button>
                                    <button
                                        @click="deleteTeam(team.id)"
                                        class="text-red-600 hover:text-red-500"
                                        :disabled="isTeamInUse(team.id)"
                                        :class="{ 'opacity-50 cursor-not-allowed': isTeamInUse(team.id) }"
                                    >
                                        <Trash2 size="18" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Modal -->
            <Teleport to="body">
                <div v-if="showUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                        <div class="p-6 border-b">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    {{ editingUser.id ? 'Editar Usuário' : 'Novo Usuário' }}
                                </h2>
                                <button @click="showUserModal = false" class="text-gray-400 hover:text-gray-500">
                                    <X size="24" />
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            <form @submit.prevent="saveUser">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                                        <input
                                            v-model="editingUser.name"
                                            type="text"
                                            required
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-primary"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input
                                            v-model="editingUser.email"
                                            type="email"
                                            required
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-primary"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                                        <select
                                            v-model="editingUser.roleId"
                                            required
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-primary"
                                        >
                                            <option v-for="role in roles" :key="role.id" :value="role.id">
                                                {{ role.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                                        <select
                                            v-model="editingUser.teamId"
                                            required
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-primary"
                                        >
                                            <option v-for="team in teams" :key="team.id" :value="team.id">
                                                {{ team.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                        <select
                                            v-model="editingUser.status"
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-primary"
                                        >
                                            <option value="active">Ativo</option>
                                            <option value="inactive">Inativo</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                                        <input
                                            v-model="editingUser.phone"
                                            type="text"
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-primary"
                                        />
                                    </div>
                                </div>

                                <div class="flex justify-end gap-2 mt-6">
                                    <button
                                        type="button"
                                        @click="showUserModal = false"
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

            <!-- Permissions Modal -->
            <Teleport to="body">
                <div
                    v-if="showPermissionsModal"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                >
                    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                        <div class="p-6 border-b">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    Permissões - {{ selectedUser?.name }}
                                </h2>
                                <button @click="showPermissionsModal = false" class="text-gray-400 hover:text-gray-500">
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
                                                :id="`permission-${permission.id}`"
                                                v-model="selectedUserPermissions"
                                                :value="permission.id"
                                                type="checkbox"
                                                class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                                            />
                                            <label
                                                :for="`permission-${permission.id}`"
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
                                    @click="showPermissionsModal = false"
                                    class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50"
                                >
                                    Cancelar
                                </button>
                                <button
                                    @click="savePermissions"
                                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90"
                                >
                                    Salvar Permissões
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Teleport>

            <!-- Role Modal -->
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
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Cargo</label>
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

            <!-- Team Modal -->
            <Teleport to="body">
                <div v-if="showTeamModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                        <div class="p-6 border-b">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    {{ editingTeam.id ? 'Editar Time' : 'Novo Time' }}
                                </h2>
                                <button @click="showTeamModal = false" class="text-gray-400 hover:text-gray-500">
                                    <X size="24" />
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            <form @submit.prevent="saveTeam">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Time</label>
                                        <input
                                            v-model="editingTeam.name"
                                            type="text"
                                            required
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-primary"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                                        <textarea
                                            v-model="editingTeam.description"
                                            rows="3"
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-primary"
                                        ></textarea>
                                    </div>
                                </div>

                                <div class="flex justify-end gap-2 mt-6">
                                    <button
                                        type="button"
                                        @click="showTeamModal = false"
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
