<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Users, CircleGauge, Workflow, HandCoins, HardHat, Construction, CircleDollarSign, FileChartColumn } from 'lucide-vue-next';
import TeamSwitcher from '@/components/TeamSwitcher.vue';
import { usePage } from '@inertiajs/vue3'

const page = usePage();
const userTeam = page.props.auth.user.teams;
const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: CircleGauge,
    },
    {
        title: 'Clientes',
        href: '/clientes',
        icon: Users,
    },
    {
        title: 'Obras',
        href: '/obras',
        icon: Construction,
    },
    {
        title: 'Orçamentos',
        href: '/orcamentos',
        icon: CircleDollarSign,
    },
    {
        title: 'Serviços',
        href: '/servicos',
        icon: HandCoins,
    },
    {
        title: 'Funcionários',
        href: '/funcionarios',
        icon: HardHat,
    },
    {
        title: 'Relatórios',
        href: '/relatorios',
        icon: FileChartColumn,
    }
];
const teams = userTeam.map((team) => ({
    name: team.name,
}));
// const footerNavItems: NavItem[] = [
//     {
//         title: 'Github Repo',
//         href: 'https://github.com/laravel/vue-starter-kit',
//         icon: Folder,
//     },
//     {
//         title: 'Wiki',
//         href: '#',
//         icon: BookOpen,
//     },
// ];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <TeamSwitcher :teams="teams" />
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
