<script setup lang="ts">
import { ref, computed } from 'vue';
import { UploadCloudIcon, UploadIcon, Loader2 } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { router } from '@inertiajs/vue3';
import { showToast } from '@/composables/useToast';
import AttachmentsDisplay from '@/components/AttachmentsDisplay.vue';
import type { Attachment } from '@/types/Employees';

const props = defineProps<{
    employeeId?: number;
    readOnly?: boolean;
}>();

const emit = defineEmits<{
    (e: 'refreshAttachments'): void;
}>();

// Model for attachments
const attachments = defineModel('attachments', { type: Array as () => Attachment[], default: () => [] });

const isDragging = ref(false);
const isUploading = ref(false);
const uploadProgress = ref(0);

/**
 * Gerencia a adição de arquivos (seja rascunho local ou upload direto).
 */
const addFiles = (files: File[]) => {
    if (props.readOnly) return;

    // Filtra duplicatas baseado em nome e tamanho
    const newFiles = files.filter(file =>
        !attachments.value.some(a => a.name === file.name && Number(a.size) === file.size)
    );

    if (newFiles.length === 0) return;

    if (props.employeeId) {
        uploadFilesToBackend(newFiles);
    } else {
        addFilesLocally(newFiles);
    }
};

/**
 * Adiciona arquivos ao array local (Modo Rascunho).
 */
const addFilesLocally = (files: File[]) => {
    const newAttachments = files.map(file => ({
        id: Date.now() + Math.random(), // ID Temporário
        name: file.name,
        type: file.type || 'application/octet-stream',
        size: file.size,
        file: file, // Mantém arquivo raw para envio posterior
        path: URL.createObjectURL(file), // URL de visualização
        created_at: new Date().toISOString(),
        uploaded_by: 'Eu', // Placeholder
    } as unknown as Attachment)); // Cast para satisfazer tipo

    attachments.value.push(...newAttachments);
};

/**
 * Envia arquivos para o backend (Modo Tempo Real).
 */
const uploadFilesToBackend = (files: File[]) => {
    console.log('Uploading files to backend:', files);
    if (!props.employeeId) return;

    const formData = new FormData();
    files.forEach(file => {
        formData.append('attachments[]', file);
    });

    router.post(route('employees.upload', props.employeeId), formData, {
        forceFormData: true,
        preserveScroll: true,
        onStart: () => {
            isUploading.value = true;
            uploadProgress.value = 0;
        },
        onProgress: (event) => {
            if (event?.lengthComputable) {
                uploadProgress.value = Math.round((event.loaded / event.total) * 100);
            }
        },
        onSuccess: () => {
            emit('refreshAttachments');
        },
        onError: () => {
            showToast('error', 'Erro', 'Falha ao enviar arquivos.');
        },
        onFinish: () => {
            isUploading.value = false;
            uploadProgress.value = 0;
        }
    });
};

/**
 * Remove um anexo (local ou remoto).
 */
const handleDelete = (attachment: Attachment) => {
    if (props.readOnly) return;

    // Se não tiver ID de funcionário verificado, trata como rascunho.
    if (!props.employeeId) {
        const index = attachments.value.findIndex(a => a.id === attachment.id);
        if (index !== -1) {
            // Revoga URL se for local (blob)
            if (attachments.value[index].path && attachments.value[index].path.startsWith('blob:')) {
                URL.revokeObjectURL(attachments.value[index].path);
            }
            attachments.value.splice(index, 1);
        }
    } else {
        // Exclusão remota através da API
        if (confirm(`Tem certeza que deseja remover o anexo "${attachment.name}"?`)) {
             try {
                 router.delete(route('employees.attachment.destroy', attachment.id), {
                    preserveScroll: true,
                    onSuccess: () => {
                        showToast('success', 'Sucesso', 'Anexo removido do sistema.');
                        // Atualiza lista local
                        const index = attachments.value.findIndex(a => a.id === attachment.id);
                        if (index !== -1) attachments.value.splice(index, 1);
                    },
                    onError: (errors) => {
                        console.error('Erro ao excluir anexo:', errors);
                        showToast('error', 'Erro', 'Falha ao remover o anexo.');
                    }
                 });
             } catch (e) {
                 // Fallback caso 'route' helper não esteja disponível ou erro na chamada
                 router.delete(`/funcionarios/anexos/${attachment.id}`, {
                     preserveScroll: true,
                     onSuccess: () => {
                         showToast('success', 'Sucesso', 'Anexo removido do sistema.');
                         const index = attachments.value.findIndex(a => a.id === attachment.id);
                         if (index !== -1) attachments.value.splice(index, 1);
                     },
                     onError: () => {
                         showToast('error', 'Erro', 'Falha ao remover o anexo.');
                     }
                 });
             }
        }
    }
};

