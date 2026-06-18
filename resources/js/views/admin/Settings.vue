<template>
  <div class="admin-page">
    <div class="admin-content">
      <div class="page-header">
        <h1>Website Settings</h1>
      </div>

      <div class="settings-grid">
        <!-- Site Info -->
        <div class="settings-card">
          <h3>Site Information</h3>
          <div class="form-group">
            <label>Website Name</label>
            <input v-model="settings.site_name" type="text" class="form-control" />
          </div>
          <div class="form-group">
            <label>Contact Phone</label>
            <input v-model="settings.site_phone" type="text" class="form-control" />
          </div>
          <div class="form-group">
            <label>Contact Email</label>
            <input v-model="settings.site_email" type="email" class="form-control" />
          </div>
          <div class="form-group">
            <label>Shop Address</label>
            <textarea v-model="settings.site_address" class="form-control" rows="2"></textarea>
          </div>

          <!-- Logo Upload -->
          <div class="form-group">
            <label>Website Logo</label>
            <div class="logo-upload">
              <input ref="logoInput" type="file" accept="image/*" @change="handleLogoChange" class="file-input" />
              <div v-if="logoPreview || settings.site_logo" class="logo-preview">
                <img :src="logoPreview || settings.site_logo" alt="Logo" />
                <button type="button" class="remove-logo" @click="clearLogo">&times;</button>
              </div>
              <div v-else class="logo-placeholder" @click="$refs.logoInput.click()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6v12a2.25 2.25 0 002.25 2.25z" />
                </svg>
                <p>Click to upload logo</p>
                <span class="upload-hint">PNG, JPG up to 2MB</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Shipping Rules -->
        <div class="settings-card">
          <h3>Shipping Rules</h3>
          <div class="form-group">
            <label>Free Shipping Threshold (৳)</label>
            <input v-model.number="settings.free_shipping_threshold" type="number" class="form-control" />
            <p class="hint">Orders above this amount get free shipping</p>
          </div>
          <p class="hint">Delivery charges are managed in <router-link to="/admin/couriers">Couriers</router-link></p>
        </div>

        <!-- Content Management Links -->
        <div class="settings-card full-width">
          <h3>Content Management</h3>
          <p class="hint">Manage homepage content from dedicated pages.</p>
          <div class="content-links">
            <router-link to="/admin/hero-slider" class="content-link">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6v12a2.25 2.25 0 002.25 2.25z" /></svg>
              <span>Hero Slider</span>
              <svg class="arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
            </router-link>
            <router-link to="/admin/home-banners" class="content-link">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.077-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.388 1.62a15.998 15.998 0 001.128-5.78 2.25 2.25 0 00-2.245-2.4 4.5 4.5 0 00-8.4 2.245c0 .399.077.78.22 1.128m0 0a15.998 15.998 0 01-3.388 1.62m5.043.025a15.994 15.994 0 01-1.622 3.395m-3.388-1.62a15.998 15.998 0 00-1.128 5.78 2.25 2.25 0 002.245 2.4 4.5 4.5 0 008.4-2.245c0-.399-.077-.78-.22-1.128z" /></svg>
              <span>Home Banners</span>
              <svg class="arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
            </router-link>
            <router-link to="/admin/home-features" class="content-link">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25ZM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25Z" /></svg>
              <span>Home Features</span>
              <svg class="arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
            </router-link>
          </div>
        </div>

        <!-- Page Content -->
        <div class="settings-card full-width">
          <h3>Page Content</h3>
          <p class="hint">Edit content that appears on your website pages.</p>
          <div class="content-tabs">
            <button
              v-for="tab in contentTabs"
              :key="tab.key"
              :class="['tab-btn', { active: activeContentTab === tab.key }]"
              @click="activeContentTab = tab.key"
            >
              {{ tab.label }}
            </button>
          </div>
          <div class="content-editor">
            <label>{{ contentTabs.find(t => t.key === activeContentTab)?.label }}</label>
            <textarea
              v-model="settings[activeContentTab]"
              class="form-control content-textarea"
              rows="10"
              placeholder="Enter page content... You can use basic HTML like <p>, <h2>, <ul>, <li>, <strong>"
            ></textarea>
            <p class="hint">Supports basic HTML: &lt;p&gt;, &lt;h2&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;</p>
          </div>
        </div>

        <!-- Database Tools -->
        <div class="settings-card full-width">
          <h3>Database Tools</h3>
          <p class="hint">Export the entire database as SQL or restore from a backup.</p>
          <div class="db-tools">
            <button @click="exportDb" :disabled="dbLoading" class="btn btn-primary">
              {{ dbLoading === 'export' ? 'Exporting...' : 'Export Database (SQL)' }}
            </button>
            <div class="import-section">
              <input type="file" ref="sqlFile" accept=".sql" @change="handleFileChange" class="form-control" />
              <button @click="importDb" :disabled="!selectedFile || dbLoading" class="btn btn-secondary">
                {{ dbLoading === 'import' ? 'Importing...' : 'Import Database' }}
              </button>
            </div>
          </div>
          <p v-if="dbMessage" :class="['db-message', dbMessageType]">{{ dbMessage }}</p>
        </div>
      </div>

      <div class="save-bar">
        <button @click="saveSettings" class="btn btn-primary" :disabled="saving">
          {{ saving ? 'Saving...' : 'Save All Settings' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const settings = ref({
  free_shipping_threshold: 1000,
  site_name: 'Glow & Glam',
  site_phone: '',
  site_email: '',
  site_address: '',
  site_logo: '',
  about_content: '',
  faq_content: '',
  shipping_content: '',
  returns_content: '',
  size_guide_content: '',
});
const heroSlides = ref([]);
const saving = ref(false);
const dbLoading = ref(false);
const dbMessage = ref('');
const dbMessageType = ref('');
const selectedFile = ref(null);
const sqlFile = ref(null);
const logoFile = ref(null);
const logoPreview = ref('');
const slideImageFiles = ref({});
const homeBanners = ref({ cosmetics: '', fashion: '' });
const inputRefs = {}; // plain object for file inputs
const activeContentTab = ref('about_content');
const contentTabs = [
  { key: 'about_content', label: 'About Us' },
  { key: 'faq_content', label: 'FAQs' },
  { key: 'shipping_content', label: 'Shipping Info' },
  { key: 'returns_content', label: 'Returns & Exchanges' },
  { key: 'size_guide_content', label: 'Size Guide' },
];

function setInputRef(el, index) {
  if (el) inputRefs[index] = el;
}

function triggerInput(index) {
  inputRefs[index]?.click();
}
const cosmeticsInput = ref(null);
const fashionInput = ref(null);
const defaultFeatures = [
  { icon: '🚚', title: 'Free Shipping', description: 'On orders over ৳1000' },
  { icon: '💵', title: 'Cash on Delivery', description: 'Pay when you receive' },
  { icon: '🎁', title: 'Gift Wrapping', description: 'Beautiful gift packages' },
  { icon: '↩️', title: 'Easy Returns', description: '7-day return policy' }
];
const homeFeatures = ref([...defaultFeatures]);

async function fetchSettings() {
  try {
    const res = await axios.get('/admin/settings/all');
    const data = res.data;
    for (const group of Object.values(data)) {
      for (const item of group) {
        const val = item.type === 'number' ? parseFloat(item.value) : (item.type === 'json' ? JSON.parse(item.value) : item.value);
        if (item.key === 'hero_slides') {
          heroSlides.value = val || [];
        } else if (item.key === 'home_banners') {
          homeBanners.value = val || { cosmetics: '', fashion: '' };
        } else if (item.key === 'home_features') {
          homeFeatures.value = val && val.length > 0 ? val : [...defaultFeatures];
        } else {
          settings.value[item.key] = val;
        }
      }
    }
  } catch (e) {
    console.error('Failed to load settings:', e);
  }
}

function addSlide() {
  heroSlides.value.push({ image: '', title: '', subtitle: '', description: '', button_text: '', button_link: '/shop' });
}

function removeSlide(i) {
  heroSlides.value.splice(i, 1);
  delete slideImageFiles.value[i];
  delete inputRefs[i];
}

function addFeature() {
  homeFeatures.value.push({ icon: '✨', title: '', description: '' });
}

function removeFeature(i) {
  homeFeatures.value.splice(i, 1);
}

async function handleSlideImageChange(e, index) {
  const file = e.target.files[0];
  if (!file) return;
  if (file.size > 2 * 1024 * 1024) {
    alert('Image must be less than 2MB');
    return;
  }
  
  // Upload image immediately and get URL
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

function handleLogoChange(e) {
  const file = e.target.files[0];
  if (!file) return;
  if (file.size > 2 * 1024 * 1024) {
    alert('Logo must be less than 2MB');
    return;
  }
  logoFile.value = file;
  const reader = new FileReader();
  reader.onload = (ev) => { logoPreview.value = ev.target.result; };
  reader.readAsDataURL(file);
}

function clearLogo() {
  logoFile.value = null;
  logoPreview.value = '';
  settings.value.site_logo = '';
  if (logoInput.value) logoInput.value.value = '';
}

async function saveSettings() {
  saving.value = true;
  try {
    const payload = [];
    for (const [key, value] of Object.entries(settings.value)) {
      if (key === 'site_logo') continue; // handled separately
      const type = typeof value === 'number' ? 'number' : 'string';
      const group = key.startsWith('delivery') || key === 'free_shipping' || key === 'couriers' ? 'shipping' : 'site';
      payload.push({ key, value, type, group, label: key });
    }
    payload.push({ key: 'hero_slides', value: heroSlides.value.filter(s => s.image), type: 'json', group: 'site', label: 'Hero Slider Slides' });
    payload.push({ key: 'home_banners', value: homeBanners.value, type: 'json', group: 'site', label: 'Home Page Banners' });
    payload.push({ key: 'home_features', value: homeFeatures.value.filter(f => f.title), type: 'json', group: 'site', label: 'Home Page Features' });

    if (logoFile.value) {
      const formData = new FormData();
      formData.append('logo_file', logoFile.value);
      formData.append('settings', JSON.stringify(payload));
      await axios.post('/admin/settings', formData, { headers: { 'Content-Type': 'multipart/form-data' } });
    } else {
      await axios.post('/admin/settings', { settings: payload });
    }

    logoFile.value = null;
    if (window.$toast) window.$toast('Settings saved!', 'success');
  } catch (err) {
    if (window.$toast) window.$toast('Failed to save settings', 'error');
  } finally {
    saving.value = false;
  }
}

function handleFileChange(e) {
  selectedFile.value = e.target.files[0];
}

async function exportDb() {
  dbLoading.value = 'export';
  dbMessage.value = '';
  try {
    const res = await axios.get('/admin/db-export', { responseType: 'blob' });
    const url = window.URL.createObjectURL(new Blob([res.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', 'glowglam_backup.sql');
    document.body.appendChild(link);
    link.click();
    link.remove();
    dbMessage.value = 'Database exported successfully!';
    dbMessageType.value = 'success';
  } catch (e) {
    dbMessage.value = 'Export failed';
    dbMessageType.value = 'error';
  } finally {
    dbLoading.value = false;
  }
}

async function importDb() {
  if (!selectedFile.value) return;
  dbLoading.value = 'import';
  dbMessage.value = '';
  try {
    const formData = new FormData();
    formData.append('sql_file', selectedFile.value);
    await axios.post('/admin/db-import', formData, { headers: { 'Content-Type': 'multipart/form-data' } });
    dbMessage.value = 'Database imported successfully!';
    dbMessageType.value = 'success';
    selectedFile.value = null;
    if (sqlFile.value) sqlFile.value.value = '';
  } catch (e) {
    dbMessage.value = e.response?.data?.message || 'Import failed';
    dbMessageType.value = 'error';
  } finally {
    dbLoading.value = false;
  }
}

onMounted(fetchSettings);
</script>

<style scoped>
.admin-page { min-height: 100vh; background: var(--gray-100); }
.admin-content { padding: 2rem; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.page-header h1 { font-size: 1.875rem; }
.settings-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
.settings-card { background: var(--white); border-radius: var(--radius-xl); padding: 1.5rem; box-shadow: var(--shadow-lg); }
.settings-card.full-width { grid-column: 1 / -1; }
.settings-card h3 { font-size: 1.125rem; margin-bottom: 1.25rem; color: var(--gray-800); }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; font-size: 0.875rem; font-weight: 500; color: var(--gray-700); margin-bottom: 0.25rem; }
.form-control { width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--gray-300); border-radius: var(--radius-md); font-size: 0.875rem; }
.slides-list { display: flex; flex-direction: column; gap: 1rem; }
.slide-item { display: flex; gap: 0.75rem; align-items: flex-start; background: var(--gray-50); padding: 1rem; border-radius: var(--radius-lg); border: 1px solid var(--gray-200); }
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
.db-tools { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
.import-section { display: flex; gap: 0.5rem; align-items: center; }
.db-message { margin-top: 0.5rem; font-size: 0.875rem; }
.db-message.success { color: #10b981; }
.db-message.error { color: #ef4444; }
.btn-danger { background: #ef4444; color: white; width: 40px; border-radius: var(--radius-md); }
.save-bar { margin-top: 2rem; display: flex; justify-content: flex-end; }
.logo-upload { position: relative; }
.file-input { display: none; }
.logo-placeholder {
  border: 2px dashed var(--gray-300);
  border-radius: var(--radius-lg);
  padding: 1.5rem;
  text-align: center;
  cursor: pointer;
  transition: border-color 0.2s;
}
.logo-placeholder:hover { border-color: var(--primary-500); }
.logo-placeholder svg { width: 2rem; height: 2rem; color: var(--gray-400); margin: 0 auto 0.5rem; }
.logo-placeholder p { font-size: 0.875rem; color: var(--gray-600); }
.logo-placeholder .upload-hint { font-size: 0.75rem; color: var(--gray-400); }
.logo-preview { position: relative; display: inline-block; }
.logo-preview img { max-height: 80px; max-width: 200px; object-fit: contain; border-radius: var(--radius-md); }
.remove-logo {
  position: absolute; top: -8px; right: -8px; width: 24px; height: 24px;
  background: #ef4444; color: white; border: none; border-radius: 50%;
  cursor: pointer; font-size: 1rem; line-height: 1;
}
.features-editor { display: flex; flex-direction: column; gap: 1rem; }
.feature-edit-item { background: var(--gray-50); padding: 1rem; border-radius: var(--radius-lg); border: 1px solid var(--gray-200); }
.feature-edit-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; }
.feature-number { font-size: 0.875rem; font-weight: 600; color: var(--primary-600); }
.feature-edit-fields { display: grid; grid-template-columns: 80px 1fr 1fr; gap: 0.5rem; }
.feature-edit-fields .form-control { font-size: 0.875rem; padding: 0.5rem; }
.btn-sm { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
.banner-images-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
.banner-image-item label { display: block; font-size: 0.875rem; font-weight: 500; color: var(--gray-700); margin-bottom: 0.5rem; }
.banner-preview { position: relative; margin-bottom: 0.5rem; border-radius: var(--radius-md); overflow: hidden; }
.banner-preview img { width: 100%; height: 150px; object-fit: cover; }
.remove-banner { position: absolute; top: 0.5rem; right: 0.5rem; width: 28px; height: 28px; background: #ef4444; color: white; border: none; border-radius: 50%; cursor: pointer; font-size: 1rem; line-height: 1; }
.banner-upload .file-input { display: none; }
.banner-placeholder { display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; border: 2px dashed var(--gray-300); border-radius: var(--radius-md); cursor: pointer; color: var(--gray-500); font-size: 0.875rem; transition: all var(--transition-fast); }
.banner-placeholder:hover { border-color: var(--primary-500); color: var(--primary-600); }
.banner-placeholder svg { width: 20px; height: 20px; }

.content-links { display: flex; flex-direction: column; gap: 0.5rem; }
.content-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1rem; background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: var(--radius-lg); color: var(--gray-800); font-size: 0.875rem; font-weight: 500; transition: all var(--transition-fast); }
.content-link:hover { background: var(--primary-50); border-color: var(--primary-300); color: var(--primary-700); }
.content-link svg { width: 1.25rem; height: 1.25rem; color: var(--primary-500); }
.content-link .arrow { margin-left: auto; color: var(--gray-400); width: 1rem; height: 1rem; }

.content-tabs { display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap; }
.tab-btn { padding: 0.5rem 1rem; border-radius: var(--radius-md); font-size: 0.875rem; font-weight: 500; background: var(--gray-100); color: var(--gray-600); border: 1px solid transparent; }
.tab-btn:hover { background: var(--gray-200); }
.tab-btn.active { background: var(--primary-500); color: white; }
.content-editor label { display: block; font-size: 0.875rem; font-weight: 500; color: var(--gray-700); margin-bottom: 0.5rem; }
.content-textarea { font-family: monospace; font-size: 0.875rem; line-height: 1.6; }

@media (max-width: 768px) {
  .settings-grid { grid-template-columns: 1fr; }
  .banner-images-grid { grid-template-columns: 1fr; }
  .admin-content { padding: 1rem; }
}
</style>
