<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  official: {
    type: Object,
    default: () => ({}),
  },
});
const formatDate = (dateString) => {
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<template>
  <AdminLayout>
    <Head title="View Official" />

    <div class="container py-5">
      <div class="card rounded-4 p-4 border-0">
        <h4 class="mb-4 text-primary">Official Details</h4>

        <!-- NAME -->
        <div class="mb-3">
          <label class="form-label fw-bold">Name</label>
          <p class="form-control-plaintext">{{ props.official.name }}</p>
        </div>

        <!-- EMAIL -->
        <div class="mb-3">
          <label class="form-label fw-bold">Email</label>
          <p class="form-control-plaintext">{{ props.official.email }}</p>
        </div>

        <!-- POSITION -->
        <div class="mb-3">
          <label class="form-label fw-bold">Position</label>
          <p class="form-control-plaintext text-capitalize">{{ props.official.position }}</p>
        </div>

        <!-- CREATED AT -->
        <div class="mb-3">
          <label class="form-label fw-bold">Created At</label>
          <p class="form-control-plaintext">{{ formatDate(props.official.created_at) }}</p>
        </div>

        <!-- BUTTON -->
        <div class="d-flex justify-content-end mt-4">
          <Link :href="route('role.index')" class="btn btn-secondary">
            Back
          </Link>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.card {
  max-width: 700px;
  margin: auto;
  background-color: #ffffff;
}

.form-control-plaintext {
  padding: 0.45rem 0;
  margin-bottom: 0;
  background: #f8f9fa;
  border-radius: 0.5rem;
  padding-left: 12px;
}
</style>
