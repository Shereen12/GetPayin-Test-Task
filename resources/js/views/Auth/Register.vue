<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Create your account
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Or
                    <router-link
                        to="/login"
                        class="font-medium text-indigo-600 hover:text-indigo-500"
                    >
                        sign in if you already have an account
                    </router-link>
                </p>
            </div>

            <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
                <div class="rounded-md shadow-sm -space-y-px">
                    <!-- Name Input -->
                    <div>
                        <label for="name" class="sr-only">Name</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            required
                            v-model="form.name"
                            :class="[errors.name ? 'border-red-300' : 'border-gray-300']"
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Full name"
                        />
                        <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</p>
                    </div>

                    <!-- Email Input -->
                    <div>
                        <label for="email-address" class="sr-only">Email address</label>
                        <input
                            id="email-address"
                            name="email"
                            type="email"
                            required
                            v-model="form.email"
                            :class="[errors.email ? 'border-red-300' : 'border-gray-300']"
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Email address"
                        />
                        <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email[0] }}</p>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            v-model="form.password"
                            :class="[errors.password ? 'border-red-300' : 'border-gray-300']"
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Password"
                        />
                        <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password[0] }}</p>
                    </div>

                    <!-- Confirm Password Input -->
                    <div>
                        <label for="password_confirmation" class="sr-only">Confirm Password</label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            required
                            v-model="form.password_confirmation"
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Confirm password"
                        />
                    </div>
                </div>

                <!-- Success Message -->
                <div v-if="success" class="rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <CheckCircleIcon class="h-5 w-5 text-green-400" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ success }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                <div v-if="error" class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <XCircleIcon class="h-5 w-5 text-red-400" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">
                                {{ error }}
                            </p>
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
                        <span v-if="loading">Registering...</span>
                        <span v-else>Register</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { LockClosedIcon, CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/solid'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
})

const loading = ref(false)
const error = ref('')
const success = ref('')
const errors = ref({})

const handleRegister = async () => {
    loading.value = true
    error.value = ''
    success.value = ''
    errors.value = {}

    try {
        await authStore.register(form.value)
        success.value = 'Registration successful! Redirecting to login...'
        
        // Reset form
        form.value = {
            name: '',
            email: '',
            password: '',
            password_confirmation: ''
        }

        console.log("hello");

        // Redirect to login after 2 seconds
        setTimeout(() => {
            router.push({
                name: 'login',
                query: { 
                    registered: 'true',
                    email: form.value.email
                }
            })
        }, 2000)
    } catch (e) {
        if (e.response?.data?.errors) {
            errors.value = e.response.data.errors
            error.value = e.response?.data?.message || 'Registration failed'
        } else {
            console.log(e)
        }
    } finally {
        loading.value = false
    }
}
</script>