<script setup>
import { ref, defineProps, watch, onMounted, onUnmounted, nextTick, computed } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import axios from 'axios'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Header from '@/Components/dashboard/admin/registeredMember/Header.vue'
import Alert from '@/Components/dashboard/admin/registeredMember/Alert.vue'
import IsDeceased from '@/Components/dashboard/admin/registeredMember/members/IsDeceased.vue'
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

const getMembers = ref([])
const showActionsPopup = ref(false)
const popupPosition = ref({ top: '0px', left: '0px' })
const activeMemberId = ref(null)
const statusChangeAlert = ref(false)
const passNameToAlert = ref('')
const searchQuery = ref('')
const getDeceasedMember = ref([])
const deceasedMember = ref({})
const showDeceasedModal = ref(false)
const editMemberValue = ref({})
const searching = ref(false)
const searchError = ref('')
const lastClickedDeceasedId = ref(null)
const show = ref(false)

//  Initialize members from props
watch(
  () => props.members,
  (newMember) => {
    getMembers.value = newMember.data || []
  },
  { immediate: true }
)

//  Initialize deceased members
watch(
  () => props.deceasedMember,
  (newData) => {
    getDeceasedMember.value = newData.data || []
  },
  { immediate: true }
)

// 🧠 Debounced real-time search + logging
let searchTimer = null
const DEBOUNCE_MS = 400

watch(searchQuery, (newQuery) => {
  if (searchTimer) clearTimeout(searchTimer)
  searchTimer = setTimeout(() => performSearch(String(newQuery || '').trim()), DEBOUNCE_MS)
})

async function performSearch(query) {
  if (!query) {
    getMembers.value = props.members.data
    console.log('Search cleared — showing default members:', getMembers.value)
    searching.value = false
    return
  }

  searching.value = true
  searchError.value = ''
  console.log(`🔍 Searching for: "${query}"`)

  try {
    const response = await axios.get(route('members.search'), {
      params: { query }
    })

    console.log(' Raw Axios response:', response)
    console.log(' Response data:', response.data)

    let membersPayload = []
    const d = response?.data || {}

    // Normalize possible backend shapes
    if (Array.isArray(d.members)) {
      membersPayload = d.members
    } else if (d.members && Array.isArray(d.members.data)) {
      membersPayload = d.members.data
    } else if (d.member && typeof d.member === 'object') {
      membersPayload = [d.member]
    } else if (Array.isArray(d.data)) {
      membersPayload = d.data
    } else {
      const possible = Object.values(d).find(v => Array.isArray(v) && v[0]?.id)
      if (possible) membersPayload = possible
    }

    console.log('🧾 Normalized members array to display:', membersPayload)
    getMembers.value = membersPayload
  } catch (error) {
     //console.error('❌ Search error:', error)
     //searchError.value = 'Error fetching search results.'
  } finally {
    searching.value = false
  }
}

// 🗑️ Trash member
const trashMember = (id) => {
  if (confirm('Are you sure you want to trash this member?')) {
    router.delete(route('deleteMember', { id }), {
      onSuccess: () => {
        alert('Member trashed')
        showActionsPopup.value = false
      }
    })
  }
}

// 🔄 Toggle active/inactive status
const toggleMemberStatus = (member) => {
  const newStatus = member.status === 'active' ? 'inactive' : 'active'
  router.put(
    route('toggleMemberStatus', { id: member.id }),
    { status: newStatus },
    {
      onSuccess: () => {
        member.status = newStatus
        statusChangeAlert.value = newStatus === 'active'
        passNameToAlert.value = `${member.first_name} ${member.middle_name} ${member.last_name}`
      }
    }
  )
}

// ⚙️ Popup logic
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
    const viewportWidth = window.innerWidth
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
  if (
    showActionsPopup.value &&
    event.target &&
    !event.target.closest('.actions-popup') &&
    !event.target.closest('.three-dots-button')
  ) {
    showActionsPopup.value = false
    activeMemberId.value = null
  }
}

onMounted(() => document.addEventListener('click', closePopup))
onUnmounted(() => document.removeEventListener('click', closePopup))

