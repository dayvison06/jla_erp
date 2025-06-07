<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import {
    Search,
    UserPlus,
    UserMinus,
    Edit,
    Shield,
    Trash2,
    X,
    Eye,
    Users
} from 'lucide-vue-next'
import AppAdminLayout from '@/layouts/AppAdminLayout.vue';

// Types
interface Address {
    zipCode: string
    street: string
    number: string
    complement: string
    neighborhood: string
    city: string
    state: string
}

interface PersonalInfo {
    cpf: string
    rg: string
    birthDate: string
}

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
    address: Address
    personalInfo: PersonalInfo
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
const showTeamModal = ref(false)
const showTeamDetailsModal = ref(false)
const showTeamPermissionsModal = ref(false)
const showTeamMembersModal = ref(false)

// Tabs
const activeTeamDetailsTab = ref('members')

// Editing state
const editingTeam = ref<Team>({
    id: '',
    name: '',
    description: '',
    permissions: []
})
const selectedTeam = ref<Team | null>(null)
const selectedTeamPermissions = ref<string[]>([])
const newMember = ref('')

// Abas de detalhes do time
const teamDetailsTabs = [
    { id: 'members', name: 'Membros' },
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
const filteredTeams = computed(() => {
    if (!searchQuery.value) return teams.value

    const query = searchQuery.value.toLowerCase()
    return teams.value.filter(team =>
        team.name.toLowerCase().includes(query) ||
        team.description.toLowerCase().includes(query)
    )
})

const availableUsers = computed(() => {
    if (!selectedTeam.value) return []
    return users.value.filter(user => user.teamId !== selectedTeam.value?.id)
})

// Methods
function getTeamMembers(teamId?: string): User[] {
    if (!teamId) return []
    return users.value.filter(user => user.teamId === teamId)
}

function getRoleName(roleId: string): string {
    const role = roles.value.find(r => r.id === roleId)
    return role ? role.name : 'Não atribuído'
}

function isTeamInUse(teamId: string): boolean {
    return users.value.some(user => user.teamId === teamId)
}

function viewTeamDetails(team: Team) {
    selectedTeam.value = { ...team }
    showTeamDetailsModal.value = true
}

function openTeamModal(team?: Team) {
    if (team) {
        editingTeam.value = { ...team }
    } else {
        editingTeam.value = {
            id: '',
            name: '',
            description: '',
            permissions: []
        }
    }
    showTeamModal.value = true
}

function openTeamPermissionsModal(team?: Team) {
    if (team) {
        selectedTeam.value = team
        selectedTeamPermissions.value = [...(team.permissions || [])]
    }
    showTeamPermissionsModal.value = true
}

function openTeamMembersModal(team?: Team) {
    if (team) {
        selectedTeam.value = team
        newMember.value = ''
    }
    showTeamMembersModal.value = true
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
            id: generateId(),
            permissions: []
        }
        teams.value.push(newTeam)
    }
    showTeamModal.value = false
}

function saveTeamPermissions() {
    if (selectedTeam.value) {
        const index = teams.value.findIndex(t => t.id === selectedTeam.value?.id)
        if (index !== -1) {
            teams.value[index].permissions = [...selectedTeamPermissions.value]
        }
    }
    showTeamPermissionsModal.value = false
}

function addMemberToTeam() {
    if (selectedTeam.value && newMember.value) {
        const userIndex = users.value.findIndex(u => u.id === newMember.value)
        if (userIndex !== -1) {
            users.value[userIndex].teamId = selectedTeam.value.id
            newMember.value = ''
        }
    }
}

