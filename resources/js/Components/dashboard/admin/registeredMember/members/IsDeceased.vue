<script setup>
import { useForm, Link, router } from "@inertiajs/vue3";
import { ref, defineProps, watch, computed } from "vue";

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
const alertType = ref(""); // 'success' or 'danger'
const showAlert = ref(false);

// Watch for selected member
watch(
  () => props.deceasedMember,
  (newVal) => {
    getDeceasedMember.value = newVal;
  },
  { immediate: true }
);

// Auto-generate message when both fields are filled
watch(
  [() => form.lastNight, () => form.startOfContribution],
  ([last, start]) => {
    if (last && start && getDeceasedMember.value?.first_name) {
      const m = getDeceasedMember.value;
      form.message = `We regret to inform you that ${m.first_name} ${m.last_name} has passed away. Last night is on ${formatDate(last)}, and the collection for burial assistance starts on ${formatDate(start)}.`;
      form.memberId = m.id;
    } else {
      form.message = "";
    }
  },
  { immediate: true }
);

// Send reminders
function sendReminders() {
  if (!form.memberId) {
    showBootstrapAlert("Missing member ID. Please select a member first.", "danger");
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
      onSuccess: () => showBootstrapAlert("SMS send successfully."),
      onError: () => showBootstrapAlert("An error while sending sms."),
    }
  );
}

// Function to show bootstrap alert
function showBootstrapAlert(message, type) {
  alertMessage.value = message;
  alertType.value = type;
  showAlert.value = true;
  setTimeout(() => (showAlert.value = false), 4000);
}

const showButtons = computed(() => form.lastNight && form.startOfContribution);

const formatDate = (dateString) => {
  const options = { year: "numeric", month: "long", day: "numeric" };
  return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<template>
  <div class="reminder-wrapper">
    <div class="card shadow-sm p-4 rounded-4 bg-white border-0">
      <!-- Bootstrap Alert -->
      <transition name="fade">
        <div
          v-if="showAlert"
          :class="['alert', alertType === 'success' ? 'alert-success' : 'alert-danger']"
          class="alert-dismissible fade show mb-3"
          role="alert"
        >
          {{ alertMessage }}
          <button
            type="button"
            class="btn-close"
            @click="showAlert = false"
            aria-label="Close"
          ></button>
        </div>
      </transition>

      <!-- Member name header -->
      <div v-if="getDeceasedMember.first_name" class="mb-4 text-center">
        <h4 class="fw-bold text-primary mb-1">
          {{ getDeceasedMember.first_name }}
          {{ getDeceasedMember.middle_name }}
          {{ getDeceasedMember.last_name }}
        </h4>
        <p class="text-muted mb-0">{{ getDeceasedMember.address }}</p>
      </div>

      <!-- Input fields -->
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold text-secondary">Enter last night:</label>
          <input type="date" class="form-control" v-model="form.lastNight" />
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold text-secondary">Start of contribution:</label>
          <input type="date" class="form-control" v-model="form.startOfContribution" />
        </div>
      </div>

      <!-- Message preview -->
      <transition name="fade">
        <p
          v-if="form.message"
          class="mt-4 bg-light p-3 rounded border-start border-4 border-primary"
        >
          {{ form.message }}
        </p>
      </transition>

      <!-- Action buttons -->
      <transition name="fade">
        <div
          v-if="showButtons"
          class="mt-4 d-flex flex-wrap gap-3 justify-content-center"
        >
          <Link
            :href="
              route('smsNotification.sendToAllSelectedV2', {
                type: 'deathReport',
                message: form.message,
                deceased: getDeceasedMember?.id,
                last: form.lastNight,
              })
            "
            class="btn-modern text-decoration-none"
          >
            <i class="bi bi-people-fill me-2"></i>Send to all selected
          </Link>

          <button class="btn-modern" @click="sendReminders">
            <i class="bi bi-send-fill me-2"></i>Send
          </button>
        </div>
      </transition>
    </div>
  </div>
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
  display: inline-flex;
  align-items: center;
  justify-content: center;
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
</style>
