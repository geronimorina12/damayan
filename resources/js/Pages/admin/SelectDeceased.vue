<script setup>
import { ref, computed } from 'vue'
import { router, Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  members: { type: Array, default: () => [] }
})
const selectedMember = ref(null)
const searchQuery = ref('')
const message = ref('')

const filteredMembers = computed(() => {
  let membersArray = [];

  if (Array.isArray(props.members)) {
    membersArray = props.members;
  } else if (typeof props.members === 'object' && props.members !== null) {
    membersArray = Object.entries(props.members).map(([name, age]) => {
      const nameParts = name.split(' ');
      const firstName = nameParts[0];
      const lastName = nameParts.length > 1 ? nameParts.slice(1).join(' ') : '';
      return {
        id: name,
        first_name: firstName,
        last_name: lastName,
        age: age
      };
    });
  } else {
    console.error("The 'members' prop is neither an array nor a valid object as expected.");
    return [];
  }
  
  if (!searchQuery.value) {
    return membersArray;
  }
  
  const searchLower = searchQuery.value.toLowerCase()

  return membersArray.filter(member => {
    const fullName = `${member.first_name} ${member.last_name}`.toLowerCase()
    return fullName.includes(searchLower)
  })
})

function sendDeathReport() {
  message.value = ''
  if (!selectedMember.value) {
    message.value = 'Please select a deceased member first.'
    return
  }

  router.post(route('smsNotification.send'), {
    type: 'deathReport',
    dead_person_name: `${selectedMember.value.first_name} ${selectedMember.value.last_name}`
  }, {
    onSuccess: () => {
      message.value = 'Death report sent successfully!'
      router.visit(route('smsNotification.smsPage'))
    },
    onError: (errors) => {
      console.error(errors)
      message.value = 'Error sending death report.'
    }
  })
}
</script>

<template>
  <AdminLayout>
    <Head title="Select Deceased Member" />

    <div class="container mt-4">
      <h4 class="fw-bold mb-3">Select Deceased Member (Namatay)</h4>

      <div class="container-fluid d-flex flex-row justify-content-between align-items-center mb-3">
        <div class="">
          <input
            type="text"
            v-model="searchQuery"
            class="form-control w-100"
            placeholder="Search by name..."
          />
        </div>

        <div>
            <button class="btn btn-success mt-3" @click="sendDeathReport">
          Send Death Report SMS
        </button>
        </div>
      </div>

      <div v-if="message" class="alert alert-info" role="alert">
        {{ message }}
      </div>
      
      <div class="table-scroll-container">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Select</th>
                <th>Name</th>
                <th>Age</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="member in filteredMembers" :key="member.id">
                <td>
                  <input type="radio" :value="member" v-model="selectedMember" />
                </td>
                <td>{{ member.first_name + " " + member.last_name }}</td>
                <td>{{ member.age }}</td>
              </tr>
              <tr v-if="filteredMembers.length === 0">
                <td colspan="3" class="text-center">No members found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.table-hover tbody tr:hover {
  background-color: #f1f1f1;
}

.table-responsive {
  overflow-x: auto;
}

.table-scroll-container {
  max-height: 400px;
  overflow-y: auto;
}
</style>
