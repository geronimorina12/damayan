<script setup>
import { useForm } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { defineProps, watch, ref } from "vue";

const props = defineProps({
    members: {
        type: Array,
        default: () => [],
    },
    users: {
        type: Array,
        default: () => [],
    },
});
let getMembers = ref([]);
let getFullName = ref([]);
const getCollectors = ref([]);
const getUsers = ref([]);

// Create a full name from first, middle, and last name || pero ang hali sa users full name na
watch(
    [() => props.members, () => props.users],
    ([membersData, usersData]) => {
        // Keep raw members
        getMembers.value = membersData || [];

        // Map members
        const memberFullNames = (membersData || []).map((member) => ({
            id: member.id,
            fullName: `${member.first_name} ${member.middle_name ?? ""} ${member.last_name}`
                .replace(/\s+/g, " ")
                .trim(),
        }));

        // Map users
        const userFullNames = (usersData || []).map((user) => ({
            id: user.id,
            fullName: user.name,
        }));

        // Combine
        getFullName.value = [...memberFullNames, ...userFullNames];

        getUsers.value = usersData || [];
        getCollectors.value = (getUsers.value || []).filter(
            (user) => user.role === "collector"
        );
    },
    { immediate: true }
);

const form = useForm({
    member_id: "",
    amount: 100,
    payment_date: "",
    collector: "",
    status: "paid",
    purok: "",
});

const submit = () => {
    form.post(route("contributions.store"), {
        onSuccess: () => alert("Contribution created!"),
        onError: (err) => console.log("An error occured: ", err),
    });
};
</script>

<template>
    <Head title="Add Contribution" />
    <AdminLayout>
        <div class="contri-container py-1">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card rounded-4">
                        <div class="card-header text-center">
                            <h4 class="mb-0">Create Contribution</h4>
                        </div>

                        <div class="card-body p-4">
                            <form @submit.prevent="submit">
                                <!-- Member Name Dropdown -->
                                <div class="mb-3">
                                    <label for="member_id" class="form-label">Member Name</label>
                                    <select
                                        v-model="form.member_id"
                                        id="member_id"
                                        class="form-select"
                                        :class="{ 'is-invalid': form.errors.member_id }"
                                    >
                                        <option disabled value="">
                                            -- Select Member --
                                        </option>
                                        <option
                                            v-for="member in getFullName"
                                            :key="member.id"
                                            :value="member.id"
                                        >
                                            {{ member.fullName }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.member_id" class="invalid-feedback">
                                        {{ form.errors.member_id }}
                                    </div>
                                </div>


                                <!-- Payment Date -->
                                <div class="mb-3">
                                    <label for="payment_date" class="form-label">Payment Date</label>
                                    <input
                                        v-model="form.payment_date"
                                        type="datetime-local"
                                        id="payment_date"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.payment_date }"
                                    />
                                    <div v-if="form.errors.payment_date" class="invalid-feedback">
                                        {{ form.errors.payment_date }}
                                    </div>
                                </div>

                                <!-- Collector Dropdown (new placement after Payment Date) -->
                                <div class="mb-3">
                                    <label for="collector" class="form-label">Collector</label>
                                    <select
                                        v-model="form.collector"
                                        id="collector"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.collector }"
                                    >
                                        <option disabled value="">
                                            -- Select Collector --
                                        </option>
                                        <option
                                            v-for="collector in getCollectors"
                                            :key="collector.id"
                                            :value="collector.name"
                                        >
                                            {{ collector.name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.collector" class="invalid-feedback">
                                        {{ form.errors.collector }}
                                    </div>
                                </div>


                                <!-- Purok -->
                                <div class="mb-3">
                                    <label for="purok" class="form-label">Purok</label>
                                    <select v-model="form.purok" class="form-control">
                                        <option value="purok1">Purok 1</option>
                                        <option value="purok2">Purok 2</option>
                                        <option value="purok3">Purok 3</option>
                                        <option value="purok4">Purok 4</option>
                                    </select>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-flex justify-content-end">
                                    <button
                                        type="submit"
                                        class="btn btn-success px-4 rounded-pill"
                                    >
                                        Submit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-bottom"></div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.contri-container {
    width: 100%;
    height: 100%;
    overflow-y: scroll;
}

.container-bottom {
    width: 50%;
    height: 30%;
}
</style>
