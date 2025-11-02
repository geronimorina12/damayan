<script setup>
import { router, Head, Link } from '@inertiajs/vue3'
import { defineProps, ref, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Add from '@/Components/dashboard/admin/official/Add.vue'
import Edit from '@/Components/dashboard/admin/official/Edit.vue'

const props = defineProps({
  officials: Array,
})
let getOfficials = ref([]);
let selectedOfficial  = ref({});

watch(
() => props.officials,
(newData) => {
    getOfficials.value =  newData;
},
{immediate: true}
)
const toggleStatus = (official) => {
  router.patch(route('officials.status.toggle', {id: official.id}), {
    status: !official.status,
  })
}

const editOfficial = (official) => {
  selectedOfficial .value = official;
  console.log("official: ", selectedOfficial .value)
}

const deleteOfficial = (id) => {
  if (confirm('Are you sure you want to delete this official?')) {
    router.delete(route('officials.delete', {id: id}))
  }
}

const addOfficial = () => {
  router.get(route('officials.addOfficialRoute'))
}
const formatDate = (dateString) => {
  const options = { year: "numeric" };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

</script>

<template>
  <Head title="View officials" />
    <div>
        <AdminLayout>
            <div class="modern-container">
              <div class="header-section">
                <h1 class="page-title">Protec Damayan Officials</h1>
                <!-- <p class="page-subtitle">Manage organization officials and their information</p> -->
              </div>
              
              <div class="content-wrapper">
                <div class="table-container" v-if="getOfficials.length > 0">
                  <div class="table-responsive">
                    <table class="modern-table">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Name</th>
                          <th>Position</th>
                          <th>Term</th>
                          <th class="text-center">Status</th>
                          <th class="text-center">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(official, index) in getOfficials" :key="official.id">
                          <td class="id-cell">{{ index +1  }}</td>
                          <td class="name-cell">{{ official.name }}</td>
                          <td class="position-cell">{{ official.position }}</td>
                          <td class="term-cell">{{ formatDate(official.term_start) }} - {{ formatDate(official.term_end) }}</td>
                          <td class="status-cell">
                            <div class="status-toggle">
                              <label class="toggle-switch">
                                <input
                                  type="checkbox"
                                  :checked="official.status"
                                  @change="toggleStatus(official)"
                                >
                                <span class="toggle-slider"></span>
                              </label>
                              <span class="status-label" :class="official.status ? 'active' : 'inactive'">
                                {{ official.status ? 'Active' : 'Inactive' }}
                              </span>
                            </div>
                          </td>
                          <td class="actions-cell">
                            <div class="action-buttons">
                              <button class="btn-action btn-edit" 
                              @click="editOfficial(official)" 
                              title="Edit"
                              data-bs-toggle="modal" 
                              data-bs-target="#editOfficial"
                              >
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                  <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                  <path d="M18.5 2.5C18.8978 2.10217 19.4374 1.87868 20 1.87868C20.5626 1.87868 21.1022 2.10217 21.5 2.5C21.8978 2.89782 22.1213 3.43739 22.1213 4C22.1213 4.56261 21.8978 5.10217 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                              </button>
                              <button class="btn-action btn-delete" @click="deleteOfficial(official.id)" title="Delete">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                  <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                  <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                  <path d="M10 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                  <path d="M14 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                              </button>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="empty-state" v-else>
                  <div class="empty-content">
                    <svg width="120" height="120" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M9 12H15" stroke="#6B7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M12 9V15" stroke="#6B7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M4 6C3.44772 6 3 6.44772 3 7V19C3 19.5523 3.44772 20 4 20H20C20.5523 20 21 19.5523 21 19V7C21 6.44772 20.5523 6 20 6H4Z" stroke="#6B7280" stroke-width="2"/>
                      <path d="M7 3V6" stroke="#6B7280" stroke-width="2" stroke-linecap="round"/>
                      <path d="M17 3V6" stroke="#6B7280" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <h3>No Officials Found</h3>
                    <p>There are no officials in the system yet. Add the first one to get started.</p>
                  </div>
                </div>

                <div class="footer-actions">
                  <Link :href="route('role.index')" class="btn btn-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="2"/>
                      <path d="M19.4 15C19.2669 15.3044 19.1337 15.6087 19.0005 15.913C18.512 16.8494 18.0235 17.7858 17.535 18.7221C17.2898 19.1902 17.0445 19.6583 16.7993 20.1264C16.373 20.9421 15.5 21.5 14.5 21.5H9.5C8.5 21.5 7.627 20.9421 7.20073 20.1264C6.95547 19.6583 6.7102 19.1902 6.46494 18.7221C5.97644 17.7858 5.48794 16.8494 4.99944 15.913C4.86622 15.6087 4.733 15.3044 4.59978 15C4.46656 14.6956 4.33333 14.3913 4.20011 14.087C3.71161 13.1506 3.22311 12.2142 2.73461 11.2779C2.48935 10.8098 2.24409 10.3417 1.99883 9.87358C1.57255 9.05792 1.5 8 1.5 8C1.5 7 2.05792 6.127 2.87358 5.70073C3.34167 5.45547 3.80976 5.2102 4.27785 4.96494C5.21418 4.47644 6.15052 3.98794 7.08685 3.49944C8.02318 3.01094 8.95952 2.52244 9.89585 2.03394C10.3639 1.78868 10.832 1.54341 11.3001 1.29815C12.1158 0.871875 13 1 14 1H15H16C17 1 17.8842 0.871875 18.6999 1.29815C19.168 1.54341 19.6361 1.78868 20.1041 2.03394C21.0405 2.52244 21.9768 3.01094 22.9132 3.49944C23.8495 3.98794 24.7858 4.47644 25.7221 4.96494C26.1902 5.2102 26.6583 5.45547 27.1264 5.70073C27.9421 6.127 28.5 7 28.5 8C28.5 8 28.4274 9.05792 28.0012 9.87358C27.7559 10.3417 27.5107 10.8098 27.2654 11.2779C26.7769 12.2142 26.2884 13.1506 25.7999 14.087C25.6667 14.3913 25.5334 14.6956 25.4002 15C25.267 15.3044 25.1338 15.6087 25.0006 15.913" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    User Roles
                  </Link>
                  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOfficial">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M12 5V19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                      <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Add New Official
                  </button>
                </div>
              </div>
            </div>
        </AdminLayout>

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
              <Edit :official="selectedOfficial "/>
            </div>
          </div>
        </div>
      </div>


    </div>
</template>

<style scoped>
.modern-container {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
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
}
</style>