function removeMemberFromTeam(userId: string) {
    const userIndex = users.value.findIndex(u => u.id === userId)
    if (userIndex !== -1) {
        users.value[userIndex].teamId = ''
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
            permissions: ['users.view', 'users.create', 'users.edit', 'users.delete', 'roles.view', 'teams.view'],
            personalInfo: {
                cpf: '123.456.789-00',
                rg: '12.345.678-9',
                birthDate: '1985-05-15'
            },
            address: {
                zipCode: '01234-567',
                street: 'Av. Paulista',
                number: '1000',
                complement: 'Sala 123',
                neighborhood: 'Bela Vista',
                city: 'São Paulo',
                state: 'SP'
            }
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
            permissions: ['users.view', 'roles.view', 'teams.view'],
            personalInfo: {
                cpf: '987.654.321-00',
                rg: '98.765.432-1',
                birthDate: '1990-10-20'
            },
            address: {
                zipCode: '04567-890',
                street: 'Rua Augusta',
                number: '500',
                complement: 'Apto 42',
                neighborhood: 'Consolação',
                city: 'São Paulo',
                state: 'SP'
            }
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
            permissions: ['users.view'],
            personalInfo: {
                cpf: '456.789.123-00',
                rg: '45.678.912-3',
                birthDate: '1992-03-25'
            },
            address: {
                zipCode: '03210-987',
                street: 'Rua Vergueiro',
                number: '1500',
                complement: '',
                neighborhood: 'Vila Mariana',
                city: 'São Paulo',
                state: 'SP'
            }
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
            permissions: ['users.view', 'reports.view', 'reports.export'],
            personalInfo: {
                cpf: '789.123.456-00',
                rg: '78.912.345-6',
                birthDate: '1988-12-10'
            },
            address: {
                zipCode: '02345-678',
                street: 'Av. Brigadeiro Faria Lima',
                number: '2500',
                complement: 'Andar 10',
                neighborhood: 'Itaim Bibi',
                city: 'São Paulo',
                state: 'SP'
            }
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
            permissions: ['users.view'],
            personalInfo: {
                cpf: '321.654.987-00',
                rg: '32.165.498-7',
                birthDate: '1995-07-30'
            },
            address: {
                zipCode: '05678-901',
                street: 'Rua Oscar Freire',
                number: '800',
                complement: 'Sala 15',
                neighborhood: 'Jardins',
                city: 'São Paulo',
                state: 'SP'
            }
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
            permissions: ['users.view'],
            personalInfo: {
                cpf: '654.321.987-00',
                rg: '65.432.198-7',
                birthDate: '1993-08-12'
            },
            address: {
                zipCode: '06789-012',
                street: 'Rua dos Pinheiros',
                number: '300',
                complement: 'Conj. 42',
                neighborhood: 'Pinheiros',
                city: 'São Paulo',
                state: 'SP'
            }
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
            permissions: ['users.view'],
            personalInfo: {
                cpf: '234.567.890-12',
                rg: '23.456.789-0',
                birthDate: '1991-04-18'
            },
            address: {
                zipCode: '07890-123',
                street: 'Av. Rebouças',
                number: '1200',
                complement: '',
                neighborhood: 'Jardim Paulista',
                city: 'São Paulo',
                state: 'SP'
            }
        }
    ]
})
</script>

