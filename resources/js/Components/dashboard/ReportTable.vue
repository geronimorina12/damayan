<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  activeStatus: { type: String, default: 'paid' },
  activePurok: { type: String, default: 'all' },
  members: { type: Array, default: () => [] },
  contributionsIds: { type: Array, default: () => [] }
})

const getMembers = ref([])
const getPurok = ref("all")
const localStatus = ref(props.activeStatus) // 👈 local status tracker

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
}

watch(
  () => [props.members, localStatus.value],
  () => {
    filterMembers()
  },
  { immediate: true, deep: true }
)

watch(
  () => props.activePurok,
  (newPurok) => {
    getPurok.value = newPurok
  },
  { immediate: true }
)

const toggleStatus = (status) => {
  localStatus.value = status // 👈 update status

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
</script>

<template>
  <div>
    <!-- STATUS TOGGLE -->
    <div class="container-fluid d-flex gap-3 align-items-center mb-3">
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

    <!-- MEMBERS TABLE -->
    <div class="table-responsive table-container">
      <table class="table" v-if="getMembers.length > 0">
        <thead>
          <tr>
            <th class="bg-light">ID</th>
            <th class="bg-light">NAME</th>
            <th class="bg-light">CONTACT NO.</th>
            <th class="bg-light">PUROK</th>
            <th class="bg-light">STATUS</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="member in getMembers" :key="member.id">
            <td class="bg-light">{{ member.id }}</td>
            <td class="bg-light">
              {{ member.first_name }} {{ member.middle_name }} {{ member.last_name }}
            </td>
            <td class="bg-light">{{ member.contact_number }}</td>
            <td class="bg-light">{{ member.purok }}</td>
            <td class="bg-light text-center">
              <i
                class="bi bi-check-circle text-success fs-5"
                v-if="props.contributionsIds.includes(member.id)"
              ></i>
              <i class="bi bi-ban text-danger fs-5" v-else></i>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-else class="text-center p-3">
        <p class="text-muted">No members found.</p>
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
</style>
