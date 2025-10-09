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
                <h5 class="fw-bold">User and Role Management</h5>

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
                        <Link :href="route('role.add')" class="btn btn-primary float-end mt-2">
                            Add User
                        </Link>
                    </div>
                </div>
                    <RoleAndPermissionTable :users="getUsers"/>
                <div class="bottom-role-container container"></div>
            </div>
        </AdminLayout>

        <!-- Modal for the action1  -->
        <div
            class="modal fade"
            id="action1Modal"
            tabindex="-1"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">
                            Action
                        </h1>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                   <div class="modal-footer">
                      <div class="container-fluid">
                        <div class="row gx-2">
                          <div class="col">
                            <button
                              type="button"
                              class="btn btn-primary w-100"
                              data-bs-dismiss="modal"
                              @click="editFunc()"
                            >
                              Edit
                            </button>
                          </div>
                          <div class="col">
                            <button type="button" class="btn btn-warning w-100"
                            data-bs-dismiss="modal"
                            @click="deleteFunc()"
                            >
                              Delete
                            </button>
                          </div>
                          <div class="col">
                            <button type="button" class="btn btn-secondary w-100"
                            data-bs-dismiss="modal"
                            >
                              Cancel
                            </button>
                          </div>
                        </div>
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
}
</style>
