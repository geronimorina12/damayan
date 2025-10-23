<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { defineProps, ref, watch, onMounted } from "vue";
import ContributionReportForAdmin from "@/Components/dashboard/report/ContributionReportForAdmin.vue";
import RecentContributionForAdmin from "@/Components/dashboard/report/RecentContributionForAdmin.vue";
import ReportHomeButton from "@/Components/dashboard/report/ReportHomeButton.vue";
import AddCollector from "@/Components/dashboard/report/AddCollector.vue";

const props = defineProps({
    contributions: {
        type: Array,
        default: () => [],
    },
    memberContributions: {
        type: Array,
        default: () => [],
    },
    deathReports: {
        type: Array,
        default: () => [],
    },
    president: {
        type: Object,
        default: () => ({})
    }
});

let getContributions = ref([]);
let showContributionsReport = ref(true);
let showRecentContributions = ref(false);
let getMemberContributions = ref([]);
let getDeathReports = ref([]);
const isDownloading = ref(false);
let selectedDeceased = ref(null);
let modalInstance = null;
let totalAmount = ref(0);
let selectedDeceasedId = ref(null);
const president = ref("");

watch(
    () => props.contributions,
    (newContributions) => {
        getContributions.value = newContributions;
    },
    { immediate: true }
);

watch(
    () => props.memberContributions,
    (newMemberContributions) => {
        // filter out null relations
        getMemberContributions.value = newMemberContributions.filter(
            (data) => data.member_contribution !== null
        );
        console.log(
            "member contributions:",
            getMemberContributions.value
        );
        totalAmount.value = getMemberContributions.value.reduce(
            (sum, data) => sum + Number(data.amount || 0),
            0
        );
    },
    { immediate: true }
);

watch(
    () => props.deathReports,
    (newDeathReports) => {
        getDeathReports.value = newDeathReports;
    },
    { immediate: true }
);
watch(
    () => props.president,
    (data) => {
        president.value = data?.name;
    }, {immediate: true}
)
const showRecent = () => {
    showRecentContributions.value = true;
    showContributionsReport.value = false;
};

const showReport = () => {
    showRecentContributions.value = false;
    showContributionsReport.value = true;
};

