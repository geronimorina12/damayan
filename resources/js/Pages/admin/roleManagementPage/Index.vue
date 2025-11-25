<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { defineProps, ref, watch, computed } from "vue";
import RoleAndPermissionTable from "@/Components/dashboard/role/RoleAndPermissionTable.vue";

const props = defineProps({
  users: {
    type: Array,
    default: () => [],
  },
  admins: {
    type: Array,
    default: () => [],
  },
});

let getUsers = ref([]);
let getAdmins = ref([]);
let userIdClicked = ref(null);
let userRole = ref("");

// Search states
const searchQuery = ref("");
const searching = ref(false);
const searchError = ref("");

// Initialize data
watch(
  () => props.admins,
  (newAdmins) => {
    getAdmins.value = newAdmins;
  },
  { immediate: true }
);

watch(
  () => props.users,
  (newUsers) => {
    getUsers.value = [...newUsers, ...getAdmins.value];
  },
  { immediate: true }
);

// Computed for filtered users
const filteredUsers = computed(() => {
  const query = searchQuery.value.trim().toLowerCase();
  if (!query) return getUsers.value;

  return getUsers.value.filter((user) => {
    const name = user.name?.toLowerCase() || "";
    const email = user.email?.toLowerCase() || "";
    const role = user.role?.toLowerCase() || "";
    const position = user.position?.toLowerCase() || "";
    return (
      name.includes(query) ||
      email.includes(query) ||
      role.includes(query) ||
      position.includes(query)
    );
  });
});

// Watch search input to simulate loading spinner
let typingTimeout;
watch(searchQuery, () => {
  searching.value = true;
  clearTimeout(typingTimeout);
  typingTimeout = setTimeout(() => {
    searching.value = false;
  }, 400);
});

// User Actions
const action1Func = (userId, role) => {
  userIdClicked.value = userId;
  userRole.value = role;
};

const editFunc = () => {
  if (userRole.value === "admin" || userRole.value === "collector") {
    router.get(route("role.edit", { user: userIdClicked.value }));
  } else {
    router.get(route("role.official.edit", { official: userIdClicked.value }));
  }
};
const viewFunc = () => {
  if (userRole.value === "admin" || userRole.value === "collector") {
    router.get(route("role.viewUser", { user: userIdClicked.value }));
  } else {
    router.get(route("role.official.view", { official: userIdClicked.value }));
  }
};
const deleteFunc = () => {
  if (confirm("Are you sure you want to delete this user?")) {
    router.delete(route("role.deleteUser", { user: userIdClicked.value }), {
      onSuccess: () => {
        alert("User deleted successfully.");
      },
      onError: (e) => {
        console.error("Failed to delete user.", e);
      },
    });
  }
};

