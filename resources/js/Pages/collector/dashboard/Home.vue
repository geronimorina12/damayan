<script setup>
import { Head } from '@inertiajs/vue3';
import CollectorLayout from '@/Layouts/CollectorLayout.vue';
import CurrentMothCard from '@/Components/dashboard/CurrentMothCard.vue';
import CurrentYearCard from '@/Components/dashboard/CurrentYearCard.vue';
import MonthlyOverviewCard from '@/Components/dashboard/MonthlyOverviewCard.vue';
import DashboardHeader from '@/Components/dashboard/admin/DashboardHeader.vue';
import { defineProps, ref, watch } from 'vue';

const props = defineProps({
  currentMonthData: {
    type: Object,
    default: () => ({})
  },
  yearData: {
    type: Object,
    default: () => ({})
  },
  monthlyOverview: {
    type: Object,
    default: () => ({})
  },
  currentDeceasedMembers: {
    type: Array,
    default: () => []
  },
  allDeceased: {
    type: Array,
    default: () => []
  },
  currentDeceasedMember: {
    type: Object,
    default: () => ({})
  }
});

let getCurrentMonthData = ref([]);
let getYearData = ref([]);
let getMothlyOverview = ref([]);
const getCurrentDeceased = ref({});
const getAllDeceased = ref([]);
const getCurrentDeceasedMember = ref({});
const getCurrentDeceasedMembers = ref([]);

watch(
  () => props.currentMonthData,
  (newData) => {
    getCurrentMonthData.value = newData ? Object.values(newData) : [];
  },
  { immediate: true }
);
watch(
  () => props.yearData,
  (newData) => {
    getYearData.value = newData ? Object.values(newData) : [];
  },
  { immediate: true }
);
watch(
  () => props.monthlyOverview,
  (newData) => {
    getMothlyOverview.value = newData ? Object.values(newData) : [];
  },
  { immediate: true }
);
watch(
  () => props.currentDeceasedMembers,
  (newData) => {
    getCurrentDeceasedMembers.value = newData ? Object.values(newData) : [];
  },
  {immediate: true}
)
watch(
  () => props.allDeceased,
  (newData) => {
    getAllDeceased.value = newData ? Object.values(newData) : [];
  },
  {immediate: true}
)
watch(
  () => props.currentDeceasedMember,
  (newData) => {
    getCurrentDeceasedMember.value = newData ? newData : {};
  },
  {immediate: true}
)
</script>

<template>
  <Head title="Collector dashboard" />

  <CollectorLayout>
    <div class="container-fluid mx-0 px-0 home-container">
      <h2 class="fw-normal fs-5 my-4 ps-3">Analytics Dashboard</h2>

        <!-- <DashboardHeader 
            :data="getCurrentDeceasedMember"
            :allDeceased="getCurrentDeceasedMembers"
            /> -->

      <div class="row g-4">
        <!-- Bar Chart -->
        <div class="col-md-8 responsive-card">
          <CurrentMothCard :data="getCurrentMonthData" />
        </div>

        <!-- Pie Chart -->
        <div class="col-md-4 responsive-card">
          <CurrentYearCard :data="getYearData" />
        </div>
      </div>

      <!-- Monthly Line Chart -->
      <div class="row mt-4">
        <div class="col-12">
          <MonthlyOverviewCard :data="getMothlyOverview" />
        </div>
      </div>
    </div>
  </CollectorLayout>
</template>

<style scoped>
.home-container {
  width: 100%;
  height: 100%;
  overflow-x: hidden;
  overflow-y: scroll;
  padding-bottom: 3rem;
}

/* Custom responsive stacking under 1204px */
@media (max-width: 1204px) {
  .responsive-card {
    width: 100% !important;
    flex: 0 0 100% !important;
    max-width: 100% !important;
  }
}
</style>
