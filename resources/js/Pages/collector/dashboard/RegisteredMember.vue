<script setup>
import { ref, defineProps, watch, computed, nextTick } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import axios from 'axios'
import CollectorLayout from '@/Layouts/CollectorLayout.vue'

const props = defineProps({
  members: {
    type: Object,
    default: () => ({ data: [], links: [], prev_page_url: null, next_page_url: null })
  }
})

let getMembers = ref([])
const searchQuery = ref("") // search input
const paginationContainer = ref(null)
const searching = ref(false)
const searchError = ref("")

//  Initialize default members
watch(
  () => props.members,
  async (newMember) => {
    getMembers.value = newMember.data || []
    await nextTick()
    centerActivePagination()
  },
  { immediate: true }
)

//  Debounced search logic
let searchTimer = null
const DEBOUNCE_MS = 400

watch(searchQuery, (newQuery) => {
  if (searchTimer) clearTimeout(searchTimer)
  searchTimer = setTimeout(() => performSearch(newQuery.trim()), DEBOUNCE_MS)
})

async function performSearch(query) {
  if (!query) {
    getMembers.value = props.members.data
    console.log('Search cleared — showing default members:', getMembers.value)
    searching.value = false
    return
  }

  searching.value = true
  searchError.value = ""
  console.log(`🔍 Searching for: "${query}"`)

  try {
    const response = await axios.get(route('members.search'), {
      params: { query }
    })

    console.log(" Raw backend response:", response.data)

    let membersPayload = []
    const d = response.data || {}

    // normalize structure
    if (Array.isArray(d.members)) {
      membersPayload = d.members
    } else if (d.members && Array.isArray(d.members.data)) {
      membersPayload = d.members.data
    } else if (d.member && typeof d.member === 'object') {
      membersPayload = [d.member]
    } else if (Array.isArray(d.data)) {
      membersPayload = d.data
    } else {
      const possible = Object.values(d).find(v => Array.isArray(v) && v[0]?.id)
      if (possible) membersPayload = possible
    }

    console.log("🧾 Normalized member data:", membersPayload)

    getMembers.value = Array.isArray(membersPayload) ? membersPayload : []
  } catch (error) {
    console.error("❌ Search error:", error)
    searchError.value = "Error fetching search results."
  } finally {
    searching.value = false
  }
}

//  Computed filtered members
const filteredMembers = computed(() => {
  if (searching.value) return [] // don't show while searching
  return getMembers.value || []
})

// 🗑️ Trash member
const trashMember = (id) => {
  if (confirm('Are you sure you want to trash this member?')) {
    router.delete(route('deleteMember', { id }), {
      onSuccess: () => console.log('Member trashed')
    })
  }
}

// 🔄 Toggle active/inactive
const toggleMemberStatus = (member) => {
  const newStatus = member.status === 'active' ? 'inactive' : 'active'
  router.put(route('toggleMemberStatus', { id: member.id }), { status: newStatus }, {
    onSuccess: () => {
      member.status = newStatus
      console.log(`Member ${member.id} status changed to ${newStatus}`)
    }
  })
}

// 🔢 Pagination
const goToPage = (url) => {
  if (url) router.visit(url, { preserveState: true })
}

// 📱 Center active pagination
const centerActivePagination = () => {
  const container = paginationContainer.value
  if (!container) return

  const activeBtn = container.querySelector('.btn.btn-primary')
  if (activeBtn) {
    const containerRect = container.getBoundingClientRect()
    const activeRect = activeBtn.getBoundingClientRect()
    const scrollOffset =
      activeRect.left - containerRect.left - containerRect.width / 2 + activeRect.width / 2

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
          class="form-control w-50" 
          placeholder="Search members by name, contact, or purok..." 
          v-model="searchQuery"
        />
      </div>

      <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
          <thead class="table-light">
            <tr>
              <th><i class="bi bi-person"></i> NAME</th>
              <th>AGE</th>
              <th>CONTACT NO.</th>
              <th>PUROK</th>
              <th>ACTION</th>
            </tr>
          </thead>
          <tbody>
            <!--  Loading State -->
            <tr v-if="searching">
              <td colspan="6" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </td>
            </tr>

            <!--  Error State -->
            <tr v-if="searchError && !searching">
              <td colspan="6" class="text-center text-danger">{{ searchError }}</td>
</tr>
            <tr v-for="(member, index) in filteredMembers" :key="index">
              <td>{{ member?.first_name }} {{ member?.middle_name }} {{ member?.last_name }}</td>
              <td>{{ member.age }}</td>
              <td>{{ member.contact_number }}</td>
              <td class="purok">{{ member.purok }}</td>
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

/*  Responsive pagination fix */
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
.purok{
  text-align: left;
  padding-left: 1rem;
}
/* On smaller screens, align to start for easy scroll */
@media (max-width: 768px) {
  .pagination-controls {
    justify-content: flex-start;
  }
}
</style>
