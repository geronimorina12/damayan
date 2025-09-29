<script setup>
import { ref, defineProps, watch, onMounted, onUnmounted, nextTick, computed } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Header from '@/Components/dashboard/admin/registeredMember/Header.vue'
import Alert from '@/Components/dashboard/admin/registeredMember/Alert.vue'
import AddNewMember from '@/Components/dashboard/admin/registeredMember/members/AddNewMember.vue'

const props = defineProps({
  members: {
    type: Object, // will contain data, links, meta
    default: () => ({ data: [], links: [], prev_page_url: null, next_page_url: null })
  }
})

let getMembers = ref([])
const showActionsPopup = ref(false)
const popupPosition = ref({ top: '0px', left: '0px' })
const activeMemberId = ref(null)
const actionButtonRefs = ref({})
const statusChangeAlert = ref(false)
const passNameToAlert = ref('')
const searchQuery = ref("")   // 🔍 search input

// Watch members from props
watch(
  () => props.members,
  (newMember) => {
    getMembers.value = newMember.data || []
  },
  { immediate: true }
)

// Computed: filter members based on searchQuery
const filteredMembers = computed(() => {
  if (!searchQuery.value) return getMembers.value
  return getMembers.value.filter((member) => {
    const fullName = `${member.first_name} ${member.middle_name || ''} ${member.last_name}`.toLowerCase()
    return (
      fullName.includes(searchQuery.value.toLowerCase()) ||
      (member.contact_number || '').toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      (member.purok || '').toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  })
})

const trashMember = (id) => {
  if (confirm('Are you sure you want to trash this member?')) {
    router.delete(route('deleteMember', { id }), {
      onSuccess: () => {
        alert('Member trashed')
        showActionsPopup.value = false
      },
    })
  }
}

const toggleMemberStatus = (member) => {
  const newStatus = member.status === 'active' ? 'inactive' : 'active'
  router.put(route('toggleMemberStatus', { id: member.id }), {
    status: newStatus
  }, {
    onSuccess: () => {
      member.status = newStatus
      statusChangeAlert.value = newStatus === 'active'
      passNameToAlert.value = `${member.first_name} ${member.middle_name} ${member.last_name}`
    }
  })
}

const togglePopup = (event, memberId) => {
  if (showActionsPopup.value && activeMemberId.value === memberId) {
    showActionsPopup.value = false
    activeMemberId.value = null
    return
  }

  activeMemberId.value = memberId
  showActionsPopup.value = true

  nextTick(() => {
    const buttonRect = event.currentTarget.getBoundingClientRect()
    const popupElement = document.querySelector('.actions-popup')
    if (!popupElement) return

    const popupWidth = popupElement.offsetWidth
    const viewportWidth = window.innerWidth || document.documentElement.clientWidth

    let newLeft = buttonRect.right + window.scrollX + 10
    if (newLeft + popupWidth > viewportWidth) {
      newLeft = buttonRect.left + window.scrollX - popupWidth - 10
      if (newLeft < 0) newLeft = 10
    }

    popupPosition.value = {
      top: `${buttonRect.top + window.scrollY}px`,
      left: `${newLeft}px`
    }
  })
}

const closePopup = (event) => {
  if (showActionsPopup.value && event.target &&
      !event.target.closest('.actions-popup') &&
      !event.target.closest('.three-dots-button')) {
    showActionsPopup.value = false
    activeMemberId.value = null
  }
}

onMounted(() => {
  document.addEventListener('click', closePopup)
})

onUnmounted(() => {
  document.removeEventListener('click', closePopup)
})

// pagination navigation
const goToPage = (url) => {
  if (url) {
    router.get(url, {}, { preserveScroll: true, preserveState: true })
  }
}
</script>

<template>
  <Head title="Registered members" />
  <AdminLayout>
    <div class="main-section bg-light">

      <Header />
      <!-- Alert for status changes -->
      <Alert 
        :status="statusChangeAlert"
        :name="passNameToAlert"
      />

      <div class="container table-container">
        
        <!-- 🔍 Search Bar -->
        <div class="mb-3">
          <input 
            type="text" 
            class="form-control" 
            placeholder="Search members by name, contact, or purok..." 
            v-model="searchQuery"
          />
        </div>

        <div class="table-responsive">
          <table class="table table-bordered align-middle text-center">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>NAME</th>
                <th>GENDER</th>
                <th>AGE</th>
                <th>CONTACT NO.</th>
                <th>STREET</th>
                <th>STATUS</th>
                <th>ACTION</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(member, index) in filteredMembers" :key="index">
                <td>{{ member.id }}</td>  
                <td class="text-start">{{ member?.first_name }} {{ member?.middle_name }} {{ member?.last_name }}</td>
                <td>{{ member.gender || 'N/A' }}</td>
                <td>{{ member.age }}</td>
                <td>{{ member.contact_number }}</td>
                <td>{{ member.purok }}</td>
                <td>
                  <div class="form-check form-switch d-inline-flex justify-content-center">
                    <input class="form-check-input" type="checkbox"
                      :checked="member.status === 'active'"
                      @click="toggleMemberStatus(member)" />
                  </div>
                </td>
                <td class="actions-column">
                  <div class="action-buttons-large">
                    <Link :href="route('viewMemberInfo', {id: member?.id})" class=" me-1">
                      <i class="bi bi-eye"></i>
                    </Link>
                    <Link :href="route('editMember', {id: member?.id})" class=" me-1">
                      <i class="bi bi-pencil"></i>
                    </Link>
                    <button class="" @click="trashMember(member.id)">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>

                  <div class="action-buttons-small">
                    <button
                      class="btn btn-sm btn-outline-dark three-dots-button"
                      @click.stop="togglePopup($event, member.id)"
                      :ref="el => { if (el) actionButtonRefs[member.id] = el }"
                    >
                      <i class="bi bi-three-dots-vertical"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="filteredMembers.length === 0">
                <td colspan="8" class="text-center text-muted">No members found</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div class="pagination-controls d-flex justify-content-center mt-3 mb-5 pb-5 pt-3">
        <button
          v-for="(link, index) in props.members.links"
          :key="index"
          class="btn"
          :class="link.active ? 'btn-primary' : 'btn-outline-primary'"
          @click="goToPage(link.url)"
          v-html="link.label"
        />
      </div>
    </div>

    <!-- Popup menu -->
    <div
      v-if="showActionsPopup"
      class="actions-popup card shadow"
      :style="{ top: popupPosition.top, left: popupPosition.left }"
    >
      <div class="list-group list-group-flush">
        <Link
          :href="route('viewMemberInfo', {id: activeMemberId})"
          class="list-group-item list-group-item-action"
          @click="showActionsPopup = false"
        >
          <i class="bi bi-eye me-2"></i> View Info
        </Link>
        <Link
          :href="route('editMember', {id: activeMemberId})"
          class="list-group-item list-group-item-action"
          @click="showActionsPopup = false"
        >
          <i class="bi bi-pencil me-2"></i> Edit Member
        </Link>
        <button
          class="list-group-item list-group-item-action text-danger"
          @click="trashMember(activeMemberId)"
        >
          <i class="bi bi-trash me-2"></i> Trash Member
        </button>
      </div>
    </div>

    <!-- Add new member modal -->
    <div class="modal fade" id="addNewMember" tabindex="-1" aria-labelledby="addNewMemberLabel" aria-hidden="true">
      <div class="modal-dialog" style="max-width: 800px;">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="addNewMemberLabel">Add New Member</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <AddNewMember />
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Save changes</button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>


<style scoped>
.main-section {
  width: 100%;
  height: 100%;
  overflow-x: hidden;
  overflow-y: scroll;
  padding-bottom: 2rem;
}

.table th,
.table td {
  vertical-align: middle;
}

.action-buttons-small {
  display: none;
}

.action-buttons-large {
  display: block;
}

.actions-popup {
  position: absolute;
  z-index: 1000;
  min-width: 150px;
}

.main-section {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.table-container {
  flex: 1;
  overflow-y: auto;
}

.pagination-controls {
  position: sticky;
  bottom: 0;
  left: 0;
  width: 100%;
  background: #fff;
  padding: 10px;
  border-top: 1px solid #dee2e6;
  z-index: 100;
}


@media (max-width: 755.98px) {
  .action-buttons-large {
    display: none;
  }

  .action-buttons-small {
    display: block; 
  }

  .table-responsive table {
    font-size: 0.85em;
  }

  .table-responsive th,
  .table-responsive td {
    padding: 0.5rem;
  }
}
</style>
