<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { defineProps, watch, ref } from 'vue';
import Purok from '@/Components/dashboard/contribution/Purok.vue';
import Header from '@/Components/dashboard/contribution/Header.vue';
import Collector from '@/Components/dashboard/contribution/Collector.vue';
import ToggleContribution from '@/Components/dashboard/contribution/ToggleContribution.vue';

const props = defineProps({
  member: {
    type: Object,
    default: () => ({ data: [], links: {}, meta: {} })
  },
  selectedPurok: {
    type: String,
    default: () => ''
  },
  collectors: {
    type: Array,
    default: () => []
  },
  currentDeceasedMembers: {
    type: Array,
    default: () => []
  },
  currentDeceasedMember: {
    type: Object,
    default: () => ({})
  }
});

let getMember = ref([]);
let getSelectedPurok = ref('');
let getCollectors = ref([]);
const getCurrentDeceasedMembers = ref([]);
const getCurrentDeceasedMember = ref({});

watch(
  () => props.member,
  (newData) => {
    getMember.value = newData?.data || [];
  },
  { immediate: true }
);

watch(
  () => props.selectedPurok,
  (newData) => {
    getSelectedPurok.value = newData;
  },
  { immediate: true }
);

watch(
  () => props.collectors,
  (newCollectors) => {
    getCollectors.value = newCollectors;
  },
  { immediate: true }
);

watch(
  () => props.currentDeceasedMembers,
  (newData) => {
    getCurrentDeceasedMembers.value = newData ? Object.values(newData) : [];
  },
  { immediate: true }
);

watch(
  () => props.currentDeceasedMember,
  (newData) => {
    getCurrentDeceasedMember.value = newData;
  },
  { immediate: true }
);

