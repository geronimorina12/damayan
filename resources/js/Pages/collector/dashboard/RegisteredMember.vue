<script setup>
import { ref, defineProps, watch, computed } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import CollectorLayout from '@/Layouts/CollectorLayout.vue'

const props = defineProps({
  members: {
    type: Object, // must be paginated response
    default: () => ({ data: [], links: [], prev_page_url: null, next_page_url: null })
  }
})

let getMembers = ref([])
const searchQuery = ref("") // 🔍 search input

watch(
  () => props.members,
  (newMember) => {
    getMembers.value = newMember.data || []
  },
  { immediate: true }
)

// 🔍 Filter members
const filteredMembers = computed(() => {
  if (!searchQuery.value) return getMembers.value
  return getMembers.value.filter((member) => {
    const fullName = `${member.first_name} ${member.middle_name || ''} ${member.last_name}`.toLowerCase()
    return (
      fullName.includes(searchQuery.value.toLowerCase()) ||
      (member.contact_number || '').toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      (member.purok || '').toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  })
})

const trashMember = (id) => {
  if (confirm('Are you sure you want to trash this member?')) {
    router.delete(route('deleteMember', { id }), {
      onSuccess: () => {
        console.log('Member trashed')
      },
    })
  }
}

const toggleMemberStatus = (member) => {
  const newStatus = member.status === 'active' ? 'inactive' : 'active'
  router.put(route('toggleMemberStatus', { id: member.id }), {
    status: newStatus
  }, {
    onSuccess: () => {
      member.status = newStatus
      console.log(`Member ${member.id} status changed to ${newStatus}`)
    }
  })
}

// 📄 Pagination navigation
const goToPage = (url) => {
  if (url) {
    router.get(url, {}, { preserveScroll: true, preserveState: true })
  }
}
</script>

<template>
  <Head title="Registered members" />
  <CollectorLayout>
    <div class="main-section bg-light">
      <div>
        <h5 class="mb-3 ms-3 fw-light">Registered Members</h5>
      </div>

      <!-- 🔍 Search -->
      <div class="mb-3 px-3">
        <input 
          type="text" 
          class="form-control" 
          placeholder="Search members by name, contact, or purok..." 
          v-model="searchQuery"
        />
      </div>

      <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th><i class="bi bi-person"></i> NAME</th>
              <th>AGE</th>
              <th>CONTACT NO.</th>
              <th>PUROK</th>
              <th>ACTION</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(member, index) in filteredMembers" :key="index">
              <td>{{ member.id }}</td>
              <td>{{ member?.first_name }} {{ member?.middle_name }} {{ member?.last_name }}</td>
              <td>{{ member.age }}</td>
              <td>{{ member.contact_number }}</td>
              <td>{{ member.purok }}</td>
              <td>
                <Link 
                  :href="route('viewMemberInfo', {id: member?.id})" 
                  class="btn btn-sm btn-outline-dark me-1 border-0"
                >
                  <i class="bi bi-eye fs-5"></i>
                </Link>
              </td>
            </tr>
            <tr v-if="filteredMembers.length === 0">
              <td colspan="6" class="text-center text-muted">No members found</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- 📄 Pagination -->
      <div class="pagination-controls d-flex justify-content-center mt-3 mb-5 pb-5 pt-3">
        <button
          v-for="(link, index) in props.members.links"
          :key="index"
          class="btn"
          :class="link.active ? 'btn-primary' : 'btn-outline-primary'"
          @click="goToPage(link.url)"
          v-html="link.label"
        />
      </div>
    </div>
  </CollectorLayout>
</template>

<style scoped>
.main-section {
  width: 100%;
  height: 100%;
  overflow-x: hidden;
  overflow-y: auto;
}

.table th,
.table td {
  vertical-align: middle;
}
</style>
