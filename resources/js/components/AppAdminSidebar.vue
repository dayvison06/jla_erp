<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { UserCog , LayoutGrid, User, Users, UserCircleIcon, KeyIcon, Workflow } from 'lucide-vue-next';
import TeamSwitcher from '@/components/TeamSwitcher.vue';
import { usePage } from '@inertiajs/vue3'

const page = usePage();
const userTeam = page.props.auth.user.teams;
console.log(page.props.auth);
const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/admin',
        icon: LayoutGrid,
    },
    {
        title: 'Administração',
        icon: UserCog,
        subItems: [
            {
                title: 'Usuários',
                href: '/admin/users',
                icon: User,
            },
            {
                title: 'Times',
                href:  '/admin/teams',
                icon: Users,
            },
            {
                title: 'Cargos',
                href: '/admin/roles',
                icon: UserCircleIcon,
            },
            {
                title: 'Permissões',
                href: '/admin/permissions',
                icon: KeyIcon,
            }
        ],
    },
    {
        title: 'Integrações',
        href: '/admin/integrations',
        icon: Workflow,
    }
];
const teams = userTeam.map((team) => ({
    name: team.name,
}));
// const footerNavItems: NavItem[] = [
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
