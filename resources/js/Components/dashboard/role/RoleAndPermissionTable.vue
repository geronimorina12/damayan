<script setup>
import { ref, watch } from "vue";
import { defineProps } from "vue";
import { router } from "@inertiajs/vue3";
const props = defineProps({
    users: {
        type: Array,
        default: () => [],
    },
});
const getUsers = ref([]);
const adminCount = ref(0);
const secretaryCount = ref(0);
const presidentCount = ref(0);
const vicePresidentCount = ref(0);
const purokLeaderCount = ref(0);
const treasurerCount = ref(0);
const colectorCount = ref(0);
const selectedRole = ref(null);
watch(
    () => props.users,
    (newUsers) => {
        getUsers.value = newUsers;
        //Kuhaon ang count san bawat role
        adminCount.value = getUsers.value.filter(
            (user) => user.role === "admin"
        ).length;
        secretaryCount.value = getUsers.value.filter(
            (user) => user.position === "secretary"
        ).length;
        presidentCount.value = getUsers.value.filter(
            (user) => user.position === "president"
        ).length;
        vicePresidentCount.value = getUsers.value.filter(
            (user) => user.position === "vise_president"
        ).length;
        purokLeaderCount.value = getUsers.value.filter(
            (user) => user.position === "purok_leader"
        ).length;
        treasurerCount.value = getUsers.value.filter(
            (user) => user.position === "treasurer"
        ).length;
        colectorCount.value = getUsers.value.filter(
            (user) => user.position === "colector"
        ).length;
    },
    { immediate: true }
);
const roles = [
    {
        name: "Admin",
        description: "Full access",
        users: adminCount.value + (adminCount.value > 1 ? " users" : " user"),
        access: "Full modules",
        icon: "bi bi-person",
    },
    {
        name: "Collector",
        description: "Jurisdiction-based",
        users: 4,
        access: "Status updates",
        icon: "bi bi-people",
    },
    
];
const viewFunc = () => {
    if (!selectedRole.value) {
        alert("No role selected. Please select a role to view.");
        return;
    }
    router.get(route('role.viewSpecificRole', {role: selectedRole.value}));
    selectedRole.value = null;  
};
</script>
<template>
    <div>
         <!-- Roles and Permissions Table -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="fw-semibold">Roles and Permissions</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Role</th>
                                        <th>Description</th>
                                        <th>Users</th>
                                        <th>Access</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(role, index) in roles"
                                        :key="index"
                                    >
                                        <td>
                                            <i :class="role.icon"></i>
                                            {{ role.name }}
                                        </td>
                                        <td>{{ role.description }}</td>
                                        <td>{{ role.users }}</td>
                                        <td>{{ role.access }}</td>
                                        <td>
                                            <button
                                                class="btn btn-sm btn-light"
                                            >
                                                <i
                                                    class="bi bi-three-dots-vertical"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#action"
                                                    @click="selectedRole = role.name.toLocaleLowerCase()"
                                                ></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Modal for actions  -->
                 <div
            class="modal fade"
            id="action"
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
                          <div class="col">
                            <button
                              type="button"
                              class="btn btn-primary w-100"
                              data-bs-dismiss="modal"
                              @click="viewFunc()"
                            >
                              View All
                            </button>
                          </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>