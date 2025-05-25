<template>
    <div class="fixed inset-0 overflow-y-auto z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-1 bg-gray-500 bg-opacity-75 transition-opacity" @click="$emit('close')" />

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
                    <div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                {{ post ? 'Edit Post' : 'Create New Post' }}
                            </h3>

                            <div class="mt-6 space-y-6">
                                <!-- Title -->
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                    <input
                                        type="text"
                                        id="title"
                                        v-model="form.title"
                                        class="mt-1 form-input"
                                        required
                                    />
                                </div>

                                <!-- Content -->
                                <div>
                                    <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                                    <textarea
                                        id="content"
                                        v-model="form.content"
                                        rows="4"
                                        class="mt-1 form-input"
                                        required
                                    />
                                    <p class="mt-2 text-sm text-gray-500">
                                        {{ form.content.length }}/280 characters
                                    </p>
                                </div>

                                <!-- Image Upload -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Image</label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                        <div class="space-y-1 text-center">
                                            <svg
                                                class="mx-auto h-12 w-12 text-gray-400"
                                                stroke="currentColor"
                                                fill="none"
                                                viewBox="0 0 48 48"
                                            >
                                                <path
                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label
                                                    for="image"
                                                    class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500"
                                                >
                                                    <span>Upload a file</span>
                                                    <input
                                                        id="image"
                                                        type="file"
                                                        class="sr-only"
                                                        @change="handleImageChange"
                                                        accept="image/*"
                                                    />
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Platforms -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Platforms</label>
                                    <div class="mt-2 space-y-2">
                                        <div
                                            v-for="platform in platforms"
                                            :key="platform.id"
                                            class="flex items-center"
                                        >
                                            <input
                                                type="checkbox"
                                                :id="'platform-' + platform.id"
                                                v-model="form.platform_ids"
                                                :value="platform.id"
                                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                            />
                                            <label
                                                :for="'platform-' + platform.id"
                                                class="ml-3 text-sm text-gray-700"
                                            >
                                                {{ platform.name }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Schedule Date/Time -->
                                <div>
                                    <label for="scheduled_time" class="block text-sm font-medium text-gray-700">
                                        Schedule Date/Time (UTC)
                                    </label>
                                    <input
                                        type="datetime-local"
                                        id="scheduled_time"
                                        v-model="form.scheduled_time"
                                        class="mt-1 form-input"
                                        required
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6">
                        <p v-if="errors.title" class="text-sm text-red-600">{{ errors.title[0] }}</p>
                        <p v-if="errors.content" class="text-sm text-red-600">{{ errors.content[0] }}</p>
                        <p v-if="errors.image" class="text-sm text-red-600">{{ errors.image[0] }}</p>
                        <p v-if="errors.platform_ids" class="text-sm text-red-600">{{ errors.platform_ids[0] }}</p>
                        <p v-if="errors.scheduled_time" class="text-sm text-red-600">{{ errors.scheduled_time[0] }}</p>
                    </div>

                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button
                            type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm"
                            :disabled="loading"
                            @click="emit('saved')"
                        >
                            {{ loading ? 'Saving...' : (post ? 'Update' : 'Create') }}
                        </button>
                        <button
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
                            @click="$emit('close')"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { usePostStore } from '@/stores/posts'
import dayjs from 'dayjs'

const props = defineProps({
    post: {
        type: Object,
        default: null
    }
})

const emit = defineEmits(['close', 'saved'])
const postStore = usePostStore()
const loading = ref(false)
const platforms = ref([
    { id: 1, name: 'Twitter' },
    { id: 2, name: 'Instagram' },
    { id: 3, name: 'LinkedIn' }
])

const form = ref({
    title: '',
    content: '',
    image: '',
    platform_ids: [],
    scheduled_time: dayjs().add(1, 'hour').format('YYYY-MM-DDTHH:mm'),
})

const errors = ref({})

const handleImageChange = (event) => {
    form.value.image = event.target.files[0]
}

const handleSubmit = async () => {
    try {
        loading.value = true
        const formData = new FormData()
        
        Object.keys(form.value).forEach(key => {
            if (key === 'platform_ids') {
                form.value[key].forEach(id => {
                    formData.append('platform_ids[]', id)
                })
            } else {
                formData.append(key, form.value[key])
            }
        })
        
        if (props.post) {
            formData.append('_method', 'PUT')
            await postStore.updatePost(props.post.id, formData)
        } else {
            await postStore.createPost(formData)
        }

        emit('saved')
    } catch (error) {
        console.error('Failed to save post:', error)
        // Handle error (e.g., show notification)
         errors.value = error.response.data.errors
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    if (props.post) {
        form.value = {
            ...props.post,
            platform_ids: props.post.platforms.map(p => p.id)
        }
    }
})
</script>