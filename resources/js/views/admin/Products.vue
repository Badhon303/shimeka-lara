<template>
  <div class="admin-page">
    <div class="admin-content">
      <div class="page-header">
        <h1>Products</h1>
        <button class="btn btn-primary" @click="openModal()">+ Add Product</button>
      </div>

      <div class="admin-card">
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Variants</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="product in products" :key="product.id">
                <td>
                  <div class="product-cell">
                    <img :src="product.featured_image || '/images/placeholder.jpg'" :alt="product.name" />
                    <span>{{ product.name }}</span>
                  </div>
                </td>
                <td>{{ product.category?.name }}</td>
                <td>৳{{ product.price }}</td>
                <td>{{ product.stock_quantity }}</td>
                <td>
                  <span v-if="product.attributes" class="variant-badge">
                    {{ Object.keys(product.attributes).join(', ') }}
                  </span>
                  <span v-else class="text-muted">None</span>
                </td>
                <td>
                  <span :class="['badge', product.is_active ? 'active' : 'inactive']">
                    {{ product.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td>
                  <button class="btn btn-sm" @click="openModal(product)">Edit</button>
                  <button class="btn btn-sm btn-danger" @click="deleteProduct(product.id)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Product Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
      <div class="modal modal-lg">
        <div class="modal-header">
          <h2>{{ editingProduct.id ? 'Edit Product' : 'Add Product' }}</h2>
          <button @click="showModal = false" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group form-half">
              <label>Product Name *</label>
              <input v-model="form.name" type="text" class="form-control" placeholder="e.g., Glow Brightening Face Wash" />
            </div>
            <div class="form-group form-half">
              <label>Category *</label>
              <select v-model="form.category_id" class="form-control">
                <option value="">Select Category</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group form-third">
              <label>Price (৳) *</label>
              <input v-model="form.price" type="number" class="form-control" />
            </div>
            <div class="form-group form-third">
              <label>Compare Price</label>
              <input v-model="form.compare_price" type="number" class="form-control" />
            </div>
            <div class="form-group form-third">
              <label>Stock Qty *</label>
              <input v-model="form.stock_quantity" type="number" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label>Featured Image URL</label>
            <input v-model="form.featured_image" type="text" class="form-control" placeholder="https://placehold.co/400x400/hex/color?text=Name" />
          </div>

          <div class="form-group">
            <label>Short Description</label>
            <input v-model="form.short_description" type="text" class="form-control" />
          </div>

          <div class="form-group">
            <label>Full Description *</label>
            <textarea v-model="form.description" class="form-control" rows="3"></textarea>
          </div>

          <div class="form-row">
            <div class="form-group form-half">
              <label>Brand</label>
              <input v-model="form.brand" type="text" class="form-control" />
            </div>
            <div class="form-group form-half">
              <label>Tags (comma separated)</label>
              <input v-model="form.tags" type="text" class="form-control" placeholder="skincare, face, glow" />
            </div>
          </div>

          <div class="form-row checkbox-row">
            <label class="checkbox-label"><input v-model="form.is_active" type="checkbox" /> Active</label>
            <label class="checkbox-label"><input v-model="form.is_featured" type="checkbox" /> Featured</label>
            <label class="checkbox-label"><input v-model="form.is_new" type="checkbox" /> New Arrival</label>
          </div>

          <!-- Variants Section -->
          <div class="variants-section">
            <h3>Variants (Optional)</h3>
            <p class="hint">Add options like Size, Color, Shade. Each option can have multiple values.</p>

            <div v-for="(attr, attrIndex) in variantAttributes" :key="attrIndex" class="variant-attr">
              <div class="attr-header">
                <input v-model="attr.name" type="text" class="form-control attr-name" placeholder="e.g., Size, Color" />
                <button type="button" class="btn btn-sm btn-danger" @click="removeAttribute(attrIndex)">Remove</button>
              </div>
              <div class="attr-values">
                <div v-for="(val, valIndex) in attr.values" :key="valIndex" class="value-chip">
                  <input v-model="attr.values[valIndex]" type="text" class="form-control chip-input" placeholder="Value" />
                  <button type="button" class="btn-icon" @click="removeValue(attrIndex, valIndex)">&times;</button>
                </div>
                <button type="button" class="btn btn-sm btn-secondary" @click="addValue(attrIndex)">+ Add Value</button>
              </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline" @click="addAttribute">+ Add Option (Size/Color/etc)</button>

            <!-- Variant combinations table -->
            <div v-if="variantCombinations.length > 0" class="combinations-table">
              <h4>Variant Combinations</h4>
              <table>
                <thead>
                  <tr>
                    <th v-for="key in combinationKeys" :key="key">{{ key }}</th>
                    <th>Price (৳)</th>
                    <th>Stock</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(combo, i) in variantCombinations" :key="i">
                    <td v-for="key in combinationKeys" :key="key">{{ combo[key] }}</td>
                    <td><input v-model.number="combo.price" type="number" class="form-control sm" /></td>
                    <td><input v-model.number="combo.stock" type="number" class="form-control sm" /></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="showModal = false" class="btn btn-secondary">Cancel</button>
          <button @click="saveProduct" class="btn btn-primary" :disabled="saving">
            {{ saving ? 'Saving...' : (editingProduct.id ? 'Update Product' : 'Create Product') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const products = ref([]);
const categories = ref([]);
const showModal = ref(false);
const editingProduct = ref({});
const saving = ref(false);
const variantAttributes = ref([]);

const defaultForm = {
  name: '',
  description: '',
  short_description: '',
  price: 0,
  compare_price: 0,
  stock_quantity: 0,
  category_id: '',
  featured_image: '',
  brand: 'Glow & Glam',
  tags: '',
  is_active: true,
  is_featured: false,
  is_new: false,
};

const form = ref({ ...defaultForm });

// Generate all combinations from attributes
const variantCombinations = computed(() => {
  const attrs = variantAttributes.value.filter(a => a.name && a.values.length > 0);
  if (attrs.length === 0) return [];

  const keys = attrs.map(a => a.name);
  const values = attrs.map(a => a.values.filter(v => v));

  function combine(arr, prefix = {}) {
    if (arr.length === 0) return [prefix];
    const [first, ...rest] = arr;
    const key = keys[keys.length - arr.length];
    return first.flatMap(v => combine(rest, { ...prefix, [key]: v }));
  }

  return combine(values).map(combo => ({
    ...combo,
    price: Number(form.value.price) || 0,
    stock: 10,
  }));
});

const combinationKeys = computed(() => {
  return variantAttributes.value.filter(a => a.name).map(a => a.name);
});

function addAttribute() {
  variantAttributes.value.push({ name: '', values: [''] });
}

function removeAttribute(index) {
  variantAttributes.value.splice(index, 1);
}

function addValue(attrIndex) {
  variantAttributes.value[attrIndex].values.push('');
}

function removeValue(attrIndex, valIndex) {
  variantAttributes.value[attrIndex].values.splice(valIndex, 1);
}

function openModal(product = null) {
  console.log('openModal called with:', product);
  editingProduct.value = product || {};
  if (product) {
    form.value = {
      name: product.name || '',
      description: product.description || '',
      short_description: product.short_description || '',
      price: product.price || 0,
      compare_price: product.compare_price || 0,
      stock_quantity: product.stock_quantity || 0,
      category_id: product.category_id || '',
      featured_image: product.featured_image || '',
      brand: product.brand || 'Glow & Glam',
      tags: product.tags || '',
      is_active: !!product.is_active,
      is_featured: !!product.is_featured,
      is_new: !!product.is_new,
    };
    // Parse existing variants
    if (product.attributes) {
      variantAttributes.value = Object.entries(product.attributes).map(([name, values]) => ({
        name,
        values: Array.isArray(values) ? [...values] : [values]
      }));
    } else {
      variantAttributes.value = [];
    }
  } else {
    form.value = { ...defaultForm };
    variantAttributes.value = [];
  }
  showModal.value = true;
}

async function saveProduct() {
  if (!form.value.name || !form.value.category_id) {
    if (window.$toast) window.$toast('Name and Category are required', 'error');
    return;
  }

  saving.value = true;

  try {
    // Build attributes and variants from variantAttributes
    const attrs = {};
    variantAttributes.value.forEach(a => {
      if (a.name && a.values.filter(v => v).length > 0) {
        attrs[a.name] = a.values.filter(v => v);
      }
    });

    const payload = {
      ...form.value,
      attributes: Object.keys(attrs).length > 0 ? attrs : null,
      variants: variantCombinations.value.length > 0 ? variantCombinations.value : null,
    };

    if (editingProduct.value.id) {
      await axios.put(`/admin/products/${editingProduct.value.id}`, payload);
      if (window.$toast) window.$toast('Product updated!', 'success');
    } else {
      await axios.post('/admin/products', payload);
      if (window.$toast) window.$toast('Product created!', 'success');
    }

    showModal.value = false;
    await fetchProducts();
  } catch (err) {
    const msg = err.response?.data?.message || 'Failed to save product';
    if (window.$toast) window.$toast(msg, 'error');
  } finally {
    saving.value = false;
  }
}

async function deleteProduct(id) {
  if (!confirm('Are you sure you want to delete this product?')) return;
  try {
    await axios.delete(`/admin/products/${id}`);
    if (window.$toast) window.$toast('Product deleted!', 'success');
    await fetchProducts();
  } catch (err) {
    if (window.$toast) window.$toast('Failed to delete', 'error');
  }
}

async function fetchProducts() {
  try {
    const response = await axios.get('/products?per_page=50');
    products.value = response.data.data || [];
  } catch (err) {
    console.error('Failed to fetch products:', err);
  }
}

async function fetchCategories() {
  try {
    const response = await axios.get('/categories');
    categories.value = response.data || [];
  } catch (err) {
    console.error('Failed to fetch categories:', err);
  }
}

onMounted(() => {
  fetchProducts();
  fetchCategories();
});
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

.product-cell {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.product-cell img {
  width: 50px;
  height: 50px;
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
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal {
  background: var(--white);
  border-radius: var(--radius-2xl);
  width: 100%;
  max-width: 700px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: var(--shadow-xl);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid var(--gray-200);
}

.modal-header h2 {
  font-size: 1.25rem;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: var(--gray-500);
}

.modal-body {
  padding: 1.5rem;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
  border-top: 1px solid var(--gray-200);
}

/* Form */
.form-row {
  display: flex;
  gap: 1rem;
  margin-bottom: 0;
}

.form-half { flex: 1 1 50%; }
.form-third { flex: 1 1 33%; }

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--gray-700);
  margin-bottom: 0.25rem;
}

.form-control {
  width: 100%;
  padding: 0.625rem 0.875rem;
  border: 1px solid var(--gray-300);
  border-radius: var(--radius-md);
  font-size: 0.875rem;
}

.form-control:focus {
  outline: none;
  border-color: var(--primary-500);
}

.form-control.sm {
  padding: 0.375rem 0.5rem;
  font-size: 0.75rem;
  width: 100px;
}

.checkbox-row {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 1rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  cursor: pointer;
}

/* Variants */
.variants-section {
  border-top: 1px solid var(--gray-200);
  padding-top: 1.5rem;
  margin-top: 1rem;
}

.variants-section h3 {
  font-size: 1.125rem;
  margin-bottom: 0.25rem;
}

.variants-section .hint {
  font-size: 0.875rem;
  color: var(--gray-500);
  margin-bottom: 1rem;
}

.variant-attr {
  background: var(--gray-50);
  border-radius: var(--radius-lg);
  padding: 1rem;
  margin-bottom: 1rem;
}

.attr-header {
  display: flex;
  gap: 0.5rem;
  align-items: center;
  margin-bottom: 0.75rem;
}

.attr-name {
  flex: 1;
  max-width: 200px;
}

.attr-values {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  align-items: center;
}

.value-chip {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.chip-input {
  width: 100px;
  padding: 0.375rem 0.5rem;
}

.btn-icon {
  background: none;
  border: none;
  color: var(--gray-500);
  cursor: pointer;
  font-size: 1.25rem;
  line-height: 1;
}

.btn-secondary {
  background: var(--gray-200);
  color: var(--gray-700);
  border: none;
  padding: 0.375rem 0.75rem;
  border-radius: var(--radius-md);
  font-size: 0.75rem;
  cursor: pointer;
}

.btn-outline {
  background: var(--white);
  color: var(--primary-600);
  border: 1px dashed var(--primary-300);
  padding: 0.5rem 1rem;
  border-radius: var(--radius-md);
  font-size: 0.875rem;
  cursor: pointer;
}

/* Combinations Table */
.combinations-table {
  margin-top: 1.5rem;
  overflow-x: auto;
}

.combinations-table h4 {
  font-size: 1rem;
  margin-bottom: 0.75rem;
}

.combinations-table table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.875rem;
}

.combinations-table th,
.combinations-table td {
  padding: 0.5rem;
  text-align: left;
  border-bottom: 1px solid var(--gray-200);
}

.combinations-table th {
  background: var(--gray-50);
  font-weight: 500;
}

/* Badges */
.variant-badge {
  display: inline-block;
  background: #e0e7ff;
  color: #3730a3;
  padding: 0.25rem 0.5rem;
  border-radius: var(--radius-full);
  font-size: 0.75rem;
  font-weight: 500;
}

.text-muted {
  color: var(--gray-400);
  font-size: 0.875rem;
}

@media (max-width: 640px) {
  .form-row {
    flex-direction: column;
    gap: 0;
  }
  .modal {
    max-width: 100%;
    margin: 0.5rem;
  }
}
</style>
