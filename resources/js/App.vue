<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Left side -->
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-xl font-bold text-indigo-600">Content Scheduler</h1>
                        </div>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center space-x-6">
                        

                        <!-- User Dropdown -->
                        <div class="relative" v-if="isAuthenticated">
                            <button
                                @click="showUserMenu = !showUserMenu"
                                class="flex items-center space-x-2 text-sm focus:outline-none"
                            >
                                <UserCircleIcon class="h-8 w-8 text-gray-400" />
                                <span class="text-gray-700">{{ userLogin }}</span>
                                <ChevronDownIcon class="h-5 w-5 text-gray-400" />
                            </button>

                            <!-- Dropdown Menu -->
                            <div
                                v-if="showUserMenu"
                                class="absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5"
                                @click.away="showUserMenu = false"
                            >

                            <router-link
                                to="/profile"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                                Profile Settings
                            </router-link>
                                <a
                                    href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    @click.prevent="handleLogout"
                                >
                                    Sign out
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <router-view></router-view>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import {
    ClockIcon,
    UserCircleIcon,
    ChevronDownIcon
} from '@heroicons/vue/24/solid'
import dayjs from 'dayjs'
import utc from 'dayjs/plugin/utc'

dayjs.extend(utc)

const router = useRouter()
const authStore = useAuthStore()
const currentDateTime = ref('2025-05-22 05:49:29')
const userLogin = ref('')
const showUserMenu = ref(false)
const isAuthenticated = ref(true)

const handleLogout = async () => {
    try {
        await authStore.logout()
        router.push('/login')
    } catch (error) {
        console.error('Logout failed:', error)
    }
}

onMounted(() => {
    // Update time every second
    setInterval(() => {
        currentDateTime.value = dayjs().utc().format('YYYY-MM-DD HH:mm:ss')
    }, 1000)

    // Check authentication status
    isAuthenticated.value = !!localStorage.getItem('token')
})
</script>