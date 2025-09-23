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
let showContributionsReport = ref(true);
let showRecentContributions = ref(false);
let getMemberContributions = ref([]);
let getDeathReports = ref([]);
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

                <div class="container d-flex flex-row gap-2 mt-3 mb-2 ps-4">
                    <button class=" btn btn-secondary" @click="showContributionsReport = false; showRecentContributions = true" v-if="!showRecentContributions">Recent Contributions</button>
                    <button class="btn btn-secondary" @click="showRecentContributions = false; showContributionsReport = true" v-if="!showContributionsReport">Contributions Report</button>
                </div>

                <div class="container">
                    <ContributionReportForAdmin
                        :contributions="getContributions"
                        v-if="showContributionsReport"
                    />
                    <RecentContributionForAdmin :recent="getContributions" v-if="showRecentContributions" />
                </div>

                <!-- View report modal -->
                <div class="modal fade" id="viewReportModal" tabindex="-1" aria-labelledby="viewReportModalLabel" aria-hidden="true"
                style="min-width: 90%;"
                >
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="viewReportModalLabel">Reports</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5 class="fw-light text-muted">Death: {{ deathReports[0].deceased_name }}</h5>
                        <p class="text-muted">{{ deathReports[0].date_of_death }}</p>
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
