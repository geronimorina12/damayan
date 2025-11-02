<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, defineProps } from "vue";

// Form for adding a single beneficiary
const beneficiarytemp = ref({
    name: "",
    relation: "",
    age: "",
    birth_date: "",
});

const beneficiary = ref([]);
const showAddBeneficiaryForm = ref(false);
const successMessage = ref(""); //  for bootstrap alert

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

const submit = () => {
    form.clearErrors();

    const phoneRegex = /^0\d{10}$/;
    if (!phoneRegex.test(form.contact_number)) {
        form.setError("contact_number", "Contact number must start with 0 and be exactly 11 digits.");
        return;
    }

    form.beneficiaries = beneficiary.value;

    form.post(route("addMemberPost"), {
        onSuccess: () => {
            successMessage.value = " Member added successfully!";
            setTimeout(() => {
                successMessage.value = "";
            }, 4000); // auto-hide after 4 seconds
        },
        onError: (err) => console.log("An error occurred => ", err),
    });
};

const addBeneficiaryFunc = () => {
    beneficiary.value.push({ ...beneficiarytemp.value });
    beneficiarytemp.value = { name: "", relation: "", age: "", birth_date: "" };
    showAddBeneficiaryForm.value = false;
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
  <div class="container-fluid d-flex flex-row justify-content-center main-container ">
    <div class="container mt-5">

       <div
        v-if="successMessage"
        class="alert alert-success alert-dismissible fade show"
        role="alert"
      >
        {{ successMessage }}
        <button type="button" class="btn-close" @click="successMessage = ''"></button>
      </div>

      <form @submit.prevent="submit" class="form">
        <!-- Member Form Fields -->
        <div class="row mb-3 d-flex align-items-center">
          <div class="col col-6">
            <input type="text" class="form-control mt-3" placeholder="Last name" v-model="form.last_name" />
          </div>
          <div class="col col-6">
            <label>Birth date</label>
            <input type="date" class="form-control" placeholder="Birth date" v-model="form.date_of_birth" />
          </div>
        </div>

        <div class="row mb-3">
          <div class="col col-6">
            <input type="text" class="form-control" placeholder="First name" v-model="form.first_name" required />
          </div>
          <div class="col col-6">
            <input type="number" class="form-control" placeholder="Age" v-model="form.age" required />
          </div>
        </div>

        <div class="row mb-3">
          <div class="col col-6">
            <input type="text" class="form-control" placeholder="Middle name" v-model="form.middle_name" />
          </div>
          <div class="col col-6">
            <select class="form-control" v-model="form.gender" required>
              <option value="" disabled>Gender</option>
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
            <input type="number" class="form-control" placeholder="Purok" v-model="form.purok" required />
          </div>
        </div>

        <div class="row mb-3">
          <div class="col col-6">
            <input type="text" class="form-control" placeholder="Occupation" v-model="form.occupation" required />
          </div>
          <div class="col col-6">
            <input type="text" class="form-control" placeholder="Contact no." v-model="form.contact_number" pattern="[0-9]*" inputmode="numeric" />
            <div v-if="form.errors.contact_number" class="text-danger mt-1">
              {{ form.errors.contact_number }}
            </div>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col col-6">
            <input type="text" class="form-control" placeholder="Address" v-model="form.address" required />
          </div>
        </div>

        <!-- Beneficiary List -->
        <div class="container text-center mt-5 fs-4" v-if="beneficiary.length < 1">
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
                  <button class="btn btn-danger" @click="deleteBeneficiary(index)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Buttons -->
        <div class="container-fluid d-flex flex-row justify-content-between align-items-center mt-4">
          <div>
            <button v-if="!showAddBeneficiaryForm" class="btn btn-primary" type="button" @click="showAddBeneficiaryForm = true">
              Add Beneficiary
            </button>
          </div>

          <div class="d-flex flex-row gap-2 align-items-center">
            <button class="btn btn-success">submit</button>
          </div>
        </div>
      </form>

      <!-- Add Beneficiary Form (appears at bottom) -->
      <div v-if="showAddBeneficiaryForm" class="card mt-4 p-3 shadow-sm">
        <h5 class="mb-3">Add Beneficiary</h5>
        <form @submit.prevent="addBeneficiaryFunc">
          <input type="text" placeholder="Name" class="form-control mb-2" v-model="beneficiarytemp.name" required />
          <input type="text" placeholder="Relation" class="form-control mb-2" v-model="beneficiarytemp.relation" required />
          <input type="number" placeholder="Age" class="form-control mb-2" v-model="beneficiarytemp.age" required />
          <label>Date of Birth</label>
          <input type="date" placeholder="Birthdate" class="form-control mb-2" v-model="beneficiarytemp.birth_date" required />

          <div class="d-flex justify-content-end gap-2 mt-3">
            <button type="button" class="btn btn-secondary" @click="showAddBeneficiaryForm = false">
              Cancel
            </button>
            <button type="submit" class="btn btn-primary">
              Save Beneficiary
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>
</template>

<style>
.main-container {
  min-height: 100vh;
}
</style>

