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
</script>

<template>
    <div>
        <Head title="Report" />
        <AdminLayout>
            <div class="container-fluid px-0 report-container">
                <!-- Header -->
                <div
                    class="head container d-flex flex-row justify-content-between align-items-center"
                >
                    <div><h5 class="mb-0 mx-3 fs-3">Report</h5></div>
                    <div class="d-flex flex-row gap-2 me-3">
                        <button
                            class="btn btn-dark"
                            data-bs-toggle="modal"
                            data-bs-target="#viewReportModal"
                        >
                            View Reports
                        </button>
                        <button
                            data-bs-toggle="modal"
                            data-bs-target="#addCollectorModal"
                            class="btn btn-primary"
                        >
                            Add Collector
                        </button>
                    </div>
                </div>

                <!-- Switch buttons -->
                <div class="container d-flex flex-row gap-2 mt-3 mb-2 ps-4">
                    <button
                        :class="
                            showRecentContributions
                                ? 'btn btn-secondary text-light'
                                : 'btn btn-outline-secondary'
                        "
                        @click="showRecent"
                    >
                        Recent Contributions
                    </button>

                    <button
                        :class="
                            showContributionsReport
                                ? 'btn btn-secondary text-light'
                                : 'btn btn-outline-secondary'
                        "
                        @click="showReport"
                    >
                        Contributions Report
                    </button>
                </div>

                <!-- Content -->
                <div class="container">
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
                    <div class="modal-dialog custom-modal-width">
                        <div class="modal-content">
                            <div
                                class="modal-header d-flex justify-content-between align-items-center"
                            >
                                <div>
                                    <p class="text-muted mb-0">
                                        Select deceased person:
                                    </p>

                                    <select
                                        class="form-select"
                                        v-model="selectedDeceased"
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

                                <div>
                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal"
                                        aria-label="Close"
                                    ></button>
                                </div>
                            </div>

                            <!-- PDF export area -->
                            <div class="modal-body" id="report-pdf-content">
                                <h1 class="fw-light fs-5 text-muted text-center">
                                    <span class="d-block">Deceased</span>
                                    <span class="fs-3">
                                        {{
                                            selectedDeceased != null
                                                ? selectedDeceased.deceased_name
                                                : deathReports[
                                                      deathReports.length - 1
                                                  ].deceased_name
                                        }}
                                    </span>
                                </h1>
                                <p class="text-muted text-center">
                                    {{
                                        selectedDeceased != null
                                            ? formatDate(
                                                  selectedDeceased.date_of_death
                                              )
                                            : formatDate(
                                                  deathReports[
                                                      deathReports.length - 1
                                                  ].date_of_death
                                              )
                                    }}
                                </p>

                                <h5 class="text-dark">
                                    List of Contributions
                                </h5>
                                <div
                                    class="table-responsive"
                                    v-if="getMemberContributions.length !== 0"
                                >
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>Name</th>
                                                <th>Purok</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(data, index) in getMemberContributions"
                                                :key="data.id"
                                            >
                                                <td>{{ index + 1 }}</td>
                                                <td>
                                                    {{
                                                        data
                                                            .member_contribution
                                                            ?.first_name || 'N/A'
                                                    }}
                                                    {{
                                                        data
                                                            .member_contribution
                                                            ?.last_name || ''
                                                    }}
                                                </td>
                                                <td>{{ data.purok }}</td>
                                                <td>{{ data.status }}</td>
                                                <td>
                                                    {{ Math.floor(data.amount) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <p class="text-muted">
                                        Total amount: {{ totalAmount }}
                                    </p>
                                </div>

                                <div
                                    class="text-center container"
                                    v-else
                                >
                                    No contributions found.
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="modal-footer">
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
                                    :disabled="isDownloading"
                                >
                                    <span
                                        v-if="isDownloading"
                                        class="spinner-border spinner-border-sm me-2"
                                        role="status"
                                        aria-hidden="true"
                                    ></span>
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
.report-container {
    overflow-y: auto;
    max-height: 100vh;
}
.extra-space {
    height: 100px;
    background: transparent;
}
.modal {
    min-width: 90%;
}
.custom-modal-width {
    max-width: 70% !important;
}
table th,
table td {
    border-right: 2px solid #dee2e6;
}
table thead th {
    background: #d4f3f9 !important;
}
</style>
