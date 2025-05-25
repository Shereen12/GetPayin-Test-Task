import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('@/views/Dashboard.vue'),
        meta: { requiresAuth: true }
    },

    {
        path: '/',
        name: 'home',
        component: () => import('@/views/Dashboard.vue'),
        meta: { requiresAuth: true }
    },
    
    {
        path: '/login',
        name: 'login',
        component: () => import('@/views/Auth/Login.vue'),
        meta: { guest: true }
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('@/views/Auth/Register.vue'),
        meta: { guest: true }
    },

    {
        path: '/profile',
        name: 'profile',
        component: () => import('@/views/Profile/Index.vue'),
        meta: { requiresAuth: true }
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore()
    const isAuthenticated = !!localStorage.getItem('token')

    // Update authentication state
    authStore.isAuthenticated = isAuthenticated

    if (to.meta.requiresAuth && !isAuthenticated) {
        next({ name: 'login' })
    } else if (to.meta.guest && isAuthenticated) {
        next({ name: 'dashboard' })
    } else {
        next()
    }
})

export default router