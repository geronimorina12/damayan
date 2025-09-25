<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { defineProps, ref, watch } from "vue";
import ContributionReportForAdmin from "@/Components/dashboard/report/ContributionReportForAdmin.vue";
import RecentContributionForAdmin from "@/Components/dashboard/report/RecentContributionForAdmin.vue";
import ReportHomeButton from "@/Components/dashboard/report/ReportHomeButton.vue";

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
let showContributionsReport = ref(true); // default: report visible
let showRecentContributions = ref(false);
let getMemberContributions = ref([]);
let getDeathReports = ref([]);
let selectedDeceased = ref(null);
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
        console.log("member contributions: ", getMemberContributions.value);
    },
    { immediate: true }
);
watch(
    () => props.deathReports,
    (newDeathReports) => {
        getDeathReports.value = newDeathReports;
        console.log("death reports: ", getDeathReports.value);
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
</script>

<template>
    <div>
        <Head title="Report" />
        <AdminLayout>
            <div class="container-fluid px-0 report-container">
                <div
                    class="head container d-flex flex-row justify-content-between align-items-center"
                >
                    <div><h5 class="mb-0 mx-3 fs-3">Report</h5></div>
                    <div class="d-flex flex-row gap-2 me-3">
                        <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#viewReportModal">View Reports</button>
                        <Link
                            :href="route('reports.addCollector')"
                            class="btn btn-primary"
                        >
                            Add Collector
                        </Link>
                    </div>
                </div>

                <!-- Buttons: class is now fully dynamic so outline replaces solid -->
                <div class="container d-flex flex-row gap-2 mt-3 mb-2 ps-4">
                    <button 
                        :class="showRecentContributions ? 'btn btn-secondary' : 'btn btn-outline-secondary'"
                        @click="showRecent"
                    >
                        Recent Contributions
                    </button>

                    <button 
                        :class="showContributionsReport ? 'btn btn-secondary' : 'btn btn-outline-secondary'"
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
                <div class="modal fade" id="viewReportModal" tabindex="-1" aria-labelledby="viewReportModalLabel" aria-hidden="true"
                >
                <div class="modal-dialog" 
                style="min-width: 70%;"
                
                >
                    <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between align-items-center">
                        <div>
                        <!-- <h1 class="modal-title fs-3" id="viewReportModalLabel">Reports</h1> -->
                            <div class="">
                                <p class="text-muted mb-0">Select deceased person: </p>

                            <select name="selectDecesed" id="" class="select form-select" v-model="selectedDeceased">
                                <option value="" disabled selected>Select Deceased</option>
                                <option v-for="data in getDeathReports" :key="data.id" :value="data">
                                    {{ data.deceased_name }} - {{ formatDate(data.date_of_death) }}
                                </option>
                            </select>
                            </div>
                        </div>

                        <div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                    <div class="modal-body">
                        <h5 class="fw-light text-muted text-center fs-3">Death: {{ selectedDeceased != null ? selectedDeceased.deceased_name : deathReports[deathReports.length -1].deceased_name }}</h5>
                        <p class="text-muted text-center">{{ selectedDeceased != null ? formatDate(selectedDeceased.date_of_death) : formatDate(deathReports[deathReports.length -1].date_of_death) }}</p>
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
                                    <tr v-for="data in getMemberContributions" :key="data.id">
                                        <td>{{ data.member_contribution.first_name }} {{ data.member_contribution.last_name }}</td>
                                        <td>{{ data.purok }}</td>
                                        <td>{{ data.status }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Download as PDF</button>
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
.modal{
    min-width: 90%;
}
</style>
