<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import CollectorLayout from '@/Layouts/CollectorLayout.vue';
import { defineProps, ref, watch, computed, nextTick } from 'vue';
import PurokComponentForCollector from '@/Components/dashboard/contribution/PurokComponentForCollector.vue';
import ToggleContribution from '@/Components/dashboard/contribution/ToggleContribution.vue';
import ViewAsCollector from '@/Components/dashboard/report/ViewAsCollector.vue';

const props = defineProps({
  member: Array,
  selectedPurok: String,
  collectors: Array,
  paidMembersId: Array,
  currentCollector: Object,
  currentDeceasedMembers: { type: Array, default: () => [] },
  currentDeceasedMember: { type: Object, default: () => ({}) },
});

let getMember = ref([]);
let getSelectedPurok = ref('');
let getCollectors = ref([]);
let getPaidMembersId = ref([]);
let selectedMemberId = ref(null);
let selectedMemberPurok = ref('');
let selectedCollector = ref('');
let getCurrentCollector = ref({});
const getCurrentDeceasedMembers = ref([]);
const getCurrentDeceasedMember = ref({});

watch(() => props.member, (newData) => getMember.value = newData, { immediate: true });
watch(() => props.selectedPurok, (newData) => getSelectedPurok.value = newData, { immediate: true });
watch(() => props.collectors, (newData) => getCollectors.value = newData, { immediate: true });
watch(() => props.paidMembersId, (newData) => getPaidMembersId.value = newData, { immediate: true });
watch(() => props.currentCollector, (newData) => getCurrentCollector.value = newData, { immediate: true });
watch(() => props.currentDeceasedMembers, (newData) => getCurrentDeceasedMembers.value = newData ? Object.values(newData) : [], { immediate: true });
watch(() => props.currentDeceasedMember, (newData) => getCurrentDeceasedMember.value = newData, { immediate: true });

const searchQuery = ref('');

const filteredMembers = computed(() => {
  if (!searchQuery.value.trim()) return getMember.value;
  const keyword = searchQuery.value.toLowerCase();
  return getMember.value.filter(mem => (
    (mem.first_name && mem.first_name.toLowerCase().includes(keyword)) ||
    (mem.last_name && mem.last_name.toLowerCase().includes(keyword)) ||
    (mem.middle_name && mem.middle_name.toLowerCase().includes(keyword)) ||
    (mem.contact_number && mem.contact_number.toLowerCase().includes(keyword)) ||
    (mem.purok && mem.purok.toLowerCase().includes(keyword))
  ));
});

// Contribution form
const form = useForm({
  member_id: null,
  amount: 100,
  payment_date: new Date().toISOString().split('T')[0],
  collector: null,
  purok: '',
  status: 'paid',
});

// Normalize Purok
const normalizePurok = (purok) => purok?.toLowerCase().replace(/\s+/g, '') || '';
const preparePayment = (memberId, memberPurok, contact_number) => {
  if (!memberId || !memberPurok) {
    console.warn("Missing member data for preparePayment()");
    return;
  }

  // Update current selections
  selectedMemberId.value = memberId;
  selectedMemberPurok.value = memberPurok;
  selectedCollector.value = '';

  // Send SMS notification
  router.post(
    route('smsNotification.sendScheduleContribution'),
    {
      id: memberId,
      message: 'Successfully paid.',
      contact_number,
      collector: getCurrentCollector.value?.name || '',
      purok: memberPurok,
      deceasedId: getCurrentDeceasedMember.value?.member_id ?? null,
    },
    {
      onSuccess: () => {
        alert('Schedule Contribution sent successfully!');
      },
      onError: () => {
        alert('Error sending Schedule Contribution');
      }
    }
  );
};

