<script setup>
import { ref, onMounted, watch } from "vue";
import axios from "axios";
import { useForm } from "@inertiajs/vue3";

const getMembers = ref([]);
const getFullName = ref([]);
const getCollectors = ref([]);
const getUsers = ref([]);
const getDeceasedMembersData = ref([]);

// Alert state
const showAlert = ref(false);
const alertMessage = ref("");

const form = useForm({
    member_id: "",
    amount: 100,
    payment_date: "",
    collector: "",
    status: "paid",
    purok: "",
    deceased_id: "",
});

// Fetch members, users, deceased data
const fetchMembersData = async () => {
    try {
        const response = await axios.get(route("contributions.members.data"));
        const { members, users, deceasedMembers } = response.data;

        getMembers.value = members || [];
        getUsers.value = users || [];
        getDeceasedMembersData.value = deceasedMembers || [];

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

        getFullName.value = [...memberFullNames, ...userFullNames];

        // Only users with role = collector
        getCollectors.value = (users || []).filter((u) => u.role === "collector");

        console.log("Collectors:", getCollectors.value);
    } catch (error) {
        console.error(error);
    }
};

onMounted(fetchMembersData);

// Watch for purok selection change
watch(
    () => form.purok,
    (newPurok) => {
        if (!newPurok) return;

        // Extract the number part (e.g., "purok1" -> "1")
        const purokNumber = newPurok.replace("purok", "");

        // Find collector with the same purok number
        const collector = getCollectors.value.find(
            (c) => String(c.purok) === String(purokNumber)
        );

        if (collector) {
            form.collector = collector.name;
        } else {
            form.collector = "";
        }
    }
);

// Submit form
const submit = () => {
    form.post(route("contributions.store"), {
        preserveScroll: true,
        onSuccess: () => {
            alertMessage.value = "Contribution successfully created!";
            showAlert.value = true;

            setTimeout(() => (showAlert.value = false), 3000);

            form.reset();
        },
    });
};
</script>

<template>
    <div>
        <div class="contri-container py-1">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card rounded-4 shadow-none border-0">
                        <div class="card-header text-center">
                            <h4 class="mb-0">Create Contribution</h4>
                        </div>

                        <div class="card-body p-4">
                            <!-- Alert -->
                            <div
                                v-if="showAlert"
                                class="alert alert-success text-center fw-semibold"
                                role="alert"
                            >
                                {{ alertMessage }}
                            </div>

                            <form @submit.prevent="submit">
                                <!-- Member Dropdown -->
                                <div class="mb-3">
                                    <label class="form-label">Member Name</label>
                                    <select
                                        v-model="form.member_id"
                                        class="form-select"
                                        :class="{ 'is-invalid': form.errors.member_id }"
                                    >
                                        <option disabled value="">-- Select Member --</option>
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
                                    <label class="form-label">Payment Date</label>
                                    <input
                                        v-model="form.payment_date"
                                        type="datetime-local"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.payment_date }"
                                    />
                                    <div v-if="form.errors.payment_date" class="invalid-feedback">
                                        {{ form.errors.payment_date }}
                                    </div>
                                </div>

                                <!-- Purok -->
                                <div class="mb-3">
                                    <label class="form-label">Purok</label>
                                    <select v-model="form.purok" class="form-control">
                                        <option disabled value="">-- Select Purok --</option>
                                        <option value="purok1">Purok 1</option>
                                        <option value="purok2">Purok 2</option>
                                        <option value="purok3">Purok 3</option>
                                        <option value="purok4">Purok 4</option>
                                    </select>
                                </div>

                                <!-- Collector -->
                                <div class="mb-3" v-if="form.collector">
                                    <label class="form-label">Collector</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="form.collector"
                                        readonly
                                    />
                                </div>

                                <div class="text-danger" v-else>No Collector for that purok.</div>

                                <!-- Deceased Member -->
                                <div class="mb-3">
                                    <label class="form-label">Select Deceased Member</label>
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

                                <!-- Submit -->
                                <div class="d-flex justify-content-end">
                                    <button
                                        type="submit"
                                        class="btn btn-success px-4 rounded-pill d-flex align-items-center gap-2"
                                        :disabled="form.processing || !form.collector"
                                    >
                                        <span
                                            v-if="form.processing"
                                            class="spinner-border spinner-border-sm"
                                        ></span>
                                        {{ form.processing ? "Submitting..." : "Submit" }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
