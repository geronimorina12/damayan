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
                <table
                    class="table table-responsive table-bordered table-striped"
                >
                    <thead class="table-light">
                        <tr>
                            <th>COLLECTOR</th>
                            <th>PUROK</th>
                            <th>ASSIGNED MEMBERS</th>
                            <th>PAID</th>
                            <th>UNPAID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in contributions" :key="index">
                            <td>
                                <i class="bi bi-person me-2"></i
                                >{{ item.collector }}
                            </td>
                            <td>{{ item.purok }}</td>
                            <td>{{ item.members }}</td>
                            <td>{{ item.paid }}</td>
                            <td>{{ item.not_paid }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="container w-50 h-50"></div>
            </div>
        </div>

        <div class="container text-muted text-center" v-else>
            <p>No contribution data.</p>
        </div>
    </div>
</template>