// Confirm payment and update UI
const confirmPayment = () => {
  form.member_id = selectedMemberId.value;
  form.purok = normalizePurok(selectedMemberPurok.value);
  form.collector = selectedCollector.value;

  form.post(route('contributions.store'), {
    onSuccess: () => {
      // Update member object reactively
      const memberIndex = getMember.value.findIndex(m => m.id === selectedMemberId.value);
      if (memberIndex !== -1) {
        getMember.value[memberIndex] = {
          ...getMember.value[memberIndex],
          paid: true,
        };
      }

      // Add to paid members array
      nextTick(() => {
        if (!getPaidMembersId.value.includes(selectedMemberId.value)) {
          getPaidMembersId.value.push(selectedMemberId.value);
        }
      });
      
      // Reset selections
      selectedMemberId.value = null;
      selectedCollector.value = '';
    },
  });
};


// Mark as unpaid
const unPaidFunc = (memberId) => {
  if (!memberId) {
    console.warn("No memberId passed in unPaidFunc()");
    return;
  }

  router.delete(route('collectorContribution.deleteContribution', memberId), {
    onSuccess: () => {
      // Update member status
      const idx = getMember.value.findIndex(m => m.id === memberId);
      if (idx !== -1) {
        getMember.value[idx] = {
          ...getMember.value[idx],
          paid: false
        };
      }

      // Remove from paid list
      getPaidMembersId.value = getPaidMembersId.value.filter(id => id !== memberId);
    },
    onError: () => {
      alert("Failed to unmark payment. Try again.");
    }
  });
};


const showReportModal = ref(false);
const openReportModal = () => showReportModal.value = true;
const closeReportModal = () => showReportModal.value = false;

// Determines if member is paid
const isPaid = (id) => getPaidMembersId.value.includes(id);

// Unified toggle function
const togglePayment = (mem) => {
  if (isPaid(mem.id)) {
    // If currently paid → Unpay
    unPaidFunc(mem.id);
  } else {
    // If currently unpaid → Prepare to pay
    preparePayment(mem.id, mem.purok, mem.contact_number);
  }
};

</script>


