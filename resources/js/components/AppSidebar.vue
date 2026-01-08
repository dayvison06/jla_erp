<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Home, LayoutDashboard, Cog, HardHat, Construction, FileChartColumn } from 'lucide-vue-next';
import TeamSwitcher from '@/components/TeamSwitcher.vue';
import { usePage } from '@inertiajs/vue3'

const page = usePage();
const permissions = page.props.session.permissions;
const userTeam = page.props.auth.user.teams;
const mainNavItems: NavItem[] = [
    {
        title: 'Início',
        href: '/',
        icon: Home,
    },
    {
        title: 'Funcionários',
        href: '/funcionarios',
        icon: HardHat,
    },
    {
        title: 'Administração',
        icon: Cog,
        subItems: [
            {
                title: 'Cargos',
                href: '/administracao/cargos',
                icon: LayoutDashboard,
            },
        ],
    }
];

const adminNavItem: NavItem[] = [
    {
    title: 'Administração',
    icon: Cog,
    subItems: [
        {
            title: 'Painel Admin',
            href: '/admin',
            icon: LayoutDashboard,
        },
        {
            title: 'Relatórios',
            href: '/admin/relatorios',
            icon: FileChartColumn,
        },
        {
            title: 'Configurações',
            href: '/admin/configuracoes',
            icon: Construction,
        },
    ],
}
];

// Adiciona o menu de administração
mainNavItems.push(...(permissions.includes('administrator') ? adminNavItem : []));

const teams = userTeam.map((team) => ({
    name: team.name,
}));
const footerNavItems: NavItem[] = [

];

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
