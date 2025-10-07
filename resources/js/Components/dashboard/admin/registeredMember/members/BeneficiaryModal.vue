<!-- Components/BeneficiaryModal.vue -->
<template>
  <div 
    v-if="show" 
    class="modal fade show" 
    :class="{ 'd-block': show }" 
    tabindex="-1" 
    role="dialog"
    style="background-color: rgba(0, 0, 0, 0.5);"
    @click.self="closeModal"
  >
    <div class="modal-dialog modal-lg" style="width: 80%; max-width: 80%;" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">{{ title }}</h5>
          <button type="button" class="btn-close btn-close-white" @click="closeModal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <slot name="body">
            <!-- Default content goes here -->
            <p>Modal body text goes here.</p>
          </slot>
        </div>
        <div class="modal-footer">
          <slot name="footer">
            <!-- Default footer -->
            <button type="button" class="btn btn-secondary" @click="closeModal">Close</button>
            <button type="button" class="btn btn-primary" @click="confirmModal">Save changes</button>
          </slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BeneficiaryModal',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    title: {
      type: String,
      default: 'Modal Title'
    }
  },
  emits: ['update:show', 'confirmed', 'closed'],
  
  watch: {
    show(newVal) {
      if (newVal) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    }
  },
  
  methods: {
    closeModal() {
      this.$emit('update:show', false);
      this.$emit('closed');
    },
    
    confirmModal() {
      this.$emit('confirmed');
      this.closeModal();
    },
    
    handleEscapeKey(event) {
      if (event.keyCode === 27 && this.show) {
        this.closeModal();
      }
    }
  },
  
  mounted() {
    document.addEventListener('keydown', this.handleEscapeKey);
  },
  
  beforeUnmount() {
    document.removeEventListener('keydown', this.handleEscapeKey);
    document.body.style.overflow = '';
  }
}
</script>

<style scoped>
.modal {
  backdrop-filter: blur(2px);
}
.modal.show {
  opacity: 1;
}
.modal-dialog {
  width: 80%;
  max-width: 80%;
}
</style>