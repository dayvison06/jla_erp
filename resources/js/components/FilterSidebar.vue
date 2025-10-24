
<script setup lang="ts">
import { ref } from 'vue'
import { X, Edit, Plus, CheckSquare, Square, Trash2, Copy } from 'lucide-vue-next'

interface Props {
    task: Task
    columns: Column[]
}

const props = defineProps<Props>()

const emit = defineEmits<{
    close: []
    update: [task: Task]
    delete: [taskId: string]
    duplicate: [task: Task]
}>()

const editedTask = ref<Task>({ ...props.task })
const isEditingTitle = ref(false)
const isEditingDescription = ref(false)
const newSubtaskTitle = ref('')
const isAddingSubtask = ref(false)
const newCustomFieldName = ref('')
const newCustomFieldValue = ref('')
const isAddingCustomField = ref(false)
const showDeleteConfirm = ref(false)

const handleTitleSave = () => {
    if (editedTask.value.title.trim()) {
        emit('update', editedTask.value)
        isEditingTitle.value = false
    }
}

const handleDescriptionSave = () => {
    emit('update', editedTask.value)
    isEditingDescription.value = false
}

const handleStatusChange = () => {
    emit('update', editedTask.value)
}

const handleDueDateChange = (event: Event) => {
    const target = event.target as HTMLInputElement
    editedTask.value.dueDate = target.value ? new Date(target.value).toISOString() : null
    emit('update', editedTask.value)
}

const toggleSubtask = (subtaskId: string) => {
    editedTask.value.subtasks = editedTask.value.subtasks.map((subtask) =>
        subtask.id === subtaskId ? { ...subtask, completed: !subtask.completed } : subtask
    )
    emit('update', editedTask.value)
}

const addSubtask = () => {
    if (!newSubtaskTitle.value.trim()) return

    const newSubtask: Subtask = {
        id: `subtask-${generateId()}`,
        title: newSubtaskTitle.value,
        completed: false,
    }

    editedTask.value.subtasks = [...editedTask.value.subtasks, newSubtask]
    emit('update', editedTask.value)
    newSubtaskTitle.value = ''
    isAddingSubtask.value = false
}

const deleteSubtask = (subtaskId: string) => {
    editedTask.value.subtasks = editedTask.value.subtasks.filter((subtask) => subtask.id !== subtaskId)
    emit('update', editedTask.value)
}

const addCustomField = () => {
    if (!newCustomFieldName.value.trim()) return

    const newField: CustomField = {
        id: `field-${generateId()}`,
        name: newCustomFieldName.value,
        value: newCustomFieldValue.value,
    }

    editedTask.value.customFields = [...editedTask.value.customFields, newField]
    emit('update', editedTask.value)
    newCustomFieldName.value = ''
    newCustomFieldValue.value = ''
    isAddingCustomField.value = false
}

const updateCustomField = (fieldId: string, value: string) => {
    editedTask.value.customFields = editedTask.value.customFields.map((field) =>
        field.id === fieldId ? { ...field, value } : field
    )
    emit('update', editedTask.value)
}

const deleteCustomField = (fieldId: string) => {
    editedTask.value.customFields = editedTask.value.customFields.filter((field) => field.id !== fieldId)
    emit('update', editedTask.value)
}

const handleDuplicateTask = () => {
    emit('duplicate', props.task)
}

const confirmDelete = () => {
    emit('delete', props.task.id)
}
</script>


<template>
    <div class="fixed inset-y-0 right-0 w-full sm:w-96 bg-white dark:bg-gray-800 shadow-lg border-l dark:border-gray-700 z-50 flex flex-col">
        <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
            <h2 class="text-lg font-semibold dark:text-gray-200">Task Details</h2>
            <button @click="$emit('close')" class="h-10 w-10 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md flex items-center justify-center">
                <X class="h-5 w-5" />
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4">
<!--            <div class="space-y-6">-->
<!--                &lt;!&ndash; Title &ndash;&gt;-->
<!--                <div>-->
<!--                    <div v-if="isEditingTitle" class="space-y-2">-->
<!--                        <input-->
<!--                            v-model="editedTask.title"-->
<!--                            class="w-full text-lg font-medium px-3 py-2 border dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200"-->
<!--                        />-->
<!--                        <div class="flex gap-2">-->
<!--                            <button @click="handleTitleSave" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">-->
<!--                                Save-->
<!--                            </button>-->
<!--                            <button-->
<!--                                @click="isEditingTitle = false"-->
<!--                                class="px-4 py-2 border dark:border-gray-600 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"-->
<!--                            >-->
<!--                                Cancel-->
<!--                            </button>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div v-else class="flex justify-between items-start">-->
<!--                        <h3 class="text-lg font-medium dark:text-gray-200">{{ editedTask.title }}</h3>-->
<!--                        <button @click="isEditingTitle = true" class="h-10 w-10 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md flex items-center justify-center">-->
<!--                            <Edit class="h-4 w-4" />-->
<!--                        </button>-->
<!--                    </div>-->
<!--                </div>-->

