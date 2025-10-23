<script setup>
import { Link } from '@inertiajs/vue3';
import AddNewMember from './members/AddNewMember.vue';
import NewlyCreated from './members/NewlyCreated.vue';

// JS logic for opening and closing modals
const openModal = (id) => {
  document.getElementById(id).classList.add('show');
};

const closeModal = (id) => {
  document.getElementById(id).classList.remove('show');
};

// Close modal when clicking outside
window.addEventListener('click', (e) => {
  document.querySelectorAll('.custom-modal').forEach((modal) => {
    if (e.target === modal) modal.classList.remove('show');
  });
});
</script>

<template>
  <div>
    <div class="container-fluid d-flex flex-row justify-content-between align-items-center mb-2">
      <div>
        <h5 class="ms-3 fw-light">Registered Members</h5>
      </div>
      <div>
        <!-- Add Member Button -->
        <button
          class="modern-btn me-3"
          @click.prevent="openModal('addNewMemberModal')"
        >
          <i class="bi bi-person-plus"></i>
          <span>Add Member</span>
        </button>

        <!-- Newly Created Button -->
        <button
          class="modern-btn secondary"
          @click="openModal('newCreatedMemberModal')"
        >
          <i class="bi bi-person-lines-fill"></i>
          <span>Newly Created</span>
        </button>
      </div>
    </div>

    <!-- Add New Member Modal -->
    <div id="addNewMemberModal" class="custom-modal">
      <div class="modal-content" style="min-width: 60%; height: auto;">
        <div class="modal-header">
          <h3>Add New Member</h3>
          <button class="close-btn" @click="closeModal('addNewMemberModal')">&times;</button>
        </div>
        <div class="modal-body">
          <AddNewMember />
          
        </div>
      </div>
    </div>

    <!-- Newly Created Member Modal -->
    <div id="newCreatedMemberModal" class="custom-modal" style="padding-top: 0;">

      <div class="modal-content " style="min-width: 60%; height: auto;">
        <div class="modal-header alig-items-center">

                <div class="">
                    <h4 class="mb-3 text-dark fw-bold d-flex align-items-center">
                      Newly Registered Member
                </h4>
            </div>

          <button class="close-btn2 fs-2" @click="closeModal('newCreatedMemberModal')">&times;</button>
        </div>
        <div class="modal-body">
          <NewlyCreated />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* ===== Buttons ===== */
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
  padding: 0.1rem 1rem;
  font-weight: 500;
  cursor: pointer;
  overflow: hidden;
  transition: all 0.3s ease;
  text-decoration: none;
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
}
.modern-btn:hover {
  background: linear-gradient(135deg, #0056d2, #003f9e);
  box-shadow: 0 4px 12px rgba(0, 86, 210, 0.4);
}
.modern-btn:hover span {
  opacity: 1;
  max-width: 200px;
  margin-left: 0.3rem;
  transition-delay: 0.3s;
}
.modern-btn:hover i {
  transform: scale(1.1);
}
.modern-btn.secondary {
  background: linear-gradient(135deg, #6c757d, #495057);
}
.modern-btn.secondary:hover {
  background: linear-gradient(135deg, #495057, #343a40);
  box-shadow: 0 4px 12px rgba(52, 58, 64, 0.4);
}

/* ===== Custom Modal ===== */
.custom-modal {
  display: none;
  position: fixed;
  z-index: 999;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.45);
  justify-content: center;
  align-items: center;
  transition: opacity 0.3s ease;
  overflow-y: scroll;
  padding-top: 20rem;
}
.custom-modal.show {
  display: flex;
}
.modal-content {
  background: #fff;
  border-radius: 12px;
  width: 480px;
  max-width: 90%;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
  animation: fadeIn 0.3s ease;
}
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.2rem;
  border-bottom: 1px solid #eee;
}
.modal-body {
  padding: 1rem 1.2rem;
  min-height: 100px; /* gives space without content */
}
.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #555;
  cursor: pointer;
  transition: color 0.2s ease;
}
.close-btn:hover {
  color: #000;
}
.close-btn2{
  transform: translateY(-20%);
}
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
</style>
