<script setup>
const toggle = defineModel('toggle')

const toggleSidebar = () => {
  toggle.value = !toggle.value
}
</script>

<template>
  <div>
    <div class="d-flex align-items-center mb-3 header container-fluid py-2">
      <button class="me-2" @click="toggleSidebar">
        <i class="bi bi-list fs-2"></i>
      </button>
      <h4 class="mb-0 title">PROTEC DAMAYAN SYSTEM</h4>
    </div>
  </div>
</template>

<style scoped>
.header {
  background: #D4F3F9;
}
.title{
  font-family: 'Poppins', sans-serif;
  font-weight: bold;
}
@media screen and (max-width: 413px){
  .title{
  font-size: 1rem;
}
}
</style>
