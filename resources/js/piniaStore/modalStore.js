import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useModalStore = defineStore('modalStore', () => {

  // GLOBAL STATE (reactive)
  const closeAddMemberModal = ref(false);

  // ACTION
  const triggerCloseMemberModal = () => {
    closeAddMemberModal.value = true;
  };

  // RESET
  const resetModal = () => {
    closeAddMemberModal.value = false;
  };

  return {
    closeAddMemberModal,
    triggerCloseMemberModal,
    resetModal
  };
});
