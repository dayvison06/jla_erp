<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';
import {
    Users,
    UserPlus,
    HardHat,
    Briefcase,
    Settings,
    LayoutDashboard
} from 'lucide-vue-next';
import { User } from '@/types';
import {
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardContent
} from '@/components/ui/card';

const page = usePage()
const user: User = page.props.auth.user

interface QuickAction {
    title: string;
    description: string;
    icon: any;
    link: string;
    variant?: 'default' | 'outline' | 'secondary' | 'ghost' | 'link';
}

// Ações rápidas mapeadas para os módulos do sistema
const quickActions = ref<QuickAction[]>([
    {
        title: 'Funcionários',
        description: 'Gerencie o quadro de colaboradores, documentos e cadastros.',
        icon: Users,
        link: '/funcionarios'
    },
    {
        title: 'Novo Funcionário',
        description: 'Inicie o processo de admissão de um novo colaborador.',
        icon: UserPlus,
        link: '/funcionarios/criar'
    },
    {
        title: 'Obras',
        description: 'Acompanhe o andamento das obras e projetos em execução.',
        icon: HardHat,
        link: '/obras'
    },
    {
        title: 'Cargos e Funções',
        description: 'Administre a estrutura de cargos e salários da empresa.',
        icon: Briefcase,
        link: '/administracao/cargos'
    },
]);

const getGreeting = () => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Bom dia';
    if (hour < 18) return 'Boa tarde';
    return 'Boa noite';
};

const breadcrumbs = [
    { title: 'Home', href: '/' },
];
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="container mx-auto px-4 py-8">
            <!-- Header com saudação -->
            <header class="mb-10">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-primary/10 rounded-lg">
                        <LayoutDashboard class="w-6 h-6 text-primary" />
                    </div>
                    <span class="text-sm font-medium text-muted-foreground uppercase tracking-wider">Visão Geral</span>
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-foreground">
                    {{ getGreeting() }}, <span class="text-primary">{{ user.name }}</span>
                </h1>
                <p class="text-muted-foreground mt-2">
                    Bem-vindo ao JLA ERP. Aqui está o resumo das suas atividades e atalhos rápidos.
                </p>
            </header>

            <!-- Ações Rápidas / Módulos -->
            <section>
                <h2 class="text-lg font-semibold text-foreground mb-6 flex items-center gap-2">
                    Acesso Rápido
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <Link
                        v-for="(action, index) in quickActions"
                        :key="index"
                        :href="action.link"
                        class="block group outline-none"
                    >
                        <Card class="h-full transition-all duration-200 hover:shadow-md hover:border-primary/50 group-focus-visible:ring-2 group-focus-visible:ring-ring">
                            <CardHeader>
                                <div class="mb-4 inline-flex items-center justify-center w-10 h-10 rounded-lg bg-secondary text-secondary-foreground group-hover:bg-primary group-hover:text-primary-foreground transition-colors duration-200">
                                    <component :is="action.icon" class="w-5 h-5" />
                                </div>
                                <CardTitle class="text-base font-semibold group-hover:text-primary transition-colors">
                                    {{ action.title }}
                                </CardTitle>
                                <CardDescription>
                                    {{ action.description }}
                                </CardDescription>
                            </CardHeader>
                        </Card>
                    </Link>
                </div>
            </section>

            <!-- Placeholder para Dashboard Futuro -->
            <section class="mt-10">
                 <div class="bg-muted/10 border-2 border-dashed border-muted rounded-xl p-8 text-center">
                    <h3 class="text-sm font-medium text-muted-foreground">
                        Painel de Indicadores em desenvolvimento
                    </h3>
                    <p class="text-xs text-muted-foreground/70 mt-1">
                        Em breve você verá estatísticas e gráficos importantes aqui.
                    </p>
                </div>
            </section>
        </main>
    </AppLayout>
</template>
