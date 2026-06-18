<template>
  <div class="admin-page">
    <div class="admin-content">
      <div class="page-header">
        <h1>Home Banners</h1>
        <button @click="saveBanners" :disabled="saving" class="btn btn-primary">
          {{ saving ? 'Saving...' : 'Save Banners' }}
        </button>
      </div>

      <div v-if="successMessage" class="alert alert-success">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        {{ successMessage }}
      </div>

      <div class="banner-images-grid">
        <div class="banner-image-item">
          <label>Cosmetics Section Banner</label>
          <div v-if="homeBanners.cosmetics" class="banner-preview">
            <img :src="homeBanners.cosmetics" alt="Cosmetics Banner" />
            <button @click="homeBanners.cosmetics = ''" class="remove-banner">&times;</button>
          </div>
          <div class="banner-upload">
            <input ref="cosmeticsInput" type="file" accept="image/*" @change="(e) => handleBannerChange(e, 'cosmetics')" class="file-input" />
            <div class="banner-placeholder" @click="cosmeticsInput?.click()">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6v12a2.25 2.25 0 002.25 2.25z" />
              </svg>
              <span>{{ homeBanners.cosmetics ? 'Change Image' : 'Upload Cosmetics Banner' }}</span>
            </div>
          </div>
        </div>

        <div class="banner-image-item">
          <label>Fashion Section Banner</label>
          <div v-if="homeBanners.fashion" class="banner-preview">
            <img :src="homeBanners.fashion" alt="Fashion Banner" />
            <button @click="homeBanners.fashion = ''" class="remove-banner">&times;</button>
          </div>
          <div class="banner-upload">
            <input ref="fashionInput" type="file" accept="image/*" @change="(e) => handleBannerChange(e, 'fashion')" class="file-input" />
            <div class="banner-placeholder" @click="fashionInput?.click()">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6v12a2.25 2.25 0 002.25 2.25z" />
              </svg>
              <span>{{ homeBanners.fashion ? 'Change Image' : 'Upload Fashion Banner' }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const homeBanners = ref({ cosmetics: '', fashion: '' });
const cosmeticsInput = ref(null);
const fashionInput = ref(null);
const saving = ref(false);
const successMessage = ref('');

async function fetchBanners() {
  try {
    const res = await axios.get('/admin/settings/all');
    const data = res.data;
    for (const group of Object.values(data)) {
      for (const item of group) {
        if (item.key === 'home_banners') {
          homeBanners.value = item.type === 'json' ? JSON.parse(item.value) : (item.value || { cosmetics: '', fashion: '' });
        }
      }
    }
  } catch (e) {
    console.error('Failed to load banners:', e);
  }
}

async function handleBannerChange(e, type) {
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
    homeBanners.value[type] = res.data.url;
    if (window.$toast) window.$toast('Banner image uploaded!', 'success');
  } catch (err) {
    if (window.$toast) window.$toast('Failed to upload banner image', 'error');
  }
}

async function saveBanners() {
  saving.value = true;
  successMessage.value = '';
  try {
    const payload = [{
      key: 'home_banners',
      value: homeBanners.value,
      type: 'json',
      group: 'site',
      label: 'Home Page Banners'
    }];
    await axios.post('/admin/settings', { settings: payload });
    successMessage.value = 'Banners saved successfully!';
    setTimeout(() => successMessage.value = '', 3000);
    if (window.$toast) window.$toast('Banners saved!', 'success');
  } catch (err) {
    if (window.$toast) window.$toast('Failed to save banners', 'error');
  } finally {
    saving.value = false;
  }
}

onMounted(fetchBanners);
</script>

<style scoped>
.admin-page { min-height: 100vh; background: var(--gray-100); }
.admin-content { padding: 2rem; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.page-header h1 { font-size: 1.875rem; }

.banner-images-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
.banner-image-item { background: var(--white); padding: 1.5rem; border-radius: var(--radius-xl); box-shadow: var(--shadow-lg); }
.banner-image-item label { display: block; font-size: 0.875rem; font-weight: 500; color: var(--gray-700); margin-bottom: 0.5rem; }
.banner-preview { position: relative; margin-bottom: 0.5rem; border-radius: var(--radius-md); overflow: hidden; }
.banner-preview img { width: 100%; height: 200px; object-fit: cover; }
.remove-banner { position: absolute; top: 0.5rem; right: 0.5rem; width: 28px; height: 28px; background: #ef4444; color: white; border: none; border-radius: 50%; cursor: pointer; font-size: 1rem; line-height: 1; }
.banner-upload .file-input { display: none; }
.banner-placeholder { display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; border: 2px dashed var(--gray-300); border-radius: var(--radius-md); cursor: pointer; color: var(--gray-500); font-size: 0.875rem; transition: all var(--transition-fast); }
.banner-placeholder:hover { border-color: var(--primary-500); color: var(--primary-600); }
.banner-placeholder svg { width: 20px; height: 20px; }
.alert { display: flex; align-items: center; gap: 0.5rem; padding: 0.875rem 1rem; border-radius: var(--radius-lg); margin-bottom: 1rem; font-size: 0.875rem; font-weight: 500; }
.alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }

@media (max-width: 768px) {
  .admin-content { padding: 1rem; }
  .banner-images-grid { grid-template-columns: 1fr; }
}
</style>
