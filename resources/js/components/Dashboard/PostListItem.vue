<template>
    <div class="flex items-center justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex items-center">
                <p class="text-sm font-medium text-indigo-600 truncate">{{ post.title }}</p>
                <span 
                    :class="[
                        'ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                        statusColors[post.status]
                    ]"
                >
                    {{ post.status }}
                </span>
            </div>
            <div class="mt-2 flex">
                <div class="flex items-center text-sm text-gray-500">
                    <ClockIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
                    <p>
                        Scheduled for: {{ formatDateTime(post.scheduled_time) }}
                    </p>
                </div>
            </div>
        </div>
        <div class="flex space-x-2">
            <button
                @click="$emit('edit', post)"
                class="inline-flex items-center p-2 border border-transparent rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-500"
            >
                <PencilIcon class="h-5 w-5" />
            </button>
            <button
                @click="$emit('delete', post.id)"
                class="inline-flex items-center p-2 border border-transparent rounded-full text-gray-400 hover:bg-gray-100 hover:text-red-500"
            >
                <TrashIcon class="h-5 w-5" />
            </button>
        </div>
    </div>
</template>

<script setup>
import { 
    ClockIcon, 
    PencilIcon, 
    TrashIcon 
} from '@heroicons/vue/24/outline'
import dayjs from 'dayjs'

const props = defineProps({
    post: {
        type: Object,
        required: true
    }
})

const statusColors = {
    draft: 'bg-gray-100 text-gray-800',
    scheduled: 'bg-yellow-100 text-yellow-800',
    published: 'bg-green-100 text-green-800',
    failed: 'bg-red-100 text-red-800'
}

const formatDateTime = (datetime) => {
    return dayjs(datetime).format('YYYY-MM-DD HH:mm:ss')
}
</script>