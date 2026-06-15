<template>
  <div class="admin-page">
    <div class="admin-header">
      <h1>Low Stock Alert</h1>
    </div>

    <div v-if="loading" class="loading">Loading...</div>

    <div v-else class="table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in products" :key="p.id" :class="{ 'low-row': p.stock_quantity <= 5, 'out-row': p.stock_quantity === 0 }">
            <td>
              <div class="product-cell">
                <img :src="p.featured_image || '/images/placeholder.jpg'" class="product-thumb" />
                <div>
                  <strong>{{ p.name }}</strong>
                  <small v-if="p.variants?.length">{{ p.variants.length }} variants</small>
                </div>
              </div>
            </td>
            <td>{{ p.category?.name || '-' }}</td>
            <td>৳{{ p.price }}</td>
            <td>
              <span :class="['stock-badge', p.stock_quantity === 0 ? 'out' : p.stock_quantity <= 5 ? 'low' : 'ok']">
                {{ p.stock_quantity }}
              </span>
            </td>
            <td>
              <span :class="['badge', p.is_active ? 'badge-success' : 'badge-danger']">
                {{ p.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td>
              <button @click="openRefill(p)" class="action-btn edit">Refill Stock</button>
              <router-link :to="`/admin/products?edit=${p.id}`" class="action-btn edit">Edit</router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Refill Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
      <div class="modal">
        <h3>Refill Stock — {{ selectedProduct?.name }}</h3>

        <div v-if="selectedProduct?.variants?.length" class="variant-refill">
          <p class="hint">Refill stock for each variant:</p>
          <div v-for="(v, i) in selectedProduct.variants" :key="i" class="refill-row">
            <span class="variant-label">{{ Object.entries(v).filter(([k]) => !['price','stock'].includes(k)).map(([k,val]) => `${k}: ${val}`).join(', ') }}</span>
            <div class="refill-inputs">
              <span>Current: {{ v.stock }}</span>
              <input v-model.number="refillAmounts[i]" type="number" min="0" class="form-control" placeholder="Add qty" />
            </div>
          </div>
        </div>

        <div v-else class="form-group">
          <label>Current Stock: {{ selectedProduct?.stock_quantity }}</label>
          <input v-model.number="refillAmount" type="number" min="0" class="form-control" placeholder="Quantity to add" />
        </div>

        <div class="modal-actions">
          <button @click="showModal = false" class="btn btn-secondary">Cancel</button>
          <button @click="saveRefill" class="btn btn-primary" :disabled="saving">{{ saving ? 'Saving...' : 'Save' }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const products = ref([]);
const loading = ref(true);
const showModal = ref(false);
const selectedProduct = ref(null);
const refillAmount = ref(0);
const refillAmounts = ref([]);
const saving = ref(false);

async function fetchProducts() {
  try {
    const res = await axios.get('/products?low_stock=1');
    products.value = res.data.data || res.data || [];
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
}

function openRefill(p) {
  selectedProduct.value = p;
  refillAmount.value = 0;
  refillAmounts.value = p.variants?.map(() => 0) || [];
  showModal.value = true;
}

async function saveRefill() {
  saving.value = true;
  try {
    if (selectedProduct.value.variants?.length) {
      // Update variant stock
      const updatedVariants = selectedProduct.value.variants.map((v, i) => ({
        ...v,
        stock: v.stock + (refillAmounts.value[i] || 0)
      }));
      await axios.put(`/admin/products/${selectedProduct.value.id}`, {
        variants: updatedVariants
      });
    } else {
      // Update base stock
      await axios.put(`/admin/products/${selectedProduct.value.id}`, {
        stock_quantity: selectedProduct.value.stock_quantity + refillAmount.value
      });
    }
    showModal.value = false;
    fetchProducts();
    if (window.$toast) window.$toast('Stock updated!', 'success');
  } catch (e) {
    if (window.$toast) window.$toast('Failed to update stock', 'error');
  } finally {
    saving.value = false;
  }
}

onMounted(fetchProducts);
</script>

<style scoped>
.admin-page { padding: 2rem; max-width: 1200px; margin: 0 auto; }
.admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.admin-header h1 { font-size: 1.875rem; font-weight: 700; }

.table-container { background: var(--white); border-radius: var(--radius-xl); box-shadow: var(--shadow-lg); overflow: hidden; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table thead { background: var(--gray-50); }
.data-table th { padding: 0.875rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--gray-500); border-bottom: 1px solid var(--gray-200); }
.data-table td { padding: 1rem; font-size: 0.875rem; border-bottom: 1px solid var(--gray-100); vertical-align: middle; }
.data-table tbody tr:hover { background: var(--gray-50); }

.low-row { background: #fffbeb; }
.out-row { background: #fef2f2; }

.product-cell { display: flex; align-items: center; gap: 0.75rem; }
.product-thumb { width: 48px; height: 48px; object-fit: cover; border-radius: var(--radius-md); }
.product-cell strong { display: block; color: var(--gray-900); }
.product-cell small { color: var(--gray-500); font-size: 0.75rem; }

.stock-badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 600; }
.stock-badge.ok { background: #dcfce7; color: #166534; }
.stock-badge.low { background: #fef3c7; color: #92400e; }
.stock-badge.out { background: #fee2e2; color: #991b1b; }

.badge { display: inline-block; padding: 0.25rem 0.625rem; border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 500; }
.badge-success { background: #dcfce7; color: #166534; }
.badge-danger { background: #fee2e2; color: #991b1b; }

.action-btn { padding: 0.375rem 0.75rem; border-radius: var(--radius-md); font-size: 0.75rem; font-weight: 500; cursor: pointer; border: none; margin-right: 0.5rem; text-decoration: none; display: inline-block; }
.action-btn.edit { background: var(--primary-50); color: var(--primary-600); }
.action-btn.edit:hover { background: var(--primary-100); }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 2000; padding: 1rem; }
.modal { background: var(--white); border-radius: var(--radius-xl); width: 100%; max-width: 520px; max-height: 90vh; overflow-y: auto; padding: 1.5rem; box-shadow: var(--shadow-2xl); }
.modal h3 { font-size: 1.25rem; font-weight: 600; margin-bottom: 1.5rem; }

.variant-refill { display: flex; flex-direction: column; gap: 1rem; }
.refill-row { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: 0.75rem; background: var(--gray-50); border-radius: var(--radius-md); }
.variant-label { font-size: 0.875rem; font-weight: 500; }
.refill-inputs { display: flex; align-items: center; gap: 0.75rem; }
.refill-inputs span { font-size: 0.875rem; color: var(--gray-500); white-space: nowrap; }
.refill-inputs .form-control { width: 100px; }

.form-group { margin-bottom: 1rem; }
.form-group label { display: block; font-size: 0.875rem; font-weight: 500; color: var(--gray-700); margin-bottom: 0.25rem; }
.form-control { width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--gray-300); border-radius: var(--radius-md); font-size: 0.875rem; }
.hint { font-size: 0.875rem; color: var(--gray-500); margin-bottom: 1rem; }
.modal-actions { display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; }

.loading { text-align: center; padding: 3rem; color: var(--gray-500); }
</style>
