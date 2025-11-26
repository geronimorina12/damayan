<script setup>
import { ref, defineProps, watch } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  officials: {
    type: Array,
    default: () => []
  }
})

let getOfficials = ref([])
const showModal = ref(false)
const modalTitle = ref('')
const modalMessage = ref('')
const modalType = ref('')
const modalAction = ref(null)
const modalData = ref(null)
const contributions = ref(null)
const totalAmount = ref(0)
const selectedOfficial = ref(null)

const showConfirmation = (title, message, action, data = null) => {
  modalTitle.value = title
  modalMessage.value = message
  modalType.value = 'confirm'
  modalAction.value = action
  modalData.value = data
  showModal.value = true
}

const showAlert = (title, message) => {
  modalTitle.value = title
  modalMessage.value = message
  modalType.value = 'alert'
  showModal.value = true
}

const executeModalAction = () => {
  if (modalAction.value && modalData.value) {
    modalAction.value(modalData.value)
  } else if (modalAction.value) {
    modalAction.value()
  }
  showModal.value = false
}

const closeModal = () => {
  showModal.value = false
  modalAction.value = null
  modalData.value = null
  contributions.value = null
  totalAmount.value = 0
}

watch(
  () => props.officials,
  (newData) => {
    getOfficials.value = newData
  },
  { immediate: true }
)

const deletePermanently = (id) => {
  showConfirmation(
    'Delete Official Permanently',
    'Are you sure you want to delete this official permanently? This action cannot be undone.',
    performDeletePermanently,
    id
  )
}

const performDeletePermanently = (id) => {
  router.delete(route('officialArchive.deleteOfficial', { id: id }), {
    onSuccess: () => {
      showAlert('Success', 'Official deleted successfully...')
    },
    onError: (err) => {
      console.log('An error occurred while deleting data.', err)
      showAlert('Error', 'An error occurred while deleting the official.')
    }
  })
}

const restoreOfficial = (id, position) => {
  showConfirmation(
    'Restore Official',
    'Are you sure you want to restore this official?',
    performRestoreOfficial,
    {id, position}
  )
}

const performRestoreOfficial = (data) => {
      const { id, position } = data
  if(position !== 'collector'){
    router.post(route('officialArchived.restoreOfficial', { id: id }), {
    onSuccess: () => {
      showAlert('Success', 'Official restored successfully...')
    },
    onError: (err) => {
      console.log('An error occurred while restoring data.', err)
      showAlert('Error', 'An error occurred while restoring the official.')
    }
  })
  }else{
    router.post(route('officialArchived.collector.restore', { id: id }), {
    onSuccess: () => {
      showAlert('Success', 'Official restored successfully...')
    },
    onError: (err) => {
      console.log('An error occurred while restoring data.', err)
      showAlert('Error', 'An error occurred while restoring the official.')
    }
  })
  }
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  const parsedDate = new Date(date)
  if (isNaN(parsedDate)) return 'N/A'
  const options = { year: 'numeric' }
  return parsedDate.toLocaleDateString(undefined, options)
}

const showTransactionInfo = async (official) => {
  selectedOfficial.value = official
  await getContributionsList(official.id)
  modalTitle.value = `Transaction Info - ${official.name}`
  modalType.value = 'transaction'
  showModal.value = true
}

const getContributionsList = async (id) => {
  console.log('Fetching contributions for official ID:', id)
  try {
    const response = await axios.get(`/contribution/list/${id}`)
    contributions.value = Array.isArray(response.data)
      ? response.data
      : [response.data]
    totalAmount.value = contributions.value.reduce((sum, c) => sum + Number(c.amount || 0), 0)
    console.log('Contributions fetched:', contributions.value || 'No data')
  } catch (error) {
    console.error('Error fetching contributions:', error)
    showAlert('Error', 'Unable to fetch contribution data.')
  }
}
</script>

