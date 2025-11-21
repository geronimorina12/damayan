<script setup>
import { useForm, Link, router } from "@inertiajs/vue3";
import { ref, defineProps, watch, computed, onMounted } from "vue";
import axios from "axios"; 
import SendToAllSelectedComponent from "../SendToAllSelectedComponent.vue";
import { useTextStore } from '@/piniaStore/useTextStore'

const store = useTextStore();

const form = useForm({
  message: "",
  type: "deathReport",
  memberId: null,
  lastNight: "",
  startOfContribution: "",
});

const props = defineProps({
  deceasedMember: {
    type: Object,
    default: () => ({}),
  },
});

const getDeceasedMember = ref({});
const alertMessage = ref("");
const alertType = ref("");
const showAlert = ref(false);
let reminderWrapper = ref(true)
let showCaret = ref(false);
let closeCaret = ref(false);
// NEW: Toggle SendToAllSelectedComponent visibility
const showSendToAllSelected = ref(false);

// VALIDATION
const lastNightError = ref("");
const hasLastNightError = computed(() => !!lastNightError.value);

const showButtons = computed(() =>
  form.lastNight &&
  form.startOfContribution &&
  !hasLastNightError.value
);

// DEFAULT DATE
onMounted(() => {
  const today = new Date();
  form.startOfContribution = today.toISOString().split("T")[0];
});

// WATCH: Selected member
watch(
  () => props.deceasedMember,
  (newVal) => {
    getDeceasedMember.value = newVal;
  },
  { immediate: true }
);

// VALIDATE
const validateLastNight = (dateString) => {
  if (!dateString) {
    lastNightError.value = "";
    return;
  }

  const selectedDate = new Date(dateString);
  const today = new Date();
  selectedDate.setHours(0, 0, 0, 0);
  today.setHours(0, 0, 0, 0);

  if (selectedDate <= today) {
    lastNightError.value = "The last night date cannot be set today or a past date.";
  } else {
    lastNightError.value = "";
  }
};

watch(() => form.lastNight, validateLastNight);
// AUTO MESSAGE
watch(
  [() => form.lastNight, () => form.startOfContribution],
  ([last, start]) => {
    if (last && start && getDeceasedMember.value?.first_name && !hasLastNightError.value) {
      const m = getDeceasedMember.value;
      form.message = `We regret to inform you that ${m.first_name} ${m.last_name} has passed away. Last night is on ${formatDate(last)}, and the collection for burial assistance starts on ${formatDate(start)}.`;
      form.memberId = m.id;
    } else {
      form.message = "";
    }
  },
  { immediate: true }
);

// BUTTON: Send to all
function sendReminders() {
  if (!form.memberId) {
    showBootstrapAlert("Missing member ID. Please select a member first.", "danger");
    return;
  }

  if (hasLastNightError.value) {
    showBootstrapAlert("Please fix the validation errors before sending.", "danger");
    return;
  }

  router.post(
    route("smsNotification.sendReminders"),
    {
      message: form.message,
      type: form.type,
      memberId: form.memberId,
      lastNight: form.lastNight,
      startOfContribution: form.startOfContribution,
    },
    {
      onSuccess: () => showBootstrapAlert("SMS sent successfully."),
      onError: () => showBootstrapAlert("An error occurred while sending SMS."),
    }
  );
}

// NEW: When clicking "Send to all selected", use AXIOS and SHOW COMPONENT
async function openSendToAllSelected() {
  store.setText('Select Members')
  showSendToAllSelected.value = true; // show the component
  reminderWrapper.value = false; // hide the reminder wrapper
  showCaret.value = true; // show the caret buttons
}

// ALERT
function showBootstrapAlert(message, type) {
  alertMessage.value = message;
  alertType.value = type;
  showAlert.value = true;
  setTimeout(() => (showAlert.value = false), 4000);
}

const formatDate = (dateString) => {
  const options = { year: "numeric", month: "long", day: "numeric" };
  return new Date(dateString).toLocaleDateString(undefined, options);
};
const hoverLeft = ref(false);
const hoverRight = ref(false);

const leftIconClass = computed(() => {
  return hoverLeft.value ? 'bi bi-caret-left-fill' : 'bi bi-caret-left';
});

const rightIconClass = computed(() => {
  return hoverRight.value ? 'bi bi-caret-right-fill' : 'bi bi-caret-right';
});
const toggleSelectMember = () => {
  store.setText('Add Deceased Info.');
  
  reminderWrapper.value = true;
  showSendToAllSelected.value = false;
  
  showCaret.value = true;
};

