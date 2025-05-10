<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar
} from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { EllipsisVertical } from 'lucide-vue-next';
import { Link, usePage } from '@inertiajs/vue3';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';

defineProps<{
    items: NavItem[];
}>();

const { isMobile } = useSidebar();
const page = usePage<SharedData>();
</script>

<template>
    <SidebarGroup>
        <SidebarGroupLabel>Menu</SidebarGroupLabel>
        <SidebarMenu v-for="item in items" :key="item.title">
            <SidebarMenuItem>
<!--                Menu sem sub-items-->
                <SidebarMenuButton class="mb-2" as-child v-if="!item.subItems" :tooltip="item.title" :is-active="item.href === page.url">
                    <Link :href="item.href">
                        <component :is="item.icon" class="!size-5" v-if="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
<!--                Menu com sub-items-->
                <DropdownMenu v-if="item.subItems">
                    <DropdownMenuTrigger as-child>
                        <SidebarMenuButton class="mb-2" :tooltip="item.title">
                            <component :is="item.icon" v-if="item.icon" />
                            <span>{{ item.title }}</span>
                            <EllipsisVertical v-if="item.subItems" class="ml-auto"/>
                        </SidebarMenuButton>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent
                        class="w-[--reka-dropdown-menu-trigger-width] min-w-56 rounded-lg"
                        align="start"
                        :side="isMobile ? 'bottom' : 'right'"
                        :side-offset="4"
                    >
                        <DropdownMenuLabel class="text-xs text-muted-foreground">
                            {{ item.title }}
                        </DropdownMenuLabel>
                        <DropdownMenuItem
                            v-for="subItem in item.subItems"
                            :key="subItem.title"
                            as-child
                            :default-open="item.isActive"
                            class="group/collapsible"
                        >
                            <Link :href="subItem.href">
                                <component :is="subItem.icon" />
                                <span>{{ subItem.title}}</span>
                            </Link>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
