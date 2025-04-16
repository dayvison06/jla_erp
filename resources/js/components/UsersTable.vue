<template>
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="flex justify-between items-center p-4 border-b">
            <h2 class="text-lg font-medium">Lista de Usuários</h2>
            <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Adicionar Usuário
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="user in users" :key="user.id">
                    <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ user.role }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ user.team }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                        <button class="text-red-600 hover:text-red-900" @click="deleteUser(user.id)">Excluir</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    team: string;
}

const users = ref<User[]>([
    { id: 1, name: 'João Silva', email: 'joao@example.com', role: 'Administrador', team: 'Desenvolvimento' },
    { id: 2, name: 'Maria Oliveira', email: 'maria@example.com', role: 'Gerente', team: 'Marketing' },
    { id: 3, name: 'Pedro Santos', email: 'pedro@example.com', role: 'Analista', team: 'Suporte' },
]);

const deleteUser = (id: number) => {
    users.value = users.value.filter(user => user.id !== id);
};
</script>
