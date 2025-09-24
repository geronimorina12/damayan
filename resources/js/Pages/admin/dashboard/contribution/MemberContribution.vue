<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { defineProps, watch, ref } from 'vue';
import Purok from '@/Components/dashboard/contribution/Purok.vue';
import Header from '@/Components/dashboard/contribution/Header.vue';
import Collector from '@/Components/dashboard/contribution/Collector.vue';

const props = defineProps({
  member: {
    type: Object,   // pagination object with data + links + meta
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
});

let getMember = ref([]);
let getSelectedPurok = ref('');
let getCollectors = ref([]);

watch(
  () => props.member,
  (newData) => {
    getMember.value = newData?.data || []; // safe fallback
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

const formatDate = (dateString) => {
  const options = { year: "numeric", month: "long", day: "numeric" };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

function formatAmount(value) {
  if (value == null) return 'N/A';
  return parseFloat(value);
}

// handle next/previous navigation with Inertia
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

          <Purok :activePurok="getSelectedPurok" />

          <div class="table-responsive mt-3" v-if="Array.isArray(getMember) && getMember.length > 0">
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
                    <span v-else class="">Unpaid</span>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Pagination Controls -->
                      <div class="pagination-controls d-flex justify-content-between mt-3">
              <button
                class="btn btn-outline-primary"
                :disabled="!props.member.prev_page_url"
                @click="goToPage(props.member.prev_page_url)"
              >
                Previous
              </button>

              <button
                class="btn btn-outline-primary ms-auto"
                :disabled="!props.member.next_page_url"
                @click="goToPage(props.member.next_page_url)"
              >
                Next
              </button>
            </div>


          </div>

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
</style>
