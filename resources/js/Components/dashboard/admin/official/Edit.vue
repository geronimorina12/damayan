<script setup>
import { useForm } from '@inertiajs/vue3'
import { defineProps, ref, watch } from 'vue'
import * as bootstrap from 'bootstrap';
import { computed } from 'vue';

const props = defineProps({
  official: {
    type: Object,
    default: () => ({}),
  },
})
// Reactive alerts
const successMessage = ref('')
const errorMessage = ref('')
const hasError = ref(false)

// Initialize form
const form = useForm({
  first_name: '',
  middle_initial: '',
  last_name: '',
  email: '',
  position: 'collector',
  term_start: '',
  term_end: '',
  status: 1,
})

// Watch for prop changes and populate form
watch(
  () => props.official,
  (newOfficial) => {
    if (newOfficial && Object.keys(newOfficial).length) {
      const [firstName, middle, lastName] = splitName(newOfficial.name)
      form.first_name = firstName
      form.middle_initial = middle
      form.last_name = lastName
      form.email = newOfficial.email || ''
      form.position = newOfficial.position || 'collector'
      form.term_start = newOfficial.term_start || ''
      form.term_end = newOfficial.term_end || ''
      form.status = newOfficial.status ?? 0
    }
    console.log('Editing Official:', form)
  },
  { immediate: true }
)

// Split full name helper
function splitName(fullName = '') {
  const parts = fullName.trim().split(' ')
  const first = parts[0] || ''
  const last = parts[parts.length - 1] || ''
  const middle = parts.length > 2 ? parts[1].replace('.', '') : ''
  return [first, middle, last]
}

// Submit form
function submit() {
  const middle = form.middle_initial ? ` ${form.middle_initial}.` : ''
  const name = `${form.first_name}${middle} ${form.last_name}`.trim()

  successMessage.value = ''
  errorMessage.value = ''
  hasError.value = false

  if(form.position == 'collector'){

  form.put(route('officials.collector.editData', { id: props.official.id }), {
    data: {
      name: name,
      position: form.position,
      term_start: form.term_start,
      term_end: form.term_end,
      status: form.status,
      email: form.email,
    },
    onSuccess: () => {
      successMessage.value = 'Official updated successfully!'

      // Close modal after a short delay (optional)
      setTimeout(() => {
        closeModal()
      }, 500)
    },
    onError: (errors) => {
      hasError.value = true
      errorMessage.value = Object.values(errors).flat().join(' ')
      console.error(errors)
    },
  })
  }else{
    
  form.put(route('officials.editData', { id: props.official.id }), {
    data: {
      name: name,
      position: form.position,
      term_start: form.term_start,
      term_end: form.term_end,
      status: form.status,
      email: form.email,
    },
    onSuccess: () => {
      successMessage.value = 'Official updated successfully!'

      // Close modal after a short delay (optional)
      setTimeout(() => {
        closeModal()
      }, 500)
    },
    onError: (errors) => {
      hasError.value = true
      errorMessage.value = Object.values(errors).flat().join(' ')
      console.error(errors)
    },
  })
  }
}

function closeModal() {
  const modalEl = document.getElementById('editOfficial')
  if (!modalEl) return

  // Get Bootstrap modal instance
  let modalInstance = bootstrap.Modal.getInstance(modalEl)
  if (!modalInstance) {
    modalInstance = new bootstrap.Modal(modalEl)
  }

  // Hide the modal
  modalInstance.hide()

  // Also remove the backdrop manually
  const backdrops = document.getElementsByClassName('modal-backdrop')
  while (backdrops[0]) {
    backdrops[0].parentNode.removeChild(backdrops[0])
  }

  // Remove "modal-open" class from body
  document.body.classList.remove('modal-open')
}
const currentYear = new Date().getFullYear()

// Term Start: only up to 3 years before now
const termStartMax = computed(() => `${currentYear - 3}-12-31`)

// Term End: only dates with year greater than today
const termEndMin = computed(() => `${currentYear + 1}-01-01`)

// Enable or disable status based on term_start year
watch(() => form.term_start, (newDate) => {
  if (!newDate) return;

  const year = new Date(newDate).getFullYear();
  const currentYear = new Date().getFullYear();

  // If term_start is this year or greater → allow editing status
  if (year >= currentYear) {
    form.status = 1; // active
  } else {
    // If term_start is in the past → disable status and show warning
    form.status = 0;
  }
});