/**
 * Remove um anexo (local ou remoto).
 */
const handleDeleteSelected = (ids: number[]) => {
    if (props.readOnly || ids.length === 0) return;

    if (!props.employeeId) {
        // Rascunho - remove localmente
        ids.forEach(id => {
            const index = attachments.value.findIndex(a => a.id === id);
            if (index !== -1) {
                if (attachments.value[index].path && attachments.value[index].path.startsWith('blob:')) {
                    URL.revokeObjectURL(attachments.value[index].path);
                }
                attachments.value.splice(index, 1);
            }
        });
    } else {
        // Remoto
         if (confirm(`Tem certeza que deseja remover ${ids.length} anexo(s)?`)) {
            // Usamos POST com _method DELETE para garantir que o corpo da requisição seja enviado corretamente
            router.post(route('employees.attachments.destroy_many'), {
                _method: 'DELETE',
                ids: ids
            }, {
                preserveScroll: true,
                onSuccess: () => {
                   showToast('success', 'Sucesso', 'Anexos removidos com sucesso.');
                   emit('refreshAttachments');
                    // Remove localmente para refletir imediatamente
                   ids.forEach(id => {
                       const index = attachments.value.findIndex(a => a.id === id);
                       if (index !== -1) attachments.value.splice(index, 1);
                   });
                },
                onError: (errors) => {
                    console.error('Erro ao excluir múltiplos anexos:', errors);
                    showToast('error', 'Erro', 'Falha ao remover anexos selecionados.');
                }
            });
         }
    }
};

/**
 * Exporta múltiplos anexos (apenas remoto).
 */
const handleExportSelected = (ids: number[]) => {
    if (props.readOnly || ids.length === 0) return;

    if (!props.employeeId) {
        showToast('error', 'Erro', 'Salve o funcionário antes de exportar anexos.');
        return;
    }

    // Usando GET via window.open para facilitar o download
    const url = route('employees.attachments.export');
    const queryString = ids.map(id => `ids[]=${id}`).join('&');
    const fullUrl = `${url}?${queryString}`;
    
    window.open(fullUrl, '_blank');
};

const handleFileUpload = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files) {
        addFiles(Array.from(input.files));
        input.value = '';
    }
};

const handleFileDrop = (event: DragEvent) => {
    isDragging.value = false;
    if (!event.dataTransfer?.files) return;
    addFiles(Array.from(event.dataTransfer.files));
};

</script>

<template>
    <div class="space-y-6">
        <!-- Cabeçalho da seção de anexos -->
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium">Anexos e Documentos</h3>
            <span v-if="isUploading" class="text-sm text-muted-foreground flex items-center gap-2">
                <Loader2 class="h-4 w-4 animate-spin" />
                Enviando {{ uploadProgress }}%
            </span>
        </div>

        <!-- Área para arrastar e soltar arquivos -->
        <div
            v-if="!readOnly"
            class="relative rounded-lg border-2 border-dashed p-8 text-center transition-all duration-200"
            :class="[
                isDragging ? 'border-primary bg-primary/5' : 'border-muted-foreground/25 hover:border-primary/50',
                isUploading ? 'opacity-50 pointer-events-none' : ''
            ]"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleFileDrop"
        >
            <div class="flex flex-col items-center justify-center">
                <div class="mb-4 rounded-full bg-muted p-4">
                     <UploadCloudIcon class="h-8 w-8 text-muted-foreground" />
                </div>
                <p class="text-lg font-medium text-foreground">
                    <span class="text-primary hover:underline cursor-pointer">
                         <label class="cursor-pointer">
                            Clique para selecionar
                            <Input type="file" class="hidden" @change="handleFileUpload" multiple />
                        </label>
                    </span>
                    ou arraste arquivos e solte aqui
                </p>
                <p class="mt-1 text-sm text-muted-foreground">
                    Suporta imagens, PDFs e documentos de texto até 10MB
                </p>
            </div>

            <!-- Progress Overlay -->
             <div v-if="isUploading" class="absolute inset-0 z-10 flex items-center justify-center bg-background/50 backdrop-blur-[1px] transition-all">
                <div class="w-1/2 max-w-xs space-y-2 text-center">
                     <div class="h-2 w-full overflow-hidden rounded-full bg-secondary">
                        <div class="h-full bg-primary transition-all duration-300 ease-out" :style="{ width: `${uploadProgress}%` }"></div>
                     </div>
                     <p class="text-xs font-medium text-foreground">Enviando arquivos...</p>
                </div>
            </div>
        </div>

        <!-- Lista de anexos -->
        <AttachmentsDisplay
            :allAttachments="attachments"
            @delete="handleDelete"
            @delete-selected="handleDeleteSelected"
            @export-selected="handleExportSelected"
        />
    </div>
</template>

<style scoped>
</style>
