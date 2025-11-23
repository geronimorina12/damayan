<script setup>
import { ref, watch, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  activeStatus: { type: String, default: 'paid' },
  activePurok: { type: String, default: 'all' },
  members: { type: Array, default: () => [] },
  contributionsIds: { type: Array, default: () => [] }
})

const getMembers = ref([])
const getPurok = computed(() => props.activePurok)
const localStatus = ref(props.activeStatus)
const searchQuery = ref('')
const searching = ref(false)

// filter members based on status + contributionsIds
const filterMembers = () => {
  if (localStatus.value === 'paid') {
    getMembers.value = props.members.filter(m =>
      props.contributionsIds.includes(m.id)
    )
  } else if (localStatus.value === 'not_paid') {
    getMembers.value = props.members.filter(m =>
      !props.contributionsIds.includes(m.id)
    )
  } else {
    getMembers.value = props.members
  }
  console.log("Filtered members in table:", getMembers.value)
}

// reactive search logic - FIXED: Use getMembers directly
const filteredMembers = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()
  if (!query) {
    console.log("No search query, showing all filtered members:", getMembers.value.length)
    return getMembers.value
  }

  const results = getMembers.value.filter(m => {
    const fullName = `${m.first_name} ${m.middle_name || ''} ${m.last_name}`.toLowerCase()
    const contact = m.contact_number?.toLowerCase() || ''
    const purok = m.purok?.toLowerCase() || ''
    return (
      fullName.includes(query) ||
      contact.includes(query) ||
      purok.includes(query)
    )
  })
  
  console.log("Search results:", results.length)
  return results
})

// simulate loading spinner while typing
let typingTimeout
watch(searchQuery, () => {
  searching.value = true
  clearTimeout(typingTimeout)
  typingTimeout = setTimeout(() => {
    searching.value = false
  }, 400)
})

watch(
  () => [props.members, localStatus.value, props.contributionsIds],
  () => {
    console.log("Props changed - filtering members")
    filterMembers()
  },
  { immediate: true, deep: true }
)

const toggleStatus = (status) => {
  console.log("Status:", status)
  console.log("Purok:", getPurok.value)

  localStatus.value = status
  searchQuery.value = '' // Clear search when changing status

  if (getPurok.value === 'all') {
    filterMembers()
    return
  }

  router.get(
    route('collector.toggleStatus', { status, purok: getPurok.value }),
    {},
    { preserveState: true, replace: true }
  )
}

// Debug function to check what's happening
const debugData = () => {
  console.log("=== DEBUG INFO ===")
  console.log("localStatus:", localStatus.value)
  console.log("getMembers length:", getMembers.value.length)
  console.log("filteredMembers length:", filteredMembers.value.length)
  console.log("searchQuery:", searchQuery.value)
  console.log("props.members length:", props.members.length)
  console.log("props.contributionsIds length:", props.contributionsIds.length)
  console.log("===================")
}

// Call debug when component mounts or status changes
watch(localStatus, debugData)
watch(filteredMembers, () => {
  console.log("filteredMembers updated:", filteredMembers.value.length)
})
</script>

<template>
  <div>
    <!-- DEBUG BUTTON (remove in production) -->
    <!-- <button @click="debugData" class="btn btn-sm btn-warning mb-2">Debug Data</button> -->

    <!-- STATUS TOGGLE + SEARCH -->
    <div class="container-fluid d-flex gap-3 align-items-center mb-3 justify-content-between">
      <div class="d-flex gap-3">
        <h5
          class="choice"
          :class="{ 'text-success': localStatus === 'paid' }"
          @click="toggleStatus('paid')"
        >
          PAID
        </h5>
        <h5
          class="choice"
          :class="{ 'text-success': localStatus === 'not_paid' }"
          @click="toggleStatus('not_paid')"
        >
          UNPAID
        </h5>
      </div>

      <div class="search-box position-relative">
        <input
          type="text"
          class="form-control"
          placeholder="Search by name, contact, or purok..."
          v-model="searchQuery"
        />
        <div
          v-if="searching"
          class="spinner-border text-primary spinner-sm position-absolute end-0 top-50 translate-middle-y me-3"
          role="status"
        >
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    </div>

    <!-- MEMBERS TABLE -->
    <div class="table-responsive table-container" :class="{ 'has-scroll': filteredMembers.length > 5 }">
      <table class="table" v-if="filteredMembers.length > 0">
        <thead>
          <tr>
            <th class="bg-light">NAME</th>
            <th class="bg-light">CONTACT</th>
            <th class="bg-light">PUROK</th>
            <th class="bg-light">STATUS</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="member in filteredMembers" :key="member.id">
            <td class="bg-light pb-3">
              {{ member.first_name }} {{ member.middle_name || '' }} {{ member.last_name }}
            </td>
            <td class="bg-light pb-3">{{ member.contact_number }}</td>
            <td class="bg-light pb-3">{{ member.purok }}</td>
            <td class="bg-light text-center pb-3">
              <span
                class="bg-success rounded px-2 py-1 text-light"
                v-if="props.contributionsIds.includes(member.id)"
              >
                paid
              </span>
              <span
                class="bg-danger rounded px-2 py-1 text-light"
                v-else
              >
                unpaid
              </span>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-else class="text-center p-3">
        <p class="text-muted">No members found.</p>
        <p class="text-muted small" v-if="searchQuery">Try clearing your search query</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.choice {
  font-size: 1rem;
  cursor: pointer;
}
.choice:hover {
  color: #007bff;
  text-decoration: underline;
}
.choice.active {
  color: #0CF32F;
  font-weight: bold;
}
.table-container {
  max-height: 600px;
  overflow-y: auto;
  overflow-x: auto;
}
/* Force overflow scroll when there are many rows */
.table-container.has-scroll {
  overflow-y: scroll !important;
}
.spinner-sm {
  width: 1rem;
  height: 1rem;
}
.search-box{
  width: 300px;
}

/* Add padding bottom to table cells */
table tbody tr td {
  padding-bottom: 1.5rem !important;
  vertical-align: middle;
}

/* Alternative: Add padding to the entire table container */
.table-container {
  padding-bottom: 30rem;
}
</style>