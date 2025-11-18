<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import HeaderComponent from '@/Components/dashboard/admin/HeaderComponent.vue'

const toggleSidebar = ref(true)
const isSmallScreen = ref(false)

const { url: currentRoute, props } = usePage()
const user = props.auth?.user

onMounted(() => {
  handleResize()
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})

const handleResize = () => {
  isSmallScreen.value = window.innerWidth < 768
  if (!isSmallScreen.value) toggleSidebar.value = false
}

const closeSidebar = () => {
  if (isSmallScreen.value && !toggleSidebar.value) toggleSidebar.value = true
}

const sidebarStyles = computed(() => isSmallScreen.value
  ? {
      position: 'absolute',
      top: 0,
      left: toggleSidebar.value ? '-100%' : '0%',
      width: '70%',
      height: '100vh',
      zIndex: 999,
      transition: 'left 0.3s ease-in-out',
      boxShadow: !toggleSidebar.value ? '2px 0 10px rgba(0,0,0,0.5)' : 'none'
    }
  : {
      width: '20%',
      minWidth: '250px',
      position: 'relative',
      left: '0'
    }
)

const rightContentStyles = computed(() => ({
  width: isSmallScreen.value ? '100%' : '80%',
  position: 'relative'
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
      zIndex: 99
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

        <div class="my-0 py-0 name-info">
          <hr class="hr">
        <Link :href="route('presidentProfile.show')" class="nav-link my-0 text-dark align-items-start py-0 d-flex flex-row justify-content-start" @click="closeSidebar">
          <div><i class="bi bi-person-circle me-2 fs-3"></i></div> 
          <div class=" text-start">
            <div class="name"> {{ user.name || 'User' }}</div>
            <div class=" text-success">President</div>  
          </div>
        </Link>
        <hr class="hr">
        </div>

        <Link href="/dashboard" class="nav-link text-dark d-flex align-items-center"
          @click="closeSidebar"
          :class="{ active: currentRoute.includes('/dashboard') }"
        >
          <i class="bi bi-house-door me-2"></i> Dashboard
        </Link>

        <Link :href="route('members.registered')" class="nav-link text-dark d-flex align-items-center"
          @click="closeSidebar"
          :class="{ active: currentRoute.includes('/registered-member') , active: currentRoute.includes('/send-to-all-selected/v2') }"
        >
          <i class="bi bi-people me-2"></i> Members
        </Link>

        <Link :href="route('contributions.index')" class="nav-link text-dark d-flex align-items-center"
          @click="closeSidebar"
          :class="{ active: currentRoute.includes('/view-contribution') }"
        >
          <i class="bi bi-cash-coin me-2"></i> Contribution
        </Link>

        <Link :href="route('reports.index')" class="nav-link text-dark d-flex align-items-center"
          @click="closeSidebar"
          :class="{ active: currentRoute.includes('/view-reports') }"
        >
          <i class="bi bi-file-earmark-text me-2"></i> Reports
        </Link>

        <Link :href="route('officials.index')" class="nav-link text-dark d-flex align-items-center"
          @click="closeSidebar"
          :class="{ active: currentRoute.includes('/officials') }"
        >
          <i class="bi bi-people me-2"></i> Officials
        </Link>

        <Link :href="route('archive.index')" class="nav-link text-dark d-flex align-items-center"
          @click="closeSidebar"
          :class="{ active: currentRoute.includes('/archive') }"
        >
          <i class="bi bi-archive me-2"></i> Archived
        </Link>
<!-- 
        <Link :href="route('smsNotification.smsPage')" class="nav-link text-dark d-flex align-items-center"
          @click="closeSidebar"
          :class="{ active: currentRoute.includes('/smsNotification/sms-page') }"
        >
          <i class="bi bi-bell me-2"></i> SMS
        </Link> -->

        <!-- <Link :href="route('settings.viewSettings')" class="nav-link text-dark d-flex align-items-center"
          @click="closeSidebar"
          :class="{ active: currentRoute.includes('/settings') }"
        >
          <i class="bi bi-gear me-2"></i> Settings
        </Link> -->

        <Link :href="route('logout')" method="post" class="nav-link text-dark d-flex align-items-center"
          @click="closeSidebar"
        >
          <i class="bi bi-box-arrow-left me-2"></i> Logout
        </Link>
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
.nav-link.active {
  background: rgba(156, 151, 151, 0.3);
}
.nav-link:hover {
  background-color: rgba(255, 255, 255, 0.2);
  border-radius: 4px;
}
.sidebar {
  background: #7FEAFE;
  color: #333;
  overflow-y: scroll;
}
.sidebar::-webkit-scrollbar{
  display: none;
}
.main-container {
  width: 100%;
  height: 100vh;
  overflow: hidden !important;
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
.name{
  font-size: 1rem;
}
.name-info hr {
  margin: 0 !important;
  height: 3px;
  background: #333;
  margin: 10px 0 !important;
}
</style>
