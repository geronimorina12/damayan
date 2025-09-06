<script setup>
import { ref, computed, watch } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import HeaderComponent from '@/Components/dashboard/HeaderComponent.vue'
import DeathReportInput from '@/Components/dashboard/smsNotificationSave/DeathReportInput.vue'

const props = defineProps({
  deathReport: Object,
  disbursed: String,
  members: Array
})

const deathReport = ref([])
const totalDisbursedAmount = ref('')
const getMembers = ref([])
const deathInput = ref('')
const lastNightInput = ref('')
const startOfContribution = ref('')
const isTextCopied = ref(false)

watch(() => props.deathReport, data => {
  deathReport.value = data
}, { immediate: true })

watch(() => props.disbursed, data => {
  totalDisbursedAmount.value = data
}, { immediate: true })

watch(() => props.members, data => {
  getMembers.value = data
}, { immediate: true })

const form = useForm({
  deathReport: '',
  scheduleContribution: '',
  reminders: '',
  fundUpdates: '',
  type: ''
})

const modalTitle = ref('')

const computedSuggestion = computed(() => {
  if (modalTitle.value === 'Death Report Suggestion') {
    return `We regret to inform you that ${deathInput.value?.first_name || '[Name of Dead]'} ${deathInput.value?.last_name || ''} has passed away. Last night will be held on ${lastNightInput.value || '[Date]'}. Collection for burial assistance starts on ${startOfContribution.value || '[Date]'}.'`
  }
  if (modalTitle.value === 'Schedule Contribution Suggestion') {
    return "Hello [Name], thank you for paying your contribution. This message confirms your payment for the upcoming schedule."
  }
  if (modalTitle.value === 'Reminder Suggestion') {
    return "Hello, our records show you still have an unpaid balance for the contribution (Damayan). Please settle it at your earliest convenience. Thank you."
  }
  if (modalTitle.value === 'Fund Update Suggestion') {
    return `Total money disbursed so far is ₱${totalDisbursedAmount.value}. Thank you for your continuous support.`
  }
  return ''
})

function showSuggestion(type) {
  modalTitle.value = type === 'deathReport' ? 'Death Report Suggestion' :
                      type === 'scheduleContribution' ? 'Schedule Contribution Suggestion' :
                      type === 'reminders' ? 'Reminder Suggestion' :
                      type === 'fundUpdates' ? 'Fund Update Suggestion' : '';
  isTextCopied.value = false;
}

function save(type) {
  form.type = type
  form.post(route('smsNotification.addDeathReport'), {
    preserveScroll: true,
    onSuccess: () => alert("Notification saved."),
    onError: () => alert('An error occurred, please try again.')
  })
}

async function copyText(text) {
  try {
    await navigator.clipboard.writeText(text)
    isTextCopied.value = true
    setTimeout(() => {
      isTextCopied.value = false;
    }, 4000);
  } catch (err) {
    console.error('Failed to copy text: ', err)
    alert('Failed to copy text. Please try again or copy manually.')
  }
}
</script>

<template>
  <div>
    <AdminLayout>
      <Head title="Sms notification" />

      <div class="container">
        <h4 class="mb-4 fw-bold sticky-header text-start ps-2 pt-3">SMS Notification</h4>

        <div class="scroll-content">
          <div class="mb-4 position-relative">
            <div class="container-fluid d-flex flex-row justify-content-between align-items-center">
              <label class="form-label">Death Report</label>
              <i class="bi bi-info-circle" data-bs-toggle="modal" data-bs-target="#suggestionModal"
                @click="showSuggestion('deathReport')"></i>
            </div>
            <textarea v-model="form.deathReport" class="form-control"></textarea>
            <button class="save-btn" @click="save('deathReport')">SAVE</button>
          </div>

          <div class="mb-4 position-relative">
            <div class="container-fluid d-flex flex-row justify-content-between align-items-center">
              <label class="form-label">Schedule Contribution</label>
              <i class="bi bi-info-circle" data-bs-toggle="modal" data-bs-target="#suggestionModal"
                @click="showSuggestion('scheduleContribution')"></i>
            </div>
            <textarea v-model="form.scheduleContribution" class="form-control"></textarea>
            <button class="save-btn" @click="save('scheduleContribution')">SAVE</button>
          </div>

          <div class="mb-4 position-relative">
            <div class="container-fluid d-flex flex-row justify-content-between align-items-center">
              <label class="form-label">Reminders</label>
              <i class="bi bi-info-circle" data-bs-toggle="modal" data-bs-target="#suggestionModal"
                @click="showSuggestion('reminders')"></i>
            </div>
            <textarea v-model="form.reminders" class="form-control"></textarea>
            <button class="save-btn" @click="save('reminders')">SAVE</button>
          </div>

          <div class="mb-4 position-relative">
            <div class="container-fluid d-flex flex-row justify-content-between align-items-center">
              <label class="form-label">Fund Updates</label>
              <i class="bi bi-info-circle" data-bs-toggle="modal" data-bs-target="#suggestionModal"
                @click="showSuggestion('fundUpdates')"></i>
            </div>
            <textarea v-model="form.fundUpdates" class="form-control"></textarea>
            <button class="save-btn" @click="save('fundUpdates')">SAVE</button>
          </div>

          <div class="container extra-space w-100"></div>
        </div>
      </div>

      <div class="modal fade" id="suggestionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5">{{ modalTitle }}</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <DeathReportInput
              v-if="modalTitle === 'Death Report Suggestion'"
                :members="getMembers"
                v-model:dead="deathInput"
                v-model:startOfContribution="startOfContribution"
                v-model:lastNight="lastNightInput"
              />

              <p>{{ computedSuggestion }}</p>
              <span 
              class="text-dark rounded copy" 
              @click="copyText(computedSuggestion)" 
              title="Copy text"
              v-if="!isTextCopied"
              >
                <i class="bi bi-copy"></i>
              </span>

              <span v-if="isTextCopied" class="text-dark rounded py-1 px-2" title="Text copied">
                <i class="bi bi-check-square"></i>
              </span>
            </div>
            <div class="modal-footer d-flex justify-content-between align-items-center">
              <p class="text-muted mt-3">Copy it and paste to the input field</p>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
    </AdminLayout>
  </div>
</template>

<style scoped>
.container {
  height: 100vh;
  display: flex;
  flex-direction: column;
}
.sticky-header {
  border-bottom: 2px solid #eaeaea;
  padding-bottom: 10px;
  background: white;
  position: sticky;
  top: 0;
  z-index: 10;
}
.scroll-content {
  flex: 1;
  overflow-y: auto;
  padding-right: 5px;
}
textarea.form-control {
  height: 120px;
  resize: none;
}
.save-btn {
  position: absolute;
  bottom: 10px;
  right: 10px;
  background-color: #16bd2f;
  border: none;
  color: white;
  padding: 6px 12px;
  border-radius: 4px;
  cursor: pointer;
}
.save-btn:hover {
  background-color: #0ad929;
}
.copy {
  cursor: pointer;
}
.extra-space {
    width: 100%;
  height: 20%;
}
</style>