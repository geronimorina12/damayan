<script setup>
import { ref, computed, defineModel } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
  data: {
    type: Object,
    default: () => ({})
  },
  allDeceased: {
    type: Array,
    default: () => []
  },
  purok: {
    type: String,
    default: () => ''
  }
});

// current report unique identifiers
const currentReportId = ref(props.data.report_id ?? null);
const currentMemberId = ref(props.data.member_id ?? null);
const selectedDeceased = defineModel('deceased');

// find exact index of the current report (use report_id because member_id may repeat)
const currentIndex = computed(() =>
  props.allDeceased.findIndex(item => {
    // item may be a Proxy; compare by report_id (unique)
    return Number(item.report_id) === Number(currentReportId.value);
  })
);

// helper: find next index that has a different member_id than the current member
function findNextDistinctIndex(startIndex) {
  if (!props.allDeceased.length) return -1;
  const length = props.allDeceased.length;
  const startMemberId = props.allDeceased[startIndex]?.member_id;
  // walk forward, wrap around, but stop if we loop fully
  for (let i = 1; i <= length; i++) {
    const idx = (startIndex + i) % length;
    const item = props.allDeceased[idx];
    if (!item) continue;
    if (String(item.member_id) !== String(startMemberId)) return idx;
  }
  return -1; // no distinct member found
}

// helper: find previous index that has a different member_id than the current member
function findPrevDistinctIndex(startIndex) {
  if (!props.allDeceased.length) return -1;
  const length = props.allDeceased.length;
  const startMemberId = props.allDeceased[startIndex]?.member_id;
  for (let i = 1; i <= length; i++) {
    const idx = (startIndex - i + length) % length;
    const item = props.allDeceased[idx];
    if (!item) continue;
    if (String(item.member_id) !== String(startMemberId)) return idx;
  }
  return -1;
}

// navigate to previous distinct member
const prevName = () => {
  const idx = currentIndex.value;
  if (idx === -1) return;

  const prevIdx = findPrevDistinctIndex(idx);
  if (prevIdx === -1) return; // nothing to go to

  const prevMember = props.allDeceased[prevIdx];
  if (!prevMember) return;

  // pass the member_id to your route as requested
  router.get(
  route("filterContribution.toggle", { 
    id: prevMember.member_id,
    purok: props.purok
  }),
  {},
  { preserveScroll: true }
);

};

// navigate to next distinct member
const nextName = () => {
  const idx = currentIndex.value;
  if (idx === -1) return;

  const nextIdx = findNextDistinctIndex(idx);
  if (nextIdx === -1) return;

  const nextMember = props.allDeceased[nextIdx];
  if (!nextMember) return;

  selectedDeceased.value = nextMember;
    router.get(
  route("filterContribution.toggle", { 
    id: nextMember.member_id,
    purok: props.purok
  }),
  {},
  { preserveScroll: true }
);

};
</script>

<template>
  <div class="d-flex flex-column align-items-center" style="height: 120px;">
    <h5 class="mb-3">This contribution belongs to:</h5>

    <div class="d-flex align-items-center gap-4">
      <!-- Left Arrow -->
      <button
        @click="prevName"
        class="btn btn-light rounded-circle shadow-sm"
      >
        <i class="bi bi-arrow-left fs-4"></i>
      </button>

      <!-- Current Name -->
      <h5 class="fw-bold text-dark mb-0">
        {{ props.data.deceased_name || "No name available" }}
      </h5>

      <!-- Right Arrow -->
      <button
        @click="nextName"
        class="btn btn-light rounded-circle shadow-sm"
      >
        <i class="bi bi-arrow-right fs-4"></i>
      </button>
    </div>
  </div>
</template>
