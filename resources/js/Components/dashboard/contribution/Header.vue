<script setup>
import AddContribution from './AddContribution.vue';
</script>

<template>
  <div>
     <div class="container-fluid d-flex flex-row justify-content-between align-items-center px-0 mb-2">
            <div>
              <h6 class="mt-4 payment-contribution-text fs-5">Payment Contribution</h6>
            </div>
            <div>
              <!-- Add contribution Component -->
              <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">Add contribution</button>
            </div>
          </div>


      <!-- Add contribution modal -->
      <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="min-width: 60%;">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">Add contribution</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <AddContribution />
            </div>
          </div>
        </div>
      </div>
  </div>
</template>

<style scoped>
@media screen and (max-width: 605px) {
  .payment-contribution-text {
    font-size: .8rem;
  }
}
</style>