</script>


<template>
  <Head title="Edit Official" />

  <div class="main-container">
    <div class="container border-0 pt-2">
      <div class="card-body px-3">

        <!--  Success Alert -->
        <div
          v-if="successMessage"
          class="alert alert-success alert-dismissible fade show"
          role="alert"
        >
          <i class="bi bi-check-circle-fill me-2"></i>
          {{ successMessage }}
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
            @click="successMessage = ''"
          ></button>
        </div>

        <!-- ⚠️ Error Alert -->
        <div
          v-if="hasError"
          class="alert alert-danger alert-dismissible fade show"
          role="alert"
        >
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          {{ errorMessage }}
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
            @click="hasError = false"
          ></button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submit">
          <!-- Full Name -->
          <label class="form-label fw-semibold">Full Name</label>
          <div class="row mb-3">
            <div class="col-md-4 mb-2 mb-md-0">
              <input
                v-model="form.first_name"
                type="text"
                class="form-control"
                placeholder="First Name"
                required
              />
            </div>
            <div class="col-md-4 mb-2 mb-md-0">
              <input
                v-model="form.middle_initial"
                type="text"
                maxlength="1"
                class="form-control"
                placeholder="M.I."
              />
            </div>
            <div class="col-md-4">
              <input
                v-model="form.last_name"
                type="text"
                class="form-control"
                placeholder="Last Name"
                required
              />
            </div>
          </div>

          <!-- Position -->
          <div class="mb-3">
            <label for="position" class="form-label fw-semibold">Position</label>
            <input
              id="position"
              class="form-control"
              v-model="form.position"
              required
              disabled
            />
          </div>

          <!-- Email -->
          <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email</label>
            <input
              v-model="form.email"
              type="email"
              id="email"
              class="form-control"
              placeholder="juan45@gmail.com"
            />
          </div>

                    <!-- Term Start -->
          <div class="mb-3">
            <label for="term_start" class="form-label fw-semibold">Term Start</label>
            <input
              v-model="form.term_start"
              type="date"
              id="term_start"
              class="form-control"
              :min="termStartMax"
              required
            />
          </div>

          <!-- Term End -->
          <div class="mb-3">
            <label for="term_end" class="form-label fw-semibold">Term End</label>
            <input
              v-model="form.term_end"
              type="date"
              id="term_end"
              class="form-control"
              :min="termEndMin"
              required
            />
          </div>

          <div class="container px-0 px-0 d-flex flex-row justify-content-start align-items-center gap-2 mb-2">
             <div class="status-cell">
                  <div class="status-toggle">
                    <label class="toggle-switch">
                      <input
                        type="checkbox"
                        v-model="form.status"
                        :disabled="form.term_start && new Date(form.term_start).getFullYear() < currentYear"
                      >

                      <span class="toggle-slider"></span>
                    </label>
                  </div>
                </div>
                <div>Status</div>
          </div>
          <p class="text-danger"
   v-if="form.term_start && new Date(form.term_start).getFullYear() < currentYear"> This official has ended their term.</p>

          <!-- Submit Button -->
          <button
            type="submit"
            class="btn btn-primary w-100 py-2 shadow-sm"
            :disabled="form.processing"
          >
            <span v-if="!form.processing">
              <i class="bi bi-save me-2"></i> Update Record
            </span>
            <span v-else>
              <span
                class="spinner-border spinner-border-sm me-2"
                role="status"
                aria-hidden="true"
              ></span>
              Updating...
            </span>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>

.status-cell {
  text-align: center;
}

.status-toggle {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
}

.toggle-switch {
  position: relative;
  display: inline-block;
  width: 44px;
  height: 24px;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #e5e7eb;
  transition: .3s;
  border-radius: 24px;
}

.toggle-slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .3s;
  border-radius: 50%;
}

input:checked + .toggle-slider {
  background-color: #10b981;
}

input:checked + .toggle-slider:before {
  transform: translateX(20px);
}

.status-label {
  font-size: 0.875rem;
  font-weight: 500;
}

.status-label.active {
  color: #10b981;
}

.status-label.inactive {
  color: #ef4444;
}

.actions-cell {
  text-align: center;
}

.action-buttons {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
}

.btn-action {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 8px;
  background: transparent;
  transition: all 0.2s ease;
  cursor: pointer;
}

</style>