<template>
  <Head title="Member contribution" />
  <CollectorLayout>
    <div class="container main-container">
      <div class="bg-light p-2 pt-0">
       <div class="container-fluid d-flex justify-content-between align-items-center px-0 header-container">
         <div class="page-title">
          <h6 class="mt-1 ms-2 fs-3">Payment Contribution</h6>
         </div>
         <div class="mt-3 mb-3 search-container">
          <input
            v-model="searchQuery"
            type="text"
            class="form-control w-100"
            placeholder="Search by name, contact, or purok..."
          />
        </div>
       </div>

        <PurokComponentForCollector 
        :activePurok="getSelectedPurok"
        :deceasedId="getCurrentDeceasedMember?.member_id"
        />
        <div class="mt-5" v-if="getCurrentDeceasedMember">
          <ToggleContribution 
            :allDeceased="getCurrentDeceasedMembers" 
            :purok="getSelectedPurok"
            :data="getCurrentDeceasedMember"
            v-model:deceased="getCurrentDeceasedMember"
        />
        </div>
       
        <div class="action-buttons">
          <button class="btn btn-dark view-report" @click="openReportModal">View Reports</button>
        </div>

        <!-- Desktop Table View -->
        <div class="table-wrapper mt-3 desktop-view" v-if="filteredMembers.length > 0">
          <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center mb-0">
              <thead class="table-light">
                <tr>
                  <th>NAME</th>
                  <th>CONTACT NO.</th>
                  <th>PUROK</th>
                  <th>STATUS</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(mem, index) in filteredMembers" :key="index">
                  <td class="text-start">
                    {{ mem?.first_name }} {{ mem?.middle_name }} {{ mem?.last_name }}
                  </td>
                  <td>{{ mem?.contact_number || 'undefined' }}</td>
                  <td>{{ mem?.purok || 'N/A' }}</td>
                 <td>
                <button
                  class="status-btn"
                  :class="{
                    'status-paid': isPaid(mem.id),
                    'status-unpaid': !isPaid(mem.id),
                    'disabled': mem.purok.slice(-1) !== getCurrentCollector?.purok
                  }"
                  @click="togglePayment(mem)"
                >
                  <span v-if="isPaid(mem.id)">✔ Paid</span>
                  <span v-else>Mark as Paid</span>
                </button>
              </td>

                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Mobile Card View -->
        <div class="mobile-view" v-if="filteredMembers.length > 0">
          <div class="mobile-cards-container">
            <div class="mobile-card" v-for="(mem, index) in filteredMembers" :key="index">
              <div class="mobile-card-header">
                <div class="mobile-member-id">ID: {{ mem?.id }}</div>
                <div class="mobile-purok">{{ mem?.purok || 'N/A' }}</div>
              </div>
              
              <div class="mobile-card-body">
                <div class="mobile-field">
                  <label>Name:</label>
                  <span class="mobile-value">{{ mem?.first_name }} {{ mem?.middle_name }} {{ mem?.last_name }}</span>
                </div>
                
                <div class="mobile-field">
                  <label>Contact:</label>
                  <span class="mobile-value">{{ mem?.contact_number || 'undefined' }}</span>
                </div>
                
                <div class="mobile-actions">
                  <button
                    v-if="!(getPaidMembersId || []).includes(mem?.id)"
                    class="btn btn-danger btn-sm mobile-pay-btn"
                    @click="preparePayment(mem.id, mem.purok, mem.contact_number)"
                    title="Mark member as paid"
                    :class="{ disabled: mem.purok.slice(-1) !== getCurrentCollector?.purok }"
                  >
                    Mark as Paid
                  </button>
                  <button
                    v-else
                    class="btn btn-success btn-sm mobile-paid-btn"
                    @click="unPaidFunc(mem.id)"
                    title="Mark member as unpaid"
                  >
                    <i class="bi bi-check-lg"></i> Paid
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div class="container mt-4 text-center" v-else>
          <img src="../../../../images/not_found.svg" alt="No Data" class="not-found-image image-rounded" />
          <h5 class="fw-light">
            No contribution found for
            <span class="text-success">{{ getSelectedPurok || 'N/A' }}</span>.
          </h5>
        </div>
      </div>
    </div>

    <div class="container extra-space bg-dark"></div>

    <!-- Collector Modal -->
    <div class="modal fade" id="collectorModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Select Collector</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <select v-model="selectedCollector" class="form-select">
              <option value="" disabled>Select collector</option>
              <option v-for="col in getCollectors" :key="col.id" :value="col.name">
                {{ col.name }}
              </option>
            </select>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button
              type="button"
              class="btn btn-primary"
              @click="confirmPayment"
              :disabled="!selectedCollector"
              data-bs-dismiss="modal"
            >
              Done
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Report Modal -->
    <div 
      v-if="showReportModal" 
      class="custom-modal-overlay"
      @click.self="closeReportModal"
    >
      <div class="custom-modal">
        <button class="close-btn" @click="closeReportModal">×</button>
        <ViewAsCollector 
          :deceased="getCurrentDeceasedMember"
          :purok="getSelectedPurok"
        />
      </div>
    </div>
  </CollectorLayout>
</template>

<style scoped>
.not-found-image {
  width: 250px;
  height: auto;
  max-width: 100%;
  display: block;
  margin: 0 auto 15px;
}
.extra-space {
  width: 50%;
  height: 100%;
}
.main-container {
  height: 100vh;
  width: 100%;
  overflow: auto;
  padding-bottom: 80px;
}
.main-container::-webkit-scrollbar {
  display: none;
}
.search-container {
  min-width: 50%;
}
.custom-modal-overlay {
  position: fixed;
  height: 100vh;
  overflow-y: scroll;
  inset: 0;
  background-color: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding-top: 10rem;
  padding-bottom: 1rem;
}
.custom-modal {
  background: white;
  border-radius: 10px;
  width: 70%;
  max-width: 900px;
  padding: 20px;
  position: relative;
  animation: fadeIn 0.2s ease-in-out;
}
.close-btn {
  position: absolute;
  top: 0;
  right: 15px;
  font-size: 20px;
  border: none;
  background: none;
  cursor: pointer;
  font-size: 2.5rem;
}
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}

