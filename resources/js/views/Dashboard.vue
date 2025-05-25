<template>
    <div class="space-y-6">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <stats-card
                title="Total Posts"
                :value="postStore.totalPosts"
                :icon="DocumentTextIcon"
            />
            <stats-card
                title="Scheduled Posts"
                :value="postStore.scheduledPosts"
                :icon="ClockIcon"
            />
        </div>

        <!-- Create Post Button -->
        <div class="flex justify-end">
            <button
                @click="showCreateModal = true"
                class="btn btn-primary flex items-center"
            >
                <PlusIcon class="h-5 w-5 mr-2" />
                Create New Post
            </button>
        </div>

        <ScheduledPostsCalendar />


        <!-- Posts List -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Posts</h3>
                <div class="flex space-x-3">
                    <!-- Filters -->
                    <select
                        v-model="filters.status"
                        class="form-select rounded-md border-gray-300 text-sm"
                        @change="loadPosts"
                    >
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="published">Published</option>
                    </select>
                </div>
                <input
                    v-model="filters.date"
                    type="date"
                    class="form-input rounded-md border-gray-300 text-sm"
                    @change="loadPosts"
                />
            </div>
            <div class="border-t border-gray-200">
                <ul v-if="postStore.posts?.data?.length" class="divide-y divide-gray-200">
                    <li
                        v-for="post in postStore.posts?.data"
                        :key="post.id"
                        class="px-4 py-4 sm:px-6 hover:bg-gray-50"
                    >
                        <post-list-item :post="post" @edit="editPost" @delete="deletePost" />
                    </li>
                </ul>
                <div v-else class="px-4 py-5 sm:px-6 text-center text-gray-500">
                    No posts found
                </div>
            </div>
        </div>

        <!-- Create/Edit Post Modal -->
        <create-post-modal
            v-if="showCreateModal"
            :post="selectedPost"
            @close="closeCreateModal"
            @saved="handlePostSaved"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { usePostStore } from '@/stores/posts'
import { 
    PlusIcon, 
    DocumentTextIcon, 
    ClockIcon 
} from '@heroicons/vue/24/solid'
import StatsCard from '@/components/Dashboard/StatsCard.vue'
import PostListItem from '@/components/Dashboard/PostListItem.vue'
import CreatePostModal from '@/components/Dashboard/CreatePostModal.vue'
import ScheduledPostsCalendar from '@/components/ScheduledPostsCalendar.vue'

const postStore = usePostStore()
const showCreateModal = ref(false)
const selectedPost = ref(null)
const currentDateTime = ref('2025-05-22 05:43:53')
const filters = ref({
    status: '',
    date: null
})

// Update time every second
onMounted(() => {
    setInterval(() => {
        const now = new Date()
        currentDateTime.value = now.toISOString().slice(0, 19).replace('T', ' ')
    }, 1000)
    loadPosts()
})

const loadPosts = async () => {
    await postStore.fetchPosts(filters.value)
}

const closeCreateModal = () => {
    showCreateModal.value = false
    selectedPost.value = null
}

const handlePostSaved = () => {
    closeCreateModal()
    loadPosts()
}

const editPost = (post) => {
    selectedPost.value = post
    showCreateModal.value = true
}

const deletePost = async (postId) => {
    if (confirm('Are you sure you want to delete this post?')) {
        await postStore.deletePost(postId)
        loadPosts()
    }
}
</script>