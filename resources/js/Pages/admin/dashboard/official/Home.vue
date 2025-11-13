<script setup>
import { ref, watch, computed } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Add from '@/Components/dashboard/admin/official/Add.vue'
import Edit from '@/Components/dashboard/admin/official/Edit.vue'

const props = defineProps({
  officials: Array,
})

let getOfficials = ref([])
let selectedOfficial = ref({})
let searchQuery = ref('')
let statusFilter = ref('all')
const successMessage = ref('')

// --- Modal State & Functions ---
const showModal = ref(false)
const modalTitle = ref('')
const modalMessage = ref('')
const modalType = ref('') // 'confirm' or 'alert'
const modalAction = ref(null)
const modalData = ref(null)

// Show confirmation modal
const showConfirmation = (title, message, action, data = null) => {
  modalTitle.value = title
  modalMessage.value = message
  modalType.value = 'confirm'
  modalAction.value = action
  modalData.value = data
  showModal.value = true
}

// Show alert modal
const showAlert = (title, message) => {
  modalTitle.value = title
  modalMessage.value = message
  modalType.value = 'alert'
  showModal.value = true
}

// Execute modal action
const executeModalAction = () => {
  if (modalAction.value && modalData.value) {
    modalAction.value(modalData.value)
  } else if (modalAction.value) {
    modalAction.value()
  }
  showModal.value = false
}

// Close modal
const closeModal = () => {
  showModal.value = false
  modalAction.value = null
  modalData.value = null
}

// Replace deleteOfficial to use modal
const deleteOfficial = (id) => {
  showConfirmation(
    'Delete Official',
    'Are you sure you want to delete this official? This action cannot be undone.',
    performDeleteOfficial,
    id
  )
}

// Actual deletion logic
const performDeleteOfficial = (id) => {
  router.delete(route('officials.delete', { id: id }), {
    onSuccess: () => showAlert('Success', 'Official deleted successfully.'),
    onError: () => showAlert('Error', 'Failed to delete the official.')
  })
}

const toggleStatus = (official) => {
  router.patch(route('officials.status.toggle', { id: official.id }), {
    status: !official.status,
  })
}

const editOfficial = (official) => {
  selectedOfficial.value = official
}

const addOfficial = () => {
  router.get(route('officials.addOfficialRoute'))
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return '~'
  const options = { year: 'numeric' }
  return date.toLocaleDateString(undefined, options)
}

// --- Filter + Search Logic ---
const filteredOfficials = computed(() => {
  return getOfficials.value.filter((official) => {
    const matchesSearch =
      official.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      official.position.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchesStatus =
      statusFilter.value === 'all'
        ? true
        : statusFilter.value === 'active'
        ? official.status
        : !official.status

    return matchesSearch && matchesStatus
  })
})

const capitalizeFirst = (str) => {
  if (!str) return ''
  return str.charAt(0).toUpperCase() + str.slice(1)
}

// Watch for props changes
watch(
  () => props.officials,
  (newData) => {
    getOfficials.value = newData
  },
  { immediate: true }
)
</script>