// Pagination
const goToPage = (url) => {
  if (url) router.visit(url, { preserveState: true })
}

// Deceased modal
const isDead = (member) => {
  deceasedMember.value = member || null
  showDeceasedModal.value = true
  lastClickedDeceasedId.value = member?.id || null
}
const closeDeceasedModal = () => {
  showDeceasedModal.value = false

  // If user closed without saving, uncheck the deceased toggle
  if (lastClickedDeceasedId.value) {
    const checkbox = document.querySelector(
      `input[type="checkbox"][data-member-id="${lastClickedDeceasedId.value}"]`
    )
    if (checkbox) checkbox.checked = false
    lastClickedDeceasedId.value = null
  }

 
}

// Edit modal
const EditMemberFunc = (member) => {
  showActionsPopup.value = false
  editMemberValue.value = { ...member }
}
const overlay = document.getElementById('memberModalOverlay');
    const modal = document.getElementById('memberModal');
    const createBtn = document.getElementById('createBtn');
    const closeBtn = document.getElementById('closeBtn');

    let autoCloseTimer = null;

    function showModal({autoClose = true, autoCloseMs = 2500} = {}) {
      overlay.classList.add('show');
      modal.classList.add('show');
      overlay.setAttribute('aria-hidden', 'false');

      // Focus management: move focus into the dialog so screen readers announce it
      // and keyboard users can close it quickly.
      closeBtn.focus();

      // Auto-close after a short delay (optional)
      if (autoClose) {
        clearTimeout(autoCloseTimer);
        autoCloseTimer = setTimeout(hideModal, autoCloseMs);
      }
    }

    function hideModal() {
      modal.classList.remove('show');
      overlay.classList.remove('show');
      overlay.setAttribute('aria-hidden', 'true');

      // Return focus to the action that opened the dialog
      createBtn.focus();
      clearTimeout(autoCloseTimer);
    }
// Parent can toggle this using v-model:showModal


const closeModal = () => {
  show.value = false
}
const displayPaginationLinks = computed(() => {
  // If table has 1–5 members, only show 1 button
  if (getMembers.value.length <= 5) {
    return [
      {
        label: "1",
        url: props.members.links[0]?.url || null,
        active: true
      }
    ]
  }

  // Otherwise show real pagination links
  return props.members.links
})

</script>

