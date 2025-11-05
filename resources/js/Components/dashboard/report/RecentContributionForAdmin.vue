<script setup>
import { defineProps, ref, watch } from "vue";
const props = defineProps({
    recent: {
        type: Array,
        default: () => [],
    },
});
const getRecent = ref([]);
watch(
    () => props.recent,
    (data) => {
        getRecent.value = data;
    },
    { immediate: true }
);

const formatDate = (dateString) => {
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<template>
    <div>
        <div class="container" v-if="getRecent.length > 0">
            <!-- Responsive table container -->
            <div class="table-responsive-wrapper">
                <table class="responsive-table">
                    <thead class="table-light">
                        <tr>
                            <th data-label="Amount">AMOUNT</th>
                            <th data-label="Members">MEMBERS</th>
                            <th data-label="Collector">COLLECTOR</th>
                            <th data-label="Purok">PUROK</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in recent" :key="index">
                            <td data-label="Amount">{{ item.total_amount }}</td>
                            <td data-label="Members">{{ item.members }}</td>
                            <td data-label="Collector">{{ item.collector }}</td>
                            <td data-label="Purok">{{ item.purok }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="container text-muted text-center" v-else>
            <p>No recent contribution data.</p>
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
        min-width: 550px; /* Force horizontal scroll */
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
        min-width: 480px; /* Force horizontal scroll */
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
    
    /* Optimize column widths for small screens */
    .responsive-table th:nth-child(1),
    .responsive-table td:nth-child(1) {
        min-width: 100px; /* Date column */
    }
    
    .responsive-table th:nth-child(2),
    .responsive-table td:nth-child(2) {
        min-width: 70px; /* Amount column */
    }
    
    .responsive-table th:nth-child(3),
    .responsive-table td:nth-child(3) {
        min-width: 60px; /* Members column */
    }
    
    .responsive-table th:nth-child(4),
    .responsive-table td:nth-child(4) {
        min-width: 90px; /* Collector column */
    }
    
    .responsive-table th:nth-child(5),
    .responsive-table td:nth-child(5) {
        min-width: 70px; /* Purok column */
    }
}

/* Ultra small devices (less than 400px) */
@media (max-width: 400px) {
    .responsive-table {
        font-size: 0.75rem;
        min-width: 420px;
    }
    
    .responsive-table th,
    .responsive-table td {
        padding: 0.5rem 0.25rem;
    }
    
    /* Further optimize column widths */
    .responsive-table th:nth-child(1),
    .responsive-table td:nth-child(1) {
        min-width: 90px;
    }
    
    .responsive-table th:nth-child(2),
    .responsive-table td:nth-child(2) {
        min-width: 60px;
    }
    
    .responsive-table th:nth-child(3),
    .responsive-table td:nth-child(3) {
        min-width: 50px;
    }
    
    .responsive-table th:nth-child(4),
    .responsive-table td:nth-child(4) {
        min-width: 80px;
    }
    
    .responsive-table th:nth-child(5),
    .responsive-table td:nth-child(5) {
        min-width: 60px;
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

/* Data label for screen readers - hidden by default */
.responsive-table th[data-label],
.responsive-table td[data-label] {
    position: relative;
}

/* Show data labels only when needed for accessibility */
@media (max-width: 575px) {
    .responsive-table {
        /* Optional: You can add data-label display for very small screens if needed */
    }
}
</style>