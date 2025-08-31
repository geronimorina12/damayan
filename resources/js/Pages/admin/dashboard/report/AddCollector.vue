<script setup>
import { ref } from 'vue'
import { useForm, Link, router } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: '',
  password: '',
  role: 'collector',
  purok: '1'
})

const puroks = ['1', '2', '3', '4']

const submitForm = () => {
 form.post(route('reports.storeCollector'), {
    onSuccess: () => {
        form.reset()
        alert('Collector added successfully!')
    },
    onError: (errors) => console.log(errors)
})

}
</script>

<template>
  <div class="container mt-5">
    <h3 class="mb-4">Add Collector</h3>

    <form @submit.prevent="submitForm" class="bg-white p-4 rounded shadow-sm">
      <!-- Name -->
      <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input 
          type="text" 
          id="name" 
          v-model="form.name" 
          class="form-control" 
          :class="{ 'is-invalid': form.errors.name }"
        >
        <div class="invalid-feedback">{{ form.errors.name }}</div>
      </div>

      <!-- Email -->
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input 
          type="email" 
          id="email" 
          v-model="form.email" 
          class="form-control"
          :class="{ 'is-invalid': form.errors.email }"
        >
        <div class="invalid-feedback">{{ form.errors.email }}</div>
      </div>

      <!-- Password -->
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input 
          type="password" 
          id="password" 
          v-model="form.password" 
          class="form-control"
          :class="{ 'is-invalid': form.errors.password }"
        >
        <div class="invalid-feedback">{{ form.errors.password }}</div>
      </div>

      <!-- Role -->
      <div class="mb-3">
        <label for="role" class="form-label">Role</label>
        <input 
          type="text" 
          id="role" 
          v-model="form.role" 
          class="form-control"
          readonly
        >
      </div>

      <!-- Purok -->
      <div class="mb-3">
        <label for="purok" class="form-label">Purok</label>
        <select 
          id="purok" 
          v-model="form.purok" 
          class="form-select"
          :class="{ 'is-invalid': form.errors.purok }"
        >
          <option disabled value="">Select Purok</option>
          <option v-for="p in puroks" :key="p" :value="p">{{ p }}</option>
        </select>
        <div class="invalid-feedback">{{ form.errors.purok }}</div>
      </div>

     <div class="container d-flex flex-row gap-3 align-items-center">
         <!-- Submit -->
      <button type="submit" class="btn btn-primary" :disabled="form.processing">
        <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
        Add Collector
      </button>

      <Link  class="btn btn-dark" :href="route('reports.index')" type="button">Back</Link>
     </div>
    </form>
  </div>
</template>

<style scoped>
.container {
  max-width: 600px;
}
</style>
