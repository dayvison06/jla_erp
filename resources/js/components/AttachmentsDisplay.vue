<script setup lang="ts">
import { ref, watch } from 'vue';
import { FileText, Download, Trash2, File, Image, Video, Music } from 'lucide-vue-next'
import type { Attachment } from '@/types/Employees'

const props = defineProps<{ allAttachments: Attachment[] }>()
const emit = defineEmits(['delete', 'delete-selected', 'export-selected'])

// Keep local reference in sync with props
const attachments = ref<Attachment[]>([])
watch(() => props.allAttachments, (newVal) => {
    attachments.value = newVal || []
}, { immediate: true })

const selectedItems = ref<number[]>([])

const getFileIcon = (type) => {
    // If type is not provided, default to 'unknown'
    const safeType = type || 'unknown';
    const imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'image/jpeg', 'image/png']
    const videoTypes = ['mp4', 'avi', 'mov', 'wmv', 'video/mp4']
    const audioTypes = ['mp3', 'wav', 'flac', 'aac', 'audio/mpeg']

    // Use includes for MIME types or extensions
    if (imageTypes.some(t => safeType.toLowerCase().includes(t))) return Image
    if (videoTypes.some(t => safeType.toLowerCase().includes(t))) return Video
    if (audioTypes.some(t => safeType.toLowerCase().includes(t))) return Music
    return File
}

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString)
    return date.toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    })
}

const getTotalSize = () => {
    const total = attachments.value.reduce((sum, file) => sum + Number(file.size || 0), 0)
    return formatFileSize(total)
}

const selectAll = () => {
    selectedItems.value = attachments.value.map(item => item.id)
}

const clearSelection = () => {
    selectedItems.value = []
}

const exportSelected = () => {
    emit('export-selected', selectedItems.value)
    // clearSelection() // Optional: clear selection after action? Better to keep it for UX.
}

const deleteSelected = () => {
    emit('delete-selected', selectedItems.value)
    clearSelection() // Clear after delete trigger
}

const downloadFile = (attachment) => {
    const link = document.createElement('a')
    link.href = attachment.path
    link.download = attachment.name
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
}

const deleteFile = (attachment) => {
    emit('delete', attachment)
}
</script>

<template>
    <div class="mx-auto p-6 space-y-6">
        <!-- Header com contador e ações -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-foreground">Anexos</h2>
                <p class="text-sm text-muted-foreground mt-1">{{ attachments.length }} arquivos</p>
            </div>

            <!-- Ações em lote -->
            <div v-if="selectedItems.length > 0" class="flex items-center gap-3">
                <span class="text-sm text-muted-foreground">{{ selectedItems.length }} selecionados</span>
                <button
                    @click="exportSelected"
                    class="px-4 py-2 bg-primary text-primary-foreground text-sm rounded-lg hover:bg-primary/90 transition-colors"
                >
                    Exportar (.zip)
                </button>
                <button
                    @click="deleteSelected"
                    class="px-4 py-2 bg-destructive text-destructive-foreground text-sm rounded-lg hover:bg-destructive/90 transition-colors"
                >
                    Excluir
                </button>
                <button
                    @click="clearSelection"
                    class="px-3 py-2 text-muted-foreground hover:text-foreground transition-colors"
                >
                    Cancelar
                </button>
            </div>
        </div>

        <!-- Lista de anexos -->
        <div class="space-y-2">
            <div
                v-for="attachment in attachments"
                :key="attachment.id"
                class="group flex items-center gap-4 p-3 rounded-lg border border-transparent hover:bg-muted/50 hover:border-border transition-all"
            >
                <!-- Checkbox -->
                <div class="inline-flex items-center">
                    <label class="flex items-center cursor-pointer relative">
                        <input :value="attachment.id"
                               v-model="selectedItems"
                               type="checkbox" class="peer h-5 w-5 cursor-pointer transition-all appearance-none rounded shadow hover:shadow-md border border-input checked:bg-primary checked:border-primary" />
                        <span class="absolute text-primary-foreground opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                    </label>
                </div>

                <!-- Ícone do arquivo -->
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-muted">
                         <component :is="getFileIcon(attachment.type)" class="w-5 h-5 text-muted-foreground" />
                    </div>
                </div>

                <!-- Informações do arquivo -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3">
                        <p class="text-sm font-medium text-foreground truncate">{{ attachment.name }}</p>
                    </div>
                    <div class="flex items-center gap-4 mt-1">
                        <span class="text-xs text-muted-foreground uppercase tracking-wide">{{ attachment.type }}</span>
                        <span class="text-xs text-muted-foreground">{{ formatFileSize(attachment.size) }}</span>
                        <span class="text-xs text-muted-foreground">{{ formatDate(attachment.created_at) }}</span>
                    </div>
                </div>

                <!-- Ações individuais -->
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button
                        @click="downloadFile(attachment)"
                        class="p-2 text-muted-foreground hover:text-primary transition-colors"
                        title="Baixar"
                    >
                        <Download class="w-4 h-4" />
                    </button>
                    <button
                        @click="deleteFile(attachment)"
                        class="p-2 text-muted-foreground hover:text-destructive transition-colors"
                        title="Excluir"
                    >
                        <Trash2 class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Estado vazio -->
        <div v-if="attachments.length === 0" class="text-center py-12 rounded-lg border border-dashed border-border/50 bg-muted/20">
            <FileText class="w-12 h-12 text-muted-foreground mx-auto mb-4" />
            <p class="text-muted-foreground font-medium">Nenhum anexo encontrado</p>
        </div>

        <!-- Seleção rápida -->
        <div v-if="attachments.length > 0" class="flex items-center justify-between pt-4 border-t border-border">
            <div class="flex items-center gap-4">
                <button
                    @click="selectAll"
                    class="text-sm text-primary hover:text-primary/80 transition-colors font-medium"
                >
                    Selecionar Todos
                </button>
                <button
                    @click="clearSelection"
                    class="text-sm text-muted-foreground hover:text-foreground transition-colors"
                >
                    Limpar Seleção
                </button>
            </div>

            <div class="text-xs text-muted-foreground font-medium">
                Total: {{ getTotalSize() }}
            </div>
        </div>
    </div>
</template>

