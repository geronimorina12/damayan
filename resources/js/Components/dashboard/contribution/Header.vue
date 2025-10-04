<script setup>
import AddContribution from '@/Components/dashboard/contribution/AddContribution.vue';
</script>

<template>
  <div>
     <div class="container-fluid d-flex flex-row justify-content-between align-items-center px-0 mb-2">
            <div>
              <h6 class="mt-4 payment-contribution-text fs-5">Payment Contribution</h6>
            </div>
            <div>
              <!-- Add contribution Component -->
              <AddContribution />
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