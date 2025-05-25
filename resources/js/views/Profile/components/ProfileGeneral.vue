<template>
    <form @submit.prevent="handleSubmit" class="space-y-6">
        <div class="bg-white shadow sm:rounded-lg">
            <!-- Error Alert -->
            <div v-if="Object.keys(errors).length > 0" class="p-4 bg-red-50">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <ExclamationCircleIcon class="h-5 w-5 text-red-400" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            There were {{ Object.keys(errors).length }} errors with your submission
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li v-for="(error, field) in errors" :key="field">
                                    {{ error[0] }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 py-5 sm:p-6">
                <!-- Avatar -->
                <div class="flex items-center space-x-5">
                    <div class="flex-shrink-0">
                        
                        
                    </div>
                    
                </div>

                <!-- Name -->
                <div class="mt-6">
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        v-model="form.name"
                        :class="[
                            'mt-1 block w-full rounded-md shadow-sm sm:text-sm',
                            errors.name 
                                ? 'border-red-300 focus:ring-red-500 focus:border-red-500' 
                                : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                        ]"
                    />
                    <p 
                        v-if="errors.name" 
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ errors.name[0] }}
                    </p>
                </div>

                <!-- Email -->
                <div class="mt-6">
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        v-model="form.email"
                        :class="[
                            'mt-1 block w-full rounded-md shadow-sm sm:text-sm',
                            errors.email 
                                ? 'border-red-300 focus:ring-red-500 focus:border-red-500' 
                                : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                        ]"
                    />
                    <p 
                        v-if="errors.email" 
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ errors.email[0] }}
                    </p>
                </div>

                

                <!-- Last Updated -->
                <div class="mt-6 text-sm text-gray-500">
                    Last updated: {{ currentDateTime }} UTC
                </div>
            </div>

            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 flex items-center justify-end space-x-3">
                <span v-if="saveSuccess" class="text-sm text-green-600 animate-fade-out">
                    Profile updated successfully
                </span>
                <button
                    type="submit"
                    :disabled="loading || !isFormValid"
                    :class="[
                        'inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white',
                        loading || !isFormValid
                            ? 'bg-indigo-400 cursor-not-allowed'
                            : 'bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500'
                    ]"
                >
                    <template v-if="loading">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                        </svg>
                        Saving...
                    </template>
                    <template v-else>
                        Save Changes
                    </template>
                </button>
            </div>
        </div>
    </form>
</template>

<script setup>
import { ref, computed } from 'vue'
import { XMarkIcon, UserCircleIcon, ExclamationCircleIcon } from '@heroicons/vue/24/solid'
import axios from 'axios'

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['updated'])

const form = ref({
    name: '',
    email: '',
})

const currentDateTime = ref('2025-05-22 07:02:13')
const loading = ref(false)
const errors = ref({})
const avatarPreview = ref(null)
const fileInput = ref(null)
const saveSuccess = ref(false)



const isFormValid = computed(() => {
    return form.value.name.length > 0 && 
           form.value.email.length > 0 
})

const validateForm = () => {
    errors.value = {}
    
    if (!form.value.name) {
        errors.value.name = ['Name is required']
    }
    
    if (!form.value.email) {
        errors.value.email = ['Email is required']
    } else if (!isValidEmail(form.value.email)) {
        errors.value.email = ['Please enter a valid email address']
    }
    
    return Object.keys(errors.value).length === 0
}

const isValidEmail = (email) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return re.test(email)
}

const handleFileChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            errors.value.avatar = ['Image size should not exceed 2MB']
            event.target.value = ''
            return
        }
        
        if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
            errors.value.avatar = ['Please upload a valid image file (JPG, PNG, GIF)']
            event.target.value = ''
            return
        }

        const reader = new FileReader()
        reader.onload = (e) => {
            avatarPreview.value = e.target.result
        }
        reader.readAsDataURL(file)
        delete errors.value.avatar
    }
}

const removeAvatar = () => {
    avatarPreview.value = null
    if (fileInput.value) {
        fileInput.value.value = ''
    }
    delete errors.value.avatar
}

const handleSubmit = async () => {
    if (!validateForm()) {
        return
    }

    loading.value = true
    saveSuccess.value = false
    errors.value = {}

    try {
        const formData = new FormData()
        Object.keys(form.value).forEach(key => {
            if (form.value[key] !== null && form.value[key] !== '') {
                formData.append(key, form.value[key])
            }
        })
        
        if (fileInput.value?.files[0]) {
            formData.append('avatar', fileInput.value.files[0])
        }

        const response = await axios.post('/api/profile', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })

        saveSuccess.value = true
        setTimeout(() => {
            saveSuccess.value = false
        }, 3000)

        emit('updated', response.data.data)
    } catch (error) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors
        } else {
            errors.value = {
                general: ['An unexpected error occurred. Please try again.']
            }
        }
    } finally {
        loading.value = false
    }
}

// Initialize form with user data
if (props.user) {
    form.value = {
        name: props.user.name || '',
        email: props.user.email || '',
    }
    if (props.user.avatar_url) {
        avatarPreview.value = props.user.avatar_url
    }
}

// Update time every second
setInterval(() => {
    const now = new Date()
    currentDateTime.value = now.toISOString().slice(0, 19).replace('T', ' ')
}, 1000)
</script>

<style scoped>
.animate-fade-out {
    animation: fadeOut 3s forwards;
}

@keyframes fadeOut {
    0% { opacity: 1; }
    70% { opacity: 1; }
    100% { opacity: 0; }
}
</style>