<template>
    <Head title="Dashboard" />
    <AppAdminLayout :breadcrumbs="breadcrumbs">
        <div class="bg-gray-50 min-h-screen p-4 md:p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="border-b p-4 md:p-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <h1 class="text-2xl font-semibold text-gray-900">Times</h1>
                            <button
                                @click="openTeamModal()"
                                class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors"
                            >
                                <UserPlus size="18" />
                                <span>Novo Time</span>
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
                                        placeholder="Buscar times..."
                                        class="pl-10 pr-4 py-2 w-full border rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Times -->
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
                                    Membros
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="team in filteredTeams" :key="team.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ team.name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">{{ team.description }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex -space-x-2 overflow-hidden">
                                        <div
                                            v-for="(member) in getTeamMembers(team.id).slice(0, 3)"
                                            :key="member.id"
                                            class="inline-block h-8 w-8 rounded-full ring-2 ring-white"
                                            :title="member.name"
                                        >
                                            <img
                                                :src="member.avatar || '/placeholder.svg?height=32&width=32'"
                                                alt=""
                                                class="h-8 w-8 rounded-full bg-gray-200"
                                            />
                                        </div>
                                        <div
                                            v-if="getTeamMembers(team.id).length > 3"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-200 ring-2 ring-white"
                                        >
                                            <span class="text-xs font-medium">+{{ getTeamMembers(team.id).length - 3 }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            @click="viewTeamDetails(team)"
                                            class="text-primary hover:text-primary/80"
                                            title="Ver detalhes"
                                        >
                                            <Eye size="18" />
                                        </button>
                                        <button
                                            @click="openTeamModal(team)"
                                            class="text-amber-600 hover:text-amber-500"
                                            title="Editar"
                                        >
                                            <Edit size="18" />
                                        </button>
                                        <button
                                            @click="openTeamPermissionsModal(team)"
                                            class="text-purple-600 hover:text-purple-500"
                                            title="Permissões"
                                        >
                                            <Shield size="18" />
                                        </button>
                                        <button
                                            @click="openTeamMembersModal(team)"
                                            class="text-green-600 hover:text-green-500"
                                            title="Membros"
                                        >
                                            <Users size="18" />
                                        </button>
                                        <button
                                            @click="deleteTeam(team.id)"
                                            class="text-red-600 hover:text-red-500"
                                            :disabled="isTeamInUse(team.id)"
                                            :class="{ 'opacity-50 cursor-not-allowed': isTeamInUse(team.id) }"
                                            title="Excluir"
                                        >
                                            <Trash2 size="18" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredTeams.length === 0">
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Nenhum time encontrado
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal de Detalhes do Time -->
            <Teleport to="body">
                <div v-if="showTeamDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                        <div class="p-6 border-b sticky top-0 bg-white z-10">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    Detalhes do Time
                                </h2>
                                <button @click="showTeamDetailsModal = false" class="text-gray-400 hover:text-gray-500">
                                    <X size="24" />
                                </button>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex flex-col md:flex-row gap-6">
                                <!-- Informações Básicas -->
                                <div class="md:w-1/3">
                                    <div class="flex flex-col p-4 border rounded-lg">
                                        <h3 class="text-lg font-medium">{{ selectedTeam?.name }}</h3>
                                        <p class="text-gray-500 mt-2">{{ selectedTeam?.description }}</p>

                                        <div class="mt-4 pt-4 border-t">
                                            <h4 class="text-sm font-medium text-gray-700 mb-2">Estatísticas</h4>
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-gray-500">Total de membros:</span>
                                                <span class="font-medium">{{ getTeamMembers(selectedTeam?.id).length }}</span>
                                            </div>
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-gray-500">Permissões:</span>
                                                <span class="font-medium">{{ selectedTeam?.permissions?.length || 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Abas de Detalhes -->
                                <div class="md:w-2/3">
                                    <div class="border-b mb-4">
                                        <div class="flex">
                                            <button
                                                v-for="tab in teamDetailsTabs"
                                                :key="tab.id"
                                                @click="activeTeamDetailsTab = tab.id"
                                                class="px-4 py-2 text-sm font-medium transition-colors relative"
                                                :class="activeTeamDetailsTab === tab.id ? 'text-primary' : 'text-gray-500 hover:text-gray-700'"
                                            >
                                                {{ tab.name }}
                                                <div
                                                    v-if="activeTeamDetailsTab === tab.id"
                                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary"
                                                ></div>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Membros -->
                                    <div v-if="activeTeamDetailsTab === 'members'" class="space-y-4">
                                        <div class="flex justify-between items-center">
                                            <h4 class="font-medium text-gray-900">Membros do Time</h4>
                                            <button
                                                @click="openTeamMembersModal(selectedTeam)"
                                                class="text-sm text-primary hover:text-primary/80 flex items-center gap-1"
                                            >
                                                <Edit size="14" />
                                                Gerenciar Membros
                                            </button>
                                        </div>

                                        <div class="space-y-2">
                                            <div v-if="getTeamMembers(selectedTeam?.id).length === 0" class="text-sm text-gray-500 italic">
                                                Nenhum membro neste time
                                            </div>
                                            <div
                                                v-for="member in getTeamMembers(selectedTeam?.id)"
                                                :key="member.id"
                                                class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50"
                                            >
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <img
                                                            :src="member.avatar || '/placeholder.svg?height=40&width=40'"
                                                            class="h-10 w-10 rounded-full bg-gray-200"
                                                            alt=""
                                                        />
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ member.name }}</div>
                                                        <div class="text-sm text-gray-500">{{ member.email }}</div>
                                                    </div>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ getRoleName(member.roleId) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Permissões -->
                                    <div v-if="activeTeamDetailsTab === 'permissions'" class="space-y-4">
                                        <div class="flex justify-between items-center">
                                            <h4 class="font-medium text-gray-900">Permissões do Time</h4>
                                            <button
                                                @click="openTeamPermissionsModal(selectedTeam)"
                                                class="text-sm text-primary hover:text-primary/80 flex items-center gap-1"
                                            >
                                                <Edit size="14" />
                                                Gerenciar Permissões
                                            </button>
                                        </div>

                                        <div class="grid gap-4">
                                            <div v-if="!selectedTeam?.permissions || selectedTeam.permissions.length === 0" class="text-sm text-gray-500 italic">
                                                Nenhuma permissão atribuída a este time
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
                              selectedTeam?.permissions?.includes(permission.id)
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
                                    @click="openTeamModal(selectedTeam)"
                                    class="px-4 py-2 border rounded-lg text-primary border-primary hover:bg-primary/5"
                                >
                                    Editar Time
                                </button>
                                <button
                                    @click="showTeamDetailsModal = false"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
                                >
                                    Fechar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Teleport>

            <!-- Modal de Time (Cadastro/Edição) -->
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
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Time *</label>
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

            <!-- Modal de Permissões do Time -->
            <Teleport to="body">
                <div
                    v-if="showTeamPermissionsModal"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                >
                    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                        <div class="p-6 border-b">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    Permissões - {{ selectedTeam?.name }}
                                </h2>
                                <button @click="showTeamPermissionsModal = false" class="text-gray-400 hover:text-gray-500">
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
                                                :id="`team-permission-${permission.id}`"
                                                v-model="selectedTeamPermissions"
                                                :value="permission.id"
                                                type="checkbox"
                                                class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                                            />
                                            <label
                                                :for="`team-permission-${permission.id}`"
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
                                    @click="showTeamPermissionsModal = false"
                                    class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50"
                                >
                                    Cancelar
                                </button>
                                <button
                                    @click="saveTeamPermissions"
                                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90"
                                >
                                    Salvar Permissões
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Teleport>

            <!-- Modal de Membros do Time -->
            <Teleport to="body">
                <div
                    v-if="showTeamMembersModal"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                >
                    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                        <div class="p-6 border-b">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    Membros - {{ selectedTeam?.name }}
                                </h2>
                                <button @click="showTeamMembersModal = false" class="text-gray-400 hover:text-gray-500">
                                    <X size="24" />
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            <!-- Adicionar novo membro -->
                            <div class="mb-6 p-4 border rounded-lg bg-gray-50">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Adicionar Membro</h3>
                                <div class="flex flex-col md:flex-row gap-4">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Usuário</label>
                                        <select
                                            v-model="newMember"
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-primary"
                                        >
                                            <option value="">Selecione um usuário</option>
                                            <option
                                                v-for="user in availableUsers"
                                                :key="user.id"
                                                :value="user.id"
                                            >
                                                {{ user.name }} ({{ user.email }})
                                            </option>
                                        </select>
                                    </div>
                                    <div class="md:self-end">
                                        <button
                                            @click="addMemberToTeam"
                                            :disabled="!newMember"
                                            class="w-full md:w-auto px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            Adicionar ao Time
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Lista de membros atuais -->
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Membros Atuais</h3>
                            <div class="space-y-2">
                                <div v-if="getTeamMembers(selectedTeam?.id).length === 0" class="text-sm text-gray-500 italic">
                                    Nenhum membro neste time
                                </div>
                                <div
                                    v-for="member in getTeamMembers(selectedTeam?.id)"
                                    :key="member.id"
                                    class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50"
                                >
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img
                                                :src="member.avatar || '/placeholder.svg?height=40&width=40'"
                                                class="h-10 w-10 rounded-full bg-gray-200"
                                                alt=""
                                            />
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ member.name }}</div>
                                            <div class="text-sm text-gray-500">{{ member.email }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="text-sm text-gray-500">
                                            {{ getRoleName(member.roleId) }}
                                        </div>
                                        <button
                                            @click="removeMemberFromTeam(member.id)"
                                            class="text-red-600 hover:text-red-500"
                                            title="Remover do time"
                                        >
                                            <UserMinus size="18" />
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                                <button
                                    @click="showTeamMembersModal = false"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
                                >
                                    Fechar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Teleport>
        </div>
    </AppAdminLayout>
</template>
