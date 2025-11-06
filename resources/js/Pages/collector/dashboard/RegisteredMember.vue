<script setup>
import { ref, defineProps, watch, computed, onMounted, nextTick } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import CollectorLayout from '@/Layouts/CollectorLayout.vue'

const props = defineProps({
  members: {
    type: Object,
    default: () => ({ data: [], links: [], prev_page_url: null, next_page_url: null })
  }
})

let getMembers = ref([])
const searchQuery = ref("") //  search input
const paginationContainer = ref(null) //  for responsive scrolling

watch(
  () => props.members,
  async (newMember) => {
    getMembers.value = newMember.data || []

    await nextTick()
    centerActivePagination()
  },
  { immediate: true }
)

//  Filter members
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
      onSuccess: () => console.log('Member trashed')
    })
  }
}

const toggleMemberStatus = (member) => {
  const newStatus = member.status === 'active' ? 'inactive' : 'active'
  router.put(route('toggleMemberStatus', { id: member.id }), { status: newStatus }, {
    onSuccess: () => {
      member.status = newStatus
      console.log(`Member ${member.id} status changed to ${newStatus}`)
    }
  })
}

const goToPage = (url) => {
  if (url) {
    router.visit(url, { preserveState: true })
  }
}

// 📱 Center the active pagination on mobile
const centerActivePagination = () => {
  const container = paginationContainer.value
  if (!container) return

  const activeBtn = container.querySelector('.btn.btn-primary')
  if (activeBtn) {
    const containerRect = container.getBoundingClientRect()
    const activeRect = activeBtn.getBoundingClientRect()
    const scrollOffset = activeRect.left - containerRect.left - containerRect.width / 2 + activeRect.width / 2

    container.scrollTo({
      left: container.scrollLeft + scrollOffset,
      behavior: 'smooth'
    })
  }
}
</script>

<template>
  <Head title="Registered members" />
  <CollectorLayout>
    <div class="main-section bg-light">
      <div>
        <h5 class="ms-3 fw-light">
          <span class="d-none d-sm-none d-lg-inline-block me-2">Registered</span>
          <span>Members</span>
        </h5>
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

      <!-- 📄 Responsive Pagination -->
      <div class="pagination-wrapper mt-3 mb-5 pb-5 pt-3">
        <div class="pagination-controls" ref="paginationContainer">
          <button
            v-for="(link, index) in props.members.links"
            :key="index"
            class="btn pagination-btn"
            :class="link.active ? 'btn-primary' : 'btn-outline-primary'"
            @click="goToPage(link.url)"
            v-html="link.label"
          />
        </div>
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

/* ✅ Responsive pagination fix */
.pagination-wrapper {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.pagination-controls {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  flex-wrap: nowrap;
  min-width: max-content;
  padding: 0.5rem 1rem;
  scroll-behavior: smooth;
}

.pagination-btn {
  white-space: nowrap;
  flex-shrink: 0;
}

/* On smaller screens, align to start for easy scroll */
@media (max-width: 768px) {
  .pagination-controls {
    justify-content: flex-start;
  }
}
</style>