const formatDate = (dateString) => {
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

// download PDF
const downloadPDF = () => {
    const element = document.getElementById("report-pdf-content");
    if (!element) return;
    isDownloading.value = true;
    import("html2pdf.js").then((html2pdf) => {
        const opt = {
            margin: 0.5,
            filename: "Report.pdf",
            image: { type: "jpeg", quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: "in", format: "letter", orientation: "portrait" },
        };
        html2pdf
            .default()
            .set(opt)
            .from(element)
            .save()
            .then(() => {
                isDownloading.value = false;
            })
            .catch(() => {
                isDownloading.value = false;
                alert("Something went wrong while downloading the PDF.");
            });
    });
};

onMounted(() => {
    const modalEl = document.getElementById("addCollectorModal");
    if (modalEl) {
        // modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl)
    }
});

const closeModal = () => {
    if (modalInstance) {
        modalInstance.hide();
    }
};
const filterData = (deceasedId) => {
    selectedDeceasedId.value = deceasedId;
    getMemberContributions.value = props.memberContributions.filter(
        (data) => data.deceased_id === deceasedId
    );
    totalAmount.value = getMemberContributions.value.reduce(
        (sum, data) => sum + Number(data.amount || 0),
        0
    );
};
</script>

<template>
    <div>
        <Head title="Report" />
        <AdminLayout>
            <div class="modern-report-container">
                <!-- Header -->
                <div class="modern-header">
                    <div class="header-content">
                        <h5 class="page-title fw-light">Reports Dashboard</h5>
                    </div>
                    <div class="header-actions">
                        <button
                            class="btn btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#viewReportModal"
                        >   
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 17H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M9 13H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M9 9H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M3 5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5Z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            View Reports
                        </button>
                    </div>
                </div>

                <!-- Switch buttons -->
                <div class="tab-navigation">
                    <button
                        :class="
                            showRecentContributions
                                ? 'tab-btn active'
                                : 'tab-btn'
                        "
                        @click="showRecent"
                    >
                        Recent Contributions
                    </button>

                    <button
                        :class="
                            showContributionsReport
                                ? 'tab-btn active'
                                : 'tab-btn'
                        "
                        @click="showReport"
                    >
                        Contributions Report
                    </button>
                </div>

                <!-- Content -->
                <div class="content-area">
                    <ContributionReportForAdmin
                        :contributions="getContributions"
                        v-if="showContributionsReport"
                    />
                    <RecentContributionForAdmin
                        :recent="getContributions"
                        v-if="showRecentContributions"
                    />
                </div>

                <!-- View report modal -->
                <div
                    class="modal fade"
                    id="viewReportModal"
                    tabindex="-1"
                    aria-labelledby="viewReportModalLabel"
                    aria-hidden="true"
                >
                    <div class="modal-dialog modern-modal-dialog">
                        <div class="modal-content modern-modal-content">
                            <div class="modal-header modern-modal-header">
                                <div class="header-content">
                                    <h2 class="modal-title">Generate Report</h2>
                                    <p class="modal-subtitle">Select a deceased person to view their contribution report</p>
                                </div>
                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"
                                ></button>
                            </div>

                            <div class="modal-body modern-modal-body">
                                <div class="selection-section">
                                    <label class="form-label">Select Deceased Person</label>
                                    <select
                                        class="modern-select"
                                        v-model="selectedDeceased"
                                        @change="filterData(selectedDeceased.member_id)"
                                    >
                                        <option value="" disabled>
                                            Select Deceased
                                        </option>
                                        <option
                                            v-for="data in getDeathReports"
                                            :key="data.id"
                                            :value="data"
                                        >
                                            {{ data.deceased_name }} -
                                            {{ formatDate(data.date_of_death) }}
                                        </option>
                                    </select>
                                </div>

                                <!-- PDF export area -->
                                <div class="pdf-export-area" id="report-pdf-content">
                                    <div class="pdf-header">
                                        <div class="organization-info">
                                            <h1 class="org-name">Protect Damayan</h1>
                                            <p class="org-subtitle">Mutual Aid Society</p>
                                        </div>
                                        <div class="report-title-section">
                                            <h2 class="report-title">Contribution Report</h2>
                                            <p class="report-date">Generated on {{ formatDate(new Date()) }}</p>
                                        </div>
                                    </div>

                                    <div class="deceased-info">
                                        <div class="deceased-name">
                                            {{ selectedDeceased != null
                                                ? selectedDeceased.deceased_name
                                                : deathReports[deathReports.length - 1]?.deceased_name || 'No deceased selected'
                                            }}
                                        </div>
                                        <div class="deceased-date">
                                            Date of Death: {{
                                                selectedDeceased != null
                                                    ? formatDate(selectedDeceased.date_of_death)
                                                    : deathReports[deathReports.length - 1] 
                                                        ? formatDate(deathReports[deathReports.length - 1].date_of_death)
                                                        : 'N/A'
                                            }}
                                        </div>
                                    </div>

                                    <div class="contributions-section">
                                        <h3 class="section-title">List of Contributions</h3>
                                        
                                        <div v-if="getMemberContributions.length !== 0">
                                            <div class="modern-table-container">
                                                <table class="modern-pdf-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">#</th>
                                                            <th>Contributor Name</th>
                                                            <th>Purok</th>
                                                            <th>Status</th>
                                                            <th class="text-end">Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr
                                                            v-for="(data, index) in getMemberContributions"
                                                            :key="data.id"
                                                        >
                                                            <td class="text-center">{{ index + 1 }}</td>
                                                            <td>
                                                                {{
                                                                    data.member_contribution?.first_name || 'N/A'
                                                                }}
                                                                {{
                                                                    data.member_contribution?.last_name || ''
                                                                }}
                                                            </td>
                                                            <td>{{ data.purok }}</td>
                                                            <td>
                                                                <span class="status-badge" :class="data.status.toLowerCase()">
                                                                    {{ data.status }}
                                                                </span>
                                                            </td>
                                                            <td class="text-end">₱{{ Math.floor(data.amount) }}</td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="total-row">
                                                            <td colspan="4" class="text-end total-label">Total Amount:</td>
                                                            <td class="text-end total-amount">₱{{ totalAmount }}</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="empty-state" v-else>
                                            <div class="empty-icon">
                                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M9 17H15" stroke="#6B7280" stroke-width="2" stroke-linecap="round"/>
                                                    <path d="M9 13H15" stroke="#6B7280" stroke-width="2" stroke-linecap="round"/>
                                                    <path d="M9 9H13" stroke="#6B7280" stroke-width="2" stroke-linecap="round"/>
                                                    <path d="M3 5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5Z" stroke="#6B7280" stroke-width="2"/>
                                                </svg>
                                            </div>
                                            <h4>No Contributions Found</h4>
                                            <p>There are no contributions recorded for this deceased person.</p>
                                        </div>
                                    </div>

                                    <div class="signature-section">
                                        <div class="signature-line"></div>
                                        <div class="president-name">{{ president }}</div>
                                        <div class="signature-label">President, Protect Damayan</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="modal-footer modern-modal-footer">
                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal"
                                >
                                    Close
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    @click="downloadPDF"
                                    :disabled="isDownloading || getMemberContributions.length === 0"
                                >
                                    <span
                                        v-if="isDownloading"
                                        class="spinner-border spinner-border-sm me-2"
                                        role="status"
                                        aria-hidden="true"
                                    ></span>
                                    <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-2">
                                        <path d="M12 16L12 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M8 8L12 4L16 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M16 12V18C16 18.5304 15.7893 19.0391 15.4142 19.4142C15.0391 19.7893 14.5304 20 14 20H10C9.46957 20 8.96086 19.7893 8.58579 19.4142C8.21071 19.0391 8 18.5304 8 18V12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    {{
                                        isDownloading
                                            ? "Downloading..."
                                            : "Download as PDF"
                                    }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Collector Modal -->
                <div
                    class="modal fade"
                    id="addCollectorModal"
                    tabindex="-1"
                    aria-labelledby="addCollectorModalLabel"
                    aria-hidden="true"
                >
                    <div class="modal-dialog" style="max-width: 700px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1
                                    class="modal-title fs-5"
                                    id="addCollectorModalLabel"
                                >
                                    Add Collector
                                </h1>
                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"
                                ></button>
                            </div>
                            <div class="modal-body">
                                <AddCollector @submitted="closeModal" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="extra-space"></div>
            </div>
        </AdminLayout>
    </div>
