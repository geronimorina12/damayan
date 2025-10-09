<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import CurrentMothCard from '@/Components/dashboard/CurrentMothCard.vue';
import CurrentYearCard from '@/Components/dashboard/CurrentYearCard.vue';
import MonthlyOverviewCard from '@/Components/dashboard/MonthlyOverviewCard.vue';
import CurrentDeceased from '@/Components/dashboard/admin/charts/CurrentDeceased.vue';
import DeceasedRecorded from '@/Components/dashboard/admin/charts/DeceasedRecorded.vue';
import { ref, defineProps, watch } from 'vue';
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
  }
});
let getCurrentMonthData = ref([]);
let getYearData = ref([]);
let getMothlyOverview = ref([]);

watch(
  () => props.currentMonthData,
  (newData) => {
    getCurrentMonthData.value = newData ? Object.values(newData) : [];
  },
  {immediate: true}
)
watch(
  () => props.yearData,
  (newData) => {
    getYearData.value = newData ? Object.values(newData) : [];
  },
  {immediate: true}
)
watch(
  () => props.monthlyOverview,
  (newData) => {
    getMothlyOverview.value = newData ? Object.values(newData) : [];
  },
  {immediate: true}
)
</script>


<template>
    <Head title="Dashboard" />

    <AdminLayout>
        <Head title="Dashboard" />
        <div class="container-fluid mx-0 px-0 home-container">
            <h2 class="fw-normal fs-5 my-4 ms-3">Analytics Dashboard</h2>

             <div class="row g-4">
            <!-- Bar Chart -->
            <div class="col-md-8 responsive-card">
              <CurrentMothCard :data="getCurrentMonthData"/>
            </div>

            <!-- Pie Chart -->
            <div class="col-md-4 responsive-card">
              <CurrentYearCard :data="getYearData"/>
            </div>
      </div>

      <!-- Monthly Line Chart -->
      <div class="row mt-4">
        <div class="col-12">
          <MonthlyOverviewCard :data="getMothlyOverview"/>
        </div>
      </div>

      <div class="dashboard-container">
    <CurrentDeceased :data="currentData" />
    <DeceasedRecorded :records="allRecords" />
  </div>

      <div class="add-space-at-bottom "></div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.home-container{
    width: 100%;
    height: 100%;
    overflow-x: hidden;
    overflow-y: scroll;
    padding-bottom: 8rem;
}
.add-space-at-bottom{
  display: none;
  width: 100%;
  height: 30%; 
}
@media screen and (max-width: 756px){
  .add-space-at-bottom{
    display: block;
  }
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