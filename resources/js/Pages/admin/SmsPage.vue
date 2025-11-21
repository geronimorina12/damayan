<script setup>
import { ref, defineProps, computed } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  deathReport: { type: Object, default: () => ({}) },
  scheduleContribution: { type: Object, default: () => ({}) },
  reminders: { type: Object, default: () => ({}) },
  fundUpdates: { type: Object, default: () => ({}) },
  members: { type: Array, default: () => [] },
  fundAmount: { type: Number, default: 0 }
})

// Use computed properties instead of ref + watch
const getDeathReport = computed(() => props.deathReport)
const getScheduleContribution = computed(() => props.scheduleContribution)
const getReminders = computed(() => props.reminders)
const getFundUpdates = computed(() => props.fundUpdates)
const getMembers = computed(() => props.members)
const getFundAmount = computed(() => props.fundAmount)

// Modal state
const showNoFundModal = ref(false)
const pendingAction = ref(null)

// Modal functions
const confirmSend = () => {
  if (pendingAction.value) pendingAction.value()
  showNoFundModal.value = false
  pendingAction.value = null
}

const cancelSend = () => {
  showNoFundModal.value = false
  pendingAction.value = null
}

const checkFundBeforeSending = (action) => {
  if (getFundAmount.value === 0) {
    showNoFundModal.value = true
    pendingAction.value = action
  } else {
    action()
  }
}

// SMS senders
const sendDeathReport = () => {
  router.post(route('smsNotification.addDeathReport'), {
    message: getDeathReport.value.message
  }, {
    onSuccess: () => alert('Death report sent successfully.'),
    onError: () => alert('Failed to send death report.')
  })
}

function sendScheduleContribution() {
  router.post(route('smsNotification.sendScheduleContribution'), {
    message: getScheduleContribution.value.message,
  }, {
    onSuccess: () => alert('Schedule Contribution sent successfully!'),
    onError: () => alert('Error sending Schedule Contribution')
  })
}

function sendReminders() {
  router.post(route('smsNotification.sendReminders'), {
    message: getReminders.value.message,
  }, {
    onSuccess: () => alert('Reminders sent successfully!'),
    onError: () => alert('Error sending Reminders')
  })
}

function sendFundUpdates() {
  router.post(route('smsNotification.sendFundUpdates'), {
    message: getFundUpdates.value,
  }, {
    onSuccess: () => alert('Fund Updates sent successfully!'),
    onError: (e) => console.log('Error sending Fund Updates', e)
  })
}

const handleSendFundUpdates = () => {
  checkFundBeforeSending(sendFundUpdates)
}
</script>

<template>
  <div>
    <AdminLayout>
      <Head title="Sms notification" />
      <div class="container">
        <h4 class="mb-4 fw-bold sticky-header text-start ps-2 pt-3">SMS</h4>

        <div class="scroll-content">
          <!-- Fund Updates -->
          <div class="mb-4 position-relative">
            <div>
              <label for="fundUpdates" class="form-label">Fund Updates</label>
            </div>

            <textarea :value="getFundUpdates.message" id="fundUpdates" class="form-control" disabled></textarea>

            <div class="container-fluid d-flex flex-row align-items-center gap-3 justify-content-end mt-3">
              <div></div>
              <div>
                <button class="save-btn text-uppercase" @click="handleSendFundUpdates">send</button>
              </div>
            </div>
          </div>

          <div class="container extra-space w-100"></div>
        </div>
      </div>

      <!-- No Fund Modal -->
      <div v-if="showNoFundModal" class="modal-backdrop show d-block">
        <div class="modal d-block fade" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Confirm Send</h5>
              </div>
              <div class="modal-body">
                <p>There's no fund collected as for now.</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="cancelSend">Ok</button>
                <!-- <button type="button" class="btn btn-primary" @click="confirmSend">Send Anyway</button> -->
              </div>
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

/* FIXED — Modal solid white, no opacity */
.modal.fade {
  opacity: 1 !important;
}

.modal-backdrop {
  background-color: rgba(0, 0, 0, 0.7) !important;
  opacity: 0.7 !important;
}

.modal-content {
  background-color: #ffffff !important;
  opacity: 1 !important;
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.modal-header,
.modal-footer,
.modal-body {
  background: white !important;
}

.btn {
  padding: 0.6rem 1.2rem;
  border-radius: 6px;
  cursor: pointer;
  border: none;
}

/* REMOVE ALL TRANSPARENCY COMPLETELY */

/* Disable Bootstrap fade opacity */
.modal,
.modal.fade,
.modal.show {
  opacity: 1 !important;
}

/* Solid white modal body */
.modal-content,
.modal-header,
.modal-body,
.modal-footer {
  background-color: #ffffff !important;
  opacity: 1 !important;
  backdrop-filter: none !important;
}

/* Black background behind modal */
.modal-backdrop.show {
  background-color: rgba(0, 0, 0, 0.7) !important;
  opacity: 1 !important;
}

/* Ensure container of modal is also solid */
.modal-dialog,
.modal-dialog-centered {
  background: transparent !important;
}

/* Buttons */
.btn {
  padding: 0.6rem 1.2rem;
  border-radius: 6px;
  cursor: pointer;
  border: none;
}

</style>
