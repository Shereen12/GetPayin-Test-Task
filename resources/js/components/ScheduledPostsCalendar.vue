<template>
    <div>
      <h1>Scheduled Posts Calendar</h1>
      <FullCalendar
        :options='calendarOptions'
      />
    </div>
  </template>
  
  <script>
  import { ref, onMounted } from 'vue';
  import FullCalendar from '@fullcalendar/vue3';
  import dayGridPlugin from '@fullcalendar/daygrid';
  import axios from 'axios';
  import dayjs from 'dayjs';

  export default {
    name: 'ScheduledPostsCalendar',
    components: {
      FullCalendar,
    },
    setup() {
      const events = ref([]);

      const calendarOptions = ref({
            plugins: [dayGridPlugin],
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay',
            },
            events: events.value,
        });
  
      onMounted(async () => {
        try {
          const response = await axios.get('/api/scheduled-posts'); // Replace with your API endpoint
          const data = response.data.data;
          events.value = data.map(post => ({
            title: post.title,
            start: dayjs(post.scheduled_time).format('YYYY-MM-DD HH:mm:ss'),
            end: dayjs(post.scheduled_time).format('YYYY-MM-DD HH:mm:ss')
          }));

         

          calendarOptions.value.events = events.value;

        } catch (error) {
          console.error('Error fetching scheduled posts:', error);
        }
      });
  
      return {
        calendarOptions,
      };
    },
  };
  </script>
  