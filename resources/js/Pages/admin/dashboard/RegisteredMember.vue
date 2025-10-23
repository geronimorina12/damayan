<script setup>
import { ref, defineProps, watch, onMounted, onUnmounted, nextTick, computed } from 'vue'
import { router, Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Header from '@/Components/dashboard/admin/registeredMember/Header.vue'
import Alert from '@/Components/dashboard/admin/registeredMember/Alert.vue'
import AddNewMember from '@/Components/dashboard/admin/registeredMember/members/AddNewMember.vue'
import IsDeceased from '@/Components/dashboard/admin/registeredMember/members/IsDeceased.vue'
import NewlyCreated from '@/Components/dashboard/admin/registeredMember/members/NewlyCreated.vue'
import EditMember from '@/Components/dashboard/admin/registeredMember/members/EditMember.vue'

const props = defineProps({
  members: {
    type: Object,
    default: () => ({ data: [], links: [], prev_page_url: null, next_page_url: null })
  },
  deceasedMember: {
    type: Array,
    default: () => []
  }
})

let getMembers = ref([])
const showActionsPopup = ref(false)
const popupPosition = ref({ top: '0px', left: '0px' })
const activeMemberId = ref(null)
const actionButtonRefs = ref({})
const statusChangeAlert = ref(false)
const passNameToAlert = ref('')
const searchQuery = ref("")
const getDeceasedMember = ref([])
const deceasedMember = ref({})
const showDeceasedModal = ref(false)
const editMemberValue = ref({})

// ✅ Watch members
watch(
  () => props.members,
  (newMember) => {
    getMembers.value = newMember.data || []
  },
  { immediate: true }
)

// ✅ Watch deceased
watch(
  () => props.deceasedMember,
  (newData) => {
    getDeceasedMember.value = newData.data || []
  },
  { immediate: true }
)

// ✅ Computed for search
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

// ✅ Trash Member
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

// ✅ Toggle Member Status
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

// ✅ Popup toggle
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

// ✅ Close popup when clicking outside
const closePopup = (event) => {
  if (showActionsPopup.value && event.target &&
      !event.target.closest('.actions-popup') &&
      !event.target.closest('.three-dots-button')) {
    showActionsPopup.value = false
    activeMemberId.value = null
  }
}

onMounted(() => document.addEventListener('click', closePopup))
onUnmounted(() => document.removeEventListener('click', closePopup))

// ✅ Pagination
const goToPage = (url) => {
  if (url) router.get(url, {}, { preserveScroll: true, preserveState: true })
}

// ✅ Custom deceased modal
const isDead = (member) => {
  deceasedMember.value = member || null
  showDeceasedModal.value = true
}

const closeDeceasedModal = () => {
  showDeceasedModal.value = false
}

// ✅ Edit Member Modal Trigger
const EditMemberFunc = (member) => {
  showActionsPopup.value = false
  editMemberValue.value = { ...member } // make it reactive and assign directly
}
</script>

<template>
  <Head title="Registered members" />
  <AdminLayout>
    <div class="main-section bg-light">
      <Header />
      <Alert :status="statusChangeAlert" :name="passNameToAlert" />

      <div class="container table-container">
        <div class="mb-3">
          <input 
            type="text" 
            class="form-control w-50" 
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
                <th>DECEASED</th>
                <th>ACTION</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(member, index) in filteredMembers" :key="index">
                <td>{{ member.id }}</td>
                <td class="text-start">{{ member.first_name }} {{ member.middle_name }} {{ member.last_name }}</td>
                <td>{{ member.gender || 'N/A' }}</td>
                <td>{{ member.age }}</td>
                <td>{{ member.contact_number }}</td>
                <td>{{ member.purok }}</td>
                <td>
                  <div class="form-check form-switch d-inline-flex justify-content-center">
                    <input 
                      class="form-check-input" 
                      type="checkbox"
                      :checked="member.status === 'active'"
                      @click="toggleMemberStatus(member)" 
                    />
                  </div>
                </td>
                <td>
                  <div class="form-check form-switch d-inline-flex justify-content-center">
                    <input 
                      class="form-check-input" 
                      type="checkbox"
                      :checked="getDeceasedMember.some(d => d.member_id === member.id)"
                      @click="isDead(member)"
                    />
                  </div>
                </td>
                <td class="actions-column">
                  <div class="action-buttons-large">
                    <Link :href="route('viewMemberInfo', {id: member.id})" class="me-1">
                      <i class="bi bi-eye"></i>
                    </Link>
                    <button data-bs-toggle="modal" data-bs-target="#editMember"
                      @click="EditMemberFunc(member)"
                      class="me-1">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button @click="trashMember(member.id)">
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
                <td colspan="9" class="text-center text-muted">No members found</td>
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

    <!-- Edit Member Modal -->
    <div class="modal fade" id="editMember" tabindex="-1" aria-labelledby="editMemberLabel" aria-hidden="true">
      <div class="modal-dialog" style="max-width: 800px;">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="text-muted">Edit info.</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!--  Reactive data passed -->
            <EditMember :member="editMemberValue"/>
          </div>
        </div>
      </div>
    </div>

    <!-- Deceased Modal -->
    <div v-if="showDeceasedModal" class="custom-modal-overlay" @click.self="closeDeceasedModal">
      <div class="custom-modal">
        <div class="custom-modal-header">
          <h3>Add Deceased Info</h3>
          <span class="close-icon" @click="closeDeceasedModal">&times;</span>
        </div>
        <div class="custom-modal-body">
          <IsDeceased :deceasedMember="deceasedMember" />
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
.table th, .table td {
  vertical-align: middle;
}
.action-buttons-small { display: none; }
.action-buttons-large { display: block; }

.actions-popup {
  position: absolute;
  z-index: 1000;
  min-width: 150px;
}

.pagination-controls {
  position: sticky;
  bottom: 0;
  background: #fff;
  padding: 10px;
  border-top: 1px solid #dee2e6;
}

@media (max-width: 755.98px) {
  .action-buttons-large { display: none; }
  .action-buttons-small { display: block; }
  .table-responsive table { font-size: 0.85em; }
  .table-responsive th, .table-responsive td { padding: 0.5rem; }
}

.custom-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.55);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1050;
  animation: fadeIn 0.3s ease;
}
.custom-modal {
  background: white;
  border-radius: 10px;
  width: 90%;
  max-width: 700px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.3);
  overflow: hidden;
  animation: scaleUp 0.25s ease;
}
.custom-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #007bff;
  color: white;
  padding: 15px 20px;
}
.custom-modal-header h3 {
  margin: 0;
  font-size: 1.2rem;
}
.close-icon {
  cursor: pointer;
  font-size: 1.6rem;
  font-weight: bold;
  transition: color 0.2s;
}
.close-icon:hover { color: #ffdddd; }
.custom-modal-body {
  padding: 20px;
  max-height: 70vh;
  overflow-y: auto;
}
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes scaleUp { from { transform: scale(0.95); opacity: 0.8; } to { transform: scale(1); opacity: 1; } }
</style>
