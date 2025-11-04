<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, defineProps } from "vue";

// Temporary beneficiary form data
const beneficiarytemp = ref({
  name: "",
  relation: "",
  age: "",
  birth_date: "",
});

const beneficiary = ref([]);
const showAddBeneficiaryForm = ref(false);
const successMessage = ref("");

// Member form
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

//  Submit form with validation
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
      successMessage.value = "Member added successfully!";
      setTimeout(() => (successMessage.value = ""), 4000);
    },
    onError: (err) => console.log("An error occurred => ", err),
  });
};

//  Add beneficiary
const addBeneficiaryFunc = () => {
  beneficiary.value.push({ ...beneficiarytemp.value });
  beneficiarytemp.value = { name: "", relation: "", age: "", birth_date: "" };
  showAddBeneficiaryForm.value = false;
};

//  Delete beneficiary
const deleteBeneficiary = (index) => {
  beneficiary.value.splice(index, 1);
};

//  Format date display
const formatDate = (dateString) => {
  const options = { year: "numeric", month: "long", day: "numeric" };
  return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<template>
  <div class="container-fluid d-flex flex-row justify-content-center main-container">
    <div class="container mt-5">

      <!--  Success message -->
      <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
        {{ successMessage }}
        <button type="button" class="btn-close" @click="successMessage = ''"></button>
      </div>

      <form @submit.prevent="submit" class="form">
        <!--  Member Info -->
        <div class="row g-3">
          <div class="col-md-6">
            <input type="text" class="form-control" placeholder="Last name" v-model="form.last_name" />
          </div>
          <div class="col-md-6">
            <label class="form-label">Birth date</label>
            <input type="date" class="form-control" v-model="form.date_of_birth" />
          </div>

          <div class="col-md-6">
            <input type="text" class="form-control" placeholder="First name" v-model="form.first_name" required />
          </div>
          <div class="col-md-6">
            <input type="number" class="form-control" placeholder="Age" v-model="form.age" required />
          </div>

          <div class="col-md-6">
            <input type="text" class="form-control" placeholder="Middle name" v-model="form.middle_name" />
          </div>
          <div class="col-md-6">
            <select class="form-select" v-model="form.gender" required>
              <option value="" disabled>Gender</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>

          <div class="col-md-6">
            <select class="form-select" v-model="form.status" required>
              <option value="" disabled>Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div class="col-md-6">
            <input type="number" class="form-control" placeholder="Purok" v-model="form.purok" required />
          </div>

          <div class="col-md-6">
            <input type="text" class="form-control" placeholder="Occupation" v-model="form.occupation" required />
          </div>
          <div class="col-md-6">
            <input
              type="text"
              class="form-control"
              placeholder="Contact no."
              v-model="form.contact_number"
              pattern="[0-9]*"
              inputmode="numeric"
            />
            <div v-if="form.errors.contact_number" class="text-danger small mt-1">
              {{ form.errors.contact_number }}
            </div>
          </div>

          <div class="col-12">
            <input type="text" class="form-control" placeholder="Address" v-model="form.address" required />
          </div>
        </div>

        <!-- Beneficiaries -->
        <div class="mt-4">
          <h5 class="text-dark fw-light">Beneficiary</h5>

          <div v-if="beneficiary.length === 0" class="text-center mt-3 text-muted">
            No beneficiary added
          </div>

          <div v-else class="table-responsive mt-2">
            <table class="table table-bordered table-striped align-middle">
              <thead class="table-light">
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
                    <button type="button" class="btn btn-danger btn-sm" @click="deleteBeneficiary(index)">
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!--  Buttons -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 gap-3">
          <button
            v-if="!showAddBeneficiaryForm"
            class="btn btn-primary w-100 w-md-auto"
            type="button"
            @click="showAddBeneficiaryForm = true"
          >
            Add Beneficiary
          </button>
          <button class="btn btn-success w-100 w-md-auto" type="submit">Submit</button>
        </div>
      </form>

      <!--  Add Beneficiary Modal -->
      <div v-if="showAddBeneficiaryForm" class="card mt-4 p-3 shadow-sm">
        <h5 class="mb-3">Add Beneficiary</h5>
        <form @submit.prevent="addBeneficiaryFunc" class="row g-2">
          <div class="col-md-6">
            <input type="text" placeholder="Name" class="form-control" v-model="beneficiarytemp.name" required />
          </div>
          <div class="col-md-6">
            <input type="text" placeholder="Relation" class="form-control" v-model="beneficiarytemp.relation" required />
          </div>
          <div class="col-md-6">
            <input type="number" placeholder="Age" class="form-control" v-model="beneficiarytemp.age" required />
          </div>
          <div class="col-md-6">
            <label class="form-label small">Birthdate</label>
            <input type="date" class="form-control" v-model="beneficiarytemp.birth_date" required />
          </div>

          <div class="col-12 d-flex justify-content-end gap-2 mt-3">
            <button type="button" class="btn btn-secondary" @click="showAddBeneficiaryForm = false">
              Cancel
            </button>
            <button type="submit" class="btn btn-primary">Save Beneficiary</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.main-container {
  min-height: 100vh;
  padding: 1rem;
  padding-top: 0;
}

.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

/* Form fields adapt well on any screen */
.form-control,
.form-select {
  font-size: 0.95rem;
}

/* For very small devices */
@media (max-width: 576px) {
  .form-control,
  .form-select {
    font-size: 0.9rem;
  }

  .btn {
    font-size: 0.9rem;
    padding: 0.45rem 0.8rem;
  }

  .table {
    font-size: 0.85rem;
  }
}
</style>
