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
              <Link 
        :href="route('addNewMember')" 
        class="modern-btn me-3"
        data-bs-toggle="modal" 
        data-bs-target="#addNewMember"
      >
        <i class="bi bi-person-plus"></i>
        <span>Add Member</span>
      </Link>

      <button
        class="modern-btn secondary"
        data-bs-toggle="modal" 
        data-bs-target="#newCreatedMember"
      >
        <i class="bi bi-person-lines-fill"></i>
        <span>Newly Created</span>
      </button>

        </div>
      </div>
  </div>
</template>

<style scoped>
.modern-btn {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  background: linear-gradient(135deg, #007bff, #0056d2);
  color: #fff;
  border: none;
  border-radius: 12px;
  padding: 0.6rem 1rem;
  font-weight: 500;
  cursor: pointer;
  overflow: hidden;
  transition: all 0.3s ease;
}

.modern-btn i {
  font-size: 1.4rem;
  transition: transform 0.3s ease;
}

.modern-btn span {
  max-width: 0;
  opacity: 0;
  white-space: nowrap;
  overflow: hidden;
  transition: all 0.4s ease;
  transition-delay: 0s; /* No delay by default */
  font-size: 0.95rem;
}

.modern-btn:hover {
  background: linear-gradient(135deg, #0056d2, #003f9e);
  box-shadow: 0 4px 12px rgba(0, 86, 210, 0.4);
}

.modern-btn:hover span {
  opacity: 1;
  max-width: 200px;
  margin-left: 0.3rem;
  transition-delay: 0.3s; /* ⏱️ Delay text reveal */
}

.modern-btn:hover i {
  transform: scale(1.1);
}

/* Secondary style for the "Newly Created" button */
.modern-btn.secondary {
  background: linear-gradient(135deg, #6c757d, #495057);
}

.modern-btn.secondary:hover {
  background: linear-gradient(135deg, #495057, #343a40);
  box-shadow: 0 4px 12px rgba(52, 58, 64, 0.4);
}
</style>