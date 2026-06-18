<template>
  <div class="admin-page">
    <div class="admin-content">
      <div class="page-header">
        <h1>Home Features</h1>
        <button @click="saveFeatures" :disabled="saving" class="btn btn-primary">
          {{ saving ? 'Saving...' : 'Save Features' }}
        </button>
      </div>

      <div v-if="successMessage" class="alert alert-success">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        {{ successMessage }}
      </div>

      <p class="hint">Edit the feature boxes shown on the home page (icon emoji, title, description).</p>

      <div class="features-editor">
        <div v-for="(feature, i) in homeFeatures" :key="i" class="feature-edit-item">
          <div class="feature-edit-header">
            <span class="feature-number">#{{ i + 1 }}</span>
            <button @click="removeFeature(i)" class="btn btn-sm btn-danger" title="Remove">&times;</button>
          </div>
          <div class="feature-edit-fields">
            <input v-model="feature.icon" type="text" class="form-control" placeholder="Icon emoji (e.g. 🚚)" maxlength="2" />
            <input v-model="feature.title" type="text" class="form-control" placeholder="Title (e.g. Free Shipping)" />
            <input v-model="feature.description" type="text" class="form-control" placeholder="Description" />
          </div>
        </div>
        <button v-if="homeFeatures.length < 6" @click="addFeature" class="btn btn-secondary">+ Add Feature</button>
      </div>

      <div class="preview-section">
        <h3>Preview</h3>
        <div class="features-preview">
          <div v-for="(feature, i) in homeFeatures.filter(f => f.title)" :key="i" class="feature-preview-item">
            <div class="feature-preview-icon">{{ feature.icon }}</div>
            <h4>{{ feature.title }}</h4>
            <p>{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const defaultFeatures = [
  { icon: '🚚', title: 'Free Shipping', description: 'On orders over ৳1000' },
  { icon: '💵', title: 'Cash on Delivery', description: 'Pay when you receive' },
  { icon: '🎁', title: 'Gift Wrapping', description: 'Beautiful gift packages' },
  { icon: '↩️', title: 'Easy Returns', description: '7-day return policy' }
];

const homeFeatures = ref([...defaultFeatures]);
const saving = ref(false);
const successMessage = ref('');

async function fetchFeatures() {
  try {
    const res = await axios.get('/admin/settings/all');
    const data = res.data;
    for (const group of Object.values(data)) {
      for (const item of group) {
        if (item.key === 'home_features') {
          const val = item.type === 'json' ? JSON.parse(item.value) : item.value;
          homeFeatures.value = val && val.length > 0 ? val : [...defaultFeatures];
        }
      }
    }
  } catch (e) {
    console.error('Failed to load features:', e);
  }
}

function addFeature() {
  homeFeatures.value.push({ icon: '✨', title: '', description: '' });
}

function removeFeature(i) {
  homeFeatures.value.splice(i, 1);
}

async function saveFeatures() {
  saving.value = true;
  successMessage.value = '';
  try {
    const payload = [{
      key: 'home_features',
      value: homeFeatures.value.filter(f => f.title),
      type: 'json',
      group: 'site',
      label: 'Home Page Features'
    }];
    await axios.post('/admin/settings', { settings: payload });
    successMessage.value = 'Features saved successfully!';
    setTimeout(() => successMessage.value = '', 3000);
    if (window.$toast) window.$toast('Features saved!', 'success');
  } catch (err) {
    if (window.$toast) window.$toast('Failed to save features', 'error');
  } finally {
    saving.value = false;
  }
}

onMounted(fetchFeatures);
</script>

<style scoped>
.admin-page { min-height: 100vh; background: var(--gray-100); }
.admin-content { padding: 2rem; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
.page-header h1 { font-size: 1.875rem; }
.hint { margin-bottom: 1.5rem; color: var(--gray-500); font-size: 0.875rem; }

.features-editor { display: flex; flex-direction: column; gap: 1rem; }
.feature-edit-item { background: var(--white); padding: 1rem; border-radius: var(--radius-lg); border: 1px solid var(--gray-200); box-shadow: var(--shadow-md); }
.feature-edit-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; }
.feature-number { font-size: 0.875rem; font-weight: 600; color: var(--primary-600); }
.feature-edit-fields { display: grid; grid-template-columns: 80px 1fr 1fr; gap: 0.5rem; }
.feature-edit-fields .form-control { font-size: 0.875rem; padding: 0.5rem; }
.btn-sm { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
.btn-danger { background: #ef4444; color: white; border-radius: var(--radius-md); }
.alert { display: flex; align-items: center; gap: 0.5rem; padding: 0.875rem 1rem; border-radius: var(--radius-lg); margin-bottom: 1rem; font-size: 0.875rem; font-weight: 500; }
.alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }

.preview-section { margin-top: 2rem; background: var(--white); padding: 1.5rem; border-radius: var(--radius-xl); box-shadow: var(--shadow-lg); }
.preview-section h3 { font-size: 1.125rem; margin-bottom: 1rem; color: var(--gray-800); }
.features-preview { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
.feature-preview-item { text-align: center; padding: 1rem; }
.feature-preview-icon { font-size: 2rem; margin-bottom: 0.5rem; }
.feature-preview-item h4 { font-size: 0.875rem; font-weight: 600; color: var(--gray-800); margin-bottom: 0.25rem; }
.feature-preview-item p { font-size: 0.75rem; color: var(--gray-500); }

@media (max-width: 768px) {
  .admin-content { padding: 1rem; }
  .feature-edit-fields { grid-template-columns: 1fr; }
  .features-preview { grid-template-columns: repeat(2, 1fr); }
}
</style>
