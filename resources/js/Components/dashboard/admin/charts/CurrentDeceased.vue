<script setup>
import { defineProps, ref, watch, computed } from "vue";

const props = defineProps({
  data: {
    type: Array,
    default: () => [],
  },
});

const currentDeceased = ref([]);
const isExpanded = ref(false);

watch(
  () => props.data,
  (newData) => {
    currentDeceased.value = newData;
    console.log("current deceased: ", currentDeceased.value)
  },
  { immediate: true }
);

const displayedRecords = computed(() => {
  if (isExpanded.value || currentDeceased.value.length <= 5) {
    return currentDeceased.value;
  }
  return currentDeceased.value.slice(0, 5);
});

const hiddenRecordsCount = computed(() => {
  return Math.max(0, currentDeceased.value.length - 5);
});

const toggleExpand = () => {
  isExpanded.value = !isExpanded.value;
};

const formatDate = (dateString) => {
  if (!dateString || dateString === 'N/A') return 'N/A';
  
  try {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  } catch (e) {
    return dateString;
  }
};
</script>

<template>
  <div class="table-card" :class="{ 'is-expanded': isExpanded }">
    <div class="card-header">
      <h5 class="card-title">
        Current Deceased Records
        <span class="record-count" v-if="currentDeceased.length > 0">
          {{ currentDeceased.length }}
        </span>
      </h5>
    </div>
    
    <div class="table-container">
      <div class="table-responsive">
        <table class="deceased-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Date of Death</th>
              <th>Last Night</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="currentDeceased.length === 0" class="no-data-row">
              <td colspan="4" class="no-data">
                No current deceased record found.
              </td>
            </tr>
            
            <tr 
              v-for="(person, index) in displayedRecords" 
              :key="index"
              class="data-row"
            >
              <td class="index-cell">{{ index + 1 }}</td>
              <td class="name-cell">{{ person.deceased_name }}</td>
              <td class="date-cell">{{ formatDate(person.date_of_death) }}</td>
              <td class="address-cell">{{ person.last_night || 'N/A' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <div 
        v-if="currentDeceased.length > 5" 
        class="expand-section"
        @click="toggleExpand"
      >
        <div class="expand-toggle">
          <span class="toggle-text">
            {{ isExpanded ? 'Show Less' : `Show ${hiddenRecordsCount} More Records` }}
          </span>
          <i class="toggle-icon" :class="isExpanded ? 'icon-eye-off' : 'icon-eye'"></i>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
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
  margin-bottom: 0;
}

.deceased-table thead th {
  background: #D4F3F9;
  color: rgb(3, 3, 3);
  font-weight: 600;
  padding: 1rem 0.75rem;
  text-align: left;
  position: relative;
  border: none;
}

.deceased-table thead th::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0.75rem;
  right: 0.75rem;
  height: 2px;
}

.deceased-table tbody tr {
  transition: background-color 0.2s ease;
}

.deceased-table tbody tr:hover {
  background-color: rgba(74, 111, 165, 0.05);
}

.deceased-table tbody td {
  padding: 1rem 0.75rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
  vertical-align: middle;
}

.no-data-row td {
  border-bottom: none;
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

.address-cell {
  color: #666;
}

.expand-section {
  margin-top: 1rem;
  cursor: pointer;
}

.expand-toggle {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1.5rem;
  background: #f8f9fa;
  border-radius: 10px;
  transition: all 0.3s ease;
  color: #4a6fa5;
  font-weight: 600;
}

.expand-toggle:hover {
  background: rgba(74, 111, 165, 0.1);
  transform: translateY(-1px);
}

.toggle-icon {
  transition: transform 0.3s ease;
}

.is-expanded .toggle-icon {
  transform: rotate(180deg);
}

/* Icons using simple CSS */
.icon-eye::before { content: "👁️"; }
.icon-eye-off::before { content: "👁️‍🗨️"; }

/* Responsive design */
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