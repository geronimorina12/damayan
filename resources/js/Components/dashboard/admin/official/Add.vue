<script setup>
import { useForm, Head } from '@inertiajs/vue3'
import { ref, watch, computed, defineModel } from 'vue'
import * as bootstrap from 'bootstrap'

const isCollector = ref(false)
const successMessage = ref('')
let hasError = ref(false)
let errorMessage = ref('')
let hasPresident = ref(false)
let close = defineModel('closeModal')
const collectors = ref(0);
const parentMessage = defineModel('parentMessage')

const form = useForm({
  first_name: '',
  middle_initial: '',
  last_name: '',
  name: '', // combined before submitting
  email: '',
  password: '',
  re_password: '', // new field for re-enter password
  position: '',
  term_start: '',
  term_end: '',
  status: 1,
  role: 'collector',
  purok: '',
})

// Check if president already exists
async function checkHasPresident() {
  try {
    const response = await fetch('/officials/president/has', {
      method: 'GET',
      headers: { 'Content-Type': 'application/json' },
    })
    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`)
    const data = await response.json()
    hasPresident.value = data.has_president
  } catch (error) {
    console.error('Error fetching president status:', error)
  }
}
async function countCollectors() {
  try {
    const response = await fetch('/collectors/count', {
      method: 'GET',
      headers: { 'Content-Type': 'application/json' },
    })
    const data = await response.json()
    collectors.value = data.collectors
  } catch (error) {
    console.error('Error fetching collectos status:', error)
  }
}
checkHasPresident()
countCollectors()
// Toggle collector based on position
watch(
  () => form.position,
  (newVal) => {
    isCollector.value = newVal === 'purok_leader'
  },
  { immediate: true }
)

// When manually toggled as collector
watch(isCollector, (newVal) => {
  if (newVal) {
    form.term_start = ''
    form.term_end = ''
    form.position = 'purok_leader'
  } else {
    if (form.position === 'purok_leader') form.position = ''
    form.email = ''
    form.password = ''
    form.re_password = ''
  }
})

//  Computed property for password match validation
const passwordsMatch = computed(() => form.password === form.re_password)

// Submit form
function submit() {
  if (!passwordsMatch.value && isCollector.value) {
    hasError.value = true
    errorMessage.value = "Passwords do not match."
    return
  }

  const middle = form.middle_initial ? ` ${form.middle_initial}.` : ''
  form.name = `${form.first_name}${middle} ${form.last_name}`.trim()

  if (isCollector.value) {
    if (form.email === '') {
      hasError.value = true
      errorMessage.value = 'Please provide an email.'
      return
    } else if (form.password === '') {
      hasError.value = true
      errorMessage.value = 'Please provide a password.'
      return
    }

    const collectorForm = useForm({
      name: form.name,
      email: form.email,
      password: form.password,
      role: 'collector',
      purok: form.purok,
    })

    collectorForm.post(route('reports.storeCollector'), {
      onSuccess: () => {
        successMessage.value = 'Collector added successfully!'
        closeModal()
        form.reset()
        isCollector.value = false
        setTimeout(() => (successMessage.value = ''), 4000)
        parentMessage.value = 'Collector added successfully!'
      },
      onError: (errors) => console.log(errors),
    })
  } else {
    // Officials submission
    form.post(route('officials.add'), {
      onSuccess: () => {
        successMessage.value = 'Official added successfully!'
        closeModal()
        form.reset()
        setTimeout(() => (successMessage.value = ''), 4000)
        parentMessage.value = 'Official added successfully!'
      },
      onError: (err) => console.log('An error occurred:', err),
    })
  }
}

// Close modal helper
function closeModal() {
  const modalEl = document.getElementById('addOfficial')
  if (modalEl) {
    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl)
    modal.hide()

    // Remove lingering backdrop and body lock after animation
    setTimeout(() => {
      const backdrop = document.querySelector('.modal-backdrop')
      if (backdrop) backdrop.remove()
      document.body.classList.remove('modal-open')
      document.body.style.removeProperty('padding-right')
    }, 300)
  }
}

</script>

<template>
  <Head title="Add Official" />
  <div class="main-container">
    <div class="container border-0 pt-2">
      <div class="card-body px-3">
        <!-- Success Alert -->
        <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i> {{ successMessage }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" @click="successMessage = ''"></button>
        </div>

        <!-- Error Alert -->
        <div v-if="hasError" class="alert alert-warning alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-circle-fill me-2"></i> {{ errorMessage }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" @click="errorMessage = ''"></button>
        </div>

        <form @submit.prevent="submit">
          <!-- Full Name -->
          <label class="form-label fw-semibold">Full Name</label>
          <div class="row mb-3">
            <div class="col-md-4 mb-2 mb-md-0">
              <input v-model="form.first_name" type="text" class="form-control" placeholder="e.g. Juan" required />
            </div>
            <div class="col-md-4 mb-2 mb-md-0">
              <input v-model="form.middle_initial" type="text" maxlength="1" class="form-control" placeholder="M.I. (optional)" />
            </div>
            <div class="col-md-4">
              <input v-model="form.last_name" type="text" class="form-control" placeholder="e.g. Reyes" required />
            </div>
          </div>

          <!-- Position -->
          <div class="mb-3">
            <label for="position" class="form-label fw-semibold">Position</label>
            <select id="position" class="form-control" v-model="form.position" required>
              <option disabled value="">Select a position</option>
              <option value="president" v-if="!hasPresident">President</option>
              <option value="vice_president">Vice President</option>
              <option value="secretary">Secretary</option>
              <option value="treasurer">Treasurer</option>
              <option value="auditor">Auditor</option>
              <option value="purok_leader" v-if="!collectors > 4">Purok Leader</option>
            </select>
          </div>

          <!-- Collector Toggle -->
          <div class="mb-4 d-flex align-items-center justify-content-start">
              <div class="collector-toggle">
                    <label class="toggle-switch">
                      <input
                        type="checkbox"
                        :checked="isCollectorClicked === isCollector"
                        v-model="isCollector"
                        :disabled="collectors > 4"
                      >
                      <span class="toggle-slider"></span>
                    </label>
                    <span class="status-label" :class="isCollector === 'active' ? 'active' : 'inactive'">
                    </span>
                  </div>
                    <div>
                       <label class="form-check-label" for="collector">Register as Collector
                        <p class="text-danger">Purok leader/ Collector limit has been reached.</p>
                       </label>
                    </div>
          </div>

          <!-- Collector Section -->
          <div v-if="isCollector">
            <div class="mb-3">
              <label for="email" class="form-label fw-semibold">Email</label>
              <input v-model="form.email" type="email" id="email" class="form-control" placeholder="juan45@gmail.com" />
            </div>

            <div class="mb-3">
              <label for="password" class="form-label fw-semibold">Password</label>
              <input v-model="form.password" type="password" id="password" class="form-control" placeholder="I@mJuan45_" />
            </div>

            <div class="mb-3">
              <label for="re_password" class="form-label fw-semibold">Re-enter Password</label>
              <input v-model="form.re_password" type="password" id="re_password" class="form-control" placeholder="Re-enter password" />
              <small v-if="!passwordsMatch" class="text-danger">Passwords do not match</small>
            </div>

            <div class="mb-3">
              <label for="purok" class="form-label fw-semibold">Purok</label>
              <select class="form-control" v-model="form.purok">
                <option value="1">Purok 1</option>
                <option value="2">Purok 2</option>
                <option value="3">Purok 3</option>
                <option value="4">Purok 4</option>
              </select>
            </div>
          </div>

          <!-- Officials Section -->
          <div v-else>
            <div class="mb-3">
              <label for="email" class="form-label fw-semibold">Email</label>
              <input v-model="form.email" type="email" id="email" class="form-control" placeholder="juan45@gmail.com" />
            </div>

            <div class="d-flex justify-content-between gap-2">
              <div class="w-50">
                <label for="term_start" class="form-label fw-semibold">Term Start</label>
                <input v-model="form.term_start" type="date" id="term_start" class="form-control" required />
              </div>

              <div class="w-50">
                <label for="term_end" class="form-label fw-semibold">Term End</label>
                <input v-model="form.term_end" type="date" id="term_end" class="form-control" required />
              </div>
            </div>
          </div>

          <!-- Active Status -->
          <div class="d-flex justify-content-start align-items-center  my-4">
             <div class="collector-toggle">
                    <label class="toggle-switch">
                      <input
                        type="checkbox"
                        :checked="isCollectorClicked === form.status"
                        id="status" v-model="form.status" true-value="1" false-value="0" 
                      >
                      <span class="toggle-slider"></span>
                    </label>
                    <span class="status-label" :class="isCollector === 'active' ? 'active' : 'inactive'">
                    </span>
                  </div>

                  <div>
                    <label class="form-check-label" for="status">Active Status</label>
                  </div>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm" :disabled="form.processing || (isCollector && !passwordsMatch)">
            <span v-if="!form.processing"><i class="bi bi-save me-2"></i> Save Record</span>
            <span v-else>
              <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
              Saving...
            </span>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
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

.collector-cell {
  text-align: center;
}

.collector-toggle {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
}
.collector-label {
  font-size: 0.875rem;
  font-weight: 500;
}

.collector-label.active {
  color: #10b981;
}

.collector-label.inactive {
  color: #ef4444;
}

@media (max-width: 768px) {
  .collector-toggle {
    flex-direction: column;
    gap: 0.5rem;
  }
}
@media (max-width: 576px) {
  .collector-toggle { flex-direction: column; gap: 0.3rem; }
}
</style>