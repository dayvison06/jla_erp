<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub, SidebarMenuSubButton, SidebarMenuSubItem, useSidebar
} from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { ChevronRight, ChevronsUpDown, Plus, EllipsisVertical } from 'lucide-vue-next';
import { Link, usePage } from '@inertiajs/vue3';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import {
    DropdownMenu,
    DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel,
    DropdownMenuSeparator, DropdownMenuShortcut,
    DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';
import type { Component } from 'vue';

defineProps<{
    items: NavItem[];
}>();

const { isMobile } = useSidebar()
const page = usePage<SharedData>();
</script>

<template>
    <SidebarGroup>
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu v-for="item in items" :key="item.title">
            <SidebarMenuItem>
<!--                Menu sem sub-items-->
                <SidebarMenuButton as-child v-if="!item.subItems" :tooltip="item.title">
                    <Link :href="item.href">
                        <component :is="item.icon" v-if="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
<!--                Menu com sub-items-->
                <DropdownMenu v-if="item.subItems">
                    <DropdownMenuTrigger as-child>
                        <SidebarMenuButton :tooltip="item.title">
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
                        <DropdownMenuSeparator />
                        <DropdownMenuItem class="gap-2 p-2">
                            <div class="flex size-6 items-center justify-center rounded-md border bg-transparent">
                                <Plus class="size-4" />
                            </div>
                            <div class="font-medium text-muted-foreground">
                                Add team
                            </div>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
