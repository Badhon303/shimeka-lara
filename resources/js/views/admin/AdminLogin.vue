<template>
  <div class="admin-login-page">
    <!-- Left Side - Branding -->
    <div class="admin-login-branding">
      <div class="branding-content">
        <div class="logo-mark">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
          </svg>
        </div>
        <img v-if="siteLogo" :src="siteLogo" :alt="siteName" class="branding-logo" />
        <h2 v-else>{{ siteName }}</h2>
        <p class="branding-tagline">Admin Dashboard</p>
        <div class="branding-divider"></div>
        <p class="branding-desc">Manage your products, orders, and customers all in one place.</p>
      </div>
      <div class="branding-pattern"></div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="admin-login-form-side">
      <div class="form-wrapper">
        <div class="form-header">
          <h1>Welcome Back</h1>
          <p>Sign in to your admin account</p>
        </div>

        <form @submit.prevent="handleLogin" class="login-form">
          <div class="input-group">
            <label>Email Address</label>
            <div class="input-wrap">
              <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
              </svg>
              <input
                v-model="form.email"
                type="email"
                placeholder="admin@example.com"
                required
              />
            </div>
          </div>

          <div class="input-group">
            <label>Password</label>
            <div class="input-wrap">
              <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
              </svg>
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Enter your password"
                required
              />
              <button type="button" @click="showPassword = !showPassword" class="toggle-password">
                <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L6.228 6.228" />
                </svg>
              </button>
            </div>
          </div>

          <button type="submit" class="login-btn" :disabled="loading">
            <span v-if="loading" class="spinner"></span>
            <span v-else>Sign In</span>
          </button>

          <div v-if="error" class="error-alert">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            {{ error }}
          </div>
        </form>

        <div class="form-footer">
          <router-link to="/" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Back to Store
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import axios from 'axios';

const router = useRouter();
const authStore = useAuthStore();

const form = ref({
  email: '',
  password: ''
});

const showPassword = ref(false);
const loading = ref(false);
const error = ref('');
const siteName = ref('Shimeka');
const siteLogo = ref('');

onMounted(async () => {
  try {
    const response = await axios.get('/settings');
    siteName.value = response.data.site_name || 'Shimeka';
    siteLogo.value = response.data.site_logo || '';
  } catch (e) {
    console.error('Failed to fetch settings:', e);
  }
});

async function handleLogin() {
  loading.value = true;
  error.value = '';

  const result = await authStore.login(form.value);

  if (result.success) {
    if (authStore.user?.is_admin) {
      router.push('/admin');
    } else {
      error.value = 'You do not have admin access.';
      await authStore.logout();
    }
  } else {
    error.value = result.error;
  }

  loading.value = false;
}
</script>

<style scoped>
.admin-login-page {
  min-height: 100vh;
  display: flex;
}

/* Left Side - Branding */
.admin-login-branding {
  flex: 1;
  background: linear-gradient(145deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.branding-pattern {
  position: absolute;
  inset: 0;
  opacity: 0.03;
  background-image:
    radial-gradient(circle at 25% 25%, white 1px, transparent 1px),
    radial-gradient(circle at 75% 75%, white 1px, transparent 1px);
  background-size: 50px 50px;
}

.branding-content {
  text-align: center;
  color: white;
  z-index: 1;
  padding: 3rem;
}

.logo-mark {
  width: 80px;
  height: 80px;
  background: rgba(255,255,255,0.1);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 2rem;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.15);
}

.logo-mark svg {
  width: 40px;
  height: 40px;
  color: #60a5fa;
}

.branding-logo {
  max-height: 60px;
  max-width: 200px;
  object-fit: contain;
  margin-bottom: 0.5rem;
}

.branding-content h2 {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  letter-spacing: -0.02em;
}

.branding-tagline {
  font-size: 1.125rem;
  color: #94a3b8;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.15em;
}

.branding-divider {
  width: 60px;
  height: 3px;
  background: linear-gradient(90deg, #60a5fa, #a78bfa);
  border-radius: 3px;
  margin: 1.5rem auto;
}

.branding-desc {
  font-size: 1rem;
  color: #64748b;
  max-width: 320px;
  line-height: 1.6;
}

/* Right Side - Form */
.admin-login-form-side {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8fafc;
  padding: 2rem;
}

.form-wrapper {
  width: 100%;
  max-width: 400px;
}

.form-header {
  margin-bottom: 2.5rem;
}

.form-header h1 {
  font-size: 1.875rem;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 0.5rem;
}

.form-header p {
  color: #64748b;
  font-size: 1rem;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.input-group label {
  display: block;
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
}

.input-wrap {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 1rem;
  width: 1.25rem;
  height: 1.25rem;
  color: #9ca3af;
  pointer-events: none;
}

.input-wrap input {
  width: 100%;
  padding: 0.875rem 1rem 0.875rem 2.75rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 0.9375rem;
  color: #1f2937;
  background: white;
  transition: all 0.2s ease;
}

.input-wrap input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
}

.input-wrap input::placeholder {
  color: #9ca3af;
}

.toggle-password {
  position: absolute;
  right: 1rem;
  background: none;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 0.25rem;
}

.toggle-password:hover {
  color: #6b7280;
}

.toggle-password svg {
  width: 1.25rem;
  height: 1.25rem;
}

.login-btn {
  width: 100%;
  padding: 0.875rem;
  background: linear-gradient(135deg, #2563eb, #4f46e5);
  color: white;
  font-size: 1rem;
  font-weight: 600;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-top: 0.5rem;
}

.login-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 10px 25px -5px rgba(37,99,235,0.4);
}

.login-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.error-alert {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.875rem 1rem;
  background: #fef2f2;
  color: #dc2626;
  border-radius: 10px;
  font-size: 0.875rem;
  font-weight: 500;
}

.error-alert svg {
  width: 1.25rem;
  height: 1.25rem;
  flex-shrink: 0;
}

.form-footer {
  margin-top: 2rem;
  text-align: center;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: #64748b;
  font-size: 0.875rem;
  font-weight: 500;
  text-decoration: none;
  transition: color 0.2s;
}

.back-link:hover {
  color: #374151;
}

.back-link svg {
  width: 1rem;
  height: 1rem;
}

/* Responsive */
@media (max-width: 900px) {
  .admin-login-branding {
    display: none;
  }

  .admin-login-form-side {
    flex: 1;
    background: linear-gradient(145deg, #0f172a 0%, #1e3a5f 100%);
  }

  .form-header h1 {
    color: white;
  }

  .form-header p {
    color: #94a3b8;
  }

  .input-group label {
    color: #e2e8f0;
  }

  .back-link {
    color: #94a3b8;
  }

  .back-link:hover {
    color: white;
  }
}
</style>
