<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import CollectorLayout from '@/Layouts/CollectorLayout.vue';
import { defineProps, watch, ref, computed } from 'vue';
import PurokComponentForCollector from '@/Components/dashboard/contribution/PurokComponentForCollector.vue';
import ToggleContribution from '@/Components/dashboard/contribution/ToggleContribution.vue';
import ViewAsCollector from '@/Components/dashboard/report/ViewAsCollector.vue';

const props = defineProps({
  member: Array,
  selectedPurok: String,
  collectors: Array,
  paidMembersId: Array,
  currentCollector: Object,
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
let getPaidMembersId = ref([]);
let selectedMemberId = ref(null);
let selectedMemberPurok = ref('');
let selectedCollector = ref('');
let getCurrentCollector = ref({});
const getCurrentDeceasedMembers = ref([]);
const getCurrentDeceasedMember = ref({});

watch(() => props.member, (newData) => (getMember.value = newData), { immediate: true });
watch(() => props.selectedPurok, (newData) => (getSelectedPurok.value = newData), { immediate: true });
watch(() => props.collectors, (newData) => (getCollectors.value = newData), { immediate: true });
watch(() => props.paidMembersId, (newData) => (getPaidMembersId.value = newData), { immediate: true });
watch(() => props.currentCollector, (newData) => {
  getCurrentCollector.value = newData;
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
    console.log("current deceased: ", getCurrentDeceasedMember.value)
  },
  { immediate: true }
);

const searchQuery = ref('');

const filteredMembers = computed(() => {
  if (!searchQuery.value.trim()) {
    return getMember.value;
  }

  const keyword = searchQuery.value.toLowerCase();
  return getMember.value.filter(mem => {
    return (
      (mem.first_name && mem.first_name.toLowerCase().includes(keyword)) ||
      (mem.last_name && mem.last_name.toLowerCase().includes(keyword)) ||
      (mem.middle_name && mem.middle_name.toLowerCase().includes(keyword)) ||
      (mem.contact_number && mem.contact_number.toLowerCase().includes(keyword)) ||
      (mem.purok && mem.purok.toLowerCase().includes(keyword))
    );
  });
});

// 🧾 Contribution form
const form = useForm({
  member_id: null,
  amount: 100,
  payment_date: new Date().toISOString().split('T')[0],
  collector: null,
  purok: '',
  status: 'paid',
});

// Normalize Purok ("Purok 1" → "purok1")
const normalizePurok = (purok) => {
  if (!purok) return '';
  return purok.toLowerCase().replace(/\s+/g, '');
};

// Assign member before opening modal
const preparePayment = (memberId, memberPurok, contact_number) => {
  selectedMemberId.value = memberId;
  selectedMemberPurok.value = memberPurok;
  selectedCollector.value = ''; // reset

  router.post(
    route('smsNotification.sendScheduleContribution'),
    {
      id: memberId,
      message: 'Successfully paid.',
      contact_number: contact_number,
      collector: getCurrentCollector.value.name,
      purok: memberPurok,
      deceasedId: getCurrentDeceasedMember.value.member_id || null,
    },
    {
      onSuccess: () => alert('Schedule Contribution sent successfully!'),
      onError: () => alert('Error sending Schedule Contribution'),
    }
  );
};

// Confirm when Done clicked
const confirmPayment = () => {
  form.member_id = selectedMemberId.value;
  form.purok = normalizePurok(selectedMemberPurok.value);
  form.collector = selectedCollector.value;

  form.post(route('contributions.store'), {
    onSuccess: () => {
      const member = getMember.value.find((m) => m.id === selectedMemberId.value);
      if (member) member.paid = true;
    },
  });
};

// Mark as unpaid
const unPaidFunc = (memberId) => {
  const member = getMember.value.find((m) => m.id === memberId);
  if (member) {
    member.paid = false;
    router.delete(route('collectorContribution.deleteContribution', memberId), {
      onSuccess: () => {
        alert('Member marked as unpaid successfully.');
      },
      onError: () => {
        alert('An error occurred, please try again.');
      },
    });
  }
};
const showReportModal = ref(false);

const openReportModal = () => {
  showReportModal.value = true;
};

const closeReportModal = () => {
  showReportModal.value = false;
};

</script>

<template>
  <Head title="Member contribution" />
  <CollectorLayout>
    <div class="container main-container">
      <div class="bg-light p-2 pt-0">
       <div class="container-fluid d-flex justify-content-between align-items-center px-0">
         <div>
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
        :deceasedId="getCurrentDeceasedMember.member_id"
        />
       <ToggleContribution 
            :allDeceased="getCurrentDeceasedMembers" 
            :purok="getSelectedPurok"
            :data="getCurrentDeceasedMember"
            v-model:deceased="getCurrentDeceasedMember"
          />
       

          <button class="btn btn-dark view-report" @click="openReportModal">View Reports</button>
        <!-- Table Section -->
        <div class="table-wrapper mt-3" v-if="filteredMembers.length > 0">
          <div class="table-responsive" style="max-height: 600px; min-height: 800px; overflow-y: auto;">
            <table class="table table-bordered table-hover align-middle text-center mb-0">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>NAME</th>
                  <th>CONTACT NO.</th>
                  <th>PUROK</th>
                  <th>STATUS</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(mem, index) in filteredMembers" :key="index">
                  <td>{{ mem?.id }}</td>
                  <td class="text-start">
                    {{ mem?.first_name }} {{ mem?.middle_name }} {{ mem?.last_name }}
                  </td>
                  <td>{{ mem?.contact_number || 'undefined' }}</td>
                  <td>{{ mem?.purok || 'N/A' }}</td>
                  <td>
                    <button
                      v-if="!(getPaidMembersId || []).includes(mem?.id)"
                      class="btn btn-danger"
                      @click="preparePayment(mem.id, mem.purok, mem.contact_number)"
                      title="Mark member as paid"
                      :class="{ disabled: mem.purok.slice(-1) !== getCurrentCollector?.purok }"
                    >
                      Paid
                    </button>
                    <button
                      v-else
                      class="btn btn-success"
                      @click="unPaidFunc(mem.id)"
                      title="Mark member as unpaid"
                    >
                      <i class="bi bi-check-lg"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
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


      <!-- Modal -->
     <div 
        v-if="showReportModal" 
        class="custom-modal-overlay"
        @click.self="closeReportModal"
      >
        <div class="custom-modal">
          <button class="close-btn" @click="closeReportModal">×</button>

          <!-- Mounts only when modal is open -->
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
.search-container{
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
</style>
