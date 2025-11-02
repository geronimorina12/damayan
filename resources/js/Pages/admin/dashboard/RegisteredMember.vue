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

//  Watch members
watch(
  () => props.members,
  (newMember) => {
    getMembers.value = newMember.data || []
  },
  { immediate: true }
)

//  Watch deceased
watch(
  () => props.deceasedMember,
  (newData) => {
    getDeceasedMember.value = newData.data || []
  },
  { immediate: true }
)

//  Computed for search
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

//  Trash Member
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

//  Toggle Member Status
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

//  Popup toggle
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

//  Close popup when clicking outside
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

//  Pagination
const goToPage = (url) => {
  if (url) router.visit(url)
}


//  Custom deceased modal
const isDead = (member) => {
  deceasedMember.value = member || null
  showDeceasedModal.value = true
}

const closeDeceasedModal = () => {
  showDeceasedModal.value = false
}

//  Edit Member Modal Trigger
const EditMemberFunc = (member) => {
  showActionsPopup.value = false
  editMemberValue.value = { ...member } // make it reactive and assign directly
}
const searchResult = ref(null)
const searching = ref(false)
const searchError = ref("")

watch(searchQuery, async (newQuery) => {
  if (!newQuery) {
    searchResult.value = null
    searchError.value = ""
    return
  }

  searching.value = true
  try {
    const response = await axios.get(route('members.search'), { params: { query: newQuery } })
    searchResult.value = response.data
    searchError.value = ""
  } catch (error) {
    searchResult.value = null
    searchError.value = error.response?.data?.message || "No member found"
  } finally {
    searching.value = false
  }
})
const searchPage = (page) => {
  router.visit(route('members.searchPage') + `?page=${page}`)
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
  <!-- Always visible search box -->
  <input 
    type="text" 
    class="form-control w-50"
    placeholder="Search members by name, contact, or purok..." 
    v-model="searchQuery"
  />

  <!-- Search status messages -->
  <div v-if="searching" class="text-muted mt-2">Searching...</div>

  <div v-else-if="searchResult" class="alert alert-success mt-2">
    <strong>Found:</strong> {{ searchResult.member.first_name }} {{ searchResult.member.last_name }} 
    <br>
    <small>Appears on page: {{ searchResult.page }}</small>
  <button
  class="btn btn-sm btn-primary ms-2"
  @click="searchPage(searchResult.page)"
>
  Go to Page {{ searchResult.page }}
</button>


  </div>

  <div v-else-if="searchError" class="alert alert-warning mt-2">
    {{ searchError }}
  </div>
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

                <td class="status-cell">
  <div class="status-toggle">
    <label class="toggle-switch">
      <input
        type="checkbox"
        :checked="member.status === 'active'"
        @change="toggleMemberStatus(member)"
      >
      <span class="toggle-slider"></span>
    </label>
    <span
      class="status-label"
      :class="member.status === 'active' ? 'active' : 'inactive'"
    >
      {{ member.status === 'active' ? 'Active' : 'Inactive' }}
    </span>
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

    <!--  Action Popup -->
    <div
      v-if="showActionsPopup"
      class="actions-popup bg-white border rounded shadow-sm p-2"
      :style="{ top: popupPosition.top, left: popupPosition.left, position: 'absolute' }"
    >
      <ul class="list-unstyled m-0">
        <li>
          <Link :href="route('viewMemberInfo', { id: activeMemberId })" class="dropdown-item">
            <i class="bi bi-eye me-2"></i> View
          </Link>
        </li>
        <li>
          <button class="dropdown-item w-100 text-start" @click="EditMemberFunc(getMembers.find(m => m.id === activeMemberId))" data-bs-toggle="modal" data-bs-target="#editMember">
            <i class="bi bi-pencil me-2"></i> Edit
          </button>
        </li>
        <li>
          <button class="dropdown-item w-100 text-start text-danger" @click="trashMember(activeMemberId)">
            <i class="bi bi-trash me-2"></i> Delete
          </button>
        </li>
      </ul>
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
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 6px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  z-index: 2000;
  min-width: 140px;
  animation: fadeIn 0.2s ease;
}
.actions-popup ul li {
  padding: 6px 10px;
  cursor: pointer;
  font-size: 0.9rem;
}
.actions-popup ul li:hover {
  background-color: #f8f9fa;
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


.status-cell {
  text-align: center;
}

.status-toggle {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
}

.toggle-switch {
  position: relative;
  display: inline-block;
  width: 44px;
  height: 24px;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #e5e7eb;
  transition: .3s;
  border-radius: 24px;
}

.toggle-slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .3s;
  border-radius: 50%;
}

input:checked + .toggle-slider {
  background-color: #10b981;
}

input:checked + .toggle-slider:before {
  transform: translateX(20px);
}

.status-label {
  font-size: 0.875rem;
  font-weight: 500;
}

.status-label.active {
  color: #10b981;
}

.status-label.inactive {
  color: #ef4444;
}

.actions-cell {
  text-align: center;
}

.action-buttons {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
}

.btn-action {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 8px;
  background: transparent;
  transition: all 0.2s ease;
  cursor: pointer;
}

.btn-edit {
  color: #6b7280;
}

.btn-edit:hover {
  background-color: #eff6ff;
  color: #3b82f6;
}

.btn-delete {
  color: #6b7280;
}

.btn-delete:hover {
  background-color: #fef2f2;
  color: #ef4444;
}

.empty-state {
  padding: 4rem 2rem;
  text-align: center;
}

.empty-content h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #374151;
  margin: 1rem 0 0.5rem;
}

.empty-content p {
  color: #6b7280;
  max-width: 400px;
  margin: 0 auto;
}

.footer-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-top: 1px solid #f3f4f6;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.2s ease;
  border: none;
  cursor: pointer;
  font-size: 0.875rem;
}

.btn-secondary {
  background-color: #f8fafc;
  color: #374151;
  border: 1px solid #e5e7eb;
}

.btn-secondary:hover {
  background-color: #f1f5f9;
  color: #1e293b;
}

.btn-primary {
  background-color: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background-color: #2563eb;
}

.modern-modal {
  max-width: 700px;
}

.modern-modal .modal-content {
  border-radius: 12px;
  border: none;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.modern-modal .modal-header {
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #f3f4f6;
}

.modern-modal .modal-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
}

.modern-modal .modal-body {
  padding: 2rem;
}

@media (max-width: 768px) {
  .modern-container {
    padding: 1rem;
  }
  
  .footer-actions {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .footer-actions .btn {
    justify-content: center;
  }
  
  .modern-table th,
  .modern-table td {
    padding: 0.75rem 1rem;
  }
  
  .status-toggle {
    flex-direction: column;
    gap: 0.5rem;
  }
}
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes scaleUp { from { transform: scale(0.95); opacity: 0.8; } to { transform: scale(1); opacity: 1; } }
</style>
