<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { defineProps, ref, watch } from "vue";
import ContributionReportForAdmin from "@/Components/dashboard/report/ContributionReportForAdmin.vue";
import RecentContributionForAdmin from "@/Components/dashboard/report/RecentContributionForAdmin.vue";
import ReportHomeButton from "@/Components/dashboard/report/ReportHomeButton.vue";
import * as html2pdf from "html2pdf.js";
// props
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

// states
let getContributions = ref([]);
let showContributionsReport = ref(true);
let showRecentContributions = ref(false);
let getMemberContributions = ref([]);
let getDeathReports = ref([]);
const isDownloading = ref(false);
// watchers
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
        getMemberContributions.value = newMemberContributions;
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

// format date
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
          html2pdf.default()
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
                        <Link
                            :href="route('reports.addCollector')"
                            class="btn btn-primary"
                        >
                            Add Collector
                        </Link>
                    </div>
                </div>

                <!-- Switch buttons -->
                <div
                    class="container d-flex flex-row gap-2 mt-3 mb-2 ps-4"
                >
                    <button
                        class=" btn btn-outline-secondary"
                        @click="
                            showContributionsReport = false;
                            showRecentContributions = true
                        "
                        :class="{ 'btn btn-secondary text-light': showRecentContributions }"
                    >
                        Recent Contributions
                    </button>
                    <button
                        class="btn btn-outline-secondary"
                        @click="
                            showRecentContributions = false;
                            showContributionsReport = true
                        "
                        :class="{ 'btn btn-secondary text-light': !showRecentContributions }"
                    >
                        Contributions Report
                    </button>
                </div>

                <!-- Body -->
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
                            <div class="modal-header">
                                <h1
                                    class="modal-title fs-5"
                                    id="viewReportModalLabel"
                                >
                                    Reports
                                </h1>
                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"
                                ></button>
                            </div>

                            <!-- Only this body will be exported to PDF -->
                            <div class="modal-body" id="report-pdf-content">
                                <h1
                                    class="fw-light text-muted text-center"
                                >
                                    <span class="fs-5 d-block">Deceased</span>
                                    {{ deathReports[0].deceased_name }}
                                </h1>
                                <p class="text-muted">
                                    {{ formatDate(deathReports[0].date_of_death) }}
                                </p>
                                <h5 class="text-dark">List of Contributions</h5>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Purok</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="data in getMemberContributions"
                                                :key="data.id"
                                            >
                                                <td>
                                                    {{
                                                        data
                                                            .member_contribution
                                                            .first_name
                                                    }}
                                                    {{
                                                        data
                                                            .member_contribution
                                                            .last_name
                                                    }}
                                                </td>
                                                <td>{{ data.purok }}</td>
                                                <td>{{ data.status }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Footer buttons (not included in PDF) -->
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
                                    <span v-if="isDownloading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    {{ isDownloading ? "Downloading..." : "Download as PDF" }}
                                </button>
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
</style>
