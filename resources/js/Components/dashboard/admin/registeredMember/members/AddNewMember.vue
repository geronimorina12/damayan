
<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, defineProps } from "vue";

// Form for adding a single beneficiary (modal input)
const beneficiarytemp = ref({
    name: "",
    relation: "",
    age: "",
    birth_date: "",
});

const beneficiary = ref([]);

// Main member form
const form = useForm({
    first_name: "",
    last_name: "",
    address: "",
    contact_number: "",
    date_of_birth: "",
    // registration_date: "",
    purok: "",
    age: "",
    middle_name: "",
    status: "",
    occupation: "",
    gender: "",
    beneficiaries: [], 
});

const submit = () => {
    form.clearErrors();

    const phoneRegex = /^0\d{10}$/;
    if (!phoneRegex.test(form.contact_number)) {
        form.setError("contact_number", "Contact number must start with 0 and be exactly 11 digits.");
        return;
    }

    form.beneficiaries = beneficiary.value;

    form.post(route("addMemberPost"), {
        onSuccess: () => alert("Member added successfully."),
        onError: (err) => console.log("An error occurred => ", err),
    });
};

const addBeneficiaryFunc = () => {
    beneficiary.value.push({ ...beneficiarytemp.value });
    beneficiarytemp.value = { name: "", relation: "", age: "", birth_date: "" };
};

const deleteBeneficiary = (index) => {
    beneficiary.value.splice(index, 1);
};

const formatDate = (dateString) => {
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<template>
  <div>
            <div
                class="container-fluid d-flex flex-row justify-content-center align-items-center main-container"
            >
                <div class="container mt-5">
                    <form @submit.prevent="submit" class="form">
                        <div class="row mb-3 d-flex align-items-center">
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
                                <!-- <label>age</label> -->
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
                                <select name="gender" class="form-control" v-model="form.gender" required>
                                    <option value="" disabled>Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col col-6">
                                <select name="gender" class="form-control" v-model="form.status" required>
                                    <option value="" disabled>Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col col-6">
                                <input
                                    type="number"
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
                                pattern="[0-9]*"
                                inputmode="numeric"
                            />
                            <div v-if="form.errors.contact_number" class="text-danger mt-1">
                                {{ form.errors.contact_number }}
                            </div>
                        </div>

                        </div>

                        <div class="row mb-3">
                            <!-- <div class="col col-6">
                                <label>Registration date</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    placeholder="Registration date"
                                    v-model="form.registration_date"
                                    required
                                />
                            </div> -->
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

                        <div
                            class="container text-center mt-5 fs-4"
                            v-if="
                                beneficiary != undefined &&
                                beneficiary.length < 1
                            "
                        >
                            No beneficiary added
                        </div>
                        <div v-else>
                            <h5 class="text-dark fw-light pt-2">Beneficiary</h5>
                            <table class="table table-responsive bg-light">
                                <thead class="thead">
                                    <tr>
                                        <th>Name</th>
                                        <th>Relation</th>
                                        <th>Age</th>
                                        <th>Bithdate</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody class="tbody">
                                    <tr
                                        class="tr"
                                        v-for="(data, index) in beneficiary"
                                        :key="data.id"
                                    >
                                        <td class="td">{{ data?.name }}</td>
                                        <td class="td">{{ data?.relation }}</td>
                                        <td class="td">{{ data?.age }}</td>
                                        <td class="td">
                                            {{ formatDate(data?.birth_date) }}
                                        </td>
                                        <th>
                                            <button
                                                class="btn btn-danger"
                                                @click="
                                                    deleteBeneficiary(
                                                        index,
                                                        data.id
                                                    )
                                                "
                                            >
                                                Delete
                                            </button>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div
                            class="container-fluid d-flex flex-row justify-content-between align-items-center"
                        >
                            <div>
                                <button
                                    class="btn btn-primary"
                                    type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#exampleModal"
                                >
                                    Add Beneficiary
                                </button>
                            </div>

                            <div
                                class="d-flex flex-row gap-2 align-items-center"
                            >
                                <button class="btn btn-success">Save</button>
                            </div>
                        </div>
                    </form>

                    <!-- Add beneficiary modal  -->

                    <!-- Modal -->
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
                                    <h1
                                        class="modal-title fs-5"
                                        id="exampleModalLabel"
                                    >
                                        Add beneficiary
                                    </h1>
                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal"
                                        aria-label="Close"
                                    ></button>
                                </div>
                                <div class="modal-body">
                                    <form
                                        @submit.prevent="addBeneficiaryFunc"
                                        class="form"
                                    >
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
                                            placeholder="Bithdate"
                                            class="form-control mb-2"
                                            v-model="beneficiarytemp.birth_date"
                                            required
                                        />

                                        <div
                                            class="container d-flex justify-content-end gap-2 mt-1"
                                        >
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


<style>

</style>