const toggleReminder = () => {
  store.setText('Select Members');
  
  reminderWrapper.value = false;
  showSendToAllSelected.value = true;
  
};


</script>

<template>

  <div class="d-flex align-items-center gap-2" v-if="showCaret && !closeCaret">
    <button 
      class="caret-wrapper btn btn-outline-dark py-0"
      @mouseenter="hoverLeft = true"
      @mouseleave="hoverLeft = false"
      @click="toggleSelectMember"
      :disabled="reminderWrapper"
    >
      <i 
        :class="leftIconClass" 
        class="fs-4"
      ></i>
    </button>

    <button
      class="caret-wrapper btn btn-outline-dark py-0"
      @mouseenter="hoverRight = true"
      @mouseleave="hoverRight = false"
      @click="toggleReminder"
      :disabled="!reminderWrapper"
    >
      <i 
        :class="rightIconClass" 
        class="fs-4"
      ></i>
  </button>
  </div>

  <div class="reminder-wrapper" v-if="reminderWrapper">
    <div class="card shadow-sm pt-0 p-4 rounded-4 bg-white border-0">

      <!-- ALERT -->
      <transition name="fade">
        <div
          v-if="showAlert"
          :class="['alert', alertType === 'success' ? 'alert-success' : 'alert-danger']"
          class="alert-dismissible fade show mb-3"
          role="alert"
        >
          {{ alertMessage }}
          <button type="button" class="btn-close" @click="showAlert = false"></button>
        </div>
      </transition>

      <!-- MEMBER INFO -->
      <div v-if="getDeceasedMember.first_name" class="mb-4 text-center">
        <h4 class="fw-bold text-primary mb-1">
          {{ getDeceasedMember.first_name }}
          {{ getDeceasedMember.middle_name }}
          {{ getDeceasedMember.last_name }}
        </h4>
        <p class="text-muted mb-0">{{ getDeceasedMember.address }}</p>
      </div>

      <!-- INPUTS -->
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold text-secondary">Enter last night:</label>
          <input 
            type="date"
            class="form-control"
            :class="{ 'is-invalid': hasLastNightError }"
            v-model="form.lastNight"
          />
          <div v-if="hasLastNightError" class="invalid-feedback d-block">
            {{ lastNightError }}
          </div>
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold text-secondary">Start of contribution:</label>
          <input type="date" class="form-control" v-model="form.startOfContribution" />
        </div>
      </div>

      <!-- MESSAGE -->
      <transition name="fade">
        <div v-if="form.message && !hasLastNightError" class="mt-4">
          <label class="form-label fw-semibold text-secondary">Message:</label>
          <textarea
            class="form-control"
            v-model="form.message"
            rows="4"
            style="resize: vertical; min-height: 100px"
          ></textarea>
        </div>
      </transition>

      <!-- BUTTONS -->
      <transition name="fade">
        <div v-if="showButtons" class="mt-4 d-flex flex-wrap gap-3 justify-content-center">

          <!-- NEW BUTTON USING AXIOS AND SHOW COMPONENT -->
          <button class="btn-modern" @click="openSendToAllSelected">
            <i class="bi bi-people-fill me-2"></i>
            Send to all selected
          </button>

          <!-- OLD BUTTON -->
          <button class="btn-modern" @click="sendReminders">
            <i class="bi bi-send-fill me-2"></i>Send to all
          </button>
        </div>
      </transition>
    </div>
  </div>

  <!-- SHOW COMPONENT ONLY WHEN CLICKED -->
  <transition name="fade">
    <SendToAllSelectedComponent
      v-if="showSendToAllSelected"
      :members="[]"
      :type="form.type"
      :message="form.message"
      :deceased="getDeceasedMember?.id"
      :last="form.lastNight"
      v-model:closeCaret="closeCaret"
      class="mt-4"
    />
  </transition>
</template>

<style scoped>
.reminder-wrapper {
  max-width: 700px;
  margin: 2rem auto;
}
.btn-modern {
  background: linear-gradient(135deg, #007bff, #0056b3);
  color: #fff;
  border: none;
  padding: 10px 20px;
  border-radius: 50px;
  font-weight: 600;
  letter-spacing: 0.5px;
  transition: all 0.3s ease;
}
.btn-modern:hover {
  background: linear-gradient(135deg, #0056b3, #003f88);
  transform: translateY(-2px);
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.4s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.caret-wrapper {
  cursor: pointer;
  padding: 5px;
}
</style>
