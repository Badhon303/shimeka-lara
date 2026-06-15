<template>
  <div class="toast-container">
    <TransitionGroup name="toast">
      <div 
        v-for="toast in toasts" 
        :key="toast.id"
        :class="['toast', toast.type]"
      >
        <span class="toast-message">{{ toast.message }}</span>
        <button @click="removeToast(toast.id)" class="toast-close">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const toasts = ref([]);
let toastId = 0;

function addToast(message, type = 'success', duration = 3000) {
  const id = ++toastId;
  toasts.value.push({ id, message, type });
  
  setTimeout(() => {
    removeToast(id);
  }, duration);
}

function removeToast(id) {
  const index = toasts.value.findIndex(t => t.id === id);
  if (index > -1) {
    toasts.value.splice(index, 1);
  }
}

// Make toast available globally
onMounted(() => {
  window.$toast = addToast;
});

defineExpose({ addToast });
</script>
