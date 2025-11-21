<script setup>
import { useForm } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';

const props = defineProps({
  user: {
    type: Object,
    default: () => ({}),
  },
});

const form = useForm({
  name: props.user.name || '',
  email: props.user.email || '',
  role: props.user.role || '',
  password: '',
});

const showSuccessAlert = ref(false);

const submit = () => {
  form.put(route('role.update', { user: props?.user.id }), {
    onSuccess: () => {
      showSuccessAlert.value = true;
      setTimeout(() => (showSuccessAlert.value = false), 3000); // Auto-hide after 3s
    },
    onError: (e) => {
      console.log('Failed to update user. Please check the form for errors.', e);
    },
    preserveScroll: true,
  });
};
</script>

<template>
  <AdminLayout>
    <Head title="Edit User" />

    <div class="container py-5">
      <div class="card rounded-4 p-4 border-0">
        <h4 class="mb-4 text-primary">Edit</h4>

        <!-- Bootstrap Success Alert -->
        <div
          v-if="showSuccessAlert"
          class="alert alert-success alert-dismissible fade show"
          role="alert"
        >
          User updated successfully!
          <button
            type="button"
            class="btn-close"
            @click="showSuccessAlert = false"
            aria-label="Close"
          ></button>
        </div>

        <form @submit.prevent="submit">
          <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              class="form-control"
              :class="{ 'is-invalid': form.errors.name }"
              placeholder="Full name"
            />
            <div class="invalid-feedback">{{ form.errors.name }}</div>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              class="form-control"
              :class="{ 'is-invalid': form.errors.email }"
              placeholder="example@email.com"
            />
            <div class="invalid-feedback">{{ form.errors.email }}</div>
          </div>

          <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select
              id="role"
              v-model="form.role"
              class="form-select"
              :class="{ 'is-invalid': form.errors.role }"
              disabled
            >
              <option value="" disabled>Select Role</option>
              <!-- <option value="admin">Admin</option> -->
              <option value="collector">Collector</option>
            </select>
            <div class="invalid-feedback">{{ form.errors.role }}</div>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">
              New Password <small class="text-muted">(leave blank to keep)</small>
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              class="form-control"
              :class="{ 'is-invalid': form.errors.password }"
              placeholder="••••••••"
            />
            <div class="invalid-feedback">{{ form.errors.password }}</div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <Link :href="route('role.index')" class="btn btn-secondary">Cancel</Link>
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
input,
select {
  border-radius: 0.5rem;
}

.card {
  max-width: 700px;
  margin: auto;
  background-color: #ffffff;
}
</style>
