<script setup>
import { defineProps, ref, watch } from "vue";

const props = defineProps({
  data: {
    type: Array,
    default: () => [],
  },
});

const currentDeceased = ref([]);

watch(
  () => props.data,
  (newData) => {
    // Filter only current deceased records
    currentDeceased.value = newData.filter((d) => d.status === "current");
  },
  { immediate: true }
);
</script>

<template>
  <div class="table-card">
    <h5 class="fw-bold mb-3">Current Deceased Records</h5>
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Date of Death</th>
            <th>Cause</th>
            <th>Address</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="currentDeceased.length === 0">
            <td colspan="5" class="text-center text-muted">No current deceased record found.</td>
          </tr>
          <tr v-for="(person, index) in currentDeceased" :key="index">
            <td>{{ index + 1 }}</td>
            <td>{{ person.name }}</td>
            <td>{{ person.date_of_death || 'N/A' }}</td>
            <td>{{ person.cause || 'N/A' }}</td>
            <td>{{ person.address || 'N/A' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
.table-card {
  flex: 1;
  min-width: 350px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  padding: 1rem;
}
</style>
