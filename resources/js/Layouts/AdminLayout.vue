<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import HeaderComponent from '@/Components/dashboard/HeaderComponent.vue'

const toggleSidebar = ref(true)
const isSmallScreen = ref(false)
onMounted(() => {
  handleResize()
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})

const handleResize = () => {
  isSmallScreen.value = window.innerWidth < 768
  // If on large screen always keep sidebar open
  if (!isSmallScreen.value) {
    toggleSidebar.value = false
  }
}

const closeSidebar = () => {
  if (isSmallScreen.value && !toggleSidebar.value) {
    toggleSidebar.value = true
  }
}

const sidebarStyles = computed(() => {
  if (isSmallScreen.value) {
    return {
      position: 'absolute',
      top: 0,
      left: toggleSidebar.value ? '-100%' : '0%',
      width: '50%',
      height: '100vh',
      zIndex: 999,
      transition: 'left 0.3s ease-in-out',
      boxShadow: !toggleSidebar.value ? '2px 0 10px rgba(0,0,0,0.5)' : 'none',
    }
  } else {
    return {
      width: '20%',
      minWidth: '250px',
      position: 'relative',
      left: '0',
    }
  }
})

const rightContentStyles = computed(() => ({
  width: isSmallScreen.value ? '100%' : '80%',
  position: 'relative',
}))

const overlayStyles = computed(() => {
  if (isSmallScreen.value && !toggleSidebar.value) {
    return {
      display: 'block',
      position: 'absolute',
      top: 0,
      left: 0,
      width: '100%',
      height: '100%',
      backgroundColor: 'rgba(0, 0, 0, 0.5)',
      zIndex: 99,
    }
  }
  return { display: 'none' }
})
</script>

<template>
  <div class="d-flex min-vh-100 main-container">
    <!-- Sidebar -->
    <div class="sidebar text-white p-3 d-flex flex-column" :style="sidebarStyles">
      <div class="text-center mb-4 logo-container" @click="() => router.push(route('dashboard'))">
        <img src="../../images/logo1.png" alt="Logo" class="img-fluid rounded-circle mb-2 logo" />
        <h5 class="fw-bold text-dark">PROTEC DAMAYAN SYSTEM</h5>
      </div>

      <div class="nav flex-column">
        <div class="nav-item mb-2">
          <Link href="/dashboard" class="nav-link text-dark d-flex align-items-center" @click="closeSidebar">
            <i class="bi bi-house-door me-2"></i> Dashboard
          </Link>
        </div>
        <div class="nav-item mb-2">
          <Link :href="route('members.registered')" class="nav-link text-dark d-flex align-items-center" @click="closeSidebar">
            <i class="bi bi-people me-2"></i> Members
          </Link>
        </div>
        <div class="nav-item mb-2">
          <Link :href="route('contributions.index')" class="nav-link text-dark d-flex align-items-center" @click="closeSidebar">
            <i class="bi bi-cash-coin me-2"></i> Contribution
          </Link>
        </div>
        <div class="nav-item mb-2">
          <Link :href="route('reports.index')" class="nav-link text-dark d-flex align-items-center" @click="closeSidebar">
            <i class="bi bi-file-earmark-text me-2"></i> Reports
          </Link>
        </div>
        <div class="nav-item mb-2">
          <Link :href="route('officials.index')" class="nav-link text-dark d-flex align-items-center" @click="closeSidebar">
            <i class="bi bi-people me-2"></i> Officials
          </Link>
        </div>
        <div class="nav-item mb-2">
          <Link :href="route('archive.index')" class="nav-link text-dark d-flex align-items-center" @click="closeSidebar">
            <i class="bi bi-archive me-2"></i> Archived
          </Link>
        </div>
        <div class="nav-item mb-2">
          <Link :href="route('smsNotification.smsPage')" class="nav-link text-dark d-flex align-items-center" @click="closeSidebar">
            <i class="bi bi-bell me-2"></i> SMS
          </Link>
        </div>
        <div class="nav-item mb-2">
          <Link :href="route('settings.viewSettings')" class="nav-link text-dark d-flex align-items-center" @click="closeSidebar">
            <i class="bi bi-gear me-2"></i> Settings
          </Link>
        </div>
        <div class="nav-item mb-2">
          <Link :href="route('logout')" method="post" class="nav-link text-dark d-flex align-items-center" @click="closeSidebar">
            <i class="bi bi-box-arrow-left me-2"></i> Logout
          </Link>
        </div>
      </div>
    </div>

    <div class="flex-grow-1 bg-light right" :style="rightContentStyles">
      <div class="sidebar-overlay" :style="overlayStyles" @click="closeSidebar"></div>

      <div class="mb-4 bg-white">
        <HeaderComponent v-model:toggle="toggleSidebar" />
      </div>

      <slot />
    </div>
  </div>
</template>

<style scoped>
.nav-link:hover {
  background-color: rgba(255, 255, 255, 0.2);
  border-radius: 4px;
}
.sidebar {
  background: #7FEAFE;
  color: #333;
}
.main-container {
  width: 100%;
  height: 100vh;
  overflow: hidden !important;
}
.hr {
  height: 3px;
  background: #333;
  margin: 10px 0;
}
.logo {
  position: relative;
  width: 60%;
  height: 140px;
  margin: auto;
}
.logo-container {
  cursor: pointer;
}
</style>
