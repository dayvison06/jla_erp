<template>
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="flex justify-between items-center p-4 border-b">
            <h2 class="text-lg font-medium">Lista de Permissões</h2>
            <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Adicionar Permissão
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargos</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="permission in permissions" :key="permission.id">
                    <td class="px-6 py-4 whitespace-nowrap">{{ permission.name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ permission.description }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ permission.roles }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                        <button class="text-red-600 hover:text-red-900" @click="deletePermission(permission.id)">Excluir</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

interface Permission {
    id: number;
    name: string;
    description: string;
    roles: number;
}

const permissions = ref<Permission[]>([
    { id: 1, name: 'Gerenciar Usuários', description: 'Criar, editar e excluir usuários', roles: 2 },
    { id: 2, name: 'Gerenciar Times', description: 'Criar, editar e excluir times', roles: 2 },
    { id: 3, name: 'Visualizar Relatórios', description: 'Acessar relatórios do sistema', roles: 3 },
]);

const deletePermission = (id: number) => {
    permissions.value = permissions.value.filter(permission => permission.id !== id);
};
</script>
