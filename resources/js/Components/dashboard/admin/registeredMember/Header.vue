<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <div>
     <div class="container-fluid d-flex flex-row justify-content-between align-items-center mb-2">
        <div>
          <h5 class=" ms-3 fw-light">Registered Members</h5>
        </div>
        <div>
          <Link :href="route('addNewMember')" 
          class="btn btn-primary d-flex align-items-center"
          data-bs-toggle="modal" data-bs-target="#addNewMember"
          >
            <i class="bi bi-person-plus me-2 fs-5 d-inline d-lg-none"></i>
            <span class="d-none d-lg-inline">Add Member</span>
          </Link>
        </div>
      </div>
  </div>
</template>

<style>

</style>