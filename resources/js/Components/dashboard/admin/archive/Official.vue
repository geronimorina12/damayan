<script setup>
import { ref, defineProps, watch } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  officials: {
    type: Array,
    default: () => []
  }
})
let getOfficials = ref([]);
watch(
  () => props.officials,
  (newData) => {
    getOfficials.value = newData;
  },
  {immediate: true}
)

 const deletePermanently = (id) => {
      if (confirm('Are you sure you want to delete this member permanently?')) {
        router.delete(route('officialArchive.deleteOfficial', {id: id}), {
          onSuccess: () => {
            alert('Member deleted successfully...')
          },
          onError: (err) => console.log('An error occured while deleting data.', err)
        })
      }
    }
const formatDate = (date) => {
  const option = {year: 'numeric'}
  return new Date(date).toLocaleDateString(undefined, option);
}

const restoreOfficial = (id) => {
  if (confirm('Are you sure you want to restore this official?')) {
    router.post(route('officialArchived.restoreOfficial', {id: id}), {
      onSuccess: () => {
        alert('Official restored successfully...')
      },
      onError: (err) => console.log('An error occured while restoring data.', err)
    })
  }
}
</script>

<template>
        <div class="main-section bg-light">


     <div class="container-fluid d-flex flex-row justify-content-between align-items-center mb-2">
           <div class="container-fluid d-flex flex-row justify-content-between px-0 align-items-center">
                <div>
                     <div>
                       <p class="text-muted">Officials</p>
                    </div>
                </div>
           </div>
     </div>

    <div class="table-responsive" v-if="getOfficials.length > 0">
      <table class="table table-bordered align-middle text-center">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th><i class="bi bi-person"></i> NAME</th>
            <th>Position</th>
            <th>Term</th>
            <th>STATUS</th>
            <th>ACTION</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(official, index) in getOfficials" :key="index">
            <td>{{ index + 1 }}</td>
            <td>{{ official?.name }}</td>
            <td>{{ official.position || 'N/A' }}</td>
            <td>{{ formatDate(official.term_start) }} - {{ formatDate(official.term_end) }}</td>
            <td>
              <div class="form-check form-switch d-inline-flex justify-content-center">
                <input class="form-check-input" type="checkbox"
              :checked="official.status === 1"
              title="can't modify"
              disabled
              />

              </div>
            </td>
            <td>
              <!-- <Link :href="route('officialArchive.view', {id: official?.id})" class="btn btn-sm btn-outline-dark me-1" title="view info.">
                <i class="bi bi-eye"></i>
              </Link> -->
              <button class="btn btn-sm btn-outline-dark me-2" @click="deletePermanently(official.id)" title="delete permanently">
                <i class="bi bi-trash"></i>
              </button>

              <button class="btn btn-sm btn-outline-dark" @click="restoreOfficial(official.id)" title="restore">
                <i class="bi bi-arrow-clockwise"></i>
              </button>

            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="container text-center mt-3" v-else>
        <h5 class="text-dark fw-light">No Official's Archive Data.</h5>
    </div>
  </div>
</template>

<style scoped>
.main-section {
  width: 100%;
  height: 100%;
  overflow-x: hidden;
  overflow-y: scroll;
}

.table th,
.table td {
  vertical-align: middle;
}
</style>
