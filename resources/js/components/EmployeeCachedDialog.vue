<script setup lang="ts">

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

defineProps<{
    isDialogOpen: boolean;
}>()

</script>

<template>
    <Dialog :default-open="true" >
        <DialogTrigger as-child>
            <Button variant="outline">
                <EditIcon/>
                Editar
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Rascunho</DialogTitle>
                <DialogDescription>
                    Você pode editar o nome e definir uma data de vencimento para este anexo, permitindo receber notificação quando estiver proximo de expirar.
                </DialogDescription>
            </DialogHeader>
            <div class="grid gap-4 py-4">
                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="name" class="text-right">
                        Nome
                    </Label>
                    <Input :default-value="teste" class="col-span-3" />
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="name" class="text-right">
                        Vencimento
                    </Label>
                    <Input :default-value="''" type="datetime-local" placeholder="" class="col-span-3" />
                </div>
            </div>
            <DialogFooter>
                <Button type="submit" class="cursor-pointer">
                    Salvar
                </Button>
                <Button variant="destructive" class="cursor-pointer ml-auto">
                    <Trash />
                    Excluir
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<style scoped>

</style>
