<script setup lang="ts">

import { UploadCloudIcon, UploadIcon } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ref } from 'vue';
import { formData } from '@/composables/useStoreEmployees';

const isDragging = ref(true);
const attachments = defineModel('attachments', { type: Array as () => Array<any>, default: () => [] });

/**
 * Remove um anexo do formulário.
 *
 * @param {number} id - O ID do anexo a ser removido.
 * @returns {void}
 */
const removeAttachment = (id: number) => {
    const index = attachments.value.findIndex((attachment) => attachment.id === id);
    if (index !== -1) {
        // Revoke object URL to prevent memory leaks
        if (formData.attachments[index].url) {
            URL.revokeObjectURL(attachments.value[index].url);
        }
        attachments.value.splice(index, 1);
    }
};

const addFile = (file: File) => {

    // evita duplicados
    if (attachments.value.some(a => a.name === file.name && a.size === fileSize)) {
        return;
    }

    const newAttachment: Attachment = {
        name: file.name,
        type: file.type || 'application/octet-stream',
        size: file.size,
        file: file,
        path: URL.createObjectURL(file),
        created_at: new Date().toISOString(),
    };

    attachments.value.push(newAttachment);
    uploadAttachments();
};

/**
 * Manipula o upload de arquivos a partir do input.
 *
 * @param {Event} event - O evento de change.
 * @returns {void}
 */
const handleFileUpload = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files) {
        Array.from(input.files).forEach(addFile);
        input.value = '';
    }
};

/**
 * Manipula o drop de arquivos na área de drop.
 *
 * @param {DragEvent} event - O evento de drop.
 * @returns {void}
 */
const handleFileDrop = (event: DragEvent) => {
    isDragging.value = false;
    if (!event.dataTransfer?.files) return;
    Array.from(event.dataTransfer.files).forEach(addFile);
};

/**
 * Envia os anexos para o servidor.
 * @returns {void}
 */
function uploadAttachments() {

    router.post(`/funcionarios/upload/${formData.id}`, formData, {
        forceFormData: true,
        onProgress: (event) => {
            if (event?.lengthComputable) {
                progressbar.value = Math.round((event.loaded / event.total) * 100);
            }
        },
        onSuccess: () => {
            progressbar.value = 0;
        },
        onError: () => {
            progressbar.value = 0;
        }
    });
}

</script>

<template>
    <!-- Cabeçalho da seção de anexos -->
    <div class="mb-4 flex items-center justify-between">
        <h3 class="text-lg font-medium">Anexos e Documentos</h3>
    </div>

    <!-- Área para arrastar e soltar arquivos -->
    <div
        class="rounded-lg border-2 border-dashed p-8 text-center transition-all duration-200"
        :class="isDragging ? 'border-foreground bg-card/50' : 'border-muted-foreground hover:border-muted-foreground/70'"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleFileDrop"
    >
        <div class="flex flex-col items-center justify-center">
            <UploadCloudIcon class="mb-3 h-12 w-12 text-gray-400" :class="{ 'text-gray-500': isDragging }" />
            <p class="text-lg font-medium" :class="{ 'text-foreground': isDragging }">Arraste e solte arquivos aqui</p>
            <p class="mt-1 text-sm text-muted-foreground">ou</p>
            <!-- Botão para selecionar arquivos -->
            <Label
                class="btn-primary mt-3 inline-flex cursor-pointer items-center rounded-md border border-transparent px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:outline-none"
            >
                <UploadIcon class="mr-1 h-4 w-4" />
                Selecionar arquivos
                <Input type="file" class="hidden" @change="handleFileUpload" multiple />
            </Label>
        </div>
    </div>
</template>

<style scoped>

</style>
