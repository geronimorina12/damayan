<script setup>
import { ref, computed, onMounted, defineModel } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

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
const isLoadingMembers = ref(false)
const members = ref(props.members || [])
const closeCarret = defineModel('closeCaret');
const showConfirmation = ref(false)   //  NEW: Show confirmation interface
const deceasedData = ref({});
let smsSent = ref(false);

const filteredMembers = computed(() => {
  if (!Array.isArray(members.value)) return []
  if (!searchQuery.value) return members.value

  const searchLower = searchQuery.value.toLowerCase()
  const filtered = members.value.filter(member => {
    const fullName = `${member.first_name} ${member.last_name}`.toLowerCase()
    return fullName.includes(searchLower)
  })

  return filtered
})

function selectAll() {
  selectedMembers.value = [...filteredMembers.value]
  showUnselectButton.value = true
}

function unselectAll() {
  selectedMembers.value = []
  showUnselectButton.value = false
}

//  NEW: When Done is clicked, switch interface but DO NOT send SMS
function showDoneConfirmation() {
    closeCarret.value = true;
  if (selectedMembers.value.length === 0) {
    message.value = "Please select at least one member first."
    return
  }
  showConfirmation.value = true
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
      showConfirmation.value = false
      smsSent.value = true;
       setTimeout(() => {
        window.location.reload()
      }, 2000)
    },
    onError: (errors) => {
      console.error(errors)
      message.value = `Failed to send ${messageType.value} SMS.`
    }
  })
}

const fetchMembers = async () => {
  isLoadingMembers.value = true;
  try {
    const response = await axios.get(route('smsNotification.members'));
    members.value = response.data.members || response.data;
  } catch (error) {
    console.error("Error fetching members:", error);
  } finally {
    isLoadingMembers.value = false;
  }
};

const fetchDeceased = async (id) => {
  isLoadingMembers.value = true;
  try {
    const response = await axios.get(route('smsNotification.deceased', id));
    deceasedData.value = response.data.deceased || response.data;
  } catch (error) {
    console.error("Error fetching deceased:", error);
  } finally {
    isLoadingMembers.value = false;
  }
};
onMounted(() => {
  if (props.deceased) {
    fetchDeceased(props.deceased);
  }
});
const processedMemberList = computed(() => {
  if (isLoadingMembers.value) return [];
  return members.value.map(member => ({
    id: member.id,
    fullName: `${member.first_name} ${member.last_name}`
  }));
});

onMounted(() => {
  if (!props.members || props.members.length === 0) {
    fetchMembers();
  }
})
const cancel = () => {
    showConfirmation.value = false;
    closeCarret.value = false;
};
</script>


<template>

  <!-- Confirmation Interface -->
<div v-if="showConfirmation && !smsSent" class="confirmation-container">
  <div class="confirmation-header">
    <h4 class="confirmation-title">Review Message</h4>
    <p class="confirmation-subtitle">Please review before sending</p>
  </div>

  <div class="confirmation-content">
    <div class="message-preview">
      <div class="preview-item">
        <span class="label">Message</span>
        <p class="value">{{ smsMessage }}</p>
      </div>
      
      <div class="preview-item">
        <span class="label">Deceased</span>
        <p class="value">{{ deceasedData.first_name || '' }} {{ deceasedData.middle_name || '' }} {{ deceasedData.last_name || '' }}</p>
      </div>
    </div>

    <div class="recipients-section">
      <span class="label">Selected Members</span>
      <div class="recipients-list">
        <div 
          v-for="m in selectedMembers" 
          :key="m.id" 
          class="recipient-item"
        >
          {{ m.first_name }} {{ m.last_name }}
        </div>
      </div>
    </div>
  </div>

  <div class="confirmation-actions">
    <button 
      class="btn btn-outline"
      @click="cancel"
    >
      Cancel
    </button>
    <button 
      class="btn btn-primary" 
      @click="sendToAllSelected"
    >
      Send Message
    </button>
  </div>
</div>  


  <!--  Original Interface (Hidden After Done) -->
  <div v-if="!showConfirmation && !smsSent" class="container mt-4">

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
        <p><span class="fw-bold">Selected:</span> {{ selectedMembers.length }}</p>
      </div>
      
      <div class="d-flex flex-row gap-2">
        <button
          v-if="!showUnselectButton"
          class="btn btn-success button-select-all w-auto"
          @click="selectAll"
        >
          Select All
        </button>
        <button
          v-else
          class="btn btn-warning py-0 button-unselect"
          @click="unselectAll"
        >
          Unselect All
        </button>

        <!--  Modified Done Button -->
        <button class="btn btn-primary py-0 button-done" @click="showDoneConfirmation">
          Done
        </button>
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

    <div class="status-container" v-if="smsSent">
  <div class="status-alert success">
    <svg class="status-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <div class="status-content">
      <p class="status-message">Messages sent successfully</p>
      <p class="status-description">SMS notifications have been delivered to all selected members.</p>
    </div>
  </div>
</div>
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
.confirmation-container {
  max-width: 500px;
  margin: 2rem auto;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  overflow: hidden;
}

.confirmation-header {
  padding: 1.5rem 1.5rem 0;
  border-bottom: 1px solid #f0f0f0;
}

.confirmation-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 0.25rem 0;
}

.confirmation-subtitle {
  font-size: 0.875rem;
  color: #666;
  margin: 0 0 1rem 0;
}

.confirmation-content {
  padding: 1.5rem;
}

.message-preview {
  margin-bottom: 1.5rem;
}

.preview-item {
  margin-bottom: 1rem;
}

.preview-item:last-child {
  margin-bottom: 0;
}

.label {
  display: block;
  font-size: 0.75rem;
  font-weight: 600;
  color: #666;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 0.25rem;
}

.value {
  margin: 0;
  color: #1a1a1a;
  line-height: 1.5;
}

.recipients-section {
  margin-top: 1.5rem;
}

.recipients-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.75rem;
}

.recipient-item {
  background: #f8f9fa;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  font-size: 0.875rem;
  color: #495057;
  border: 1px solid #e9ecef;
}

.confirmation-actions {
  display: flex;
  gap: 0.75rem;
  padding: 1rem 1.5rem 1.5rem;
  background: #fafafa;
}

.btn {
  flex: 1;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: center;
}

.btn-outline {
  background: white;
  color: #666;
  border: 1px solid #ddd;
}

.btn-outline:hover {
  background: #f8f9fa;
  border-color: #ccc;
}

.btn-primary {
  background: #10b981;
  color: white;
}

.btn-primary:hover {
  background: #059669;
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}
.button-unselect, .button-done, .button-select-all{
    padding-top: 0 !important;
    padding-bottom: 0 !important;
    height: 2rem !important;
    width: 200px !important;
}
.button-done{
    width: auto !important;
}
.status-container {
  max-width: 500px;
  margin: 2rem auto;
}

.status-alert {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  padding: 1rem 1.25rem;
  border-radius: 8px;
  border: 1px solid;
}

.status-alert.success {
  background: #f0fdf4;
  border-color: #bbf7d0;
  color: #166534;
}

.status-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
  margin-top: 0.125rem;
}

.status-content {
  flex: 1;
}

.status-message {
  font-weight: 600;
  margin: 0 0 0.25rem 0;
  font-size: 0.95rem;
}

.status-description {
  margin: 0;
  font-size: 0.875rem;
  opacity: 0.9;
  line-height: 1.4;
}
</style>
