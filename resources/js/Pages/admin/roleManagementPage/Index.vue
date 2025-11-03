<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { defineProps, ref, watch } from "vue";
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
        getUsers.value = newUsers;
        getUsers.value.push(...getAdmins.value);
    },
    { immediate: true }
);

const action1Func = (userId) => {
    userIdClicked.value = userId;
};
const editFunc = () => {
    router.get(route('role.edit', {id: userIdClicked.value}));
};
const deleteFunc = () => {
    if(confirm("Are you sure you want to delete this user?")) {
        router.delete(route('role.deleteUser', {user: userIdClicked.value}), {
            onSuccess: () => {
                alert("User deleted successfully.");
            },
            onError: (e) => {
                console.error("Failed to delete user.", e);
            }
        });
    }
};
const getUserAcess = (role, position) => {
    if(role === 'collector' || position === 'collector') return 'Status updates'
    if(role === 'admin' || position === 'admin') return 'Full modules'
    if(role === 'secretary' || position === 'secretary') return 'Records and Reports'
    if(role === 'vise_president' || position === 'vise_president') return 'Review and Approval'
    else return "N/A"

}
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
                        <h6 class="fw-semibold">User Management</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>NAME</th>
                                        <th>EMAIL</th>
                                        <th>Role</th>
                                        <th>Access</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(user, index) in getUsers"
                                        :key="index"
                                    >
                                        <td>{{ user.id }}</td>
                                        <td>{{ user.name }}</td>
                                        <td>{{ user.email || 'N/A' }}</td>
                                        <td>{{ user?.role || user?.position }}</td>
                                        <td>{{ getUserAcess(user.role, user.position) }}</td>
                                        <td>
                                            <button
                                                class="btn btn-sm btn-light"
                                            >
                                                <i
                                                    class="bi bi-three-dots-vertical action1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#action1Modal"
                                                    @click="action1Func(user.id)"
                                                ></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                    <RoleAndPermissionTable :users="getUsers"/>
                <div class="bottom-role-container container"></div>
            </div>
        </AdminLayout>

      <!-- Modern Action Modal -->
<div class="modal fade" id="action1Modal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modern-modal">
      <div class="modal-header modern-header">
        <div class="header-content">
          <i class="modal-icon fas fa-exclamation-circle"></i>
          <h2 class="modal-title text-dark" id="actionModalLabel">Confirm Action</h2>
        </div>
        <button type="button" class="btn-close modern-close" data-bs-dismiss="modal" aria-label="Close">
         <i class="bi bi-x-lg text-dark"></i>
        </button>
      </div>
      <div class="modal-body modern-body">
        <p>Please select an action to perform. <span class="text-danger">This action cannot be undone.</span></p>
      </div>
      <div class="modal-footer modern-footer">
        <div class="action-buttons">
          <button type="button" class="btn btn-edit text-dark" data-bs-dismiss="modal" @click="editFunc()">
            <i class="fas fa-edit"></i>
            <span>Edit</span>
          </button>
          <button type="button" class="btn btn-delete text-dark" data-bs-dismiss="modal" @click="deleteFunc()">
            <i class="fas fa-trash-alt"></i>
            <span>Delete</span>
          </button>
          <button type="button" class="btn btn-cancel text-dark" data-bs-dismiss="modal">
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
