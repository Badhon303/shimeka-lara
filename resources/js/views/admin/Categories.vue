<template>
  <div class="admin-page">
    <div class="admin-content">
      <div class="page-header">
        <h1>Categories</h1>
        <button class="btn btn-primary" @click="openModal()">+ Add Category</button>
      </div>

      <div class="admin-card">
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Type</th>
                <th>Products</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="category in categories" :key="category.id">
                <td>
                  <img
                    :src="category.image || '/images/placeholder.jpg'"
                    :alt="category.name"
                    class="category-thumb"
                  />
                </td>
                <td>{{ category.name }}</td>
                <td>
                  <span :class="['type-badge', category.type]">{{ category.type }}</span>
                </td>
                <td>{{ category.products_count || 0 }}</td>
                <td>
                  <span :class="['badge', category.is_active ? 'active' : 'inactive']">
                    {{ category.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td>
                  <button class="btn btn-sm" @click="openModal(category)">Edit</button>
                  <button class="btn btn-sm btn-danger" @click="deleteCategory(category.id)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Category Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
      <div class="modal">
        <div class="modal-header">
          <h2>{{ editingCategory.id ? 'Edit Category' : 'Add Category' }}</h2>
          <button @click="showModal = false" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Category Name *</label>
            <input v-model="form.name" type="text" class="form-control" placeholder="e.g., Skincare" />
          </div>

          <div class="form-group">
            <label>Slug</label>
            <input v-model="form.slug" type="text" class="form-control" placeholder="auto-generated-if-empty" />
            <p class="hint">Leave empty to auto-generate from name</p>
          </div>

          <div class="form-row">
            <div class="form-group form-half">
              <label>Type *</label>
              <select v-model="form.type" class="form-control">
                <option value="">Select Type</option>
                <option value="cosmetics">Cosmetics</option>
                <option value="dress">Dress / Fashion</option>
              </select>
            </div>
            <div class="form-group form-half">
              <label>Sort Order</label>
              <input v-model="form.sort_order" type="number" class="form-control" placeholder="0" />
            </div>
          </div>

          <div class="form-group">
            <label>Description</label>
            <textarea v-model="form.description" class="form-control" rows="2" placeholder="Short description..."></textarea>
          </div>

          <!-- Image Upload -->
          <div class="form-group">
            <label>Category Image</label>
            <div class="image-upload-area">
              <input
                ref="fileInput"
                type="file"
                accept="image/*"
                @change="handleFileChange"
                class="file-input"
              />
              <div v-if="imagePreview" class="image-preview">
                <img :src="imagePreview" alt="Preview" />
                <button type="button" class="remove-image" @click="clearImage">&times;</button>
              </div>
              <div v-else class="upload-placeholder" @click="$refs.fileInput.click()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6v12a2.25 2.25 0 002.25 2.25z" />
                </svg>
                <p>Click to upload image</p>
                <span class="upload-hint">JPG, PNG up to 2MB</span>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Icon (optional)</label>
            <input v-model="form.icon" type="text" class="form-control" placeholder="e.g., lucide icon name or emoji" />
          </div>

          <div class="form-group">
            <label class="checkbox-label">
              <input v-model="form.is_active" type="checkbox" />
              Active
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="showModal = false" class="btn btn-secondary">Cancel</button>
          <button @click="saveCategory" class="btn btn-primary" :disabled="saving">
            {{ saving ? 'Saving...' : (editingCategory.id ? 'Update' : 'Create') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const categories = ref([]);
const showModal = ref(false);
const editingCategory = ref({});
const saving = ref(false);
const imagePreview = ref('');
const selectedFile = ref(null);
const fileInput = ref(null);

const defaultForm = {
  name: '',
  slug: '',
  description: '',
  type: '',
  image: '',
  icon: '',
  sort_order: 0,
  is_active: true,
};

const form = ref({ ...defaultForm });

async function fetchCategories() {
  try {
    const response = await axios.get('/categories');
    categories.value = response.data || [];
  } catch (err) {
    console.error('Failed to fetch categories:', err);
  }
}

function openModal(category = null) {
  editingCategory.value = category || {};
  if (category) {
    form.value = { ...category };
    imagePreview.value = category.image || '';
  } else {
    form.value = { ...defaultForm };
    imagePreview.value = '';
  }
  selectedFile.value = null;
  showModal.value = true;
}

function handleFileChange(event) {
  const file = event.target.files[0];
  if (!file) return;

  if (file.size > 2 * 1024 * 1024) {
    alert('Image must be less than 2MB');
    return;
  }

  selectedFile.value = file;
  const reader = new FileReader();
  reader.onload = (e) => {
    imagePreview.value = e.target.result;
  };
  reader.readAsDataURL(file);
}

function clearImage() {
  selectedFile.value = null;
  imagePreview.value = '';
  form.value.image = '';
  if (fileInput.value) fileInput.value.value = '';
}

async function saveCategory() {
  if (!form.value.name || !form.value.type) {
    alert('Name and Type are required');
    return;
  }

  saving.value = true;

  try {
    const data = new FormData();
    data.append('name', form.value.name);
    data.append('type', form.value.type);
    data.append('description', form.value.description || '');
    data.append('sort_order', form.value.sort_order || 0);
    data.append('is_active', form.value.is_active ? 1 : 0);

    if (form.value.slug) data.append('slug', form.value.slug);
    if (form.value.icon) data.append('icon', form.value.icon);
    if (selectedFile.value) {
      data.append('image_file', selectedFile.value);
    } else if (form.value.image) {
      data.append('image', form.value.image);
    }

    if (editingCategory.value.id) {
      // Laravel needs _method=PUT for FormData
      data.append('_method', 'PUT');
      await axios.post(`/admin/categories/${editingCategory.value.id}`, data, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    } else {
      await axios.post('/admin/categories', data, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    }

    showModal.value = false;
    await fetchCategories();
  } catch (err) {
    console.error('Save failed:', err);
    alert(err.response?.data?.message || 'Failed to save category');
  } finally {
    saving.value = false;
  }
}

async function deleteCategory(id) {
  if (!confirm('Are you sure you want to delete this category?')) return;

  try {
    await axios.delete(`/admin/categories/${id}`);
    await fetchCategories();
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to delete category');
  }
}

onMounted(fetchCategories);
</script>

<style scoped>
.admin-page {
  min-height: 100vh;
  background: var(--gray-100);
}

.admin-content {
  padding: 2rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 1.875rem;
}

.admin-card {
  background: var(--white);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
}

.table-responsive {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid var(--gray-100);
}

th {
  background: var(--gray-50);
  font-weight: 500;
  color: var(--gray-600);
}

.category-thumb {
  width: 48px;
  height: 48px;
  object-fit: cover;
  border-radius: var(--radius-md);
}

.badge {
  padding: 0.25rem 0.75rem;
  border-radius: var(--radius-full);
  font-size: 0.75rem;
  font-weight: 500;
}

.badge.active {
  background: #d1fae5;
  color: #065f46;
}

.badge.inactive {
  background: #fee2e2;
  color: #991b1b;
}

.type-badge {
  padding: 0.25rem 0.5rem;
  border-radius: var(--radius-md);
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.type-badge.cosmetics {
  background: #fce7f3;
  color: #be185d;
}

.type-badge.dress {
  background: #dbeafe;
  color: #1e40af;
}

.btn-sm {
  padding: 0.375rem 0.75rem;
  font-size: 0.75rem;
}

.btn-danger {
  background: #ef4444;
  color: white;
  margin-left: 0.5rem;
}

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 100;
  padding: 1rem;
}

.modal {
  background: var(--white);
  border-radius: var(--radius-xl);
  width: 100%;
  max-width: 520px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: var(--shadow-xl);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--gray-100);
}

.modal-header h2 {
  font-size: 1.25rem;
  font-weight: 600;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: var(--gray-400);
  cursor: pointer;
}

.modal-body {
  padding: 1.5rem;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
  border-top: 1px solid var(--gray-100);
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--gray-700);
  margin-bottom: 0.375rem;
}

.form-control {
  width: 100%;
  padding: 0.625rem 0.875rem;
  border: 1px solid var(--gray-200);
  border-radius: var(--radius-md);
  font-size: 0.875rem;
}

.form-control:focus {
  outline: none;
  border-color: var(--primary-500);
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.hint {
  font-size: 0.75rem;
  color: var(--gray-400);
  margin-top: 0.25rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

/* Image Upload */
.image-upload-area {
  position: relative;
}

.file-input {
  display: none;
}

.upload-placeholder {
  border: 2px dashed var(--gray-300);
  border-radius: var(--radius-lg);
  padding: 2rem;
  text-align: center;
  cursor: pointer;
  transition: border-color 0.2s;
}

.upload-placeholder:hover {
  border-color: var(--primary-500);
}

.upload-placeholder svg {
  width: 2.5rem;
  height: 2.5rem;
  color: var(--gray-400);
  margin: 0 auto 0.5rem;
}

.upload-placeholder p {
  font-size: 0.875rem;
  color: var(--gray-600);
  margin-bottom: 0.25rem;
}

.upload-hint {
  font-size: 0.75rem;
  color: var(--gray-400);
}

.image-preview {
  position: relative;
  display: inline-block;
}

.image-preview img {
  width: 120px;
  height: 120px;
  object-fit: cover;
  border-radius: var(--radius-lg);
}

.remove-image {
  position: absolute;
  top: -8px;
  right: -8px;
  width: 24px;
  height: 24px;
  background: #ef4444;
  color: white;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  font-size: 1rem;
  line-height: 1;
}
</style>
