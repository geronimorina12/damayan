<script setup>
import { defineProps, defineModel } from "vue";
const props = defineProps({
    showContributionsReport: {
        type: Boolean,
        default: true,
    },
    showRecentContributions: {
        type: Boolean,
        default: false,
    },
});

const showContributionsReport = defineModel(true);
</script>

<template>
    <div>
        <div class="container d-flex flex-row gap-2 mt-3 mb-2 ps-4">
            <button class=" btn btn-secondary" @click="emit('showContributionsReport')" v-if="!showRecentContributions">Contributions Report</button>
            <button class="btn btn-secondary" @click="emit('showRecentContributions')" v-if="!showContributionsReport">Recent Contributions</button>
        </div>
    </div>
</template>

<style></style>
