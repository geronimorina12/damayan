<script setup>
import { defineProps, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
const props = defineProps({
    activePurok: {
        type: String,
        default: () => "",
    },
});
let getActivePurok = ref("");
watch(
    () => props.activePurok,
    (newData) => {
        getActivePurok.value = newData;
    },
    { immediate: true }
);
const puroks = ["purok1", "purok2", "purok3", "purok4"];
const togglePurok = (purok) => {
    router.get(route("contributions.togglePurok", { purok: purok }), {
        onSuccess: () => console.log("changing purok"),
        onError: (err) => console.log("An error occured: ", err),
    });
};
</script>

<template>
    <div>
        <div class="d-flex align-items-center gap-2 text-muted small">
            <span
                class="purok all-members-text"
                :class="{ 'fw-bold active': getActivePurok == 'all' }"
                @click="togglePurok('all')"
                >ALL MEMBERS</span
            >
            <div>
                <span
                    v-for="purok in puroks"
                    :key="purok"
                    class="mx-2 purok"
                    :class="{ 'fw-bold active': purok === getActivePurok }"
                    @click="togglePurok(purok)"
                >
                    {{ purok }}
                </span>
            </div>
        </div>
    </div>
</template>
<style scoped>
.purok {
    cursor: pointer;
}
.purok:hover {
    color: #353232;
}
.active {
    color: #28a745;
}
@media screen and (max-width: 605px) {
  .all-members-text {
    font-size: .8rem;
  }
}
</style>
