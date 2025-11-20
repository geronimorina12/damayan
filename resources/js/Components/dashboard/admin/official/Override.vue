<template>
  <div
    v-if="visible"
    class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
  >
    <div class="bg-white shadow-xl rounded-xl p-6 w-full max-w-sm animate-fade-in">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">
        Do you want to override this current official?
      </h2>

      <div class="flex justify-end gap-3 mt-6">
        <button
          @click="cancelAction"
          class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100 transition"
        >
          Cancel
        </button>

        <button
          @click="approveAction"
          class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition"
        >
          Approve
        </button>
      </div>
    </div>
    Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsa dolores odio quibusdam tenetur velit? Nisi et, ipsum molestiae distinctio adipisci explicabo exercitationem perspiciatis quos minus sit repellat tempora labore reiciendis minima omnis. Repudiandae, aliquid minus! Quibusdam quidem ipsam eum voluptatum itaque, corrupti esse molestiae unde ratione nemo voluptatem repellat libero!
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
  visible: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['approve', 'cancel'])

const cancelAction = () => emit('cancel')
const approveAction = () => emit('approve')
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.25s ease-out;
}
</style>