<template>
  <Head title="View officials" />
  <AdminLayout>
    <div class="modern-container">
      <div class="scroll-content">
        <div class="header-section mb-3 d-flex align-items-center justify-content-between">
          <h5 class="fw-light mb-0">Protec Damayan Officials</h5>
        </div>

        <!-- Search & Filter Section -->
        <div class="d-flex flex-wrap align-items-center mb-3 gap-2">
          <div class="input-group" style="max-width: 300px;">
            <span class="input-group-text bg-white">
              <i class="bi bi-search"></i>
            </span>
            <input
              type="text"
              class="form-control"
              placeholder="Search by name or position..."
              v-model="searchQuery"
            />
          </div>

          <div class="input-group" style="max-width: 200px;">
            <span class="input-group-text bg-white">
              <i class="bi bi-funnel"></i>
            </span>
            <select class="form-select" v-model="statusFilter">
              <option value="all">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>

        <div
          v-if="successMessage"
          class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 mt-3 me-3 shadow-lg"
          role="alert"
          style="z-index: 9999; min-width: 280px;"
        >
          <i class="bi bi-check-circle-fill me-2"></i> {{ successMessage }}
          <button
            type="button"
            class="btn-close"
            @click="successMessage = ''"
          ></button>
        </div>

        <!-- Officials Table -->
        <div class="content-wrapper">
          <div class="table-container" v-if="filteredOfficials.length > 0">
            <div class="table-responsive">
              <table class="modern-table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Term</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="official in filteredOfficials" :key="official.id">
                    <td>{{ official.name }}</td>
                    <td>{{ capitalizeFirst(official.position) || 'Collector' }}</td>
                    <td>
                      {{ formatDate(official.term_start || official.created_at) }} -
                      {{ formatDate(official.term_end || new Date(new Date(official.created_at).setFullYear(new Date(official.created_at).getFullYear() + 2))) }}
                    </td>
                    <td class="text-center">
                      <div class="form-check form-switch d-flex align-items-center justify-content-center gap-2">
                        <!-- <input
                          class="form-check-input"
                          type="checkbox"
                          role="switch"
                          :checked="official.status || official.role === 'collector'"
                          @change="toggleStatus(official)"
                          :disabled="official.role === 'collector'"
                        /> -->
                        <span :class="official.status ? 'text-success' : 'text-danger'" class="fw-bold">
                          {{ official.status || official.role === 'collector' ? 'Active' : 'Inactive' }}
                        </span>
                      </div>
                    </td>
                    <td class="text-center">
                      <button
                        class="btn btn-outline-primary btn-sm me-2"
                        @click="editOfficial(official)"
                        data-bs-toggle="modal"
                        data-bs-target="#editOfficial"
                      >
                        <i class="bi bi-pencil text-dark"></i>
                      </button>
                      <button
                        class="btn btn-outline-danger btn-sm"
                        @click="deleteOfficial(official.id)"
                      >
                        <i class="bi bi-trash text-dark"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="empty-state text-center py-5" v-else>
            <i class="bi bi-people fs-1 text-secondary mb-2"></i>
            <h5>No Officials Found</h5>
            <p class="text-muted">Try adjusting your search or filters.</p>
          </div>

          <div class="footer-actions d-flex justify-content-between mt-4">
            <Link :href="route('role.index')" class="btn btn-secondary">
              <i class="bi bi-shield-lock me-1"></i> User Roles
            </Link>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOfficial">
              <i class="bi bi-plus-lg me-1"></i> Add New Official
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add New Official Modal -->
    <div class="modal fade" id="addOfficial" tabindex="-1" aria-labelledby="addOfficialLabel" aria-hidden="true">
      <div class="modal-dialog modern-modal">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title">Add Official</h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <Add />
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Official Modal -->
    <div class="modal fade" id="editOfficial" tabindex="-1" aria-labelledby="editOfficialLabel" aria-hidden="true">
      <div class="modal-dialog modern-modal">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title">Edit Official</h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <Edit :official="selectedOfficial" />
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation / Alert Modal -->
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0,0,0,0.5)">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ modalTitle }}</h5>
            <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>{{ modalMessage }}</p>
          </div>
          <div class="modal-footer">
            <button 
              v-if="modalType === 'confirm'" 
              type="button" 
              class="btn btn-secondary" 
              @click="closeModal"
            >
              Cancel
            </button>
            <button 
              v-if="modalType === 'confirm'" 
              type="button" 
              class="btn btn-primary" 
              @click="executeModalAction"
            >
              Confirm
            </button>
            <button 
              v-if="modalType === 'alert'" 
              type="button" 
              class="btn btn-primary" 
              @click="closeModal"
            >
              OK
            </button>
          </div>
        </div>
      </div>
    </div>

  </AdminLayout>
</template>


<style scoped>
.input-group-text i {
  font-size: 1rem;
  color: #6c757d;
}
.modern-container {
  height: 100vh;
  overflow-y: auto;
  overflow-x: hidden;
  padding: 2rem;
  box-sizing: border-box;
}

.scroll-content {
  min-height: 100%;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding-bottom: 5rem;
}

.header-section {
  margin-bottom: 2rem;
}

.page-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.page-subtitle {
  color: #6b7280;
  font-size: 1rem;
}

.content-wrapper {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.table-container {
  overflow-x: auto;
}

.modern-table {
  width: 100%;
  border-collapse: collapse;
}

.modern-table thead {
  background-color: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
}

.modern-table th {
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  color: #374151;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  background: #D4F3F9 !important;
}

.modern-table td {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f3f4f6;
  color: #4b5563;
}

.modern-table tbody tr {
  transition: background-color 0.15s ease;
}

.modern-table tbody tr:hover {
  background-color: #f9fafb;
}

.id-cell {
  font-weight: 500;
  color: #6b7280;
}

.name-cell {
  font-weight: 500;
  color: #1f2937;
}

.position-cell {
  color: #4b5563;
}

.term-cell {
  color: #6b7280;
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
  .scroll-content {
  min-height: 100%;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding-bottom: 10rem;
}
}
</style>