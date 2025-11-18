<script setup>
import { defineProps, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3';

const props = defineProps({
    activePurok: {
        type: String,
        default: () => 'all'
    },
    activeStatus: {
        type: String,
        default: () => 'paid'
    }
})

let getActiveStatus = ref('');
let getActivePurok = ref('all');

watch(() => props.activePurok, (newData) => {
    getActivePurok.value = newData;
}, { immediate: true })

watch(() => props.activeStatus, (newStatus) => {
    getActiveStatus.value = newStatus;
}, { immediate: true })

// Raw purok values (used in route)
const puroks = ['purok1', 'purok2', 'purok3', 'purok4'];

// Send clean purok string to backend
const togglePurok = (purok) => {
    router.get(route('collector.togglePurok', { 
        status: getActiveStatus.value, 
        purok: purok
    }), {
        onSuccess: () => console.log("changing purok"),
        onError: (err) => console.log("An error occured: ", err)
    });
};

// Format for UI only: purok1 → Purok 1
const displayPurok = (purok) => {
    return `Purok ${purok.replace('purok','')}`;
};
</script>
<template>
    <div class="d-flex flex-row justify-content-between align-items-center px-0">
        <div>
            <span 
                v-for="purok in puroks" 
                :key="purok" 
                class="mx-2 purok"
                :class="{ 'fw-bold active': purok === getActivePurok }"
                @click="togglePurok(purok)"
            >
                {{ displayPurok(purok) }}
            </span>
        </div>
    </div>
</template>
<style scoped> .purok{ cursor: pointer; } .purok:hover{ color: #353232; } .active { color: #28a745; } </style>