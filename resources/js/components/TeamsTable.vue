<template>
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="flex justify-between items-center p-4 border-b">
            <h2 class="text-lg font-medium">Lista de Times</h2>
            <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Adicionar Time
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Membros</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Líder</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="team in teams" :key="team.id">
                    <td class="px-6 py-4 whitespace-nowrap">{{ team.name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ team.members }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ team.leader }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                        <button class="text-red-600 hover:text-red-900" @click="deleteTeam(team.id)">Excluir</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

interface Team {
    id: number;
    name: string;
    members: number;
    leader: string;
}

const teams = ref<Team[]>([
    { id: 1, name: 'Desenvolvimento', members: 8, leader: 'João Silva' },
    { id: 2, name: 'Marketing', members: 5, leader: 'Maria Oliveira' },
    { id: 3, name: 'Suporte', members: 12, leader: 'Pedro Santos' },
]);

const deleteTeam = (id: number) => {
    teams.value = teams.value.filter(team => team.id !== id);
};
</script>
