<script setup lang="ts">
import AppLayout from "@/layouts/AppLayout.vue";
import { Head, usePage } from '@inertiajs/vue3';
import { ref, reactive, onMounted, computed } from 'vue';
import axios from 'axios';
import {
    PlusIcon,
    Search,
    HardHat,
    Calendar,
    MapPin,
    MoreVertical,
    Edit,
    Trash
} from 'lucide-vue-next';

// Shadcn Components
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardHeader, CardTitle, CardContent, CardDescription, CardFooter } from '@/components/ui/card';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
    SheetFooter,
    SheetClose
} from '@/components/ui/sheet';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { showToast } from '@/composables/useToast';

// Interfaces matches Backend Model
interface Work {
    id?: number;
    name: string;
    client?: string;
    technical_manager?: string;
    start_date?: string;
    expected_end_date?: string;
    status: string; // 'pending', 'active', 'completed', 'canceled'
    type?: string;
    total_area?: string | number;
    address?: string;
    description?: string;
    created_at?: string;
}

// State
const works = ref<Work[]>([]);
const searchQuery = ref('');
const isSheetOpen = ref(false);
const isEditing = ref(false);
const isLoading = ref(false);

const defaultWork: Work = {
    name: '',
    client: '',
    technical_manager: '',
    start_date: '',
    expected_end_date: '',
    status: 'pending',
    type: 'residential',
    total_area: '',
    address: '',
    description: ''
};

const formData = reactive<Work>({ ...defaultWork });

// Computed
const filteredWorks = computed(() => {
    if (!searchQuery.value) return works.value;
    const query = searchQuery.value.toLowerCase();
    return works.value.filter(work =>
        work.name.toLowerCase().includes(query) ||
        work.client?.toLowerCase().includes(query)
    );
});

// Methods
const fetchWorks = async () => {
    try {
        const response = await axios.get('/obras/lista');
        works.value = response.data;
    } catch (error) {
        console.error('Erro ao buscar obras:', error);
        showToast('error', 'Falha ao carregar obras.');
    }
};

const openCreateModal = () => {
    isEditing.value = false;
    Object.assign(formData, defaultWork);
    isSheetOpen.value = true;
};

const openEditModal = (work: Work) => {
    isEditing.value = true;
    Object.assign(formData, work);
    isSheetOpen.value = true;
};

const saveWork = async () => {
    isLoading.value = true;
    try {
        if (isEditing.value && formData.id) {
            await axios.put(`/obras/${formData.id}`, formData);
            showToast('success', 'Obra atualizada com sucesso!');
        } else {
            await axios.post('/obras', formData);
            showToast('success', 'Obra cadastrada com sucesso!');
        }
        await fetchWorks();
        isSheetOpen.value = false;
    } catch (error) {
        console.error('Erro ao salvar obra:', error);
        showToast('error', 'Falha ao salvar obra. Verifique os dados.');
    } finally {
        isLoading.value = false;
    }
};

const deleteWork = async (id: number) => {
    if (!confirm('Tem certeza que deseja excluir esta obra?')) return;

    try {
        await axios.delete(`/obras/${id}`);
        showToast('success', 'Obra excluída com sucesso!');
        await fetchWorks();
    } catch (error) {
        console.error('Erro ao excluir obra:', error);
        showToast('error', 'Falha ao excluir obra.');
    }
};

const getStatusBadgeClass = (status: string) => {
    switch (status) {
        case 'active': return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300';
        case 'completed': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300';
        case 'canceled': return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300';
        default: return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
    }
};

const getStatusLabel = (status: string) => {
    const labels: Record<string, string> = {
        'pending': 'Pendente',
        'active': 'Em Andamento',
        'completed': 'Concluído',
        'canceled': 'Cancelado'
    };
    return labels[status] || status;
};

const formatDate = (dateString?: string) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('pt-BR');
};

onMounted(() => {
    fetchWorks();
});
</script>

