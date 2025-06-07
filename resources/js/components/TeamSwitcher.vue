<script setup lang="ts">
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'

import {
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  useSidebar,
} from '@/components/ui/sidebar'
import { ChevronsUpDown } from 'lucide-vue-next'
import { ref } from 'vue'
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { appName } from '@/composables/useAppName.ts';


const props = defineProps<{
  teams: {
    name: string
  }[]
}>()
console.log('Times', props.teams)
const { isMobile } = useSidebar()
const activeTeam = ref(props.teams[0] || { name: 'Sem time'})
</script>

<template>
  <SidebarMenu>
    <SidebarMenuItem>
      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <SidebarMenuButton
            size="lg"
            class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
          >
            <div class="flex aspect-square size-8 items-center justify-center rounded-lg text-sidebar-primary-foreground">
              <AppLogoIcon />
            </div>
            <div class="grid flex-1 text-left text-sm leading-tight">
              <span class="truncate font-medium">
                {{ appName }}
              </span>
              <span class="truncate text-xs">{{ activeTeam.name }}</span>
            </div>
            <ChevronsUpDown v-if="teams.length > 0" class="ml-auto" />
          </SidebarMenuButton>
        </DropdownMenuTrigger>
        <DropdownMenuContent v-if="teams.length > 0"
          class="w-[--reka-dropdown-menu-trigger-width] min-w-56 rounded-lg"
          align="start"
          :side="isMobile ? 'bottom' : 'right'"
          :side-offset="4"
        >
          <DropdownMenuLabel class="text-xs text-muted-foreground">
            Times
          </DropdownMenuLabel>
          <DropdownMenuItem
            v-for="(team) in teams"
            :key="team.name"
            class="gap-2 p-2"
            @click="activeTeam = team"
          >
            {{ team.name }}
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </SidebarMenuItem>
  </SidebarMenu>
</template>
