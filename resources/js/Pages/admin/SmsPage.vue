<script setup>
import { ref, defineProps, watch } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  deathReport: { type: Object, default: () => ({}) },
  scheduleContribution: { type: Object, default: () => ({}) },
  reminders: { type: Object, default: () => ({}) },
  fundUpdates: { type: Object, default: () => ({}) },
  members: { type: Array, default: () => [] },
})

const getDeathReport = ref({})
const getScheduleContribution = ref({})
const getReminders = ref({})
const getFundUpdates = ref({})
const getMembers = ref([]);

watch(() => props.deathReport, (data) => { getDeathReport.value = data }, { immediate: true })
watch(() => props.scheduleContribution, (data) => { getScheduleContribution.value = data }, { immediate: true })
watch(() => props.reminders, (data) => { getReminders.value = data }, { immediate: true })
watch(() => props.fundUpdates, (data) => { getFundUpdates.value = data }, { immediate: true })
watch(() => props.members, (data) => { getMembers.value = data }, { immediate: true })

// Separated functions kada sms

const sendDeathReport = () => {
  router.post(route('smsNotification.addDeathReport'), {
    message: getDeathReport.value.message
  }, {
    onSuccess: () => {
      alert('Death report sent successfully.')
    },
    onError: (errors) => {
      console.error(errors)
      alert('Failed to send death report.')
    }
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
    message: getFundUpdates.value.message,
  }, {
    onSuccess: () => alert('Fund Updates sent successfully!'),
    onError: () => alert('Error sending Fund Updates')
  })
}
</script>

<template>
  <div>
    <AdminLayout>
      <Head title="Sms notification" />
      <div class="container">
        <h4 class="mb-4 fw-bold sticky-header text-start ps-2 pt-3">SMS</h4>

        <div class="scroll-content">
          <!-- Death Report -->
          <div class="mb-4 position-relative">
            <div>
              <label for="deathReport" class="form-label">Death Report</label>
            </div>
            <textarea v-model="getDeathReport.message" id="deathReport" class="form-control"></textarea>

            <div class="container-fluid d-flex flex-row align-items-center gap-3 justify-content-end mt-3">
              <div>
                <Link :href="route('smsNotification.sendToAllSelected', {type: 'deathReport', message: getDeathReport.message})" class="save-btn text-uppercase text-decoration-none">
                send to all selected
              </Link>

              </div>
              <div>
                <button @click="sendDeathReport" class="save-btn text-uppercase text-decoration-none">
                  send
                </button>
              </div>
            </div>
          </div>

          <!-- Schedule Contribution -->
          <div class="mb-4 position-relative">
            <div>
              <label for="scheduleContribution" class="form-label">Schedule Contribution</label>
            </div>
            <textarea v-model="getScheduleContribution.message" id="scheduleContribution" class="form-control"></textarea>

            <div class="container-fluid d-flex flex-row align-items-center gap-3 justify-content-end mt-3">
              <Link :href="route('smsNotification.sendToAllSelected', {type: 'scheduleContribution', message: getScheduleContribution.message})" class="save-btn text-uppercase text-decoration-none">
                send to all selected
              </Link>
              <div>
                <button class="save-btn text-uppercase" @click="sendScheduleContribution">send</button>
              </div>
            </div>
          </div>

          <!-- Reminders -->
          <div class="mb-4 position-relative">
            <div>
              <label for="reminders" class="form-label">Reminders</label>
            </div>
            <textarea v-model="getReminders.message" id="reminders" class="form-control"></textarea>

            <div class="container-fluid d-flex flex-row align-items-center gap-3 justify-content-end mt-3">
              <div>
                <Link :href="route('smsNotification.sendToAllSelected', {type: 'reminders', message: getReminders.message})" class="save-btn text-uppercase text-decoration-none">
                  send to all selected
                </Link>
              </div>
              <div>
                <button class="save-btn text-uppercase" @click="sendReminders">send</button>
              </div>
            </div>
          </div>

          <!-- Fund Updates -->
          <div class="mb-4 position-relative">
            <div>
              <label for="fundUpdates" class="form-label">Fund Updates</label>
            </div>
            <textarea v-model="getFundUpdates.message" id="fundUpdates" class="form-control"></textarea>

            <div class="container-fluid d-flex flex-row align-items-center gap-3 justify-content-end mt-3">
              <div>
                <Link :href="route('smsNotification.sendToAllSelected', {type: 'fundUpdates', message: getFundUpdates.message})" class="save-btn text-uppercase text-decoration-none">
                  send to all selected
                </Link>
              </div>
              <div>
                <button class="save-btn text-uppercase" @click="sendFundUpdates">send</button>
              </div>
            </div>
          </div>

          <div class="container extra-space w-100"></div>
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

.position-relative {
  position: relative;
}

textarea.form-control {
  height: 120px;
  resize: none;
}

.save-btn {
  position: relative;
  right: 0;
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

.extra-space {
  width: 100%;
  height: 20%;
}
</style>
