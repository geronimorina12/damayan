
<script setup>
import { ref, defineProps } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
const props = defineProps({
  deceasedMembers: {
    type: Array,
    default: () => []
  }
});
const getDeceasedMembers = ref(props.deceasedMembers || []);
console.log(getDeceasedMembers);
const formatDate = (dateString) => {
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};
const undo = (id) => {
      if (confirm('Are you sure you want to undo this member?')) {
        router.post(route('deathReport.undo', {id: id}), {
          onSuccess: () => {
            alert('Member restored successfully...')
          },
          onError: (err) => console.log('An error occured while restoring data.', err)
        })
      }
    }
    const deletePermanently = (id) => {
        if (confirm('Are you sure you want to delete this member permanently?')) {
            router.delete(route('deathReport.deletePermanently', {id: id}), {
            onSuccess: () => {
                alert('Member deleted successfully...')
            },
            onError: (err) => console.log('An error occured while deleting data.', err)
            })
        }
        }
</script>

<template>
    <AdminLayout>
  <div>
    <div class="container mt-3">

      <p class="text-muted">Deceased Members</p>
    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;" v-if="getDeceasedMembers.length != 0">

      <table class="table">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th><i class="bi bi-person"></i> NAME</th>
            <th>DECEASED DATE</th>
            <!-- <th>Actions</th> -->
          </tr>
        </thead>
        <tbody>
          <tr v-for="(member, index) in getDeceasedMembers" :key="index">
            <td>{{ index + 1 }}</td>
            <td>{{ member?.deceased_name }}</td>
            <td>{{ formatDate(member?.date_of_death) }}</td>
            <!-- <td>
              <button class="btn btn-sm btn-outline-dark me-1" @click="deletePermanently(member.member_id)" title="delete permanently">
                <i class="bi bi-trash"></i> 
              </button>

              <button class="btn btn-sm btn-outline-dark" @click="undo(member.member_id)" title="undo delete">
                <i class="bi bi-arrow-counterclockwise"></i>
              </button> 
              </td> -->
          </tr>
        </tbody>
      </table>
      </div>

      <div class="container text-center fs-5" v-else>
        No Deceased Members Found.
      </div>

          <Link :href="route('archive.index')" class="btn btn-dark mt-2"
        >
         Go back
        </Link>
    </div>
  </div>
  </AdminLayout>
</template>

<style>
table th {
  background: #D4F3F9 !important;
}
</style>