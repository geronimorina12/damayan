<script setup>
import { Head, router } from '@inertiajs/vue3';
import CollectorLayout from '@/Layouts/CollectorLayout.vue';
import { defineProps, watch, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import PurokComponentForCollector from '@/Components/dashboard/contribution/PurokComponentForCollector.vue';

const props = defineProps({
  member: Array,
  selectedPurok: String,
  collectors: Array,
  paidMembersId: Array,
})

let getMember = ref([]);
let getSelectedPurok = ref('');
let getCollectors = ref([]);
let getPaidMembersId = ref([]);
let selectedMemberId = ref(null);
let selectedMemberPurok = ref('');
let selectedCollector = ref('');

watch(() => props.member, (newData) => getMember.value = newData, { immediate: true });
watch(() => props.selectedPurok, (newData) => getSelectedPurok.value = newData, { immediate: true });
watch(() => props.collectors, (newData) => getCollectors.value = newData, { immediate: true });
watch(() => props.paidMembersId, (newData) => getPaidMembersId.value = newData, { immediate: true });

const form = useForm({
  member_id: null,
  amount: 100,
  payment_date: new Date().toISOString().split("T")[0],
  collector: null,
  purok: '',
  status: 'paid',
});

// convert ("Purok 1") to enum ("purok1")
//since nag ka conflict sa database
const normalizePurok = (purok) => {
  if (!purok) return '';
  return purok.toLowerCase().replace(/\s+/g, ''); // "Purok 1" → "purok1"
};

// assign member before opening modal
const preparePayment = (memberId, memberPurok) => {
  selectedMemberId.value = memberId;
  selectedMemberPurok.value = memberPurok;
  selectedCollector.value = ''; // reset
};

// confirm when Done clicked
const confirmPayment = () => {
  form.member_id = selectedMemberId.value;
  form.purok = normalizePurok(selectedMemberPurok.value);
  form.collector = selectedCollector.value;

  form.post(route('contributions.store'), {
    onSuccess: () => {
      const member = getMember.value.find(m => m.id === selectedMemberId.value);
      if (member) member.paid = true;
      // Bootstrap auto closes modal (since we used data-bs-dismiss)
    }
  });
};

const unPaidFunc = (memberId) => {
  // Logic to mark a member as unpaid
  const member = getMember.value.find(m => m.id === memberId);
  if (member){
     member.paid = false;
    router.delete(route('collectorContribution.deleteContribution', memberId), {
      onSuccess: () => {
        alert('Member marked as unpaid successfully.');
      },
      onError: () => {
        alert("An error occured, please try again.");
      }
    });
    };
};
</script>

<template>
  <Head title="Member contribution"/>
  <CollectorLayout>
    <div class="container main-container">
      <div class="bg-light p-2 pt-0">
        <h6 class="mt-4 ms-2 fs-1">Payment Contribution</h6>

        <PurokComponentForCollector :activePurok="getSelectedPurok"/>

        <div class="table-wrapper mt-3" v-if="getMember.length > 0">
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
                <tr v-for="(mem, index) in getMember" :key="index">
                  <td>{{ mem?.id }}</td>
                  <td class="text-start">{{ mem?.first_name }} {{ mem?.middle_name }} {{ mem?.last_name }}</td>
                  <td>{{ mem?.contact_number || 'undefined' }}</td>
                  <td>{{ mem?.purok || 'N/A' }}</td>
                  <td>
                    <button 
                      v-if="!(getPaidMembersId || []).includes(mem?.id)"
                      class="btn btn-danger"
                      data-bs-toggle="modal"
                      data-bs-target="#collectorModal"
                      @click="preparePayment(mem.id, mem.purok)"
                      title="Mark member as paid"
                      >
                      Paid
                    </button>
                    <button 
                    v-else 
                    class="btn btn-success" @click="unPaidFunc(mem.id)" title="Mark member as unpaid">
                      <i class="bi bi-check-lg"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="container mt-4 text-center" v-else>
          <img src="../../../../images/not_found.svg" alt="No Data" class="not-found-image image-rounded">
          <h5 class="fw-light">No contribution found in 
            <span class="text-success"> {{ getSelectedPurok || 'N/A' }}</span>.
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
              data-bs-dismiss="modal">
              Done
            </button>
          </div>
        </div>
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
.extra-space{
  width: 50%;
  height: 100%;
}
.main-container{
  height: 100vh;
  width: 100%;
  overflow: auto;
  padding-bottom: 80px;
}
.main-container::-webkit-scrollbar{
  display: none;
}
</style>
