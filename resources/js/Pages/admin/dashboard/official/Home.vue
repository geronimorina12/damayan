
<script setup>
import { router, Head } from '@inertiajs/vue3'
import { defineProps, ref, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Add from '@/Components/dashboard/admin/official/Add.vue'

const props = defineProps({
  officials: Array,
})
let getOfficials = ref([]);
watch(
() => props.officials,
(newData) => {
    getOfficials.value =  newData;
},
{immediate: true}
)
const toggleStatus = (official) => {
  router.patch(route('officials.status.toggle', {id: official.id}), {
    status: !official.status,
  })
}

const editOfficial = (official) => {
  router.get(route('officials.edit', {id: official}))
}

const deleteOfficial = (id) => {
  if (confirm('Are you sure you want to delete this official?')) {
    router.delete(route('officials.delete', {id: id}))
  }
}

const addOfficial = () => {
  router.get(route('officials.addOfficialRoute'))
}
const formatDate = (dateString) => {
  const options = { year: "numeric" };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

</script>

<template>
  <Head title="View officials" />
    <div>
        <AdminLayout>
            <div class="container mt-4">
    <h4 class="mb-4">Protect Damayan Officials</h4>
    <table class="table table-bordered table-striped table-responsive" v-if="getOfficials.length > 0">
      <thead class="table-primary">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Position</th>
          <th>Term</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="official in getOfficials" :key="official.id">
          <td>{{ official.id }}</td>
          <td>{{ official.name }}</td>
          <td>{{ official.position }}</td>
          <td>{{ formatDate(official.term_start) }} - {{ formatDate(official.term_end) }}</td>
          <td>
            <div class="form-check form-switch d-flex justify-content-center">
             <input
              type="checkbox"
              :checked="official.status"
              @change="toggleStatus(official)"
              :class="{'form-check-input': true, 'bg-success': official.status, 'bg-danger': !official.status}"
          />
            </div>
          </td>
          <td>
            <button class="btn btn-sm btn-outline-light me-2" @click="editOfficial(official.id)">
              <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-outline-light" @click="deleteOfficial(official.id)">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <div class="container text-center" v-else>
        <img src="../../../../../images/not_found.svg" alt="NO data" class="image-fluid no-data">
        <h5 class="text-dark fw-light">No data for official.</h5>
    </div>

            <div class="container-fluid d-flex justify-content-end">
                 <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addOfficial">
                       Add New Official
                </button>
            </div>
  </div>
        </AdminLayout>


      <!-- Add New Official Modal -->
      <div class="modal fade" id="addOfficial" tabindex="-1" aria-labelledby="addOfficialLabel" aria-hidden="true">
        <div class="modal-dialog" style="min-width: 700px;">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">Add Official</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <Add/>
            </div>
          </div>
        </div>
      </div>
    </div>

</template>

<style scoped>
.no-data{
    width: 200px;
    height: 200px;
    margin: auto;
}
.bi{
  font-size: 1rem;
  color: #333;
}
.bi:hover{
  color: #517e65;
}
</style>