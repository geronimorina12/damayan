<script setup>
import { useForm } from '@inertiajs/vue3'
import { defineProps, ref, watch } from 'vue'

const props = defineProps({
  official: {
    type: Object,
    default: () => ({}),
  },
})

//  Reactive alerts
const successMessage = ref('')
const errorMessage = ref('')
const hasError = ref(false)

// Initialize form
const form = useForm({
  first_name: '',
  middle_initial: '',
  last_name: '',
  email: '',
  position: '',
  term_start: '',
  term_end: '',
  status: 1,
})

//  When modal opens or prop changes, update form fields
watch(
  () => props.official,
  (newOfficial) => {
    if (newOfficial && Object.keys(newOfficial).length) {
      const [firstName, middle, lastName] = splitName(newOfficial.name)
      form.first_name = firstName
      form.middle_initial = middle
      form.last_name = lastName
      form.email = newOfficial.email || ''
      form.position = newOfficial.position || ''
      form.term_start = newOfficial.term_start || ''
      form.term_end = newOfficial.term_end || ''
      form.status = newOfficial.status || 0
    }
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

//  Submit form handler
function submit() {
  const middle = form.middle_initial ? ` ${form.middle_initial}.` : ''
  const name = `${form.first_name}${middle} ${form.last_name}`.trim()
  successMessage.value = ''
  errorMessage.value = ''
  hasError.value = false

  form.put(route('officials.editData', { id: props.official.id }), {
    data: {
      name : name,
      position: form.position,
      term_start: form.term_start,
      term_end: form.term_end,
      status: form.status,
      email: form.email,
    },
    onSuccess: () => {
      successMessage.value = 'Official updated successfully!'
      setTimeout(() => {
        closeModal()
      }, 1000)
    },
    onError: (errors) => {
      hasError.value = true
      // Combine all error messages into one string
      errorMessage.value = Object.values(errors).flat().join(' ')
      console.error(errors)
    },
  })
}

//  Close Bootstrap modal
function closeModal() {
  const modalEl = document.getElementById('editOfficial')
  if (modalEl) {
    const modal = bootstrap.Modal.getInstance(modalEl)
    modal?.hide()
  }
}
</script>

<template>
  <Head title="Edit Official" />

  <div class="main-container">
    <div class="container border-0 pt-2">
      <div class="card-body px-3">

        <!-- ✅ Success Alert -->
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
            <select
              id="position"
              class="form-control"
              v-model="form.position"
              required
            >
              <option disabled value="">Select a position</option>
              <option value="president">President</option>
              <option value="vice_president">Vice President</option>
              <option value="secretary">Secretary</option>
              <option value="treasurer">Treasurer</option>
              <option value="auditor">Auditor</option>
              <option value="purok_leader">Purok Leader</option>
            </select>
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

          <!-- Term Dates -->
          <div class="mb-3">
            <label for="term_start" class="form-label fw-semibold">Term Start</label>
            <input
              v-model="form.term_start"
              type="date"
              id="term_start"
              class="form-control"
              required
            />
          </div>

          <div class="mb-3">
            <label for="term_end" class="form-label fw-semibold">Term End</label>
            <input
              v-model="form.term_end"
              type="date"
              id="term_end"
              class="form-control"
              required
            />
          </div>

          <!-- Status Switch -->
          <div class="form-check form-switch mb-4">
            <input
              class="form-check-input"
              type="checkbox"
              id="status"
              v-model="form.status"
              true-value="1"
              false-value="0"
            />
            <label class="form-check-label" for="status">Active Status</label>
          </div>

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