<template>
  <Head title="Registered members" />
  <AdminLayout>
    <div class="main-section bg-light">
      <Header />
      <Alert :status="statusChangeAlert" :name="passNameToAlert" />

      <div class="container table-container">
        <!-- 🔍 Search box -->
        <div class="search-box mb-3 position-relative">
          <input 
            type="text" 
            class="form-control search-input"
            placeholder="Search members by name, contact, or purok..." 
            v-model="searchQuery"
          />
          <div v-if="searching" class="spinner-border text-primary spinner-sm position-absolute end-0 top-50 translate-middle-y me-3" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered align-middle text-center">
            <thead class="table-light">
              <tr>
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
              <!-- Loading animation row -->
              <tr v-if="searching">
                <td colspan="9" class="text-center">
                  <div class="spinner-border text-info" role="status"></div>
                  <p class="mt-2 text-muted">Searching...</p>
                </td>
              </tr>

              <!-- Error message -->
              <tr v-if="searchError && !searching">
                <td colspan="9" class="text-danger text-center">{{ searchError }}</td>
              </tr>

              <!-- Results -->
              <tr v-for="(member, index) in getMembers" :key="index">
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
                    <span class="status-label" :class="member.status === 'active' ? 'active' : 'inactive'">
                      {{ member.status === 'active' ? 'Active' : 'Inactive' }}
                    </span>
                  </div>
                </td>

                <td class="status-cell">
                  <div class="status-toggle">
                    <label class="toggle-switch">
                      <input
                        type="checkbox"
                        :data-member-id="member.id"
                        :checked="getDeceasedMember.some(d => d.member_id === member.id)"
                        @click="isDead(member)"
                      >
                      <span class="toggle-slider"></span>
                    </label>
                  </div>
                </td>

                <td class="actions-column">
                  <div class="action-buttons-large">
                    <Link :href="route('viewMemberInfo', {id: member.id})" class="me-1"><i class="bi bi-eye"></i></Link>
                    <button data-bs-toggle="modal" data-bs-target="#editMember" @click="EditMemberFunc(member)" class="me-1">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button @click="trashMember(member.id)"><i class="bi bi-trash"></i></button>
                  </div>

                  <div class="action-buttons-small">
                    <button class="btn btn-sm btn-outline-dark three-dots-button"
                      @click.stop="togglePopup($event, member.id)">
                      <i class="bi bi-three-dots-vertical"></i>
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="!searching && getMembers.length === 0">
                <td colspan="9" class="text-center text-muted">No members found</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="pagination-wrapper mt-3 mb-5">
          <div class="pagination-controls">
            <button
              v-for="(link, index) in displayPaginationLinks"
              :key="index"
              class="btn pagination-btn"
              :class="link.active ? 'btn-primary' : 'btn-outline-primary'"
              @click="goToPage(link.url)"
              v-html="link.label"
            />

          </div>
        </div>
      </div>
    </div>

    <!-- Actions Popup -->
    <div v-if="showActionsPopup"
      class="actions-popup bg-white border rounded shadow-sm p-2"
      :style="{ top: popupPosition.top, left: popupPosition.left }">
      <ul class="list-unstyled m-0">
        <li><Link :href="route('viewMemberInfo', { id: activeMemberId })" class="dropdown-item"><i class="bi bi-eye me-2"></i> View</Link></li>
        <li><button class="dropdown-item w-100 text-start" data-bs-toggle="modal" data-bs-target="#editMember" @click="EditMemberFunc(getMembers.find(m => m.id === activeMemberId))"><i class="bi bi-pencil me-2"></i> Edit</button></li>
        <li><button class="dropdown-item w-100 text-start text-danger" @click="trashMember(activeMemberId)"><i class="bi bi-trash me-2"></i> Delete</button></li>
      </ul>
    </div>

    <!-- Edit Member Modal -->
    <div class="modal fade" id="editMember" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5>Edit Member Info</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body"><EditMember :member="editMemberValue" /></div>
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
table th {
  background: #D4F3F9 !important;
}
table td{
  padding-top: .5rem;
  padding-bottom: .5rem;
}
.modern-modal .modal-body {
  padding: 3rem !important;
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
.main-section { width: 100%; height: 100%; overflow-x: hidden; }
.search-input { width: 50%; min-width: 280px; }

@media (max-width: 992px) {
  .search-input { width: 70%; }
}
@media (max-width: 768px) {
  .search-input { width: 100%; }
  .action-buttons-large { display: none; }
  .action-buttons-small { display: block; }
  .table { font-size: 0.85rem; }
}
@media (max-width: 576px) {
  .table th, .table td { padding: 0.4rem; font-size: 0.8rem; }
  .status-toggle { flex-direction: column; gap: 0.3rem; }
  .custom-modal { width: 95%; }
}
.actions-popup { position: absolute; min-width: 140px; z-index: 2000; }
.custom-modal-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.55);
  display: flex; justify-content: center; align-items: center; z-index: 1050;
}
.custom-modal {
  background: white; border-radius: 10px; width: 90%; max-width: 700px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.3); overflow: hidden;
}
.custom-modal-header { display: flex; justify-content: space-between; background: #007bff; color: white; padding: 15px 20px; }
.close-icon { cursor: pointer; font-size: 1.6rem; }
.pagination-wrapper {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.pagination-controls {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  flex-wrap: nowrap;
  min-width: max-content; /* prevents buttons from squishing */
  padding: 0.5rem 1rem;
}

.pagination-btn {
  white-space: nowrap;
  flex-shrink: 0;
}

/* On small screens, align pagination to left for better visibility */
@media (max-width: 768px) {
  .pagination-controls {
    justify-content: flex-start;
  }
}

</style>
