<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";

const contributions = ref([]);
const collectorFilter = ref("");

// Fetch contributions
const fetchContributions = async () => {
  try {
    const response = await axios.get(route("contributions.list"));
    contributions.value = response.data;
  } catch (error) {
    console.error("Error fetching contributions:", error);
  }
};

// Group + Sum per collector
const groupedContributions = computed(() => {
  let groups = {};

  contributions.value.forEach((item) => {
    if (!item.collector) return;
    if (!groups[item.collector]) groups[item.collector] = 0;
    groups[item.collector] += parseFloat(item.amount);
  });

  return Object.keys(groups).map((collector) => ({
    collector,
    collected: groups[collector],
  }));
});

// Filter
const filteredData = computed(() => {
  if (!collectorFilter.value) return groupedContributions.value;
  return groupedContributions.value.filter((row) =>
    row.collector.toLowerCase().includes(collectorFilter.value.toLowerCase())
  );
});

onMounted(fetchContributions);
</script>

<template>
  <div class="table-card">
    <div class="card-header">
      <h5 class="card-title">
        Collector Contributions Summary
        <span class="record-count" v-if="filteredData.length > 0">
          {{ filteredData.length }}
        </span>
      </h5>
    </div>

    <div class="table-container">
      <!-- Filter -->
      <div class="mb-3">
        <input
          type="text"
          class="form-control"
          placeholder="Filter collector..."
          v-model="collectorFilter"
        />
      </div>

      <div class="table-responsive">
        <table class="deceased-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Collector</th>
              <th>Collected</th>
            </tr>
          </thead>

          <tbody>
            <tr v-if="filteredData.length === 0" class="no-data-row">
              <td colspan="3" class="no-data">
                No contributions found.
              </td>
            </tr>

            <tr v-for="(row, index) in filteredData" :key="row.collector" class="data-row">
              <td class="index-cell">{{ index + 1 }}</td>
              <td class="name-cell">{{ row.collector }}</td>
              <td class="date-cell">₱{{ row.collected.toFixed(2) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</template>

<style scoped>
/* ✅ Exact styling from your original component */
.table-card {
  flex: 1;
  min-width: 350px;
  background: #fff;
  border-radius: 16px;
  padding: 0;
  overflow: hidden;
  transition: all 0.3s ease;
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.card-header {
  padding: 1.5rem 1.5rem 1rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.08);
}

.card-title {
  color: #2c3e50;
  font-weight: 700;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1.1rem;
}

.record-count {
  background: #e74c3c;
  color: white;
  border-radius: 12px;
  padding: 0.25rem 0.75rem;
  font-size: 0.8rem;
  font-weight: 600;
  margin-left: auto;
}

.table-container {
  padding: 0 1.5rem 1.5rem;
}

.deceased-table {
  width: 100%;
  border-collapse: collapse;
}

.deceased-table thead th {
  background: #D4F3F9;
  color: rgb(3, 3, 3);
  font-weight: 600;
  padding: 1rem 0.75rem;
  text-align: left;
  border: none;
}

.deceased-table tbody tr:hover {
  background-color: rgba(74, 111, 165, 0.05);
}

.deceased-table tbody td {
  padding: 1rem 0.75rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}

.no-data {
  padding: 2.5rem 1rem;
  text-align: center;
  color: #6c757d;
}

.index-cell {
  font-weight: 600;
  color: #4a6fa5;
  text-align: center;
  width: 60px;
}

.name-cell {
  font-weight: 500;
}

.date-cell {
  color: #666;
}

/* Responsive */
@media (max-width: 768px) {
  .table-card {
    min-width: 100%;
  }

  .card-header,
  .table-container {
    padding: 1rem;
  }

  .deceased-table thead th,
  .deceased-table tbody td {
    padding: 0.75rem 0.5rem;
  }
}
</style>
