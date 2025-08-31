<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    activeStatus: { type: String, default: 'paid' },
    contributionsIds: { type: Array, default: () => [] },
    members: { type: Array, default: () => [] }
});
</script>

<template>
    <div>
        <!-- STATUS TOGGLE (still here to switch parent filter) -->
        <div class="container-fluid d-flex gap-3 align-items-center mb-3">
            <h5 
                class="choice" 
                :class="{'text-success': activeStatus == 'paid'}">
                PAID
            </h5>
            <h5 
                class="choice" 
                :class="{'text-success': activeStatus == 'not_paid'}">
                UNPAID
            </h5>
        </div>

        <!-- MEMBERS TABLE -->
        <div class="table-responsive table-container">
            <table class="table" v-if="members.length > 0">
                <thead>
                    <tr>
                        <th class="bg-light">ID</th>
                        <th class="bg-light">NAME</th>
                        <th class="bg-light">CONTACT NO.</th>
                        <th class="bg-light">PUROK</th>
                        <th class="bg-light">STATUS</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="member in members" :key="member.id">
                        <td class="bg-light">{{ member.id }}</td>
                        <td class="bg-light">
                            {{ member.first_name }} {{ member.middle_name }} {{ member.last_name }}
                        </td>
                        <td class="bg-light">{{ member.contact_number }}</td>
                        <td class="bg-light">{{ member.purok }}</td>
                        <td class="bg-light text-center">
                            <i class="bi bi-check-circle text-success fs-5" v-if="contributionsIds.includes(member.id)"></i>
                            <i class="bi bi-ban text-danger fs-5" v-else></i>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-else class="text-center p-3">
                <p class="text-muted">No members found.</p>
            </div>
        </div>
    </div>
</template>


<style lang="css" scoped>
.choice {
    font-size: 1rem;
    cursor: pointer;
}
.choice:hover {
    color: #007bff;
    text-decoration: underline;
}
.choice.active {
    color: #0CF32F;
    font-weight: bold;
}

.table-container {
    max-height: 400px; 
    overflow-y: auto;
    overflow-x: auto;
}
</style>