<!--                &lt;!&ndash; Status &ndash;&gt;-->
<!--                <div>-->
<!--                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-1">Status</label>-->
<!--                    <select-->
<!--                        v-model="editedTask.status"-->
<!--                        @change="handleStatusChange"-->
<!--                        class="w-full px-3 py-2 border dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200"-->
<!--                    >-->
<!--                        <option v-for="column in columns" :key="column.id" :value="column.title">-->
<!--                            {{ column.title }}-->
<!--                        </option>-->
<!--                    </select>-->
<!--                </div>-->

<!--                &lt;!&ndash; Due Date &ndash;&gt;-->
<!--                <div>-->
<!--                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 block mb-1">Due Date</label>-->
<!--                    <input-->
<!--                        type="date"-->
<!--                        :value="editedTask.dueDate ? new Date(editedTask.dueDate).toISOString().split('T')[0] : ''"-->
<!--                        @change="handleDueDateChange"-->
<!--                        class="w-full px-3 py-2 border dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200"-->
<!--                    />-->
<!--                </div>-->

<!--                &lt;!&ndash; Description &ndash;&gt;-->
<!--                <div>-->
<!--                    <div class="flex justify-between items-center mb-1">-->
<!--                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>-->
<!--                        <button-->
<!--                            v-if="!isEditingDescription"-->
<!--                            @click="isEditingDescription = true"-->
<!--                            class="text-sm px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md flex items-center"-->
<!--                        >-->
<!--                            <Edit class="h-3 w-3 mr-1" /> Edit-->
<!--                        </button>-->
<!--                    </div>-->

<!--                    <div v-if="isEditingDescription" class="space-y-2">-->
<!--            <textarea-->
<!--                v-model="editedTask.description"-->
<!--                placeholder="Add a description..."-->
<!--                rows="4"-->
<!--                class="w-full px-3 py-2 border dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200"-->
<!--            />-->
<!--                        <div class="flex gap-2">-->
<!--                            <button @click="handleDescriptionSave" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">-->
<!--                                Save-->
<!--                            </button>-->
<!--                            <button-->
<!--                                @click="isEditingDescription = false"-->
<!--                                class="px-4 py-2 border dark:border-gray-600 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"-->
<!--                            >-->
<!--                                Cancel-->
<!--                            </button>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div v-else class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 p-3 rounded-md min-h-[60px]">-->
<!--                        {{ editedTask.description || 'No description provided.' }}-->
<!--                    </div>-->
<!--                </div>-->

<!--                <div class="border-t dark:border-gray-700 my-4" />-->

<!--                &lt;!&ndash; Subtasks &ndash;&gt;-->
<!--                <div>-->
<!--                    <div class="flex justify-between items-center mb-2">-->
<!--                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Subtasks</h4>-->
<!--                        <button-->
<!--                            @click="isAddingSubtask = true"-->
<!--                            class="text-sm px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md flex items-center"-->
<!--                        >-->
<!--                            <Plus class="h-3 w-3 mr-1" /> Add-->
<!--                        </button>-->
<!--                    </div>-->

<!--                    <div v-if="isAddingSubtask" class="mb-3 space-y-2">-->
<!--                        <input-->
<!--                            v-model="newSubtaskTitle"-->
<!--                            placeholder="Subtask title"-->
<!--                            class="w-full px-3 py-2 border dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200"-->
<!--                        />-->
<!--                        <div class="flex gap-2">-->
<!--                            <button @click="addSubtask" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">-->
<!--                                Add-->
<!--                            </button>-->
<!--                            <button-->
<!--                                @click="isAddingSubtask = false"-->
<!--                                class="px-4 py-2 border dark:border-gray-600 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"-->
<!--                            >-->
<!--                                Cancel-->
<!--                            </button>-->
<!--                        </div>-->
<!--                    </div>-->