<template>
    <Head title="Obras" />
    <AppLayout :breadcrumbs="[{ title: 'Obras', href: '/obras' }]">
        <main class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">Gerenciamento de Obras</h1>
                    <p class="text-muted-foreground mt-1">Acompanhe e gerencie todos os projetos em execução.</p>
                </div>
                <Button @click="openCreateModal" class="btn-primary">
                    <PlusIcon class="w-4 h-4 mr-2" />
                    Nova Obra
                </Button>
            </div>

            <!-- Filters -->
            <div class="mb-6">
                <div class="relative max-w-sm">
                    <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Buscar obras por nome ou cliente..."
                        class="pl-8 bg-background"
                    />
                </div>
            </div>

            <!-- Grid -->
            <div v-if="filteredWorks.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card v-for="work in filteredWorks" :key="work.id" class="flex flex-col h-full hover:shadow-md transition-shadow">
                    <CardHeader class="pb-3">
                        <div class="flex justify-between items-start">
                            <div class="space-y-1">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                    :class="getStatusBadgeClass(work.status)"
                                >
                                    {{ getStatusLabel(work.status) }}
                                </span>
                                <CardTitle class="text-xl mt-2 line-clamp-1" :title="work.name">{{ work.name }}</CardTitle>
                                <CardDescription class="line-clamp-1">{{ work.client || 'Cliente não informado' }}</CardDescription>
                            </div>
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="ghost" class="h-8 w-8 p-0">
                                        <span class="sr-only">Menu</span>
                                        <MoreVertical class="h-4 w-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuItem @click="openEditModal(work)">
                                        <Edit class="mr-2 h-4 w-4" />
                                        Editar
                                    </DropdownMenuItem>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem @click="deleteWork(work.id!)" class="text-destructive focus:text-destructive">
                                        <Trash class="mr-2 h-4 w-4" />
                                        Excluir
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>
                    </CardHeader>
                    <CardContent class="flex-1 space-y-4">
                         <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="flex items-center text-muted-foreground">
                                <Calendar class="mr-2 h-4 w-4" />
                                <span>Início: {{ formatDate(work.start_date) }}</span>
                            </div>
                             <div class="flex items-center text-muted-foreground">
                                 <HardHat class="mr-2 h-4 w-4" />
                                 <span class="capitalize">{{ work.type }}</span>
                             </div>
                            <div class="flex items-center text-muted-foreground col-span-2">
                                <MapPin class="mr-2 h-4 w-4" />
                                <span class="truncate">{{ work.address || 'Endereço não informado' }}</span>
                            </div>
                         </div>
                        <p v-if="work.description" class="text-sm text-muted-foreground line-clamp-2 mt-2">
                            {{ work.description }}
                        </p>
                    </CardContent>
                    <CardFooter class="pt-4 border-t bg-muted/20">
                        <div class="w-full flex justify-between text-xs text-muted-foreground">
                            <span>Resp: {{ work.technical_manager || '-' }}</span>
                            <span v-if="work.total_area">{{ work.total_area }} m²</span>
                        </div>
                    </CardFooter>
                </Card>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                 <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-muted mb-4">
                    <HardHat class="h-8 w-8 text-muted-foreground" />
                </div>
                <h3 class="text-lg font-medium text-foreground">Nenhuma obra encontrada</h3>
                <p class="text-muted-foreground mt-2">
                    {{ searchQuery ? 'Tente ajustar sua busca.' : 'Comece cadastrando sua primeira obra.' }}
                </p>
                <Button v-if="!searchQuery" @click="openCreateModal" class="mt-4 btn-primary">
                    Cadastrar Obra
                </Button>
            </div>

            <!-- Create/Edit Form Sheet -->
            <Sheet v-model:open="isSheetOpen">
                <SheetContent class="w-full sm:max-w-xl overflow-y-auto">
                    <SheetHeader>
                        <SheetTitle>{{ isEditing ? 'Editar Obra' : 'Nova Obra' }}</SheetTitle>
                        <SheetDescription>
                            {{ isEditing ? 'Atualize os dados da obra.' : 'Preencha os dados básicos para iniciar um novo projeto.' }}
                        </SheetDescription>
                    </SheetHeader>

                    <form @submit.prevent="saveWork" class="space-y-6 mt-6 p-4">
                        <div class="space-y-4">
                            <!-- Nome -->
                            <div class="grid gap-2">
                                <Label for="name">Nome da Obra *</Label>
                                <Input id="name" v-model="formData.name" required placeholder="Ex: Edifício Aurora" />
                            </div>

                            <!-- Cliente e Responsável -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="grid gap-2">
                                    <Label for="client">Cliente</Label>
                                    <Input id="client" v-model="formData.client" placeholder="Nome do cliente" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="technical_manager">Resp. Técnico</Label>
                                    <Input id="technical_manager" v-model="formData.technical_manager" placeholder="Eng. Responsável" />
                                </div>
                            </div>

                             <!-- Datas -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="grid gap-2">
                                    <Label for="start_date">Data de Início</Label>
                                    <Input id="start_date" type="date" v-model="formData.start_date" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="expected_end_date">Previsão Término</Label>
                                    <Input id="expected_end_date" type="date" v-model="formData.expected_end_date" />
                                </div>
                            </div>

                            <!-- Status e Tipo -->
                             <div class="grid grid-cols-2 gap-4">
                                <div class="grid gap-2">
                                    <Label for="status">Status *</Label>
                                    <Select v-model="formData.status" required>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecione" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="pending">Pendente</SelectItem>
                                            <SelectItem value="active">Em Andamento</SelectItem>
                                            <SelectItem value="completed">Concluída</SelectItem>
                                            <SelectItem value="canceled">Cancelada</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div class="grid gap-2">
                                    <Label for="type">Tipo</Label>
                                    <Select v-model="formData.type">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecione" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="residential">Residencial</SelectItem>
                                            <SelectItem value="commercial">Comercial</SelectItem>
                                            <SelectItem value="industrial">Industrial</SelectItem>
                                            <SelectItem value="other">Outro</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>

                            <!-- Area e Endereço -->
                             <div class="grid gap-2">
                                <Label for="total_area">Área Total (m²)</Label>
                                <Input id="total_area" type="number" step="0.01" v-model="formData.total_area" placeholder="0.00" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="address">Endereço</Label>
                                <Textarea id="address" v-model="formData.address" rows="2" placeholder="Endereço completo da obra" />
                            </div>

                             <div class="grid gap-2">
                                <Label for="description">Descrição</Label>
                                <Textarea id="description" v-model="formData.description" rows="3" placeholder="Detalhes adicionais sobre o projeto" />
                            </div>
                        </div>

                        <SheetFooter>
                            <SheetClose as-child>
                                <Button type="button" variant="outline">Cancelar</Button>
                            </SheetClose>
                            <Button type="submit" :disabled="isLoading" class="btn-primary">
                                {{ isLoading ? 'Salvando...' : (isEditing ? 'Atualizar' : 'Cadastrar') }}
                            </Button>
                        </SheetFooter>
                    </form>
                </SheetContent>
            </Sheet>
        </main>
    </AppLayout>
</template>
