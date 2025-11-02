<script setup>
import { defineProps, ref, watch } from "vue";
const props = defineProps({
    contributions: {
        type: Array,
        default: () => [],
    },
});
const getContributions = ref([]);
const purok1Count = ref([]);
const purok2Count = ref([]);
const purok3Count = ref([]);
const purok4Count = ref([]);

watch(
    () => props.contributions,
    (data) => {
        getContributions.value = data;
        purok1Count.value = getContributions.value.filter(
            (item) => item.purok == "purok1"
        );
        purok2Count.value = getContributions.value.filter(
            (item) => item.purok == "purok2"
        );
        purok3Count.value = getContributions.value.filter(
            (item) => item.purok == "purok3"
        );
        purok4Count.value = getContributions.value.filter(
            (item) => item.purok == "purok4"
        );
    },
    { immediate: true }
);
</script>

<template>
    <div>
        <div class="mb-4" v-if="getContributions.length > 0">
            <div class="container">
                <!-- Responsive table container -->
                <div class="table-responsive-wrapper">
                    <table class="responsive-table">
                        <thead class="table-light">
                            <tr>
                                <th data-label="Collector">COLLECTOR</th>
                                <th data-label="Purok">PUROK</th>
                                <th data-label="Assigned Members">ASSIGNED MEMBERS</th>
                                <th data-label="Paid">PAID</th>
                                <th data-label="Unpaid">UNPAID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in contributions" :key="index">
                                <td data-label="Collector">
                                    <i class="bi bi-person me-2"></i>{{ item.collector }}
                                </td>
                                <td data-label="Purok">{{ item.purok }}</td>
                                <td data-label="Assigned Members">{{ item.members }}</td>
                                <td data-label="Paid">{{ item.paid }}</td>
                                <td data-label="Unpaid">{{ item.not_paid }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="container w-50 h-50"></div>
            </div>
        </div>

        <div class="container text-muted text-center" v-else>
            <p>No contribution data.</p>
        </div>
    </div>
</template>

<style scoped>
/* Base table styles */
.responsive-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
    font-size: 0.95rem;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
}

.responsive-table th,
.responsive-table td {
    padding: 1rem 0.75rem;
    text-align: left;
    border: 1px solid #dee2e6;
    border-right: 2px solid #dee2e6;
}

.responsive-table thead th {
    background: #D4F3F9 !important;
    font-weight: 600;
    color: #2c3e50;
    position: sticky;
    top: 0;
    z-index: 10;
}

.responsive-table tbody tr {
    transition: background-color 0.2s ease;
}

.responsive-table tbody tr:hover {
    background-color: rgba(212, 243, 249, 0.2);
}

.responsive-table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
}

.responsive-table tbody tr:nth-child(even):hover {
    background-color: rgba(212, 243, 249, 0.3);
}

/* Responsive wrapper */
.table-responsive-wrapper {
    overflow-x: auto;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

/* Large devices (desktops, 1200px and up) */
@media (min-width: 1200px) {
    .responsive-table {
        font-size: 1rem;
    }
    
    .responsive-table th,
    .responsive-table td {
        padding: 1.25rem 1rem;
    }
}

/* Medium devices (tablets, 768px to 1199px) */
@media (max-width: 1199px) and (min-width: 768px) {
    .responsive-table {
        font-size: 0.9rem;
    }
    
    .responsive-table th,
    .responsive-table td {
        padding: 0.875rem 0.625rem;
    }
    
    .table-responsive-wrapper {
        margin: 0 -0.5rem;
    }
}

/* Small devices (landscape phones, 576px to 767px) */
@media (max-width: 767px) and (min-width: 576px) {
    .responsive-table {
        font-size: 0.85rem;
        min-width: 600px; /* Force horizontal scroll */
    }
    
    .responsive-table th,
    .responsive-table td {
        padding: 0.75rem 0.5rem;
    }
    
    .table-responsive-wrapper {
        margin: 0 -0.75rem;
        border-left: none;
        border-right: none;
        border-radius: 0;
    }
    
    .container {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
}

/* Extra small devices (portrait phones, less than 576px) */
@media (max-width: 575px) {
    .responsive-table {
        font-size: 0.8rem;
        min-width: 500px; /* Force horizontal scroll */
    }
    
    .responsive-table th,
    .responsive-table td {
        padding: 0.625rem 0.375rem;
        white-space: nowrap;
    }
    
    .table-responsive-wrapper {
        margin: 0 -1rem;
        border-left: none;
        border-right: none;
        border-radius: 0;
    }
    
    .container {
        padding-left: 0.25rem;
        padding-right: 0.25rem;
    }
    
    /* Compact view for very small screens */
    .responsive-table th:nth-child(3),
    .responsive-table td:nth-child(3) {
        min-width: 80px;
    }
    
    .responsive-table th:nth-child(4),
    .responsive-table td:nth-child(4),
    .responsive-table th:nth-child(5),
    .responsive-table td:nth-child(5) {
        min-width: 60px;
        text-align: center;
    }
}

/* Ultra small devices (less than 400px) */
@media (max-width: 400px) {
    .responsive-table {
        font-size: 0.75rem;
        min-width: 450px;
    }
    
    .responsive-table th,
    .responsive-table td {
        padding: 0.5rem 0.25rem;
    }
    
    .bi-person {
        display: none; /* Hide icon on very small screens */
    }
}

/* Print styles */
@media print {
    .table-responsive-wrapper {
        overflow-x: visible;
        border: none;
    }
    
    .responsive-table {
        box-shadow: none;
        font-size: 10pt;
    }
    
    .responsive-table th {
        background: #f8f9fa !important;
        color: #000 !important;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .responsive-table th,
    .responsive-table td {
        border-color: #000;
    }
    
    .responsive-table thead th {
        background: #000 !important;
        color: #fff !important;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .responsive-table tbody tr {
        transition: none;
    }
}

/* Horizontal scrollbar styling */
.table-responsive-wrapper::-webkit-scrollbar {
    height: 8px;
}

.table-responsive-wrapper::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-responsive-wrapper::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.table-responsive-wrapper::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>