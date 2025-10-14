1<script setup lang="ts">

import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader, DialogTitle,
    DialogTrigger
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { EditIcon, Trash } from 'lucide-vue-next';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { ref } from 'vue';

const emit = defineEmits<{
    (e: 'continue'): void
    (e: 'destroy'): void
}>()

</script>

<template>
    <Dialog :default-open="true" >
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Cadastro de funcionário em andamento</DialogTitle>
                <DialogDescription>
                    Você possui um cadastro de funcionário em andamento. Deseja continuar editando?
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button type="submit" @click="emit('continue')" class="cursor-pointer">
                    Continuar
                </Button>
                <Button variant="destructive" @click="emit('destroy')" class="cursor-pointer ml-auto">
                    <Trash />
                    Cadastrar novo
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<style scoped>

</style>
