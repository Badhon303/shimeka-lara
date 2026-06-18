<template>
  <div class="about-page">
    <div class="page-header">
      <div class="container">
        <h1>About Us</h1>
        <p>Your trusted destination for beauty and fashion</p>
      </div>
    </div>

    <div class="container">
      <div class="about-content">
        <div v-if="loading" class="loading-text">Loading...</div>
        <div v-else-if="content" class="about-body" v-html="content"></div>
        <div v-else class="about-fallback">
          <p>Welcome to our store! We are dedicated to bringing you the best cosmetics and fashion products.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const content = ref('');
const loading = ref(true);

async function fetchSettings() {
  try {
    const res = await axios.get('/settings');
    content.value = res.data.about_content || '';
  } catch (e) {
    console.error('Failed to load about content:', e);
  } finally {
    loading.value = false;
  }
}

onMounted(fetchSettings);
</script>

<style scoped>
.about-page {
  min-height: 100vh;
  background: var(--gray-50);
}

.about-content {
  padding: 4rem 0;
}

.about-body {
  background: var(--white);
  padding: 3rem;
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-sm);
  line-height: 1.8;
  color: var(--gray-700);
}

.about-body :deep(h2) {
  font-size: 1.75rem;
  margin-bottom: 1rem;
  color: var(--gray-900);
}

.about-body :deep(p) {
  margin-bottom: 1rem;
}

.about-body :deep(ul) {
  margin-bottom: 1rem;
  padding-left: 1.5rem;
}

.loading-text {
  text-align: center;
  padding: 4rem;
  color: var(--gray-500);
}

.about-fallback {
  text-align: center;
  padding: 4rem;
  color: var(--gray-500);
}

@media (max-width: 768px) {
  .about-content {
    padding: 2rem 0;
  }
  .about-body {
    padding: 1.5rem;
  }
}
</style>
