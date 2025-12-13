<script setup lang="ts">
import { Check } from 'lucide-vue-next';
defineProps({
    value: {
        type: String,
        required: false
    }
})
const check = defineModel()

</script>

<template>
    <div class="inline-flex items-center">
        <label class="flex items-center cursor-pointer relative">
            <input
                v-model="check"
                :value="value"
                type="checkbox"
                class="peer h-5 w-5 cursor-pointer transition-all appearance-none rounded shadow hover:shadow-md border border-muted-foreground checked:bg-checked checked:border-checked" />
            <span class="absolute text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                <Check class="w-4 h-4" />
            </span>
        </label>
    </div>
</template>
