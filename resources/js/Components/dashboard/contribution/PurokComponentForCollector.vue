<script setup>
import { defineProps, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3';
const props = defineProps({
    activePurok: {
        type: String,
        default: () => ''
    },
    deceasedId: {
        type: Number,
        default: () => 0
    }
})
let getActivePurok = ref('');
watch(
    () => props.activePurok,
    (newData) => {
        getActivePurok.value = newData;
    },
    {immediate: true}
)
const puroks = ['purok1', 'purok2', 'purok3', 'purok4']
const togglePurok = (purok) =>{
    router.get(route('collectorContribution.togglePurok', {purok: purok, deceasedId: props.deceasedId}), {
        onSuccess: () => console.log("changing purok"),
        onError: (err) => console.log("An error occured: " , err)
    })
}
</script>

<template>
    <div class="d-flex flex-row justify-content-between align-items-center px-0">
          <div>
             <span v-for="purok in puroks" :key="purok" class="mx-2 purok"
                :class="{ 'fw-bold active': purok === getActivePurok }"
                @click="togglePurok(purok)"
                >
            {{ purok }}
          </span>
          </div>
          <div class="mx-2 purok"
          @click="togglePurok('all')"
          :class="{ 'fw-bold active':  getActivePurok == 'all' }"
          >
             All members
        </div>
    </div>
</template>
<style scoped>
.purok{
    cursor: pointer;
}
.purok:hover{
    color: #353232;
}
.active {
    color: #28a745;
}
</style>