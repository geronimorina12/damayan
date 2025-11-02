<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { defineProps, ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BeneficiaryComponent from '@/Components/dashboard/BeneficiaryComponent.vue';
const props = defineProps({
    member: {
        type: Object,
        default: () => ({})
    },
});
let getMember = ref({});
watch(
    () => props.member,
    (newdata) => {
        getMember.value = newdata;
    },
    {immediate: true}
)
</script>

<template>
    <Head title="View Member Info" />
    <div>
        <AdminLayout>

            <div class="container my-4 border p-3">
    <h6 class="text-center fw-bold mb-2 text-danger">
     This Member is Deleted
    </h6>

    <table class="table table-bordered" v-if="getMember">
      <tbody>
        <tr>
          <th>FIRST NAME</th>
          <td>{{ getMember.first_name }}</td>
          <th>MIDDLE NAME</th>
          <td>{{ getMember.middle_name }}</td>
          <th>LAST NAME</th>
          <td>{{ getMember.last_name }}</td>
          <th>SUFFIX</th>
          <td></td>
        </tr>
        <tr>
          <th>BIRTHDATE</th>
          <td>{{ getMember.date_of_birth }}</td>
          <th>AGE</th>
          <td>{{ getMember.age }} YEARS OLD</td>
          <th>GENDER</th>
          <td>{{ getMember.gender }}</td>
          <th>CONTACT NO.</th>
          <td>{{ getMember.contact_number }}</td>
        </tr>
        <tr>
          <th>ADDRESS</th>
          <td colspan="3">{{ getMember.address }}</td>
          <th>PUROK</th>
          <td>{{ getMember.purok }}</td>
          <th>OCCUPATION</th>
          <td>{{ getMember.occupation }}</td>
        </tr>
        <tr>
          <th>STATUS</th>
          <td>{{ getMember.status }}</td>
          <td colspan="6"></td>
        </tr>
      </tbody>
    </table>
  </div>
        <!-- Beneficiary component  -->
        <BeneficiaryComponent :bene="getMember.beneficiaries"/>
        </AdminLayout>
    </div>
</template>

<style scoped>
table th,
table td {
  vertical-align: middle;
  text-align: left;
}
</style>