<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useForm } from "@inertiajs/vue3";

const getMembers = ref([]);
const getFullName = ref([]);
const getCollectors = ref([]);
const getUsers = ref([]);
const getDeceasedMembersData = ref([]);

const form = useForm({
    member_id: "",
    amount: 100,
    payment_date: "",
    collector: "",
    status: "paid",
    purok: "",
    deceased_id: "",
});

// Fetch all the members, users, and deceased members
const fetchMembersData = async () => {
    try {
        const response = await axios.get(route("contributions.members.data"));
        const { members, users, deceasedMembers } = response.data;

        // Raw data
        getMembers.value = members || [];
        getUsers.value = users || [];
        getDeceasedMembersData.value = deceasedMembers || [];

        // Build full names
        const memberFullNames = (members || []).map((m) => ({
            id: m.id,
            fullName: `${m.first_name} ${m.middle_name ?? ""} ${m.last_name}`
                .replace(/\s+/g, " ")
                .trim(),
        }));

        const userFullNames = (users || []).map((u) => ({
            id: u.id,
            fullName: u.name,
        }));

        // Combine both
        getFullName.value = [...memberFullNames, ...userFullNames];

        // Filter collectors
        getCollectors.value = (users || []).filter((u) => u.role === "collector");
    } catch (error) {
        console.error("Error fetching members data:", error);
    }
};

// Run fetch on mount
onMounted(fetchMembersData);

// Form submit
const submit = () => {
    form.post(route("contributions.store"), {
        onSuccess: () => alert("Contribution created!"),
        onError: (err) => console.log("An error occurred:", err),
    });
};
</script>


<template>
    <div>
         <div class="contri-container py-1">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card rounded-4 shadow-none border-0 ">
                        <div class="card-header text-center ">
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

                                <!-- Select deceased member  -->
                                 <div class="mb-3">
                                    <label for="deceased_member" class="form-label">Select Deceased Member</label>
                                    <select v-model="form.deceased_id" class="form-control">
                                        <option disabled value="">-- Select Deceased Member --</option>
                                        <option
                                            v-for="deceased in getDeceasedMembersData"
                                            :key="deceased.id"
                                            :value="deceased.member_id"
                                        >
                                            {{ deceased.deceased_name }}
                                        </option>
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
    </div>
</template>

<style></style>
