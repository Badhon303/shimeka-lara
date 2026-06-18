<template>
  <div class="admin-page">
    <div class="admin-header">
      <h1>Coupons</h1>
      <button @click="showModal = true; editingCoupon = null; resetForm()" class="btn btn-primary">
        + Add Coupon
      </button>
    </div>

    <div v-if="loading" class="loading">Loading...</div>

    <div v-else-if="coupons.length === 0" class="empty-state">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="width: 64px; height: 64px; color: var(--gray-300); margin-bottom: 1rem;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H4.5a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A7.125 7.125 0 0 0 4.875 12M12 4.875a7.125 7.125 0 0 1 7.125 7.125M12 4.875v14.25" />
      </svg>
      <h3>No coupons yet</h3>
      <p>Create your first coupon to offer discounts to customers.</p>
      <button @click="showModal = true; resetForm()" class="btn btn-primary">Create Coupon</button>
    </div>

    <div v-else class="table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>Code</th>
            <th>Type</th>
            <th>Value</th>
            <th>Min Order</th>
            <th>Usage</th>
            <th>Expires</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="c in coupons" :key="c.id">
            <td><strong>{{ c.code }}</strong><br><small>{{ c.description }}</small></td>
            <td>{{ c.type === 'percentage' ? '%' : 'Fixed' }}</td>
            <td>{{ c.type === 'percentage' ? c.value + '%' : '৳' + c.value }}</td>
            <td>৳{{ c.min_order_amount }}</td>
            <td>{{ c.usage_count }}/{{ c.usage_limit || '∞' }}</td>
            <td>{{ c.expires_at ? formatDate(c.expires_at) : 'Never' }}</td>
            <td>
              <span :class="['badge', c.is_active ? 'badge-success' : 'badge-danger']">
                {{ c.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td>
              <button @click="editCoupon(c)" class="action-btn edit">Edit</button>
              <button @click="deleteCoupon(c.id)" class="action-btn delete">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
      <div class="modal">
        <h3>{{ editingCoupon ? 'Edit' : 'Add' }} Coupon</h3>
        <form @submit.prevent="saveCoupon">
          <div class="form-group">
            <label>Code *</label>
            <input v-model="form.code" class="form-input" required placeholder="e.g. SAVE20" />
          </div>
          <div class="form-group">
            <label>Description</label>
            <input v-model="form.description" class="form-input" placeholder="e.g. 20% off on all items" />
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Type *</label>
              <select v-model="form.type" class="form-input">
                <option value="percentage">Percentage (%)</option>
                <option value="fixed">Fixed Amount (৳)</option>
              </select>
            </div>
            <div class="form-group">
              <label>Value *</label>
              <input v-model.number="form.value" type="number" class="form-input" required min="0" />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Min Order Amount</label>
              <input v-model.number="form.min_order_amount" type="number" class="form-input" min="0" />
            </div>
            <div class="form-group">
              <label>Max Discount</label>
              <input v-model.number="form.max_discount" type="number" class="form-input" min="0" placeholder="No limit" />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Usage Limit</label>
              <input v-model.number="form.usage_limit" type="number" class="form-input" min="1" placeholder="Unlimited" />
            </div>
            <div class="form-group">
              <label>Status</label>
              <select v-model="form.is_active" class="form-input">
                <option :value="true">Active</option>
                <option :value="false">Inactive</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Starts At</label>
              <input v-model="form.starts_at" type="datetime-local" class="form-input" />
            </div>
            <div class="form-group">
              <label>Expires At</label>
              <input v-model="form.expires_at" type="datetime-local" class="form-input" />
            </div>
          </div>
          <div class="modal-actions">
            <button type="button" @click="showModal = false" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const coupons = ref([]);
const loading = ref(true);
const showModal = ref(false);
const editingCoupon = ref(null);

const form = ref({
  code: '', description: '', type: 'percentage', value: 0,
  min_order_amount: 0, max_discount: null, usage_limit: null,
  starts_at: '', expires_at: '', is_active: true
});

function resetForm() {
  form.value = {
    code: '', description: '', type: 'percentage', value: 0,
    min_order_amount: 0, max_discount: null, usage_limit: null,
    starts_at: '', expires_at: '', is_active: true
  };
}

function editCoupon(c) {
  editingCoupon.value = c;
  form.value = { ...c };
  showModal.value = true;
}

async function fetchCoupons() {
  try {
    const res = await axios.get('/admin/coupons');
    coupons.value = res.data;
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
}

async function saveCoupon() {
  try {
    const payload = { ...form.value };
    if (editingCoupon.value) {
      await axios.put(`/admin/coupons/${editingCoupon.value.id}`, payload);
    } else {
      await axios.post('/admin/coupons', payload);
    }
    showModal.value = false;
    fetchCoupons();
  } catch (e) {
    alert(e.response?.data?.message || 'Error saving coupon');
  }
}

async function deleteCoupon(id) {
  if (!confirm('Delete this coupon?')) return;
  try {
    await axios.delete(`/admin/coupons/${id}`);
    fetchCoupons();
  } catch (e) {
    alert('Failed to delete');
  }
}

function formatDate(d) {
  return new Date(d).toLocaleDateString();
}

onMounted(fetchCoupons);
</script>

<style scoped>
.admin-page { padding: 2rem; max-width: 1200px; margin: 0 auto; }
.admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.admin-header h1 { font-size: 1.875rem; font-weight: 700; color: var(--gray-900); }

.table-container { background: var(--white); border-radius: var(--radius-xl); box-shadow: var(--shadow-lg); overflow: hidden; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table thead { background: var(--gray-50); }
.data-table th { padding: 0.875rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--gray-500); border-bottom: 1px solid var(--gray-200); }
.data-table td { padding: 1rem; font-size: 0.875rem; color: var(--gray-700); border-bottom: 1px solid var(--gray-100); vertical-align: top; }
.data-table tbody tr:hover { background: var(--gray-50); }
.data-table td:first-child { min-width: 160px; }
.data-table td:first-child strong { font-weight: 600; color: var(--gray-900); }
.data-table td:first-child small { color: var(--gray-500); font-size: 0.75rem; }

.badge { display: inline-block; padding: 0.25rem 0.625rem; border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 500; }
.badge-success { background: #dcfce7; color: #166534; }
.badge-danger { background: #fee2e2; color: #991b1b; }

.action-btn { padding: 0.375rem 0.75rem; border-radius: var(--radius-md); font-size: 0.75rem; font-weight: 500; cursor: pointer; border: none; margin-right: 0.5rem; }
.action-btn.edit { background: var(--primary-50); color: var(--primary-600); }
.action-btn.edit:hover { background: var(--primary-100); }
.action-btn.delete { background: #fee2e2; color: #991b1b; }
.action-btn.delete:hover { background: #fecaca; }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 2000; padding: 1rem; }
.modal { background: var(--white); border-radius: var(--radius-xl); width: 100%; max-width: 520px; max-height: 90vh; overflow-y: auto; padding: 1.5rem; box-shadow: var(--shadow-2xl); }
.modal h3 { font-size: 1.25rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--gray-900); }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; font-size: 0.875rem; font-weight: 500; color: var(--gray-700); margin-bottom: 0.25rem; }
.form-input { width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--gray-300); border-radius: var(--radius-md); font-size: 0.875rem; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.modal-actions { display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem; }

.loading { text-align: center; padding: 3rem; color: var(--gray-500); }

@media (max-width: 768px) {
  .admin-page { padding: 1rem; }
  .data-table { display: block; overflow-x: auto; }
  .form-row { grid-template-columns: 1fr; }
}
</style>
