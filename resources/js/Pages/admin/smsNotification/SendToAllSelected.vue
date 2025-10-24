<script setup>
import { ref, computed } from 'vue'
import { router, Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  members: { type: Array, default: () => [] },
  type: { type: String, default: 'deathReport' },
  message: { type: String, default: '' },
  deceased: {type: String},
  last: {type: String}
})

const selectedMembers = ref([])
const searchQuery = ref('')
const messageType = ref(props.type)
const message = ref('')
const smsMessage = ref(props.message || '')
const showUnselectButton = ref(false)

const filteredMembers = computed(() => {
  if (!Array.isArray(props.members)) return []
  if (!searchQuery.value) return props.members

  const searchLower = searchQuery.value.toLowerCase()
  return props.members.filter(member => {
    const fullName = `${member.first_name} ${member.last_name}`.toLowerCase()
    return fullName.includes(searchLower)
  })
})

function selectAll() {
  selectedMembers.value = [...filteredMembers.value]
  showUnselectButton.value = true
}

function unselectAll() {
  selectedMembers.value = []
  showUnselectButton.value = false
}

function sendToAllSelected() {
  message.value = ''

  if (selectedMembers.value.length === 0) {
    message.value = 'Please select at least one member first.'
    return
  }
  const memberIds = selectedMembers.value.map(m => m.id)

  router.post(route(`smsSelectMember.${messageType.value}`), {
    member_ids: memberIds,
    message: smsMessage.value,
    deceased: props.deceased || 0,
    last: props.last
  }, {
    onSuccess: () => {
      message.value = `${messageType.value} SMS sent successfully.`
      selectedMembers.value = []
      showUnselectButton.value = false
    },
    onError: (errors) => {
      console.error(errors)
      message.value = `Failed to send ${messageType.value} SMS.`
    }
  })
}
</script>

<template>
  <AdminLayout>
    <Head title="Select Members to Send SMS" />

    <div class="container mt-4">
      <h4 class="fw-bold mb-3">Select Multiple Members to Send SMS</h4>

      <div class="mb-3">
        <input
          type="text"
          v-model="searchQuery"
          class="form-control"
          placeholder="Search by name..."
        />
      </div>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex justify-content-between align-items-center gap-3">
          <p><span class="fw-bold">Type:</span> {{ messageType }}</p>
          <p><span class="fw-bold">Selected:</span> {{ selectedMembers.length }}</p>
        </div>
        
        <div class="d-flex flex-row gap-2">
          <button
            v-if="!showUnselectButton"
            class="btn btn-success"
            @click="selectAll"
          >
            Select All
          </button>
          <button
            v-else
            class="btn btn-warning"
            @click="unselectAll"
          >
            Unselect All
          </button>
          <button class="btn btn-primary" @click="sendToAllSelected">Send</button>
        </div>
      </div>

      <div v-if="message" class="alert alert-info" role="alert">
        {{ message }}
      </div>

      <div class="table-scroll-container">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Select</th>
                <th>Name</th>
                <th>Age</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="member in filteredMembers" :key="member.id">
                <td>
                  <input type="checkbox" :value="member" v-model="selectedMembers" />
                </td>
                <td>{{ member.first_name }} {{ member.last_name }}</td>
                <td>{{ member.age }}</td>
              </tr>
              <tr v-if="filteredMembers.length === 0">
                <td colspan="3" class="text-center">No members found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.table-hover tbody tr:hover {
  background-color: #f1f1f1;
}
.table-responsive {
  overflow-x: auto;
}
.table-scroll-container {
  max-height: 400px;
  overflow-y: auto;
}
</style>
