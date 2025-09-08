<script setup>
import { defineProps, ref, watch, computed, toRaw } from 'vue';
import CollectorLayout from '@/Layouts/CollectorLayout.vue';
import { Head } from '@inertiajs/vue3';
import SubHeaderForCollectorReport from '@/Components/dashboard/SubHeaderForCollectorReport.vue';
import ReportTable from '@/Components/dashboard/ReportTable.vue';
import PurokComponentForCollectorReport from '@/Components/dashboard/contribution/PurokComponentForCollectorReport.vue';

const props = defineProps({
    contributions: { type: Array, default: () => [] },
    activePurok: { type: String, default: () => 'all' },
    activeStatus: { type: String, default: () => 'paid' },
    contributionsIds: { type: Array, default: () => [] },
    members: { type: Array, default: () => [] }
});

let getContributions = ref([]);
let getActivePurok = ref('all');
let getAmmount = ref(0);
let getActiveStatus = ref('paid');
let getContributionsIds = ref([]);
const getMembers = ref([]);

// WATCHERS
watch(() => props.contributions, (newContributions) => {
    getContributions.value = newContributions;
    getAmmount.value = parseInt(
        newContributions.reduce((total, con) => total + parseFloat(con.amount), 0)
    );
}, { immediate: true });

watch(() => props.activePurok, (newPurok) => {
    getActivePurok.value = newPurok;
}, { immediate: true });

watch(() => props.activeStatus, (newStatus) => {
    getActiveStatus.value = newStatus;
}, { immediate: true });

watch(() => props.contributionsIds, (newContributionsIds) => {
    getContributionsIds.value = Array.isArray(newContributionsIds) ? [...newContributionsIds] : [];
}, { immediate: true });

watch(() => props.members, (newMembers) => {
    getMembers.value = newMembers;
}, { immediate: true });

//  Use contributionsIds.includes(member.id) for counts
const getPaidMembers = computed(() => {
    return getMembers.value.filter(m => getContributionsIds.value.includes(m.id)).length;
});

const getUnpaidMembers = computed(() => {
    return getMembers.value.filter(m => !getContributionsIds.value.includes(m.id)).length;
});

const getMembersCount = computed(() => getMembers.value.length);
</script>

<template>
    <div>
        <Head title="Report" />
        <CollectorLayout>
            <div class="head container">
                <h5 class="mb-0">Report</h5>
                <p>Contribution Report</p>
            </div>

            <div class="purok-container container-fluid d-flex justify-content-end align-items-center">
                <PurokComponentForCollectorReport
                    :activePurok="getActivePurok"
                    :activeStatus="getActiveStatus"
                />
            </div>

            <SubHeaderForCollectorReport
                :membersCount="getMembersCount"
                :amount="Number(getAmmount)"
                :activePurok="getActivePurok"
                :paidMembers="getPaidMembers"
                :unpaidMembers="getUnpaidMembers"
            />

            <ReportTable 
                :activeStatus="getActiveStatus"
                :contributionsIds="getContributionsIds"
                :members="getMembers"
            />
        </CollectorLayout>
    </div>
</template>