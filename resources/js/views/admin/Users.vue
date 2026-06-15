<template>
  <div class="admin-page">
    <div class="admin-content">
      <div class="page-header">
        <h1>Users</h1>
        <button @click="openCreate" class="btn btn-primary">+ Add User</button>
      </div>

      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>User</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Role</th>
              <th>Status</th>
              <th>Joined</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id">
              <td>
                <div class="user-cell">
                  <img :src="user.avatar || '/images/avatar-placeholder.jpg'" :alt="user.name" />
                  <span>{{ user.name }}</span>
                </div>
              </td>
              <td>{{ user.email }}</td>
              <td>{{ user.phone || '-' }}</td>
              <td>
                <span :class="['badge', user.is_admin ? 'badge-admin' : 'badge-customer']">
                  {{ user.is_admin ? 'Admin' : 'Customer' }}
                </span>
              </td>
              <td>
                <span :class="['badge', user.status === 'active' ? 'badge-success' : 'badge-danger']">
                  {{ user.status === 'active' ? 'Active' : 'Suspended' }}
                </span>
              </td>
              <td>{{ formatDate(user.created_at) }}</td>
              <td>
                <button @click="editUser(user)" class="action-btn edit">Edit</button>
                <button v-if="user.status === 'active'" @click="toggleStatus(user)" class="action-btn suspend">Suspend</button>
                <button v-else @click="toggleStatus(user)" class="action-btn activate">Activate</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- User Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
      <div class="modal">
        <h3>{{ editingUser ? 'Edit' : 'Add' }} User</h3>
        <form @submit.prevent="saveUser">
          <div class="form-group">
            <label>Full Name *</label>
            <input v-model="form.name" class="form-control" required />
          </div>
          <div class="form-group">
            <label>Email *</label>
            <input v-model="form.email" type="email" class="form-control" required />
          </div>
          <div class="form-group">
            <label>Phone</label>
            <input v-model="form.phone" class="form-control" />
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Role</label>
              <select v-model="form.is_admin" class="form-control">
                <option :value="false">Customer</option>
                <option :value="true">Admin</option>
              </select>
            </div>
            <div class="form-group">
              <label>Status</label>
              <select v-model="form.status" class="form-control">
                <option value="active">Active</option>
                <option value="suspended">Suspended</option>
              </select>
            </div>
          </div>
          <div v-if="!editingUser" class="form-group">
            <label>Password *</label>
            <input v-model="form.password" type="password" class="form-control" required minlength="6" />
          </div>
          <div class="modal-actions">
            <button type="button" @click="showModal = false" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Saving...' : 'Save' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const users = ref([]);
const loading = ref(true);
const showModal = ref(false);
const editingUser = ref(null);
const saving = ref(false);

const form = ref({
  name: '', email: '', phone: '', is_admin: false, status: 'active', password: ''
});

function resetForm() {
  form.value = { name: '', email: '', phone: '', is_admin: false, status: 'active', password: '' };
}

function openCreate() {
  editingUser.value = null;
  resetForm();
  showModal.value = true;
}

function editUser(user) {
  editingUser.value = user;
  form.value = { name: user.name, email: user.email, phone: user.phone || '', is_admin: user.is_admin, status: user.status || 'active', password: '' };
  showModal.value = true;
}

async function fetchUsers() {
  try {
    const response = await axios.get('/admin/users');
    users.value = response.data.data || [];
  } catch (err) {
    console.error('Failed to fetch users:', err);
  } finally {
    loading.value = false;
  }
}

async function saveUser() {
  saving.value = true;
  try {
    const payload = { ...form.value };
    if (editingUser.value) {
      delete payload.password;
      await axios.put(`/admin/users/${editingUser.value.id}`, payload);
    } else {
      await axios.post('/admin/users', payload);
    }
    showModal.value = false;
    fetchUsers();
    if (window.$toast) window.$toast(editingUser.value ? 'User updated!' : 'User created!', 'success');
  } catch (err) {
    if (window.$toast) window.$toast(err.response?.data?.message || 'Error', 'error');
  } finally {
    saving.value = false;
  }
}

async function toggleStatus(user) {
  const newStatus = user.status === 'active' ? 'suspended' : 'active';
  if (!confirm(`Are you sure you want to ${newStatus} this user?`)) return;
  try {
    await axios.put(`/admin/users/${user.id}`, { status: newStatus });
    fetchUsers();
    if (window.$toast) window.$toast(`User ${newStatus}!`, 'success');
  } catch (err) {
    if (window.$toast) window.$toast('Failed to update status', 'error');
  }
}

function formatDate(date) {
  return new Date(date).toLocaleDateString();
}

onMounted(fetchUsers);
</script>

<style scoped>
.admin-page { min-height: 100vh; background: var(--gray-100); }
.admin-content { padding: 2rem; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.page-header h1 { font-size: 1.875rem; }

.table-container { background: var(--white); border-radius: var(--radius-xl); box-shadow: var(--shadow-lg); overflow: hidden; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table thead { background: var(--gray-50); }
.data-table th { padding: 0.875rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--gray-500); border-bottom: 1px solid var(--gray-200); }
.data-table td { padding: 1rem; font-size: 0.875rem; border-bottom: 1px solid var(--gray-100); vertical-align: middle; }
.data-table tbody tr:hover { background: var(--gray-50); }

.user-cell { display: flex; align-items: center; gap: 0.75rem; }
.user-cell img { width: 40px; height: 40px; object-fit: cover; border-radius: var(--radius-full); }

.badge { display: inline-block; padding: 0.25rem 0.625rem; border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 500; }
.badge-admin { background: #ede9fe; color: #5b21b6; }
.badge-customer { background: #dbeafe; color: #1e40af; }
.badge-success { background: #dcfce7; color: #166534; }
.badge-danger { background: #fee2e2; color: #991b1b; }

.action-btn { padding: 0.375rem 0.75rem; border-radius: var(--radius-md); font-size: 0.75rem; font-weight: 500; cursor: pointer; border: none; margin-right: 0.25rem; }
.action-btn.edit { background: var(--primary-50); color: var(--primary-600); }
.action-btn.suspend { background: #fef3c7; color: #92400e; }
.action-btn.activate { background: #dcfce7; color: #166534; }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 2000; padding: 1rem; }
.modal { background: var(--white); border-radius: var(--radius-xl); width: 100%; max-width: 480px; max-height: 90vh; overflow-y: auto; padding: 1.5rem; box-shadow: var(--shadow-2xl); }
.modal h3 { font-size: 1.25rem; font-weight: 600; margin-bottom: 1.5rem; }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; font-size: 0.875rem; font-weight: 500; color: var(--gray-700); margin-bottom: 0.25rem; }
.form-control { width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--gray-300); border-radius: var(--radius-md); font-size: 0.875rem; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.modal-actions { display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem; }
</style>