</template>

<style lang="css" scoped>
.modern-report-container {
    padding: 0;
    background-color: #f8fafc;
    min-height: 100vh;
}

.modern-header {
    /* background: #D4F3F9; */
    color: #333;
    padding: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    /* box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); */
}

.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.page-subtitle {
    opacity: 0.9;
    margin-bottom: 0;
}

.header-actions .btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #3b82f6;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    transition: all 0.2s ease;
}

.header-actions .btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-1px);
}

.tab-navigation {
    display: flex;
    padding: 0 2rem;
    margin-top: 1.5rem;
    gap: 0.5rem;
}

.tab-btn {
    padding: 0.75rem 1.5rem;
    border: none;
    background: white;
    color: #6b7280;
    border-radius: 8px 8px 0 0;
    font-weight: 500;
    transition: all 0.2s ease;
    border-bottom: 2px solid transparent;
}

.tab-btn.active {
    color: #3b82f6;
    border-bottom: 2px solid #3b82f6;
    background: white;
}

.tab-btn:hover:not(.active) {
    color: #374151;
    background: #f9fafb;
}

.content-area {
    padding: 1.5rem 2rem;
    background: white;
    margin: 0 2rem 2rem;
    border-radius: 0 0 12px 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Modern Modal Styles */
.modern-modal-dialog {
    max-width: 900px !important;
}

.modern-modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.modern-modal-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #f3f4f6;
}

