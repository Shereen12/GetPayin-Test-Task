<template>
    <button
        @click="handleLogout"
        :disabled="loading"
        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
    >
        <span v-if="loading">Signing out...</span>
        <span v-else>Sign out</span>
    </button>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const loading = ref(false)

const handleLogout = async () => {
    loading.value = true
    try {
        await authStore.logout()
        router.push('/login')
    } catch (error) {
        console.error('Logout failed:', error)
    } finally {
        loading.value = false
    }
}
</script>