<script setup lang="ts">
import { reactive, ref, watch } from 'vue';
import { FileText, Download, Share2, Trash2, File, Image, Video, Music } from 'lucide-vue-next'
import type { Attachment } from '@/types/Employees'

const props = defineProps<{ allAttachments: Attachment[] }>()
const attachments = ref(props.allAttachments || [])


const selectedItems = ref([])
console.log('Selected Items:', selectedItems.value)
const getFileIcon = (type) => {
    const imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp']
    const videoTypes = ['mp4', 'avi', 'mov', 'wmv']
    const audioTypes = ['mp3', 'wav', 'flac', 'aac']

    if (imageTypes.includes(type.toLowerCase())) return Image
    if (videoTypes.includes(type.toLowerCase())) return Video
    if (audioTypes.includes(type.toLowerCase())) return Music
    return File
}

const getFileIconBg = (type) => {
    const imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp']
    const videoTypes = ['mp4', 'avi', 'mov', 'wmv']
    const audioTypes = ['mp3', 'wav', 'flac', 'aac']
    const ext = type.split('/').pop()?.toLowerCase() || type.toLowerCase()
    if (imageTypes.includes(ext)) return 'bg-green-100'
    if (videoTypes.includes(ext)) return 'bg-purple-100'
    if (audioTypes.includes(ext)) return 'bg-yellow-100'
    if (ext === 'pdf') return 'bg-red-100'
    return 'bg-gray-100'
}

const getFileIconColor = (type) => {
    const imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp']
    const videoTypes = ['mp4', 'avi', 'mov', 'wmv']
    const audioTypes = ['mp3', 'wav', 'flac', 'aac']
    const ext = type.split('/').pop()?.toLowerCase() || type.toLowerCase()
    if (imageTypes.includes(ext)) return 'text-green-600'
    if (videoTypes.includes(ext)) return 'text-purple-600'
    if (audioTypes.includes(ext)) return 'text-yellow-600'
    if (ext === 'pdf') return 'text-red-600'
    return 'text-gray-600'
}

const formatFileSize = (bytes) => {

    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const formatDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    })
}

const getTotalSize = () => {
    const total = attachments.value.reduce((sum, file) => sum + file.size, 0)
    return formatFileSize(total)
}

const selectAll = () => {
    selectedItems.value = attachments.value.map(item => item.id)
}

const clearSelection = () => {
    selectedItems.value = []
}

const exportSelected = () => {
    const selectedFiles = attachments.value.filter(file =>
        selectedItems.value.includes(file.id)
    )
    console.log('Arquivos selecionados para exportação:', selectedFiles)
    console.log('Exportando arquivos:', selectedFiles)
    // Implementar lógica de exportação
    alert(`Exportando ${selectedFiles.length} arquivos...`)
}

const downloadFile = (attachment) => {
    console.log('Download:', attachment)
    const link = document.createElement('a')
    link.href = attachment.path
    link.download = attachment.name
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
}

const shareFile = (attachment) => {
    // Implementar lógica de compartilhamento
}

const deleteFile = (attachment) => {
    if (confirm(`Deseja excluir ${attachment.name}?`)) {
        const index = attachments.value.findIndex(item => item.id === attachment.id)
        if (index > -1) {
            attachments.value.splice(index, 1)
            selectedItems.value = selectedItems.value.filter(id => id !== attachment.id)
        }
    }
}
</script>

<template>
    <div class=" mx-auto p-6">
        <!-- Header com contador e ações -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-foreground">Anexos</h2>
                <p class="text-sm text-muted-foreground mt-1">{{ attachments.length }} arquivos</p>
            </div>

            <!-- Ações em lote -->
            <div v-if="selectedItems.length > 0" class="flex items-center gap-3">
                <span class="text-sm text-muted-foreground">{{ selectedItems.length }} selecionados</span>
                <button
                    @click="exportSelected"
                    class="px-4 py-2 btn-primary text-foreground text-sm rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Exportar Selecionados
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
        <div class="space-y-1">
            <div
                v-for="attachment in attachments"
                :key="attachment.id"
                class="group flex items-center gap-4 p-3 rounded-lg hover:bg-card/50 transition-colors"
            >
                <!-- Checkbox -->
                <div class="inline-flex items-center">
                    <label class="flex items-center cursor-pointer relative">
                        <input :value="attachment.id"
                               v-model="selectedItems"
                               type="checkbox" class="peer h-6 w-6 cursor-pointer transition-all appearance-none rounded-full shadow hover:shadow-md border-2 border-muted checked:bg-secondary checked:bg-secondary" />
                        <span class="absolute text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" stroke="currentColor" stroke-width="1">
                                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    </span>
                    </label>
                </div>

                <!-- Ícone do arquivo -->
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" :class="getFileIconBg(attachment.type)">
                        <component :is="getFileIcon(attachment.type)" class="w-4 h-4" :class="getFileIconColor(attachment.type)" />
                    </div>
                </div>

                <!-- Informações do arquivo -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3">
                        <p class="text-sm font-medium text-foreground truncate">{{ attachment.name }}</p>
                        <span class="text-xs text-muted-foreground uppercase tracking-wide">{{ attachment.type }}</span>
                    </div>
                    <div class="flex items-center gap-4 mt-1">
                        <span class="text-xs text-muted-foreground">{{ formatFileSize(attachment.size) }}</span>
                        <span class="text-xs text-muted-foreground">{{ formatDate(attachment.created_at) }}</span>
                    </div>
                </div>

                <!-- Ações individuais -->
                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button
                        @click="downloadFile(attachment)"
                        class="p-2 text-gray-400 hover:text-blue-600 transition-colors"
                        title="Download"
                    >
                        <Download class="w-4 h-4" />
                    </button>
                    <button
                        @click="shareFile(attachment)"
                        class="p-2 text-gray-400 hover:text-green-600 transition-colors"
                        title="Compartilhar"
                    >
                        <Share2 class="w-4 h-4" />
                    </button>
                    <button
                        @click="deleteFile(attachment)"
                        class="p-2 text-gray-400 hover:text-red-600 transition-colors"
                        title="Excluir"
                    >
                        <Trash2 class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Estado vazio -->
        <div v-if="attachments.length === 0" class="text-center py-12">
            <FileText class="w-12 h-12 text-gray-300 mx-auto mb-4" />
            <p class="text-gray-500">Nenhum anexo encontrado</p>
        </div>

        <!-- Seleção rápida -->
        <div v-if="attachments.length > 0" class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
            <div class="flex items-center gap-4">
                <button
                    @click="selectAll"
                    class="text-sm text-blue-600 hover:text-blue-700 transition-colors"
                >
                    Selecionar Todos
                </button>
                <button
                    @click="clearSelection"
                    class="text-sm text-gray-500 hover:text-gray-700 transition-colors"
                >
                    Limpar Seleção
                </button>
            </div>

            <div class="text-xs text-gray-500">
                Total: {{ getTotalSize() }}
            </div>
        </div>
    </div>
</template>

