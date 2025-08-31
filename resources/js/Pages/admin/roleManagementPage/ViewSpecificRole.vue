<script setup>
import HeaderComponent from '@/Components/dashboard/HeaderComponent.vue';
import { Head, Link } from '@inertiajs/vue3';
import { defineProps, watch, ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  users: {
    type: Array,
    default: () => [],
  },
  role: {
    type: String,
    default: '',
  }
});
let getUsers = ref([]);
watch(
  () => props.users,
  (newUsers) => {
    getUsers.value = newUsers;
    console.log("View specific value" , getUsers.value);
  },
  { immediate: true }
);
const formatDate = (dateString) => {
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<template>
  <Head title="Role Management" />
  <AdminLayout>
    <div class="container mt-4">
      <h2 class="mb-3">Users</h2>
      <div class="table-responsive table-striped" v-if="getUsers.length > 0">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Verified At</th>
              <th>Role</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in getUsers" :key="user.id">
              <td>{{ user.id }}</td>
              <td>{{ user.name }}</td>
              <td>{{ user.email }}</td>
              <td>{{ formatDate(user.email_verified_at) || 'Unverified' }}</td>
              <td>{{ user.role }}</td>
            </tr>
          </tbody>
        </table>
        <div class="container ps-0">
          <Link :href="route('role.index')" class="btn btn-primary">
            Back
          </Link>
        </div>
      </div>
      <div class="container d-flex flex-column align-items-center gap-4" v-else>
        <div>
          <img src="../../../../images/not_found.svg" alt="Not found image" class="img-fluid" style="width: 360px"/>
        </div>
        <div>
          <h5 class="fw-lighter">No data.</h5>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.container {
  max-width: 1000px;
}
</style>