<template>
  <div class="main-section bg-light">
    <div
      class="container-fluid d-flex flex-row justify-content-between align-items-center mb-2 flex-wrap"
    >
      <div class="d-flex flex-row justify-content-between align-items-center w-100">
        <p class="text-muted mb-0">Officials</p>
      </div>
    </div>

    <div class="table-responsive" v-if="getOfficials.length > 0">
      <table class="table table-bordered align-middle text-center">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>NAME</th>
            <th>POSITION</th>
            <th>TERM</th>
            <th>STATUS</th>
            <th>ACTION</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(official, index) in getOfficials" :key="index">
            <td>{{ index + 1 }}</td>
            <td>{{ official?.name }}</td>
            <td>{{ official.position ? official.position : official.role || 'N/A' }}</td>
            <td>
              {{ formatDate(official.term_start) }} -
              {{ formatDate(official.term_end) }}
            </td>
            <td>
              <div
                class="form-check form-switch d-inline-flex justify-content-center"
              >
                <input
                  class="form-check-input"
                  type="checkbox"
                  :checked="official.status === 1"
                  title="can't modify"
                  disabled
                />
              </div>
            </td>
            <td>
              <div
                class="d-flex flex-row justify-content-center align-items-center action-buttons"
              >
                <button
                  class="btn btn-sm btn-outline-dark me-2"
                  @click="deletePermanently(official.id)"
                  title="delete permanently"
                >
                  <i class="bi bi-trash"></i>
                </button>

                <button
                  class="btn btn-sm btn-outline-dark me-2"
                  @click="restoreOfficial(official.id , official.position || official.role)"
                  title="restore"
                >
                  <i class="bi bi-arrow-clockwise"></i>
                </button>

                <button
                  class="btn btn-sm btn-outline-dark"
                  :disabled="official.role !== 'collector'"
                  @click="official.role === 'collector' ? showTransactionInfo(official) : null"
                  title="view transaction info"
                >
                  <i class="bi bi-clock-history"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="container text-center mt-3" v-else>
      <h5 class="text-dark fw-light">No Official's Archive Data.</h5>
    </div>

    <!-- MODAL -->
    <div
      v-if="showModal"
      class="modal fade show d-block"
      tabindex="-1"
      role="dialog"
      style="background-color: rgba(0,0,0,0.5)"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ modalTitle }}</h5>
            <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
          </div>

          <!-- Transaction Info -->
          <div v-if="modalType === 'transaction'" class="modal-body">
            <div v-if="contributions && contributions.length > 0">

              <table class="table table-bordered text-center mt-3">
                <div v-if="contributions && contributions.length">
               <span class="text-success fw-bold">{{ selectedOfficial?.name }}</span>
                   has diligently served as a collector, successfully collecting a total amount 
                   of  <span class="text-success fw-bold">₱{{ totalAmount }}</span> 
                    during his/her service.
              </div>
              </table>
            </div>
            <div v-else>
              <p>No contribution data found.</p>
            </div>
          </div>

          <!-- Alert / Confirm Modal -->
          <div v-else class="modal-body">
            <p>{{ modalMessage }}</p>
          </div>

          <div class="modal-footer">
            <button
              v-if="modalType === 'confirm'"
              type="button"
              class="btn btn-primary"
              @click="executeModalAction"
            >
              Confirm
            </button>
            <button
              v-if="modalType === 'alert'"
              type="button"
              class="btn btn-primary"
              @click="closeModal"
            >
              OK
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.main-section {
  width: 100%;
  height: 30% !important;
}

.table th,
.table td {
  vertical-align: middle;
  white-space: nowrap;
}

.table-responsive {
  overflow-x: auto;
}

.action-buttons {
  flex-wrap: nowrap !important;
  gap: 0.25rem;
}
table th {
  background: #D4F3F9 !important;
}
@media (max-width: 768px) {
  .table th,
  .table td {
    font-size: 0.85rem;
  }

  .action-buttons .btn {
    padding: 0.25rem 0.4rem;
  }
}
</style>