/* Desktop Table Styles */
.desktop-view {
  display: block;
}
.table-responsive {
  max-height: 600px;
  min-height: 400px;
  overflow-y: auto;
}
.table-wrapper {
  margin-top: 1rem;
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
  margin-top: 1rem;
}
.mobile-card {
  background: #fff;
  border-radius: 12px;
  padding: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  border: 1px solid #e9ecef;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.mobile-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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
.mobile-purok {
  background: #e9ecef;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 500;
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
.mobile-actions {
  margin-top: 8px;
  display: flex;
  justify-content: flex-end;
}
.mobile-pay-btn, .mobile-paid-btn {
  min-width: 120px;
  font-size: 0.85rem;
  padding: 6px 12px;
}
.action-buttons {
  margin: 1rem 0;
  display: flex;
  justify-content: flex-end;
  padding: 0 8px;
}
.status-btn {
  padding: 6px 14px;
  border-radius: 6px;
  font-weight: 600;
  transition: 0.25s ease;
  border: none;
}

.status-unpaid {
  background: #dc3545;
  color: white;
}

.status-unpaid:hover {
  background: #b02a37;
}

.status-paid {
  background: #198754;
  color: white;
}

.status-paid:hover {
  background: #157347;
}

.status-btn.disabled {
  opacity: 0.5;
  pointer-events: none;
}

.mobile-btn {
  width: 100%;
  margin-top: 6px;
}

/* Responsive Breakpoints */
@media (max-width: 1024px) {
  .desktop-view table th,
  .desktop-view table td {
    padding: 0.5rem 0.3rem;
    font-size: 0.9rem;
  }
  .search-container {
    min-width: 40%;
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
    padding-bottom: 20px;
  }
  .header-container {
    flex-direction: column;
    align-items: stretch;
    gap: 1rem;
  }
  .page-title h6 {
    text-align: center;
    margin: 0;
    font-size: 1.5rem !important;
  }
  .search-container {
    min-width: 100%;
    margin-top: 0.5rem !important;
    margin-bottom: 0.5rem !important;
  }
  .action-buttons {
    justify-content: center;
  }
  .view-report {
    width: 100%;
    max-width: 200px;
  }
  .custom-modal {
    width: 90%;
    padding: 15px;
  }
}

@media (max-width: 576px) {
  .mobile-card {
    padding: 12px;
  }
  .mobile-card-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
  .mobile-member-id, .mobile-purok {
    font-size: 0.85rem;
  }
  .mobile-value {
    font-size: 0.9rem;
  }
  .mobile-actions {
    justify-content: stretch;
  }
  .mobile-pay-btn, .mobile-paid-btn {
    flex: 1;
    min-width: auto;
  }
  .mobile-cards-container {
    padding: 0 4px;
  }
}

@media (max-width: 380px) {
  .mobile-card {
    padding: 10px;
  }
  .mobile-field label {
    font-size: 0.75rem;
  }
  .mobile-value {
    font-size: 0.85rem;
  }
  .mobile-pay-btn, .mobile-paid-btn {
    font-size: 0.8rem;
    padding: 5px 8px;
  }
}

/* Landscape mobile optimization */
@media (max-height: 600px) and (orientation: landscape) {
  .table-responsive {
    max-height: 400px;
    min-height: 300px;
  }
  .mobile-cards-container {
    max-height: 50vh;
    overflow-y: auto;
  }
  .main-container {
    height: auto;
  }
}

/* Print Styles */
@media print {
  .mobile-view, .desktop-view {
    display: block !important;
  }
  .action-buttons, .search-container, .view-report {
    display: none;
  }
}
</style>