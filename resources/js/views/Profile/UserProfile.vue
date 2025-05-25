<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Profile Header -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Profile Settings</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage your account and preferences</p>
                    </div>
                    <div class="text-sm text-gray-500">Last updated: {{ currentDateTime }} UTC</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Basic Information -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Basic Information</h3>
                    <div class="mt-6 grid grid-cols-1 gap-y-6">
                        <!-- Avatar -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Photo</label>
                            <div class="mt-2 flex items-center">
                                <div v-if="avatarPreview" class="relative">
                                    <img :src="avatarPreview" class="h-16 w-16 rounded-full object-cover" />
                                    <button
                                        @click="deleteAvatar"
                                        class="absolute -top-2 -right-2 bg-red-100 rounded-full p-1 text-red-600 hover:bg-red-200"
                                    >
                                        <XMarkIcon class="h-4 w-4" />
                                    </button>
                                </div>
                                <div v-else class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center">
                                    <UserCircleIcon class="h-12 w-12 text-gray-400" />
                                </div>
                                <button
                                    @click="$refs.avatarInput.click()"
                                    class="ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Change
                                </button>
                                <input
                                    ref="avatarInput"
                                    type="file"
                                    class="hidden"
                                    accept="image/*"
                                    @change="handleAvatarChange"
                                />
                            </div>
                        </div>

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input
                                type="text"
                                id="name"
                                v-model="form.name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input
                                type="email"
                                id="email"
                                v-model="form.email"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                        </div>

                        <!-- Bio -->
                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                            <textarea
                                id="bio"
                                v-model="form.bio"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                        </div>

                        <!-- Save Button -->
                        <div class="flex justify-end">
                            <button
                                @click="updateProfile"
                                :disabled="loading"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                {{ loading ? 'Saving...' : 'Save Changes' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password Update -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Update Password</h3>
                    <div class="mt-6 grid grid-cols-1 gap-y-6">
                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700">
                                Current Password
                            </label>
                            <input
                                type="password"
                                id="current_password"
                                v-model="passwordForm.current_password"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700">
                                New Password
                            </label>
                            <input
                                type="password"
                                id="new_password"
                                v-model="passwordForm.new_password"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">
                                Confirm New Password
                            </label>
                            <input
                                type="password"
                                id="new_password_confirmation"
                                v-model="passwordForm.new_password_confirmation"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                        </div>

                        <!-- Update Password Button -->
                        <div class="flex justify-end">
                            <button
                                @click="updatePassword"
                                :disabled="passwordLoading"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                {{ passwordLoading ? 'Updating...' : 'Update Password' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { UserCircleIcon, XMarkIcon } from '@heroicons/vue/24/solid'
import axios from 'axios'
import { useToast } from '@/composables/useToast'

const currentDateTime = ref('2025-05-22 06:44:08')
const loading = ref(false)
const passwordLoading = ref(false)
const avatarPreview = ref(null)
const { showToast } = useToast()

const form = ref({
    name: '',
    email: '',
    bio: '',
    avatar: null
})

const passwordForm = ref({
    current_password: '',
    new_password: '',
    new_password_confirmation: ''
})

const handleAvatarChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.value.avatar = file
        avatarPreview.value = URL.createObjectURL(file)
    }
}

const deleteAvatar = async () => {
    try {
        await axios.delete('/api/profile/avatar')
        form.value.avatar = null
        avatarPreview.value = null
        showToast('Avatar deleted successfully', 'success')
    } catch (error) {
        showToast('Failed to delete avatar', 'error')
    }
}

const updateProfile = async () => {
    loading.value = true
    try {
        const formData = new FormData()
        Object.keys(form.value).forEach(key => {
            if (form.value[key] !== null) {
                formData.append(key, form.value[key])
            }
        })

        const response = await axios.post('/api/profile', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })

        showToast('Profile updated successfully', 'success')
    } catch (error) {
        showToast(error.response?.data?.message || 'Failed to update profile', 'error')
    } finally {
        loading.value = false
    }
}

const updatePassword = async () => {
    passwordLoading.value = true
    try {
        await axios.put('/api/profile/password', passwordForm.value)
        passwordForm.value = {
            current_password: '',
            new_password: '',
            new_password_confirmation: ''
        }
        showToast('Password updated successfully', 'success')
    } catch (error) {
        showToast(error.response?.data?.message || 'Failed to update password', 'error')
    } finally {
        passwordLoading.value = false
    }
}

onMounted(async () => {
    try {
        const response = await axios.get('/api/profile')
        const user = response.data.data
        form.value = {
            name: user.name,
            email: user.email,
            bio: user.bio || '',
            avatar: null
        }
        if (user.avatar_url) {
            avatarPreview.value = user.avatar_url
        }
    } catch (error) {
        showToast('Failed to load profile', 'error')
    }

    // Update time every second
    setInterval(() => {
        const now = new Date()
        currentDateTime.value = now.toISOString().slice(0, 19).replace('T', ' ')
    }, 1000)
})
</script>