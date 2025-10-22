<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import axios from 'axios'

const toggle = defineModel('toggle')
const hasNotification = ref(false)
const notifications = ref([])
const showModal = ref(false)

const toggleSidebar = () => {
  toggle.value = !toggle.value
}

// Fetch notifications from backend
const fetchNotification = async () => {
  try {
    const response = await axios.get('/collector/notification/has', { withCredentials: true })
    hasNotification.value = response.data.read_status === false
    notifications.value = response.data.notifications || []
  } catch (error) {
    console.error('Error fetching notification:', error)
  }
}

// Mark a notification as read
const markAsRead = async (id) => {
  try {
    await axios.post(`/collector/notification/mark-as-read/${id}`, {}, { withCredentials: true })
    // Refresh the list after marking as read
    hasNotification.value = false;
    fetchNotification()
  } catch (error) {
    console.error('Error marking notification as read:', error)
  }
}

onMounted(() => {
  fetchNotification()
  setInterval(fetchNotification, 30000)
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

    <div class="position-relative me-4">
      <button
        type="button"
        class="btn btn-link text-dark p-0 position-relative"
        data-bs-toggle="modal"
        data-bs-target="#notificationModal"
      >
        <i class="bi bi-bell fs-3"></i>
        <span
          v-if="hasNotification"
          class="notification-dot position-absolute top-0 start-100 translate-middle rounded-circle bg-success"
        ></span>
      </button>
    </div>
  </div>

  <!-- Notification Modal -->
  <div
    class="modal fade"
    id="notificationModal"
    tabindex="-1"
    aria-labelledby="notificationModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow-lg rounded-4">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="notificationModalLabel">
            <i class="bi bi-bell-fill me-2"></i>Notifications
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          <div v-if="notifications.length > 0" class="list-group list-group-flush">
            <div
              v-for="notif in notifications"
              :key="notif.id"
              class="list-group-item d-flex justify-content-between align-items-start"
              :class="{ 'bg-light': notif.read_status }"
            >
              <div>
                <div class="text-muted small">{{ notif.message }}</div>
                <div class="text-end text-secondary small">
                  {{ new Date(notif.created_at).toLocaleString() }}
                </div>
              </div>

              <button
                v-if="!notif.read_status"
                @click="markAsRead(notif.id)"
                class="btn btn-outline-success btn-sm ms-3 px-2"
                title="Mark as Read"
              >
                <i class="bi bi-check2-circle"></i>
              </button>
            </div>
          </div>
          <div v-else class="text-center py-4 text-muted">
            No notifications found.
          </div>
        </div>
      </div>
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

.list-group-item {
  border: none;
  border-bottom: 1px solid #eee;
}

.list-group-item:last-child {
  border-bottom: none;
}

.btn-outline-success {
  border-radius: 50%;
  width: 32px;
  height: 32px;
  padding: 0;
}

@media screen and (max-width: 413px) {
  .title {
    font-size: 1rem;
  }
}
</style>
