<template>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Profile Header -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-xl font-semibold text-gray-900">Profile Settings</h1>
                            <p class="mt-1 text-sm text-gray-500">
                                Manage your account settings and preferences
                            </p>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ currentDateTime }} UTC
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button
                        v-for="tab in tabs"
                        :key="tab.name"
                        @click="currentTab = tab.value"
                        :class="[
                            currentTab === tab.value
                                ? 'border-indigo-500 text-indigo-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                            'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                        ]"
                    >
                        {{ tab.name }}
                    </button>
                </nav>
            </div>

            <!-- Content -->
            <div v-if="currentTab === 'general'" class="space-y-6">
                <ProfileGeneral 
                    :user="user"
                    @updated="handleProfileUpdate"
                />
            </div>
        </div>

        <!-- Success Toast -->
        <div
            v-if="showToast"
            class="fixed bottom-4 right-4 bg-green-50 p-4 rounded-md shadow-lg"
        >
            <div class="flex">
                <div class="flex-shrink-0">
                    <CheckCircleIcon class="h-5 w-5 text-green-400" />
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ toastMessage }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { CheckCircleIcon } from '@heroicons/vue/24/solid'
import ProfileGeneral from './components/ProfileGeneral.vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const currentDateTime = ref('2025-05-22 06:52:12')
const currentTab = ref('general')
const user = ref(null)
const showToast = ref(false)
const toastMessage = ref('')

const tabs = [
    { name: 'General', value: 'general' }
]

const showSuccessToast = (message) => {
    toastMessage.value = message
    showToast.value = true
    setTimeout(() => {
        showToast.value = false
    }, 3000)
}

const handleProfileUpdate = async (updatedData) => {
    try {
        user.value = { ...user.value, ...updatedData }
        showSuccessToast('Profile updated successfully')
    } catch (error) {
        console.error('Profile update failed:', error)
    }
}


onMounted(async () => {
    try {
        const response = await authStore.fetchUser()
        user.value = response.data
    } catch (error) {
        console.error('Failed to fetch user data:', error)
    }

    // Update time every second
    setInterval(() => {
        const now = new Date()
        currentDateTime.value = now.toISOString().slice(0, 19).replace('T', ' ')
    }, 1000)
})
</script>