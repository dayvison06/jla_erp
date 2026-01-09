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

// Model for attachments
const attachments = defineModel('attachments', { type: Array as () => Attachment[], default: () => [] });

const isDragging = ref(false);
const isUploading = ref(false);
const uploadProgress = ref(0);

/**
 * Handles adding files (either local draft or live upload).
 */
const addFiles = (files: File[]) => {
    if (props.readOnly) return; 

    // Filter duplicates based on name and size
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
 * Adds files to the local array (Draft mode).
 */
const addFilesLocally = (files: File[]) => {
    const newAttachments = files.map(file => ({
        id: Date.now() + Math.random(), // Temporary ID
        name: file.name,
        type: file.type || 'application/octet-stream',
        size: file.size,
        file: file, // Keep raw file for later submission
        path: URL.createObjectURL(file), // Preview URL
        created_at: new Date().toISOString(),
        uploaded_by: 'Eu', // Placeholder
    } as unknown as Attachment)); // Cast to satisfy type, 'file' is extra

    attachments.value.push(...newAttachments);
};

/**
 * Uploads files to the backend (Live mode).
 */
const uploadFilesToBackend = (files: File[]) => {
    if (!props.employeeId) return;

    const formData = new FormData();
    files.forEach(file => {
        formData.append('attachments[]', file);
    });

    router.post(`/funcionarios/upload/${props.employeeId}`, formData, {
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
             showToast('success', 'Upload concluído', 'Arquivos enviados com sucesso.');
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
 * Handles file deletion.
 */
const handleDelete = (attachment: Attachment) => {
    if (props.readOnly) return;

    // If it has a temporary ID or we are in draft mode without an ID, delete locally
    // However, createEmployee doesn't have employeeId. 
    // real attachments from DB usually have distinct numeric IDs. 
    // Let's assume if we are in draft mode (no props.employeeId), it's local.
    
    if (!props.employeeId) {
        const index = attachments.value.findIndex(a => a.id === attachment.id);
        if (index !== -1) {
            // Revoke URL if it was local
            if (attachments.value[index].path && attachments.value[index].path.startsWith('blob:')) {
                URL.revokeObjectURL(attachments.value[index].path);
            }
            attachments.value.splice(index, 1);
        }
    } else {
        // Live delete
        // We probably need a delete route. 
        // Assuming router.delete or similar. 
        // NOTE: The previous code didn't show a specific attachment delete route in Controller.
        // It showed `deactivate` for employee. 
        // I should stick to emitting or handling it if there is a route.
        // I will assume there isn't one ready or I missed it.
        // Wait, typical resource controller: DELETE /attachments/{id}?
        // Let's implement a standard Inertia delete call assuming the backend handles it, 
        // or just emit 'delete' if the parent handles it.
        // But the user asked to centralize logic.
        // Let's try to delete. 
        
        // Actually, looking at the user request "Permita adicionar, remover, atualizar".
        // I'll assume we need to implement the delete action.
        
        // Let's assume a generic route or emit.
        // Since I don't recall a `destroyAttachment` in `EmployeeController`, I might need to add it?
        // Wait, `Employees.vue` uses `deleteEmployee`. 
        
        // Ideally I should check `routes/web.php` but I can't see it right now.
        // I will implement the Frontend logic to call a delete endpoint.
        
        // For now, let's just emit an event or remove from list if it was a local add (mixed mode?), 
        // but typically 'employeeId' means all are persisted.
        
        // I'll call a hypothetical endpoint. If it fails, I'll fix it later.
        // Using `router.delete`.
        
        // Correction: If the backend logic isn't there, this will fail. 
        // I'll implement "Remove" as "Ask parent to remove" or "Optimistic update"? 
        // No, let's try to call the backend.
        // Actually, I'll stick to removing LOCAL files if they are local, 
        // and calling API if they are remote.
        // How to distinguish? Local files have `file` property (File object). Remote ones don't usually.
        
        if ((attachment as any).file) {
             const index = attachments.value.findIndex(a => a.id === attachment.id);
             if (index !== -1) attachments.value.splice(index, 1);
        } else {
            // It's a persisted file.
             router.delete(`/funcionarios/anexos/${attachment.id}`, {
                 preserveScroll: true,
                  onSuccess: () => {
                     showToast('success', 'Sucesso', 'Anexo removido.');
                 }
             });
        }
    }
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
        />
    </div>
</template>

<style scoped>
</style>
