<template>
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="flex justify-between items-center p-4 border-b">
            <h2 class="text-lg font-medium">Lista de Cargos</h2>
            <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Adicionar Cargo
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissões</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuários</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="role in roles" :key="role.id">
                    <td class="px-6 py-4 whitespace-nowrap">{{ role.name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ role.permissions }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ role.users }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                        <button class="text-red-600 hover:text-red-900" @click="deleteRole(role.id)">Excluir</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

interface Role {
    id: number;
    name: string;
    permissions: number;
    users: number;
}

const roles = ref<Role[]>([
    { id: 1, name: 'Administrador', permissions: 15, users: 3 },
    { id: 2, name: 'Gerente', permissions: 10, users: 5 },
    { id: 3, name: 'Analista', permissions: 7, users: 8 },
]);

const deleteRole = (id: number) => {
    roles.value = roles.value.filter(role => role.id !== id);
};
</script>
