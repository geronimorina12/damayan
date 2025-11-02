<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import axios from 'axios'

const toggle = defineModel('toggle')
const hasNotification = ref(false)

const toggleSidebar = () => {
  toggle.value = !toggle.value
}

// Fetch notification status
const fetchNotification = async () => {
  try {
    const response = await axios.get('/collector/notification/has')
    hasNotification.value = response.data.status === true
  } catch (error) {
    console.error('Error fetching notification:', error)
  }
}

onMounted(() => {
  fetchNotification()
  setInterval(fetchNotification, 30000) // refresh every 30s
})
</script>

<template>
  <div class="header container-fluid d-flex flex-row justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center container-fluid py-2">
      <button class="me-2" @click="toggleSidebar">
        <i class="bi bi-list fs-2 d-block d-lg-none"></i>
      </button>
      <h4 class="mb-0 title">PROTEC DAMAYAN SYSTEM</h4>
    </div>

    <div class="position-relative me-3">
      <Link :href="route('smsNotification.smsPage')" class="bi bi-bell fs-3 text-dark"></Link>
      <span
        v-if="hasNotification"
        class="notification-dot position-absolute top-0 start-100 translate-middle rounded-circle bg-success"
      ></span>
    </div>
  </div>
</template>

<style scoped>
.header {
  background: #D4F3F9;
  padding: 0.7rem 0;
}

.title {
  font-family: 'Poppins', sans-serif;
  font-weight: bold;
}

.notification-dot {
  width: 12px;
  height: 12px;
  border: 2px solid white;
}

@media screen and (max-width: 413px) {
  .title {
    font-size: 1rem;
  }
}
</style>
