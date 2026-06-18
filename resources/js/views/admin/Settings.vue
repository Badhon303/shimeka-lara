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

        <!-- Hero Slider -->
        <div class="settings-card full-width">
          <h3>Hero Slider Slides</h3>
          <p class="hint">Add multiple slides for the homepage hero section. They will auto-rotate every 5 seconds.</p>
          <div class="slides-list">
            <div v-for="(slide, i) in heroSlides" :key="i" class="slide-item">
              <div class="slide-fields">
                <input v-model="slide.image" type="text" class="form-control" placeholder="Image URL" />
                <input v-model="slide.title" type="text" class="form-control" placeholder="Title" />
                <input v-model="slide.subtitle" type="text" class="form-control" placeholder="Subtitle" />
                <input v-model="slide.description" type="text" class="form-control" placeholder="Description" />
                <input v-model="slide.button_text" type="text" class="form-control" placeholder="Button Text" />
                <input v-model="slide.button_link" type="text" class="form-control" placeholder="Button Link (e.g. /shop)" />
              </div>
              <button @click="removeSlide(i)" class="btn btn-danger">&times;</button>
            </div>
            <button @click="addSlide" class="btn btn-secondary">+ Add Slide</button>
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

async function fetchSettings() {
  try {
    const res = await axios.get('/admin/settings/all');
    const data = res.data;
    for (const group of Object.values(data)) {
      for (const item of group) {
        const val = item.type === 'number' ? parseFloat(item.value) : (item.type === 'json' ? JSON.parse(item.value) : item.value);
        if (item.key === 'hero_slides') {
          heroSlides.value = val || [];
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
.slide-item { display: flex; gap: 0.5rem; align-items: flex-start; }
.slide-fields { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; flex: 1; }
.slide-fields .form-control { font-size: 0.875rem; padding: 0.5rem; }
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
@media (max-width: 768px) {
  .settings-grid { grid-template-columns: 1fr; }
  .admin-content { padding: 1rem; }
}
</style>
