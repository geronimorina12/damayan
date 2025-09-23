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
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>DATE</th>
                        <th>AMOUNT</th>
                        <th>MEMBERS</th>
                        <th>COLLECTOR</th>
                        <th>PUROK</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in recent" :key="index">
                        <td>{{ formatDate(item.date) }}</td>
                        <td>{{ item.total_amount }}</td>
                        <td>{{ item.members }}</td>
                        <td>{{ item.collector }}</td>
                        <td>{{ item.purok }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="container text-muted text-center" v-else>
            <p>No recent contribution data.</p>
        </div>
    </div>
</template>
