<script setup>
import { defineProps, ref, watch, computed, toRaw } from 'vue';
import CollectorLayout from '@/Layouts/CollectorLayout.vue';
import { Head } from '@inertiajs/vue3';
import SubHeaderForCollectorReport from '@/Components/dashboard/SubHeaderForCollectorReport.vue';
import ReportTable from '@/Components/dashboard/ReportTable.vue';
import PurokComponentForCollectorReport from '@/Components/dashboard/contribution/PurokComponentForCollectorReport.vue';
import Toggle from '@/Components/dashboard/report/Toggle.vue';
import ViewAsCollector from '@/Components/dashboard/report/ViewAsCollector.vue';
const props = defineProps({
    contributions: { type: Array, default: () => [] },
    activePurok: { type: String, default: () => 'all' },
    activeStatus: { type: String, default: () => 'paid' },
    contributionsIds: { type: Array, default: () => [] },
    members: { type: Array, default: () => [] }, 
    currentDeceasedMembers: {
        type: Array,
        default: () => []
    },
    currentDeceasedMember: {
        type: Object,
        default: () => ({})
    }
});

let getContributions = ref([]);
let getActivePurok = ref('all');
let getAmmount = ref(0);
let getActiveStatus = ref('paid');
let getContributionsIds = ref([]);
const getMembers = ref([]);
const getCurrentDeceasedMembers = ref([]);
const getCurrentDeceasedMember = ref({});

// WATCHERS
watch(() => props.contributions, (newContributions) => {
    getContributions.value = newContributions;
    console.log("Updated contributions:", toRaw(newContributions));
    getAmmount.value = parseInt(
        newContributions.reduce((total, con) => total + parseFloat(con.amount), 0)
    );
}, { immediate: true });

watch(() => props.activePurok, (newPurok) => {
    getActivePurok.value = newPurok;
}, { immediate: true });

watch(() => props.activeStatus, (newStatus) => {
    getActiveStatus.value = newStatus;
}, { immediate: true });

watch(() => props.contributionsIds, (newContributionsIds) => {
    getContributionsIds.value = Array.isArray(newContributionsIds) ? [...newContributionsIds] : [];
}, { immediate: true });

watch(() => props.members, (newMembers) => {
    getMembers.value = newMembers;
}, { immediate: true });

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


//  Use contributionsIds.includes(member.id) for counts
const getPaidMembers = computed(() => {
    return getMembers.value.filter(m => getContributionsIds.value.includes(m.id)).length;
});

const getUnpaidMembers = computed(() => {
    return getMembers.value.filter(m => !getContributionsIds.value.includes(m.id)).length;
});

const getMembersCount = computed(() => getMembers.value.length);
const showReportModal = ref(false);
const openReportModal = () => {
    showReportModal.value = true;
    console.log("Report Modal Opened");
}
const closeReportModal = () => showReportModal.value = false;

</script>

<template>
    <div>
        <Head title="Report" />
        <CollectorLayout>
            <div class="head container">
                <h5 class="mb-0">Report</h5>
                <p class="d-none d-lg-block">Contribution Report</p>
            </div>

            <div class="mt-3" v-if="getCurrentDeceasedMember">
            <Toggle 
              :allDeceased="getCurrentDeceasedMembers" 
              :data="getCurrentDeceasedMember"
              :purok="getActivePurok"
            />
          </div>
       <div class="action-buttons">
          <button class="btn btn-dark view-report" @click="openReportModal">View Reports</button>
        </div>
            <div class="purok-container container-fluid d-flex justify-content-end align-items-center">
                <PurokComponentForCollectorReport
                    :activePurok="getActivePurok"
                    :activeStatus="getActiveStatus"
                    :deceasedId="getCurrentDeceasedMember.member_id"
                />
            </div>

            <SubHeaderForCollectorReport
                :membersCount="getMembersCount"
                :amount="Number(getAmmount)"
                :activePurok="getActivePurok"
                :paidMembers="getPaidMembers"
                :unpaidMembers="getUnpaidMembers"
            />

            <!-- <ReportTable 
                :activeStatus="getActiveStatus"
                :contributionsIds="getContributionsIds"
                :members="getMembers"
            /> -->
            
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
                :purok="getActivePurok"
                />
            </div>
            </div>
        </CollectorLayout>
    </div>
</template>

<style scoped>

.custom-modal-overlay {
  position: fixed;
  inset: 0;
  background-color: rgba(0,0,0,0.5);
  display: flex;
  align-items: flex-start;
  justify-content: center;
  z-index: 9999;
  padding-top: 2rem;
  padding-bottom: 2rem;
  overflow-y: auto; /* allow overlay scrolling */
}

.custom-modal {
  background: white;
  border-radius: 10px;
  width: 70%;
  max-width: 900px;
  padding: 20px;
  position: relative;
  animation: fadeIn 0.2s ease-in-out;

  /* NEW: allow modal content to scroll */
  max-height: 90vh;
  overflow-y: auto;
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