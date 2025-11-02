<script setup>
import { ref, defineProps, watch } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DeceasedMember from '@/Components/dashboard/admin/archive/DeceasedMember.vue'
import Official from '@/Components/dashboard/admin/archive/Official.vue'

const props = defineProps({
  members: {
    type: Array,
    default: () => []
  },
  deceasedMembers: {
    type: Array,
    default: () => []
  },
  officials: {
    type: Array,
    default: () => []
  }
})
let getMembers = ref([]);
const getDeceasedMembers = ref([]);
let getOfficials = ref([]);

watch(
  () => props.officials,
  (newData) => {
    getOfficials.value = newData;
  },
  {immediate: true}
)

watch(
  () => props.deceasedMembers,
  (newDeceasedMembers) => {
    getDeceasedMembers.value = newDeceasedMembers;
    filterAliveMembers();
  },
  { immediate: true }
);

watch(
  () => props.members,
  (newMembers) => {
    filterAliveMembers(newMembers);
  },
  { immediate: true }
);

function filterAliveMembers(membersList = props.members) {
  if (!membersList) {
    getMembers.value = [];
    return;
  }

  getMembers.value = membersList.filter((m) => {
    const fullName = `${m.first_name} ${m.last_name}`.toLowerCase();
    return !getDeceasedMembers.value.some(
      (d) => d.deceased_name.toLowerCase() === fullName
    );
  });
}


 const deletePermanently = (id) => {
      if (confirm('Are you sure you want to delete this member permanently?')) {
        router.delete(route('archive.deleteMember', {id: id}), {
          onSuccess: () => {
            alert('Member deleted successfully...')
          },
          onError: (err) => console.log('An error occured while deleting data.', err)
        })
      }
    }

const undoMember = (id) => {
  if (confirm('Are you sure you want to undo this member?')) {
    router.post(route('archive.undo', { id: id }), {
      onSuccess: () => {
        alert('Member restored successfully...');
      },
      onError: (err) => console.log('An error occurred while restoring data.', err)
    });
  }
}
</script>

<template>
  <Head title="Registered members" />
    <AdminLayout>
        <div class="main-section bg-light">


     <div class="container-fluid d-flex flex-row justify-content-between align-items-center mb-2">
           <div class="">
                <div>
                     <div>
                       <h5 class="fw-semibold fs-3">Archived</h5>
                    </div>
                </div>
           </div>

           <div>
            <Link :href="route('archive.viewDeceasedMember')" class="btn btn-dark">
            <span class="d-none d-md-none d-lg-inline">View Deceased Member</span>
            <i class="bi bi-eye d-inline d-md-inline d-lg-none"></i>
            </Link>
           </div>
     </div>

       <p class="text-muted ms-2">Members</p>
    <div class="container" v-if="getMembers.length">
    <div class="table-responsive" >
      <table class="table table-bordered align-middle text-center">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th><i class="bi bi-person"></i> NAME</th>
            <th>GENDER</th>
            <th>AGE</th>
            <th>CONTACT NO.</th>
            <th>STREET</th>
            <th>STATUS</th>
            <th>ACTION</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(member, index) in getMembers" :key="index">
            <td>{{ index + 1 }}</td>
            <td>{{ member?.first_name }} {{ member?.middle_name }} {{ member?.last_name }}</td>
            <td>{{ member.gender || 'N/A' }}</td>
            <td>{{ member.age }}</td>
            <td>{{ member.contact_number }}</td>
            <td>{{ member.purok }}</td>
            <td>{{ member.status }}</td>
            <td>
              <Link :href="route('archive.view', {id: member?.id})" class="btn btn-sm btn-outline-dark me-1" title="view info.">
                <i class="bi bi-eye"></i>
              </Link>
              <button class="btn btn-sm btn-outline-dark me-1" @click="deletePermanently(member.id)" title="delete permanently">
                <i class="bi bi-trash"></i>
              </button>

              <button class="btn btn-sm btn-outline-dark" @click="undoMember(member.id)" title="undo delete">
                <i class="bi bi-arrow-counterclockwise"></i>
              </button>

            </td>
          </tr>
        </tbody>
      </table>
    </div>

    </div>
    <div class="container text-center mt-3" v-else>
        <h5 class="text-dark fw-light">No Member's Archive Data.</h5>
    </div>


    <Official :officials="getOfficials" />
  </div>
    </AdminLayout>
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
