import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        isAuthenticated: false,
        userLogin: null,
        token: localStorage.getItem('token') || null,
        currentDateTime: '2025-05-22 05:49:29'
    }),

    actions: {
        async login(credentials) {
            try {
                const response = await axios.post('/api/login', credentials)
                const { token, user } = response.data.data
                
                this.setAuthData(token, user)
                return response
            } catch (error) {
                throw error
            }
        },

        async register(userData) {
            try {
                const response = await axios.post('/api/register', userData)
                const { token, user } = response.data.data
                
                this.setAuthData(token, user)
                return response
            } catch (error) {
                throw error
            }
        },

        async logout() {
            try {
                // Call backend logout endpoint if it exists
                if (this.token) {
                    await axios.post('/api/logout', {}, {
                        headers: {
                            'Authorization': `Bearer ${this.token}`
                        }
                    })
                }
            } catch (error) {
                console.error('Logout API error:', error)
            } finally {
                // Always clear local state
                this.clearAuthData()
            }
        },

        setAuthData(token, user) {
            this.token = token
            this.user = user
            this.isAuthenticated = true
            this.userLogin = user.name
            localStorage.setItem('token', token)
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        },

        clearAuthData() {
            this.token = null
            this.user = null
            this.isAuthenticated = false
            localStorage.removeItem('token')
            delete axios.defaults.headers.common['Authorization']
        }
    },

    getters: {
        userFullName: (state) => state.user?.name,
        userEmail: (state) => state.user?.email || '',
        getCurrentDateTime: (state) => state.currentDateTime
    }
})