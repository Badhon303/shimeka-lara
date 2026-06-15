<template>
  <div class="admin-page">
    <div class="admin-content">
      <div class="page-header">
        <h1>Courier Management</h1>
      </div>

      <div class="settings-card">
        <h3>Available Courier Companies</h3>
        <p class="hint">These couriers will appear in the order status dropdown for admin assignment.</p>
        <div class="couriers-list">
          <div v-for="(c, i) in couriers" :key="i" class="courier-item">
            <span class="courier-number">{{ i + 1 }}</span>
            <input v-model="couriers[i]" type="text" class="form-control" placeholder="Courier name" />
            <button @click="removeCourier(i)" class="btn btn-danger">&times;</button>
          </div>
          <button @click="addCourier" class="btn btn-secondary">+ Add Courier</button>
        </div>
      </div>

      <div class="settings-card">
        <h3>Courier Charges</h3>
        <p class="hint">Default charges applied when assigning courier to an order.</p>
        <div class="form-row">
          <div class="form-group">
            <label>Inside Dhaka Charge (৳)</label>
            <input v-model.number="charges.inside" type="number" class="form-control" />
          </div>
          <div class="form-group">
            <label>Outside Dhaka Charge (৳)</label>
            <input v-model.number="charges.outside" type="number" class="form-control" />
          </div>
        </div>
      </div>

      <div class="save-bar">
        <button @click="saveCouriers" class="btn btn-primary" :disabled="saving">
          {{ saving ? 'Saving...' : 'Save Courier Settings' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const couriers = ref(['Pathao', 'RedX', 'Paperfly', 'Steadfast', 'eCourier']);
const charges = ref({ inside: 60, outside: 120 });
const saving = ref(false);

async function fetchData() {
  try {
    const res = await axios.get('/admin/settings/all');
    const data = res.data;
    for (const group of Object.values(data)) {
      for (const item of group) {
        if (item.key === 'couriers') {
          couriers.value = JSON.parse(item.value);
        } else if (item.key === 'courier_charge_inside') {
          charges.value.inside = parseFloat(item.value);
        } else if (item.key === 'courier_charge_outside') {
          charges.value.outside = parseFloat(item.value);
        }
      }
    }
  } catch (e) {
    console.error(e);
  }
}

function addCourier() {
  couriers.value.push('');
}

function removeCourier(i) {
  couriers.value.splice(i, 1);
}

async function saveCouriers() {
  saving.value = true;
  try {
    const payload = [
      { key: 'couriers', value: couriers.value.filter(c => c), type: 'json', group: 'shipping', label: 'Available Couriers' },
      { key: 'courier_charge_inside', value: charges.value.inside, type: 'number', group: 'shipping', label: 'Courier Inside Dhaka' },
      { key: 'courier_charge_outside', value: charges.value.outside, type: 'number', group: 'shipping', label: 'Courier Outside Dhaka' },
    ];
    await axios.post('/admin/settings', { settings: payload });
    if (window.$toast) window.$toast('Saved!', 'success');
  } catch (e) {
    if (window.$toast) window.$toast('Failed to save', 'error');
  } finally {
    saving.value = false;
  }
}

onMounted(fetchData);
</script>

<style scoped>
.admin-page { min-height: 100vh; background: var(--gray-100); }
.admin-content { padding: 2rem; }
.page-header { margin-bottom: 2rem; }
.page-header h1 { font-size: 1.875rem; }
.settings-card { background: var(--white); border-radius: var(--radius-xl); padding: 1.5rem; box-shadow: var(--shadow-lg); margin-bottom: 1.5rem; }
.settings-card h3 { font-size: 1.125rem; margin-bottom: 0.5rem; color: var(--gray-800); }
.hint { font-size: 0.875rem; color: var(--gray-500); margin-bottom: 1rem; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; font-size: 0.875rem; font-weight: 500; color: var(--gray-700); margin-bottom: 0.25rem; }
.form-control { width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--gray-300); border-radius: var(--radius-md); font-size: 0.875rem; }
.couriers-list { display: flex; flex-direction: column; gap: 0.5rem; }
.courier-item { display: flex; gap: 0.5rem; align-items: center; }
.courier-number { width: 24px; height: 24px; background: var(--primary-100); color: var(--primary-600); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600; }
.courier-item .form-control { flex: 1; }
.btn-danger { background: #ef4444; color: white; width: 40px; border-radius: var(--radius-md); }
.save-bar { display: flex; justify-content: flex-end; }
@media (max-width: 768px) {
  .form-row { grid-template-columns: 1fr; }
  .admin-content { padding: 1rem; }
}
</style>
