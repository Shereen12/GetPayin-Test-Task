import { defineStore } from 'pinia'
import axios from 'axios'

export const usePostStore = defineStore('posts', {
    state: () => ({
        posts: [],
        loading: false,
        error: null,
        totalPosts: 0,
        scheduledPosts: 0
    }),

    actions: {
        async fetchPosts(filters = {}) {
            this.loading = true
            try {
                const response = await axios.get('/api/posts', { params: filters })
                this.posts = response.data.data
                this.updateStats()
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch posts'
            } finally {
                this.loading = false
            }
        },

        async createPost(postData) {
            try {
                const response = await axios.post('/api/posts', postData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                
                this.updateStats()
                return response.data
            } catch (error) {
                throw error
            }
        },

        async updatePost(postId, postData) {
            try {
                const response = await axios.post(`/api/posts/${postId}`, postData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                const index = this.posts.data.findIndex(post => post.id === postId)
                if (index !== -1) {
                    this.posts[index] = response.data.data
                    this.updateStats()
                }
                return response.data
            } catch (error) {
                throw error
            }
        },

        async deletePost(postId) {
            try {
                await axios.delete(`/api/posts/${postId}`)
                this.posts.data = this.posts.data.filter(post => post.id !== postId)
                this.updateStats()
            } catch (error) {
                throw error
            }
        },

        updateStats() {
            this.totalPosts = this.posts.data.length
            this.scheduledPosts = this.posts.data.filter(post => post.status === 'scheduled').length
        }
    }
})