// Role access mapping
const getUserAcess = (role, position) => {
  if (role === "collector" || position === "collector") return "Status updates";
  if (role === "admin" || position === "president") return "Full modules";
  if (role === "secretary" || position === "secretary") return "Records and Reports";
  if (role === "vise_president" || position === "vise_president") return "Review and Approval";
  return "N/A";
};
const formatDate = (dateString) => {
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<template>
  <div>
    <Head title="Role Management" />
    <AdminLayout>
      <div class="container mt-4 role-container">
        <h5 class="fw-light">User and Role Management</h5>

        <!-- User Management Table -->
        <div class="card mt-3">
          <div class="card-body">
            <div class="container-fluid d-flex align-items-center justify-content-between px-0">
              <div>
                <h6 class="fw-semibold">User Management</h6>
              </div>

              <div class="search-box mb-3 position-relative">
                <input
                  type="text"
                  class="form-control search-input"
                  placeholder="Search..."
                  v-model="searchQuery"
                />
                <div
                  v-if="searching"
                  class="spinner-border text-primary spinner-sm position-absolute end-0 top-50 translate-middle-y me-3"
                  role="status"
                >
                  <span class="visually-hidden">Loading...</span>
                </div>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-bordered align-middle">
                <thead class="table-light">
                  <tr>
                    <th>NAME</th>
                    <th>EMAIL</th>
                    <th>Role</th>
                    <th>Access</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="filteredUsers.length === 0">
                    <td colspan="6" class="text-center text-muted py-3">
                      No users found.
                    </td>
                  </tr>
                  <tr v-for="(user, index) in filteredUsers" :key="index">
                    <td>{{ user.name }}</td>
                    <td>{{ user.email || "N/A" }}</td>
                    <td>
                      <span v-if="user.position === 'vice_president'">vice President</span>
                      <span v-else>{{ user?.role || user?.position }}</span>
                    </td>
                    <td>{{ getUserAcess(user.role, user.position) }}</td>
                    <td>
                      <button class="btn btn-sm btn-light">
                        <i
                          class="bi bi-three-dots-vertical action1"
                          data-bs-toggle="modal"
                          data-bs-target="#action1Modal"
                          @click="action1Func(user.id, user.role || user.position)"
                        ></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <RoleAndPermissionTable :users="filteredUsers" />
        <div class="bottom-role-container container"></div>
      </div>
    </AdminLayout>

    <!-- Modern Action Modal -->
    <div
      class="modal fade"
      id="action1Modal"
      tabindex="-1"
      aria-labelledby="actionModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modern-modal">
          <div class="modal-header modern-header">
            <div class="header-content">
              <i class="modal-icon fas fa-exclamation-circle"></i>
              <h2 class="modal-title text-dark" id="actionModalLabel">Confirm Action</h2>
            </div>
            <button
              type="button"
              class="btn-close modern-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            >
              <i class="bi bi-x-lg text-dark"></i>
            </button>
          </div>
          <div class="modal-body modern-body">
            <p>
              Please select an action to perform.
              <span class="text-danger">This action cannot be undone.</span>
            </p>
          </div>
          <div class="modal-footer modern-footer">
            <div class="action-buttons">
              <button
                type="button"
                class="btn btn-edit text-dark"
                data-bs-dismiss="modal"
                @click="editFunc()"
                v-if="userRole == 'admin' || userRole == 'president'"
              >
                <i class="fas fa-edit"></i>
                <span>Update</span>
              </button>

              <button
                type="button"
                class="btn btn-edit text-dark"
                data-bs-dismiss="modal"
                @click="viewFunc()"
                v-else
              >
                <i class="fas fa-eye"></i>
                <span>View</span>
              </button>

              <button
                type="button"
                class="btn btn-delete text-dark"
                data-bs-dismiss="modal"
                @click="deleteFunc()"
              >
                <i class="fas fa-trash-alt"></i>
                <span>Delete</span>
              </button>
              <button
                type="button"
                class="btn btn-cancel text-dark"
                data-bs-dismiss="modal"
              >
                <i class="fas fa-times"></i>
                <span>Cancel</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.role-container {
    width: 100%;
    height: 100%;
    overflow-y: scroll;
}
.bottom-role-container {
    width: 100%;
    height: 30%;
}/* Modern Modal Styles */
.modern-modal {
  border-radius: 16px;
  border: none;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  background: #fff;
}

.modern-header {
  border-bottom: none;
  padding: 24px 30px 20px;
  position: relative;
}

.modern-header .header-content {
  display: flex;
  align-items: center;
  gap: 12px;
}

.modern-header .modal-icon {
  font-size: 24px;
  color: rgba(255, 255, 255, 0.9);
}

.modern-header .modal-title {
  font-size: 20px;
  font-weight: 600;
  margin: 0;
  color: white;
}

.modern-close {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  opacity: 0.9;
  transition: all 0.2s ease;
  padding: 0;
}

.modern-close:hover {
  background: rgba(255, 255, 255, 0.3);
  opacity: 1;
  transform: rotate(90deg);
}

.modern-body {
  padding: 30px;
  text-align: center;
  color: #5a5a5a;
  font-size: 16px;
  line-height: 1.6;
}

.modern-footer {
  border-top: 1px solid #f0f0f0;
  padding: 24px 30px;
  justify-content: center;
}

.action-buttons {
  display: flex;
  gap: 12px;
  width: 100%;
  justify-content: center;
}

.action-buttons .btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 16px 20px;
  border-radius: 12px;
  border: none;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.3s ease;
  min-width: 100px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  position: relative;
  overflow: hidden;
}

.action-buttons .btn i {
  font-size: 18px;
  margin-bottom: 6px;
}

.action-buttons .btn span {
  font-size: 13px;
}

.action-buttons .btn:before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.1);
  transform: translateY(100%);
  transition: transform 0.3s ease;
}

.action-buttons .btn:hover:before {
  transform: translateY(0);
}

.btn-edit {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  color: white;
}

.btn-edit:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(79, 172, 254, 0.4);
}

.btn-delete {
  background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
  color: white;
}

.btn-delete:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(250, 112, 154, 0.4);
}

.btn-cancel {
  background: linear-gradient(135deg, #a8caba 0%, #5d8aa8 100%);
  color: white;
}

.btn-cancel:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(93, 138, 168, 0.4);
}

/* Animation for modal appearance */
.modal.fade .modern-modal {
  transform: translateY(-50px) scale(0.95);
  opacity: 0;
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.modal.show .modern-modal {
  transform: translateY(0) scale(1);
  opacity: 1;
}
table th {
  background: #D4F3F9 !important;
}
/* Responsive adjustments */
@media (max-width: 576px) {
  .action-buttons {
    flex-direction: column;
  }
  
  .action-buttons .btn {
    width: 100%;
  }
  
  .modern-header, .modern-body, .modern-footer {
    padding-left: 20px;
    padding-right: 20px;
  }
}
</style>
