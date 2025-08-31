<script setup lang="ts">
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Trash } from 'lucide-vue-next';
import { defineEmits } from 'vue';

const emit = defineEmits(['remove']);

defineProps<{
    attachment: {
        id: number;
        name: string;
    };
}>()
console.log(name);
</script>

<template>
    <Dialog>
        <DialogTrigger as-child>
            <Button variant="outline">
                Edit Profile
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Edit profile</DialogTitle>
                <DialogDescription>
                    Make changes to your profile here. Click save when you're done.
                </DialogDescription>
            </DialogHeader>
            <div class="grid gap-4 py-4">
                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="name" class="text-right">
                        Name
                    </Label>
                    <Input :default-value="attachment.name" class="col-span-3" />
                </div>
            </div>
            <DialogFooter>
                <Button type="submit" class="cursor-pointer">
                    Salvar
                </Button>
                <Button @click="emit('remove', attachment.id)" variant="destructive" class="cursor-pointer ml-auto">
                    <Trash />
                    Excluir
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<style scoped>

</style>
