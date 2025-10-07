<script setup>
import { useForm } from '@inertiajs/vue3'
import {Head, Link} from '@inertiajs/vue3'
const form = useForm({
  name: '',
  email: '',
  position: '',
  term_start: '',
  term_end: '',
  status: 1
})

function submit() {
  form.post(route('officials.add'), {
    onSuccess: () => alert("Official created"),
    onError: (err) => console.log('An error occured: ' , err)
  })
}
</script>


<template>
    <Head title="Add Official" />
  <div class="main-container">
    <div class="container border-0 pt-2">
      <div class="card-body px-3 ">

        <form @submit.prevent="submit">
          <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Name</label>
            <input v-model="form.name" type="text" id="name" class="form-control" :class="{ 'is-invalid': form.errors.name }" required>
            <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
          </div>

           <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Email</label>
            <input v-model="form.email" type="email" id="email" class="form-control"  required>
          </div>


          <div class="mb-3">
            <label for="position" class="form-label fw-semibold">Position</label>
            <select name="position" class="form-control" v-model="form.position" required>
                <option value="president">President</option>
                <option value="vise_president">Vise-president</option>
                <option value="secretary">Secretary</option>
                <option value="treasurer">Treasurer</option>
                <option value="auditor">Auditor</option>
                <option value="purok_leader">Purok Leader</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="term_start" class="form-label fw-semibold">Term Start</label>
            <input v-model="form.term_start" type="date" id="term_start" class="form-control" :class="{ 'is-invalid': form.errors.term_start }" required>
            <div v-if="form.errors.term_start" class="invalid-feedback">{{ form.errors.term_start }}</div>
          </div>

          <div class="mb-3">
            <label for="term_end" class="form-label fw-semibold">Term End</label>
            <input v-model="form.term_end" type="date" id="term_end" class="form-control" :class="{ 'is-invalid': form.errors.term_end }" required>
            <div v-if="form.errors.term_end" class="invalid-feedback">{{ form.errors.term_end }}</div>
          </div>

          <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" id="status" v-model="form.status" true-value="1" false-value="0">
            <label class="form-check-label" for="status">Active Status</label>
          </div>

          <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm">
            <i class="bi bi-save me-2"></i> Save Record
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

