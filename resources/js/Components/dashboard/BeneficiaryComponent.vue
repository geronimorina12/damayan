<script setup>
import { defineProps, ref, watch } from 'vue';

const props = defineProps({
  bene: {
    type: Array,
    default: () => [],
  },
});

let getBeneficiary = ref([]);

watch(
  () => props.bene,
  (newData) => {
    getBeneficiary.value = newData;
  },
  { immediate: true }
);

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<template>
  <div>
    <div
      class="container-fluid scroll-container"
      v-if="getBeneficiary?.length !== 0 && getBeneficiary != null"
    >
      <table class="table table-bordered">
        <tbody>
          <tr>
            <td colspan="8" class="text-center fw-bold">BENEFICIARY</td>
          </tr>

          <tr v-for="(data, index) in getBeneficiary" :key="index">
            <td colspan="2">{{ data.name }}</td>
            <td colspan="2">{{ data.relation }}</td>
            <td colspan="2">{{ data.age }} YEARS OLD</td>
            <td colspan="2">{{ formatDate(data.birth_date) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="container text-center" v-else>
      No Beneficiary
    </div>
  </div>
</template>

<style scoped>
.scroll-container {
  max-height: 300px; /* adjust height as needed */
  overflow-y: auto;
  padding-right: 8px;
}

/* Optional custom scrollbar style */
.scroll-container::-webkit-scrollbar {
  width: 8px;
}
.scroll-container::-webkit-scrollbar-thumb {
  background-color: #ccc;
  border-radius: 4px;
}

table th,
table td {
  vertical-align: middle;
  text-align: left;
}

@media screen and (max-width: 756px) {
  table th,
  table td {
    font-size: 0.8rem;
  }
}
</style>
