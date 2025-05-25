import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import router from '@/router'

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.withCredentials = true

// Add auth token if it exists
const token = localStorage.getItem('token')
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

// Add interceptor to handle authentication errors
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            const authStore = useAuthStore()
            authStore.clearAuthData()
            router.push('/login')
        }
        return Promise.reject(error)
    }
)