const formatDate = (dateString) => {
  const options = { year: "numeric", month: "long", day: "numeric" };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

function formatAmount(value) {
  if (value == null) return 'N/A';
  return parseFloat(value);
}

function goToPage(url) {
  if (url) {
    router.get(url, {}, { preserveScroll: true, preserveState: true });
  }
}
</script>

<template>
  <Head title="Member contribution" />
  <div>
    <AdminLayout>
      <div class="main-container">
        <div class="bg-light p-2">
          <Header />
          <Purok 
            :activePurok="getSelectedPurok" 
            :currentDeceasedId="getCurrentDeceasedMember?.member_id"
          />
         <div class="mt-3" v-if="getCurrentDeceasedMember">
           <ToggleContribution 
            :allDeceased="getCurrentDeceasedMembers" 
            :data="getCurrentDeceasedMember"
            :purok="getSelectedPurok"
          />
         </div>

          <!-- Desktop Table View -->
          <div class="table-responsive mt-3 desktop-view" v-if="Array.isArray(getMember) && getMember.length > 0">
            <table class="table table-bordered table-hover align-middle text-center">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>NAME</th>
                  <th>AMOUNT</th>
                  <th>DATE</th>
                  <th>COLLECTOR</th>
                  <th>PUROK</th>
                  <th>STATUS</th>
                </tr>
              </thead>
              <tbody class="scrollable-tbody">
                <tr v-for="(mem, index) in getMember" :key="index">
                  <td>{{ mem?.id }}</td>
                  <td class="text-start">
                    {{ mem?.first_name }} {{ mem?.middle_name }} {{ mem?.last_name }}
                  </td>
                  <td>{{ formatAmount(mem?.contributions[0]?.amount) }}</td>
                  <td>
                    <span v-if="mem?.contributions[0]?.payment_date">
                      {{ formatDate(mem?.contributions[0]?.payment_date) }}
                    </span>
                    <span v-else class="text-muted">...</span>
                  </td>
                  <td>
                    <Collector
                      :collectors="getCollectors"
                      :purok="mem.purok"
                    />
                  </td>
                  <td>{{ mem?.purok || 'N/A' }}</td>
                  <td>
                    <span
                      v-if="mem?.contributions[0]?.status === 'paid'"
                      class="badge bg-success"
                    >Paid</span>  
                    <span v-else class="text-muted">Unpaid</span>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Pagination Controls -->
            <div class="pagination-controls d-flex align-items-center justify-content-end gap-2 mt-3">
              <button
                class="btn btn-outline-primary"
                :disabled="!props.member.prev_page_url"
                @click="goToPage(props.member.prev_page_url)"
              >
                Previous
              </button>
              <button
                class="btn btn-outline-primary"
                :disabled="!props.member.next_page_url"
                @click="goToPage(props.member.next_page_url)"
              >
                Next
              </button>
            </div>
          </div>

          <!-- Mobile Card View -->
          <div class="mobile-view" v-if="Array.isArray(getMember) && getMember.length > 0">
            <div class="mobile-cards-container">
              <div class="mobile-card" v-for="(mem, index) in getMember" :key="index">
                <div class="mobile-card-header">
                  <div class="mobile-member-id">ID: {{ mem?.id }}</div>
                  <div class="mobile-status">
                    <span
                      v-if="mem?.contributions[0]?.status === 'paid'"
                      class="badge bg-success"
                    >Paid</span>  
                    <span v-else class="text-muted">Unpaid</span>
                  </div>
                </div>
                
                <div class="mobile-card-body">
                  <div class="mobile-field">
                    <label>Name:</label>
                    <span class="mobile-value">{{ mem?.first_name }} {{ mem?.middle_name }} {{ mem?.last_name }}</span>
                  </div>
                  
                  <div class="mobile-field-group">
                    <div class="mobile-field">
                      <label>Amount:</label>
                      <span class="mobile-value">{{ formatAmount(mem?.contributions[0]?.amount) }}</span>
                    </div>
                    
                    <div class="mobile-field">
                      <label>Purok:</label>
                      <span class="mobile-value">{{ mem?.purok || 'N/A' }}</span>
                    </div>
                  </div>
                  
                  <div class="mobile-field">
                    <label>Date:</label>
                    <span class="mobile-value" v-if="mem?.contributions[0]?.payment_date">
                      {{ formatDate(mem?.contributions[0]?.payment_date) }}
                    </span>
                    <span v-else class="text-muted">...</span>
                  </div>
                  
                  <div class="mobile-field">
                    <label>Collector:</label>
                    <span class="mobile-value">
                      <Collector
                        :collectors="getCollectors"
                        :purok="mem.purok"
                      />
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Mobile Pagination -->
            <div class="mobile-pagination d-flex align-items-center justify-content-between mt-3">
              <button
                class="btn btn-outline-primary btn-sm"
                :disabled="!props.member.prev_page_url"
                @click="goToPage(props.member.prev_page_url)"
              >
                ← Previous
              </button>
              <button
                class="btn btn-outline-primary btn-sm"
                :disabled="!props.member.next_page_url"
                @click="goToPage(props.member.next_page_url)"
              >
                Next →
              </button>
            </div>
          </div>

          <!-- No Data State -->
          <div class="container mt-4 text-center" v-else>
            <img
              src="../../../../../images/not_found.svg"
              alt="No Data"
              class="not-found-image image-rounded"
            >
            <h5 class="fw-light">
              No contribution found in <span class="text-success">{{ getSelectedPurok || 'N/A' }}</span>.
            </h5>
          </div>

          <div class="container space-at-bottom"></div>
        </div>
      </div>
    </AdminLayout>
  </div>
</template>

<style scoped>
.main-container {
  height: 100vh;
  overflow-y: auto;
}
.main-container::-webkit-scrollbar {
  display: none;
}
.space-at-bottom {
  height: 150px;
}
.not-found-image {
  width: 200px;
  margin: auto;
  margin-bottom: 2rem;
}

/* Desktop Table Styles */
.desktop-view {
  display: block;
}
.scrollable-tbody {
  display: block;
  max-height: 700px;
  overflow-y: auto;
}
.scrollable-tbody tr {
  display: table;
  width: 100%;
  table-layout: fixed;
}
thead tr {
  display: table;
  width: 100%;
  table-layout: fixed;
}
.pagination-controls {
  position: sticky;
  bottom: 0;
  background: #fff; 
  padding: 10px 0;
}
table th {
  background: #D4F3F9;
}

/* Mobile Card Styles */
.mobile-view {
  display: none;
}
.mobile-cards-container {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 0 8px;
}
.mobile-card {
  background: #fff;
  border-radius: 12px;
  padding: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  border: 1px solid #e9ecef;
}
.mobile-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  padding-bottom: 8px;
  border-bottom: 1px solid #f1f3f4;
}
.mobile-member-id {
  font-weight: 600;
  color: #4a6fa5;
  font-size: 0.9rem;
}
.mobile-status .badge {
  font-size: 0.75rem;
  padding: 4px 8px;
}
.mobile-card-body {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.mobile-field {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.mobile-field-group {
  display: flex;
  gap: 16px;
}
.mobile-field-group .mobile-field {
  flex: 1;
}
.mobile-field label {
  font-size: 0.8rem;
  color: #6c757d;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.mobile-value {
  font-size: 0.95rem;
  color: #2c3e50;
  font-weight: 500;
}
.mobile-pagination {
  padding: 0 8px;
}

/* Responsive Breakpoints */
@media (max-width: 1024px) {
  .desktop-view table th,
  .desktop-view table td {
    padding: 0.5rem 0.3rem;
    font-size: 0.9rem;
  }
}

@media (max-width: 768px) {
  .desktop-view {
    display: none;
  }
  .mobile-view {
    display: block;
  }
  .main-container {
    height: auto;
    min-height: 100vh;
  }
  .scrollable-tbody {
    max-height: 500px;
  }
}

@media (max-width: 576px) {
  .mobile-field-group {
    flex-direction: column;
    gap: 10px;
  }
  .mobile-card {
    padding: 12px;
  }
  .mobile-member-id {
    font-size: 0.85rem;
  }
  .mobile-value {
    font-size: 0.9rem;
  }
  .mobile-pagination .btn {
    font-size: 0.85rem;
    padding: 6px 12px;
  }
}

@media (max-width: 380px) {
  .mobile-card-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
  .mobile-cards-container {
    padding: 0 4px;
  }
}

/* Landscape mobile optimization */
@media (max-height: 600px) and (orientation: landscape) {
  .scrollable-tbody {
    max-height: 300px;
  }
  .mobile-cards-container {
    max-height: 60vh;
    overflow-y: auto;
  }
}
</style>