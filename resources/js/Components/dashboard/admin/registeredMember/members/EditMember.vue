<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, defineProps, watch, reactive } from "vue";

const props = defineProps({
    member: {
        type: Object,
        default: () => ({}),
    },
});

// Reactive form (Inertia)
const form = useForm({
    first_name: "",
    last_name: "",
    address: "",
    contact_number: "",
    date_of_birth: "",
    purok: "",
    age: "",
    middle_name: "",
    status: "",
    occupation: "",
    gender: "",
    beneficiaries: [],
});

const beneficiary = ref([]);
const beneficiarytemp = ref({
    name: "",
    relation: "",
    age: "",
    birth_date: "",
});

// ✅ Watch props.member for any changes and sync to form
watch(
    () => props.member,
    (newData) => {
        if (!newData) return;

        console.log("Reactive update detected:", newData);

        // Reset and sync form with latest data
        form.defaults({
            first_name: newData.first_name || "",
            last_name: newData.last_name || "",
            address: newData.address || "",
            contact_number: newData.contact_number || "",
            date_of_birth: newData.date_of_birth || "",
            purok: newData.purok || "",
            age: newData.age || "",
            middle_name: newData.middle_name || "",
            status: newData.status || "",
            occupation: newData.occupation || "",
            gender: newData.gender || "",
            beneficiaries: newData.beneficiaries || [],
        });

        form.reset(); // ensure form values match defaults
        beneficiary.value = newData?.beneficiaries || [];
    },
    { immediate: true, deep: true }
);

// ✅ Submit handler
const submit = () => {
    form.clearErrors();

    const phoneRegex = /^0\d{10}$/;
    if (!phoneRegex.test(form.contact_number)) {
        form.setError(
            "contact_number",
            "Contact number must start with 0 and be exactly 11 digits."
        );
        return;
    }

    form.beneficiaries = beneficiary.value;

    form.put(route("members.update", props.member.id), {
        onSuccess: () => alert("Member updated successfully."),
        onError: (err) => console.error("An error occurred => ", err),
    });
};

// ✅ Add & delete beneficiaries
const addBeneficiaryFunc = () => {
    if (
        !beneficiarytemp.value.name ||
        !beneficiarytemp.value.relation ||
        !beneficiarytemp.value.age ||
        !beneficiarytemp.value.birth_date
    )
        return;

    beneficiary.value.push({ ...beneficiarytemp.value });
    beneficiarytemp.value = { name: "", relation: "", age: "", birth_date: "" };
};

const deleteBeneficiary = (index) => {
    beneficiary.value.splice(index, 1);
};

// ✅ Format date helper
const formatDate = (dateString) => {
    if (!dateString) return "";
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<template>
  <div>
     <div class="container-fluid d-flex flex-row justify-content-center align-items-center main-container">
        <div class="container mt-3">
            <form @submit.prevent="submit" class="form">
                <div class="row mb-3 d-flex flex-row align-items-center">
                    <div class="col col-6">
                        <input
                            type="text"
                            class="form-control mt-3"
                            placeholder="Last name"
                            v-model="form.last_name"
                        />
                    </div>
                    <div class="col col-6">
                        <label>Birth date</label>
                        <input
                            type="date"
                            class="form-control"
                            placeholder="Birth date"
                            v-model="form.date_of_birth"
                        />
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col col-6">
                        <input
                            type="text"
                            class="form-control"
                            placeholder="First name"
                            v-model="form.first_name"
                            required
                        />
                    </div>
                    <div class="col col-6">
                        <label>Age</label>
                        <input
                            type="number"
                            class="form-control"
                            placeholder="Age"
                            v-model="form.age"
                            required
                        />
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col col-6">
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Middle name"
                            v-model="form.middle_name"
                            required
                        />
                    </div>
                    <div class="col col-6">
                        <label>Gender</label>
                        <select class="form-control" v-model="form.gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col col-6">
                        <select class="form-control" v-model="form.status" required>
                            <option value="" disabled>Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col col-6">
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Purok"
                            v-model="form.purok"
                            required
                        />
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col col-6">
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Occupation"
                            v-model="form.occupation"
                            required
                        />
                    </div>
                    <div class="col col-6">
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Contact no."
                            v-model="form.contact_number"
                            required
                            pattern="[0-9]*"
                            inputmode="numeric"
                        />
                        <div v-if="form.errors.contact_number" class="text-danger mt-1">
                            {{ form.errors.contact_number }}
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col col-6">
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Address"
                            v-model="form.address"
                            required
                        />
                    </div>
                </div>

                <!-- Beneficiary List -->
                <div v-if="!beneficiary.length" class="container text-center mt-5 fs-4">
                    No beneficiary added
                </div>
                <div v-else>
                    <h5 class="text-dark fw-light pt-2">Beneficiary</h5>
                    <table class="table table-responsive bg-light">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Relation</th>
                                <th>Age</th>
                                <th>Birthdate</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(data, index) in beneficiary" :key="index">
                                <td>{{ data.name }}</td>
                                <td>{{ data.relation }}</td>
                                <td>{{ data.age }}</td>
                                <td>{{ formatDate(data.birth_date) }}</td>
                                <td>
                                    <button
                                        class="btn btn-danger"
                                        type="button"
                                        @click="deleteBeneficiary(index)"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="container-fluid d-flex flex-row justify-content-between align-items-center">
                    <button
                        class="btn btn-primary"
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#exampleModal"
                    >
                        Add Beneficiary
                    </button>

                    <button class="btn btn-success" type="submit">Save</button>
                </div>
            </form>

            <!-- Modal for Beneficiary -->
            <div
                class="modal fade"
                id="exampleModal"
                tabindex="-1"
                aria-labelledby="exampleModalLabel"
                aria-hidden="true"
            >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">
                                Add Beneficiary
                            </h1>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent="addBeneficiaryFunc">
                                <input
                                    type="text"
                                    placeholder="Name"
                                    class="form-control mb-2"
                                    v-model="beneficiarytemp.name"
                                    required
                                />
                                <input
                                    type="text"
                                    placeholder="Relation"
                                    class="form-control mb-2"
                                    v-model="beneficiarytemp.relation"
                                    required
                                />
                                <input
                                    type="number"
                                    placeholder="Age"
                                    class="form-control mb-2"
                                    v-model="beneficiarytemp.age"
                                    required
                                />
                                <label>Date of birth</label>
                                <input
                                    type="date"
                                    class="form-control mb-2"
                                    v-model="beneficiarytemp.birth_date"
                                    required
                                />

                                <div class="container d-flex justify-content-end gap-2 mt-1">
                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        data-bs-dismiss="modal"
                                    >
                                        Close
                                    </button>
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                        data-bs-dismiss="modal"
                                    >
                                        Save changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>
  </div>
</template>
