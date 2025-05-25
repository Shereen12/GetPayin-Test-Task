<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Sign in to your account
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Or
                    <router-link
                        to="/register"
                        class="font-medium text-indigo-600 hover:text-indigo-500"
                    >
                        create a new account
                    </router-link>
                </p>
            </div>

            <!-- Registration Success Message -->
            <div v-if="showRegistrationSuccess" class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <CheckCircleIcon class="h-5 w-5 text-green-400" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            Registration successful! Please sign in with your credentials.
                        </p>
                    </div>
                </div>
            </div>

            <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
                <!-- ... rest of the login form ... -->
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="email-address" class="sr-only">Email address</label>
                        <input
                            id="email-address"
                            name="email"
                            type="email"
                            required
                            v-model="form.email"
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Email address"
                        />
                    </div>
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            v-model="form.password"
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Password"
                        />
                    </div>
                </div>

                <div v-if="error" class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <XCircleIcon class="h-5 w-5 text-red-400" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ error }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <LockClosedIcon class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" />
                        </span>
                        <span v-if="loading">Signing in...</span>
                        <span v-else>Sign in</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { LockClosedIcon, CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/solid'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const form = ref({
    email: '',
    password: ''
})

const loading = ref(false)
const error = ref('')
const showRegistrationSuccess = ref(false)

onMounted(() => {
    // Check if user was redirected from registration
    if (route.query.registered === 'true') {
        showRegistrationSuccess.value = true
        if (route.query.email) {
            form.value.email = route.query.email
        }
    }
})

const handleLogin = async () => {
    loading.value = true
    error.value = ''

    try {
        await authStore.login(form.value)
        router.push('/dashboard')
    } catch (e) {
        error.value = e.response?.data?.message || 'Login failed'
    } finally {
        loading.value = false
    }
}
</script>