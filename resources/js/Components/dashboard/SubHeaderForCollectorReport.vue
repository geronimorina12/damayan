<script setup>
import { defineProps, ref, watch } from 'vue';

const props = defineProps({
  membersCount: {
    type: Number,
    default: 0
  },
  amount: {
    type: Number,
    default: 0
  },
  activePurok: {
    type: String,
    default: ''
  },
  paidMembers: {
    type: Number,
    default: 0
  },
  unpaidMembers: {
    type: Number,
    default: 0
  }
});

let getMembersCount = ref(0);
let getAmount = ref(0);
let getActivePurok = ref('');
let getPaidMembers = ref('');
let getUnpaidMembers = ref('');

watch(() => props.amount, (newAmount) => {
  getAmount.value = newAmount;
}, { immediate: true });

watch(() => props.membersCount, (newCount) => {
  getMembersCount.value = newCount;
}, { immediate: true });

watch(() => props.activePurok, (newPurok) => {
  getActivePurok.value = newPurok;
}, { immediate: true });

watch(() => props.paidMembers, (newPaid) => {
  // getPaidMembers.value = newPaid.toString() + (newPaid === 1 ? ' Member' : ' Members');
  getPaidMembers.value = getAmount.value / 100;
}, { immediate: true });

watch(() => props.unpaidMembers, (newUnpaid) => {
  getUnpaidMembers.value = newUnpaid.toString() + (newUnpaid === 1 ? ' Member' : ' Members');
}, { immediate: true });
</script>

<template>
  <div class="table-wrapper mt-3">
    <div class="container-fluid header-container">
      <table class="table">
        <thead class="thead">
          <tr style="border-top: 1px solid #000; border-bottom: 1px solid #000;">
            <td class="bg-light">MEMBERS</td>
            <td class="bg-light">AMOUNT</td>
            <td class="bg-light">PUROK</td>
            <td class="bg-light">PAID</td>
            <td class="bg-light">UNPAID</td>
          </tr>
        </thead>
        <tbody class="tbody">
          <tr style="border-top: 1px solid #000; border-bottom: 1px solid #000;">
            <td class="bg-light">{{ getMembersCount }} Members</td>
            <td class="bg-light">{{ getAmount }}</td>
            <td class="bg-light">{{ getActivePurok[5] || 'All'}}</td>
            <td class="bg-light">{{ getPaidMembers }}</td>
            <td class="bg-light">{{ getUnpaidMembers }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
.table-wrapper {
  width: 100%;
  overflow-x: auto; /* Enable horizontal scroll on small screens */
}

.table {
  min-width: 600px; /* Prevent columns from collapsing */
  width: 100%;
  border-collapse: collapse;
}

.table td, .table th {
  white-space: nowrap; /* Prevent text wrapping */
  text-align: center;
  vertical-align: middle;
}

@media screen and (max-width: 768px) {
  .table {
    min-width: 500px;
  }
}

@media screen and (max-width: 480px) {
  .table {
    min-width: 450px;
  }
}
</style>
