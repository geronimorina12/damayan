<template>
  <div class="container mt-4">

    <div v-if="loading" class="text-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-else-if="error" class="alert alert-danger">
      {{ error }}
    </div>

    <div v-else-if="member" class="table-responsive">
      <table class="table table-striped table-bordered shadow-sm">
        <thead class="table-primary">
          <tr>
            <th>Name</th>
            <th>Address</th>
            <th>Contact</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Registration Date</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>{{ member.first_name }} {{ member.middle_name }} {{ member.last_name }}</td>
            <td>{{ member.address }}</td>
            <td>{{ member.contact_number }}</td>
            <td>{{ member.age }}</td>
            <td>{{ member.gender }}</td>
            <td>{{ formatDate(member.created_at) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else class="alert alert-info">
      No newly registered member found.
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const member = ref(null)
const loading = ref(true)
const error = ref(null)

const fetchLatestMember = async () => {
  try {
    const response = await axios.get('/member/new')
    member.value = response.data.data
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to fetch latest member.'
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

onMounted(fetchLatestMember)
</script>

<style scoped>
.table {
  border-radius: 12px;
  overflow: hidden;
}
</style>
