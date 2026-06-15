<template>
  <div class="profile-page">
    <div class="page-header">
      <div class="container">
        <h1>My Profile</h1>
        <p>Manage your account settings</p>
      </div>
    </div>

    <div class="container">
      <div class="profile-layout">
        <!-- Sidebar -->
        <aside class="profile-sidebar">
          <div class="profile-card">
            <div class="avatar">
              <img :src="user.avatar || '/images/avatar-placeholder.jpg'" alt="" />
              <button class="change-avatar">📷</button>
            </div>
            <h3>{{ user.name }}</h3>
            <p>{{ user.email }}</p>
          </div>
          
          <nav class="profile-nav">
            <a href="#" class="active" @click.prevent="activeTab = 'profile'">
              <span>👤</span> Profile Info
            </a>
            <a href="#" @click.prevent="activeTab = 'password'">
              <span>🔒</span> Change Password
            </a>
            <a href="#" @click.prevent="activeTab = 'addresses'">
              <span>📍</span> Addresses
            </a>
            <router-link to="/orders">
              <span>📦</span> My Orders
            </router-link>
            <a href="#" @click.prevent="logout">
              <span>🚪</span> Logout
            </a>
          </nav>
        </aside>

        <!-- Main Content -->
        <div class="profile-main">
          <!-- Profile Info -->
          <div v-if="activeTab === 'profile'" class="profile-section">
            <h2>Profile Information</h2>
            <form @submit.prevent="updateProfile">
              <div class="form-grid">
                <div class="form-group">
                  <label class="form-label">Full Name</label>
                  <input v-model="form.name" type="text" class="form-input" />
                </div>
                <div class="form-group">
                  <label class="form-label">Email</label>
                  <input v-model="form.email" type="email" class="form-input" disabled />
                </div>
                <div class="form-group">
                  <label class="form-label">Phone</label>
                  <input v-model="form.phone" type="tel" class="form-input" />
                </div>
                <div class="form-group">
                  <label class="form-label">City</label>
                  <input v-model="form.city" type="text" class="form-input" />
                </div>
                <div class="form-group full-width">
                  <label class="form-label">Address</label>
                  <textarea v-model="form.address" class="form-input" rows="3"></textarea>
                </div>
                <div class="form-group">
                  <label class="form-label">Postal Code</label>
                  <input v-model="form.postal_code" type="text" class="form-input" />
                </div>
              </div>
              <button type="submit" class="btn btn-primary" :disabled="updating">
                {{ updating ? 'Saving...' : 'Save Changes' }}
              </button>
            </form>
          </div>

          <!-- Password -->
          <div v-if="activeTab === 'password'" class="profile-section">
            <h2>Change Password</h2>
            <form @submit.prevent="updatePassword">
              <div class="form-group">
                <label class="form-label">Current Password</label>
                <input v-model="passwordForm.current_password" type="password" class="form-input" required />
              </div>
              <div class="form-group">
                <label class="form-label">New Password</label>
                <input v-model="passwordForm.password" type="password" class="form-input" required minlength="8" />
              </div>
              <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input v-model="passwordForm.password_confirmation" type="password" class="form-input" required />
              </div>
              <button type="submit" class="btn btn-primary" :disabled="updatingPassword">
                {{ updatingPassword ? 'Updating...' : 'Update Password' }}
              </button>
            </form>
          </div>

          <!-- Addresses -->
          <div v-if="activeTab === 'addresses'" class="profile-section">
            <h2>Saved Addresses</h2>
            <div class="address-list">
              <div class="address-card">
                <div class="address-header">
                  <span class="badge default">Default</span>
                  <div class="address-actions">
                    <button>Edit</button>
                    <button class="delete">Delete</button>
                  </div>
                </div>
                <p><strong>{{ user.name }}</strong></p>
                <p>{{ user.address }}</p>
                <p>{{ user.city }} - {{ user.postal_code }}</p>
                <p>Phone: {{ user.phone }}</p>
              </div>
              <button class="add-address">
                <span>+</span> Add New Address
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';

const router = useRouter();
const authStore = useAuthStore();

const user = computed(() => authStore.user);
const activeTab = ref('profile');
const updating = ref(false);
const updatingPassword = ref(false);

const form = ref({
  name: user.value?.name || '',
  email: user.value?.email || '',
  phone: user.value?.phone || '',
  address: user.value?.address || '',
  city: user.value?.city || '',
  postal_code: user.value?.postal_code || ''
});

const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
});

async function updateProfile() {
  updating.value = true;
  const result = await authStore.updateProfile(form.value);
  
  if (result.success) {
    if (window.$toast) window.$toast('Profile updated successfully!', 'success');
  } else {
    if (window.$toast) window.$toast(result.error, 'error');
  }
  
  updating.value = false;
}

async function updatePassword() {
  if (passwordForm.value.password !== passwordForm.value.password_confirmation) {
    if (window.$toast) window.$toast('Passwords do not match', 'error');
    return;
  }

  updatingPassword.value = true;
  
  try {
    await axios.put('/profile/password', passwordForm.value);
    if (window.$toast) window.$toast('Password updated!', 'success');
    passwordForm.value = { current_password: '', password: '', password_confirmation: '' };
  } catch (err) {
    if (window.$toast) window.$toast(err.response?.data?.message || 'Failed to update password', 'error');
  } finally {
    updatingPassword.value = false;
  }
}

function logout() {
  authStore.logout();
  router.push('/');
}
</script>

<style scoped>
.profile-page {
  min-height: 100vh;
  background: var(--gray-50);
}

.profile-layout {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 2rem;
  padding: 2rem 0;
}

.profile-sidebar {
  position: sticky;
  top: 100px;
  height: fit-content;
}

.profile-card {
  background: var(--white);
  border-radius: var(--radius-xl);
  padding: 1.5rem;
  text-align: center;
  box-shadow: var(--shadow-sm);
  margin-bottom: 1rem;
}

.avatar {
  position: relative;
  width: 100px;
  height: 100px;
  margin: 0 auto 1rem;
}

.avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: var(--radius-full);
}

.change-avatar {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 32px;
  height: 32px;
  background: var(--primary-500);
  border-radius: var(--radius-full);
  font-size: 0.875rem;
}

.profile-card h3 {
  font-size: 1.125rem;
  margin-bottom: 0.25rem;
}

.profile-card p {
  font-size: 0.875rem;
  color: var(--gray-500);
}

.profile-nav {
  background: var(--white);
  border-radius: var(--radius-xl);
  overflow: hidden;
  box-shadow: var(--shadow-sm);
}

.profile-nav a {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
  font-size: 0.875rem;
  color: var(--gray-600);
  transition: all var(--transition-fast);
  border-bottom: 1px solid var(--gray-100);
}

.profile-nav a:hover,
.profile-nav a.active {
  background: var(--primary-50);
  color: var(--primary-600);
}

.profile-nav a:last-child {
  border-bottom: none;
}

.profile-main {
  background: var(--white);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-sm);
}

.profile-section {
  padding: 2rem;
}

.profile-section h2 {
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--gray-200);
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.form-grid .full-width {
  grid-column: 1 / -1;
}

@media (max-width: 768px) {
  .profile-layout {
    grid-template-columns: 1fr;
  }
  .profile-sidebar {
    position: static;
  }
  .form-grid {
    grid-template-columns: 1fr;
  }
}
</style>