.modern-modal-header .modal-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.modern-modal-header .modal-subtitle {
    color: #6b7280;
    margin-bottom: 0;
}

.modern-modal-body {
    padding: 2rem;
}

.modern-modal-footer {
    padding: 1.5rem 2rem;
    border-top: 1px solid #f3f4f6;
}

/* Selection Section */
.selection-section {
    margin-bottom: 2rem;
}

.form-label {
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
}

.modern-select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background-color: white;
    color: #374151;
    transition: all 0.2s ease;
}

.modern-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* PDF Export Area Styles */
.pdf-export-area {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.pdf-header {
    text-align: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #e5e7eb;
}

.org-name {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.org-subtitle {
    color: #6b7280;
    font-size: 1rem;
    margin-bottom: 1rem;
}

.report-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.25rem;
}

.report-date {
    color: #6b7280;
    font-size: 0.875rem;
}

.deceased-info {
    text-align: center;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 8px;
}

.deceased-name {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.deceased-date {
    color: #6b7280;
    font-size: 1rem;
}

.contributions-section {
    margin-bottom: 3rem;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.modern-table-container {
    overflow-x: auto;
}

.modern-pdf-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.modern-pdf-table thead {
    background-color: #f8fafc;
    border-bottom: 2px solid #e5e7eb;
}

.modern-pdf-table th {
    padding: 0.75rem 1rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 1px solid #e5e7eb;
}

.modern-pdf-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f3f4f6;
    color: #4b5563;
}

.modern-pdf-table tbody tr:hover {
    background-color: #f9fafb;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.paid {
    background-color: #d1fae5;
    color: #065f46;
}

.status-badge.pending {
    background-color: #fef3c7;
    color: #92400e;
}

.status-badge.overdue {
    background-color: #fee2e2;
    color: #991b1b;
}

.total-row {
    background-color: #f8fafc;
    font-weight: 600;
}

.total-label {
    color: #374151;
    padding-right: 1rem;
}

.total-amount {
    color: #065f46;
    font-size: 1rem;
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: #6b7280;
}

.empty-icon {
    margin-bottom: 1rem;
}

.empty-state h4 {
    color: #374151;
    margin-bottom: 0.5rem;
}

/* Signature Section */
.signature-section {
    margin-top: 3rem;
    text-align: right;
    padding-top: 2rem;
}

.signature-line {
    width: 200px;
    height: 1px;
    background-color: #374151;
    margin: 2rem 0 0.5rem auto;
}

.president-name {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.signature-label {
    color: #6b7280;
    font-size: 0.875rem;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    text-decoration: none;
}

.btn-primary {
    background-color: #3b82f6;
    color: white;
}

.btn-primary:hover:not(:disabled) {
    background-color: #2563eb;
    transform: translateY(-1px);
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.btn-secondary {
    background-color: #f8fafc;
    color: #374151;
    border: 1px solid #e5e7eb;
}

.btn-secondary:hover {
    background-color: #f1f5f9;
}

.extra-space {
    height: 100px;
    background: transparent;
}

/* Responsive Design */
@media (max-width: 768px) {
    .modern-header {
        padding: 1.5rem;
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .tab-navigation {
        padding: 0 1rem;
        overflow-x: auto;
    }
    
    .content-area {
        margin: 0 1rem 1rem;
        padding: 1rem;
    }
    
    .modern-modal-body {
        padding: 1rem;
    }
    
    .pdf-export-area {
        padding: 1rem;
    }
}
</style>