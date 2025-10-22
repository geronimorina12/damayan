<script setup>
import { useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { ref, watch, defineProps } from 'vue'
import { errorMessages } from 'vue/compiler-sfc'


const isCollector = ref(false)
const successMessage = ref('')
let hasError = ref(false);
let errorMessage = ref("");
let hasPresident = ref(false);

const form = useForm({
  first_name: '',
  middle_initial: '',
  last_name: '',
  name: '', // combined before submitting
  email: '',
  password: '',
  position: '',
  term_start: '',
  term_end: '',
  status: 1,
  role: 'collector', // used only if collector
  purok: '1', // used only if collector
})

async function checkHasPresident() {
    try {
        const response = await fetch('/officials/president/has', {
            method: 'GET', 
            headers: {
                'Content-Type': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        const data = await response.json();
        hasPresident.value = data.has_president;
        console.log("has president: ", hasPresident.value)
        return hasPresident;

    } catch (error) {
        console.error("Error fetching president status:", error);
        return false;
    }
}
checkHasPresident();
watch(isCollector, (newVal) => {
  if (newVal) {
    form.term_start = ''
    form.term_end = ''
    form.position = 'Purok Leader'
  } else {
    form.email = ''
    form.password = ''
  }
})
function submit() {
  const middle = form.middle_initial ? ` ${form.middle_initial}.` : ''
  form.name = `${form.first_name}${middle} ${form.last_name}`.trim()

  // If collector toggle ON → send to collector route
  if (isCollector.value) {
    const collectorForm = useForm({
      name: form.name,
      email: form.email,
      password: form.password,
      role: 'collector',
      purok: '1',
    })

    if(form.email == ''){
      hasError.value  = true;
      errorMessage.value = "Please provide a email."
    }else if(form.password == ''){
      hasError.value  = true;
      errorMessage.value = "Please provide a password."
    }
    collectorForm.post(route('reports.storeCollector'), {
      onSuccess: () => {
        successMessage.value = 'Collector added successfully!'
        closeModal()
        form.reset()
        isCollector.value = false
        setTimeout(() => (successMessage.value = ''), 4000)
      },
      onError: (errors) => console.log(errors),
    })
  } else {
    // Otherwise → send to officials route
    form.post(route('officials.add'), {
      onSuccess: () => {
        successMessage.value = 'Official added successfully!'
        closeModal()
        form.reset()
        setTimeout(() => (successMessage.value = ''), 4000)
      },
      onError: (err) => console.log('An error occurred:', err),
    })
  }
}

// Helper to close the modal
function closeModal() {
  const modalEl = document.getElementById('addOfficialModal')
  if (modalEl) {
    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl)
    modal.hide()
  }
}
</script>

<template>
  <Head title="Add Official" />
  <div class="main-container">
    <div class="container border-0 pt-2">
      <div class="card-body px-3">
        <!-- Success Alert -->
        <div
          v-if="successMessage"
          class="alert alert-success alert-dismissible fade show"
          role="alert"
        >
          <i class="bi bi-check-circle-fill me-2"></i> {{ successMessage }}
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
            @click="successMessage = ''"
          ></button>
        </div>

        <div
          v-if="hasError"
          class="alert alert-warning alert-dismissible fade show"
          role="alert"
        >
          <i class="bi bi-check-circle-fill me-2"></i> {{ errorMessage }}
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
            @click="errorMessage = ''"
          ></button>
        </div>

        <form @submit.prevent="submit">
          <!-- Name Fields -->
          <label class="form-label fw-semibold">Full Name</label>
          <div class="row mb-3">
            <div class="col-md-4 mb-2 mb-md-0">
              <input
                v-model="form.first_name"
                type="text"
                class="form-control"
                placeholder="e.g. Juan"
                required
              />
            </div>
            <div class="col-md-4 mb-2 mb-md-0">
              <input
                v-model="form.middle_initial"
                type="text"
                maxlength="1"
                class="form-control"
                placeholder="M.I. (optional)"
              />
            </div>
            <div class="col-md-4">
              <input
                v-model="form.last_name"
                type="text"
                class="form-control"
                placeholder="e.g. Reyes"
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
              :disabled="isCollector"
            >
              <option disabled value="">Select a position</option>
              <option value="president" v-if="!hasPresident">President</option>
              <option value="vice_president">Vice President</option>
              <option value="secretary">Secretary</option>
              <option value="treasurer">Treasurer</option>
              <option value="auditor">Auditor</option>
              <option value="purok_leader">Purok Leader</option>
            </select>
          </div>

          <!-- Collector Toggle -->
          <div class="form-check form-switch mb-4">
            <input
              class="form-check-input"
              type="checkbox"
              id="collector"
              v-model="isCollector"
            />
            <label class="form-check-label" for="collector">
              Register as Collector
            </label>
          </div>

          <!-- Collector Email & Password -->
          <div v-if="isCollector" class="mb-3">
            <label for="email" class="form-label fw-semibold">Collector Email</label>
            <input
              v-model="form.email"
              type="email"
              id="email"
              class="form-control"
              placeholder="Enter collector email"
            />
          </div>

          <div v-if="isCollector" class="mb-3">
            <label for="email" class="form-label fw-semibold">Collector password</label>
            <input
              v-model="form.password"
              type="password"
              id="password"
              class="form-control"
              placeholder="I@mJuan45_"
            />
          </div>

            <div class="mb-3" v-if="!isCollector">
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
          <div v-if="!isCollector" class="mb-3">
            <label for="term_start" class="form-label fw-semibold">Term Start</label>
            <input
              v-model="form.term_start"
              type="date"
              id="term_start"
              class="form-control"
              :class="{ 'is-invalid': form.errors.term_start }"
              required
            />
            <div v-if="form.errors.term_start" class="invalid-feedback">
              {{ form.errors.term_start }}
            </div>
          </div>

          <div v-if="!isCollector" class="mb-3">
            <label for="term_end" class="form-label fw-semibold">Term End</label>
            <input
              v-model="form.term_end"
              type="date"
              id="term_end"
              class="form-control"
              :class="{ 'is-invalid': form.errors.term_end }"
              required
            />
            <div v-if="form.errors.term_end" class="invalid-feedback">
              {{ form.errors.term_end }}
            </div>
          </div>

          <!-- Active Status -->
          <div class="form-check form-switch mb-4">
            <input
              class="form-check-input"
              type="checkbox"
              id="status"
              v-model="form.status"
              true-value="1"
              false-value="0"
              :disabled="isCollector"
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
              <i class="bi bi-save me-2"></i> Save Record
            </span>
            <span v-else>
              <span
                class="spinner-border spinner-border-sm me-2"
                role="status"
                aria-hidden="true"
              ></span>
              Saving...
            </span>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>
