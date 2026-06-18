<template>
  <div class="admin-page">
    <div class="admin-content">
      <div class="page-header">
        <h1>Hero Slider</h1>
        <div class="header-actions">
          <button @click="addSlide" class="btn btn-secondary">+ Add Slide</button>
          <button @click="saveSlides" :disabled="saving" class="btn btn-primary">
            {{ saving ? 'Saving...' : 'Save Slides' }}
          </button>
        </div>
      </div>

      <div v-if="successMessage" class="alert alert-success">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        {{ successMessage }}
      </div>

      <div class="slides-list">
        <div v-for="(slide, i) in heroSlides" :key="i" class="slide-item">
          <div class="slide-preview" v-if="slide.image">
            <img :src="slide.image" :alt="slide.title" />
          </div>
          <div class="slide-fields">
            <div class="slide-image-upload">
              <input :ref="el => setInputRef(el, i)" type="file" accept="image/*" @change="(e) => handleSlideImageChange(e, i)" class="file-input" />
              <div v-if="!slide.image" class="slide-image-placeholder" @click="triggerInput(i)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6v12a2.25 2.25 0 002.25 2.25z" />
                </svg>
                <span>Click to upload slide image</span>
              </div>
              <div v-else class="slide-image-change" @click="triggerInput(i)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <span>Change Image</span>
              </div>
            </div>
            <input v-model="slide.title" type="text" class="form-control" placeholder="Title" />
            <input v-model="slide.subtitle" type="text" class="form-control" placeholder="Subtitle" />
            <input v-model="slide.description" type="text" class="form-control" placeholder="Description" />
            <input v-model="slide.button_text" type="text" class="form-control" placeholder="Button Text" />
            <input v-model="slide.button_link" type="text" class="form-control" placeholder="Button Link (e.g. /shop)" />
          </div>
          <button @click="removeSlide(i)" class="btn btn-danger" title="Remove Slide">&times;</button>
        </div>
      </div>

      <div v-if="heroSlides.length === 0" class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="width: 64px; height: 64px; color: var(--gray-300); margin-bottom: 1rem;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6v12a2.25 2.25 0 002.25 2.25z" />
        </svg>
        <h3>No slides yet</h3>
        <p>Add slides to show on the homepage hero section.</p>
        <button @click="addSlide" class="btn btn-primary">+ Add Slide</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const heroSlides = ref([]);
const saving = ref(false);
const successMessage = ref('');
const inputRefs = {}; // plain object, not reactive

function setInputRef(el, index) {
  if (el) inputRefs[index] = el;
}

function triggerInput(index) {
  inputRefs[index]?.click();
}

async function fetchSlides() {
  try {
    const res = await axios.get('/admin/settings/all');
    const data = res.data;
    for (const group of Object.values(data)) {
      for (const item of group) {
        if (item.key === 'hero_slides') {
          heroSlides.value = item.type === 'json' ? JSON.parse(item.value) : (item.value || []);
        }
      }
    }
  } catch (e) {
    console.error('Failed to load slides:', e);
  }
}

function addSlide() {
  heroSlides.value.push({ image: '', title: '', subtitle: '', description: '', button_text: '', button_link: '/shop' });
}

function removeSlide(i) {
  heroSlides.value.splice(i, 1);
  delete inputRefs[i];
}

async function handleSlideImageChange(e, index) {
  const file = e.target.files[0];
  if (!file) return;
  if (file.size > 2 * 1024 * 1024) {
    alert('Image must be less than 2MB');
    return;
  }
  const formData = new FormData();
  formData.append('slide_image', file);
  try {
    const res = await axios.post('/admin/upload-slide-image', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    heroSlides.value[index].image = res.data.url;
    if (window.$toast) window.$toast('Image uploaded!', 'success');
  } catch (err) {
    if (window.$toast) window.$toast('Failed to upload image', 'error');
  }
}

async function saveSlides() {
  saving.value = true;
  successMessage.value = '';
  try {
    const payload = [{
      key: 'hero_slides',
      value: heroSlides.value.filter(s => s.image),
      type: 'json',
      group: 'site',
      label: 'Hero Slider Slides'
    }];
    await axios.post('/admin/settings', { settings: payload });
    successMessage.value = 'Slides saved successfully!';
    setTimeout(() => successMessage.value = '', 3000);
    if (window.$toast) window.$toast('Slides saved!', 'success');
  } catch (err) {
    if (window.$toast) window.$toast('Failed to save slides', 'error');
  } finally {
    saving.value = false;
  }
}

onMounted(fetchSlides);
</script>

<style scoped>
.admin-page { min-height: 100vh; background: var(--gray-100); }
.admin-content { padding: 2rem; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.page-header h1 { font-size: 1.875rem; }
.header-actions { display: flex; gap: 0.5rem; }

.slides-list { display: flex; flex-direction: column; gap: 1rem; }
.slide-item { display: flex; gap: 0.75rem; align-items: flex-start; background: var(--white); padding: 1rem; border-radius: var(--radius-lg); border: 1px solid var(--gray-200); box-shadow: var(--shadow-md); }
.slide-preview { width: 120px; height: 80px; border-radius: var(--radius-md); overflow: hidden; flex-shrink: 0; }
.slide-preview img { width: 100%; height: 100%; object-fit: cover; }
.slide-fields { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; flex: 1; }
.slide-fields .form-control { font-size: 0.875rem; padding: 0.5rem; }
.slide-image-upload { grid-column: 1 / -1; }
.slide-image-upload .file-input { display: none; }
.slide-image-placeholder { display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; border: 2px dashed var(--gray-300); border-radius: var(--radius-md); cursor: pointer; color: var(--gray-500); font-size: 0.875rem; transition: all var(--transition-fast); }
.slide-image-placeholder:hover { border-color: var(--primary-500); color: var(--primary-600); }
.slide-image-placeholder svg { width: 20px; height: 20px; }
.slide-image-change { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: var(--primary-50); border-radius: var(--radius-md); cursor: pointer; color: var(--primary-600); font-size: 0.875rem; width: fit-content; }
.slide-image-change:hover { background: var(--primary-100); }
.slide-image-change svg { width: 16px; height: 16px; }
.btn-danger { background: #ef4444; color: white; width: 40px; border-radius: var(--radius-md); }
.alert { display: flex; align-items: center; gap: 0.5rem; padding: 0.875rem 1rem; border-radius: var(--radius-lg); margin-bottom: 1rem; font-size: 0.875rem; font-weight: 500; }
.alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
.empty-state { text-align: center; padding: 4rem 2rem; color: var(--gray-500); background: var(--white); border-radius: var(--radius-xl); box-shadow: var(--shadow-lg); }
.empty-state h3 { font-size: 1.25rem; color: var(--gray-800); margin-bottom: 0.5rem; }
.empty-state p { margin-bottom: 1.5rem; }

@media (max-width: 768px) {
  .admin-content { padding: 1rem; }
  .slide-fields { grid-template-columns: 1fr; }
  .slide-item { flex-direction: column; }
  .slide-preview { width: 100%; height: 150px; }
  .page-header { flex-wrap: wrap; gap: 1rem; }
}
</style>
