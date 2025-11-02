<script setup>
import { router, Head, Link } from '@inertiajs/vue3'
import { defineProps, ref, watch } from 'vue'
import CollectorLayout from '@/Layouts/CollectorLayout.vue'
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
        <CollectorLayout>
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
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(official, index) in getOfficials" :key="official.id">
                          <td class="id-cell">{{ index +1  }}</td>
                          <td class="name-cell">{{ official.name }}</td>
                          <td class="position-cell">{{ official.position }}</td>
                          <td class="term-cell">{{ formatDate(official.term_start) }} - {{ formatDate(official.term_end) }}</td>
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

             
              </div>
            </div>
        </CollectorLayout>

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