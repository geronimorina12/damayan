import { defineStore } from 'pinia';

// The store ID is now "deceased title"
// We keep the export name descriptive for use in components (useTextStore)
export const useTextStore = defineStore('deceased title', {
  state: () => ({
    currentText: 'Add Deceased Info.',
  }),

  actions: {
    setText(newText) {
      this.currentText = newText;
      console.log(`Text updated in store: ${this.currentText}`);
    },
  },
});