<!--                    <div class="space-y-2">-->
<!--                        <p v-if="editedTask.subtasks.length === 0" class="text-sm text-gray-500 dark:text-gray-400">-->
<!--                            No subtasks yet.-->
<!--                        </p>-->
<!--                        <div-->
<!--                            v-for="subtask in editedTask.subtasks"-->
<!--                            :key="subtask.id"-->
<!--                            class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 p-2 rounded-md"-->
<!--                        >-->
<!--                            <div class="flex items-center">-->
<!--                                <button-->
<!--                                    @click="toggleSubtask(subtask.id)"-->
<!--                                    class="h-6 w-6 mr-2 flex items-center justify-center"-->
<!--                                >-->
<!--                                    <CheckSquare v-if="subtask.completed" class="h-4 w-4 text-blue-500" />-->
<!--                                    <Square v-else class="h-4 w-4" />-->
<!--                                </button>-->
<!--                                <span-->
<!--                                    :class="[-->
<!--                    'text-sm',-->
<!--                    subtask.completed ? 'line-through text-gray-500 dark:text-gray-400' : 'dark:text-gray-200'-->
<!--                  ]"-->
<!--                                >-->
<!--                  {{ subtask.title }}-->
<!--                </span>-->
<!--                            </div>-->
<!--                            <button-->
<!--                                @click="deleteSubtask(subtask.id)"-->
<!--                                class="h-6 w-6 text-gray-400 hover:text-red-500 dark:hover:text-red-400 flex items-center justify-center"-->
<!--                            >-->
<!--                                <Trash2 class="h-4 w-4" />-->
<!--                            </button>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->

<!--                <div class="border-t dark:border-gray-700 my-4" />-->

<!--                &lt;!&ndash; Custom Fields &ndash;&gt;-->
<!--                <div>-->
<!--                    <div class="flex justify-between items-center mb-2">-->
<!--                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Custom Fields</h4>-->
<!--                        <button-->
<!--                            @click="isAddingCustomField = true"-->
<!--                            class="text-sm px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md flex items-center"-->
<!--                        >-->
<!--                            <Plus class="h-3 w-3 mr-1" /> Add-->
<!--                        </button>-->
<!--                    </div>-->

<!--                    <div v-if="isAddingCustomField" class="mb-3 space-y-2">-->
<!--                        <div class="grid grid-cols-2 gap-2">-->
<!--                            <input-->
<!--                                v-model="newCustomFieldName"-->
<!--                                placeholder="Field name"-->
<!--                                class="px-3 py-2 border dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200"-->
<!--                            />-->
<!--                            <input-->
<!--                                v-model="newCustomFieldValue"-->
<!--                                placeholder="Field value"-->
<!--                                class="px-3 py-2 border dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200"-->
<!--                            />-->
<!--                        </div>-->
<!--                        <div class="flex gap-2">-->
<!--                            <button @click="addCustomField" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">-->
<!--                                Add-->
<!--                            </button>-->
<!--                            <button-->
<!--                                @click="isAddingCustomField = false"-->
<!--                                class="px-4 py-2 border dark:border-gray-600 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"-->
<!--                            >-->
<!--                                Cancel-->
<!--                            </button>-->
<!--                        </div>-->
<!--                    </div>-->

<!--                    <div class="space-y-2">-->
<!--                        <p v-if="editedTask.customFields.length === 0" class="text-sm text-gray-500 dark:text-gray-400">-->
<!--                            No custom fields yet.-->
<!--                        </p>-->
<!--                        <div-->
<!--                            v-for="field in editedTask.customFields"-->
<!--                            :key="field.id"-->
<!--                            class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 p-2 rounded-md"-->
<!--                        >-->
<!--                            <div class="grid grid-cols-2 gap-2 flex-1 mr-2">-->
<!--                                <div class="text-sm font-medium dark:text-gray-200">{{ field.name }}:</div>-->
<!--                                <input-->
<!--                                    :value="field.value || ''"-->
<!--                                    @input="updateCustomField(field.id, ($event.target as HTMLInputElement).value)"-->
<!--                                    class="h-7 text-sm px-2 border dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200"-->
<!--                                />-->
<!--                            </div>-->
<!--                            <button-->
<!--                                @click="deleteCustomField(field.id)"-->
<!--                                class="h-6 w-6 text-gray-400 hover:text-red-500 dark:hover:text-red-400 flex items-center justify-center"-->
<!--                            >-->
<!--                                <Trash2 class="h-4 w-4" />-->
<!--                            </button>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
        </div>

    </div>
</template>
