<template>
  <div class="auth-page">
    <div class="auth-container">
      <div class="auth-card">
        <div class="auth-header">
          <router-link to="/" class="logo">
            <img v-if="siteLogo" :src="siteLogo" :alt="siteName" class="logo-img" />
            <span v-else class="logo-text">{{ siteName }}</span>
          </router-link>
          <h1>Create Account</h1>
          <p>Join us and discover amazing products</p>
        </div>

        <form @submit.prevent="handleRegister" class="auth-form">
          <div class="form-group">
            <label class="form-label">Full Name</label>
            <input 
              v-model="form.name" 
              type="text" 
              class="form-input" 
              placeholder="John Doe"
              required
            />
          </div>

          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input 
              v-model="form.email" 
              type="email" 
              class="form-input" 
              placeholder="you@example.com"
              required
            />
          </div>

          <div class="form-group">
            <label class="form-label">Phone Number</label>
            <input 
              v-model="form.phone" 
              type="tel" 
              class="form-input" 
              placeholder="01XXXXXXXXX"
            />
          </div>

          <div class="form-group">
            <label class="form-label">Password</label>
            <div class="password-input">
              <input 
                v-model="form.password" 
                :type="showPassword ? 'text' : 'password'" 
                class="form-input" 
                placeholder="••••••••"
                required
                minlength="8"
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
            <p class="password-hint">Must be at least 8 characters</p>
          </div>

          <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <input 
              v-model="form.password_confirmation" 
              type="password" 
              class="form-input" 
              placeholder="••••••••"
              required
            />
          </div>

          <div class="form-group">
            <label class="terms-checkbox">
              <input type="checkbox" v-model="form.terms" required />
              <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></span>
            </label>
          </div>

          <button type="submit" class="btn btn-primary btn-block" :disabled="loading">
            <span v-if="loading">Creating account...</span>
            <span v-else>Create Account</span>
          </button>

          <div v-if="error" class="error-message">{{ error }}</div>
        </form>

        <div class="social-login">
          <p>Or register with</p>
          <div class="social-buttons">
            <button class="social-btn google">
              <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
              </svg>
              Google
            </button>
            <button class="social-btn facebook">
              <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
              Facebook
            </button>
          </div>
        </div>

        <div class="auth-footer">
          <p>Already have an account? <router-link to="/login">Sign in</router-link></p>
        </div>
      </div>

      <div class="auth-image">
        <img src="https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=800&q=80" alt="Beauty" />
        <div class="image-overlay">
          <h3>Start Your Journey</h3>
          <p>Create an account for exclusive offers</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import axios from 'axios';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const form = ref({
  name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  terms: false
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

async function handleRegister() {
  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'Passwords do not match';
    return;
  }

  loading.value = true;
  error.value = '';

  const result = await authStore.register(form.value);

  if (result.success) {
    // Redirect to the page user came from, or home
    const redirectTo = route.query.redirect || '/';
    router.push(redirectTo);
  } else {
    error.value = result.error;
  }

  loading.value = false;
}
</script>

<style scoped>
.logo {
  display: flex;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.logo-img {
  max-height: 50px;
  max-width: 180px;
  object-fit: contain;
}

.logo-text {
  font-size: 1.75rem;
  font-weight: 700;
  background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.password-hint {
  font-size: 0.75rem;
  color: var(--gray-500);
  margin-top: 0.25rem;
}

.terms-checkbox {
  display: flex;
  align-items: flex-start;
  gap: 0.5rem;
  font-size: 0.875rem;
}

.terms-checkbox input {
  margin-top: 0.25rem;
}

.terms-checkbox a {
  color: var(--primary-600);
}

.social-login {
  margin-top: 1.5rem;
  text-align: center;
}

.social-login p {
  font-size: 0.875rem;
  color: var(--gray-500);
  margin-bottom: 0.75rem;
}

.social-buttons {
  display: flex;
  gap: 0.75rem;
}

.social-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.625rem;
  background: var(--white);
  border: 1px solid var(--gray-200);
  border-radius: var(--radius-md);
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all var(--transition-fast);
}

.social-btn:hover {
  background: var(--gray-50);
}

.social-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

.social-btn.google { color: #EA4335; }
.social-btn.facebook { color: #1877F2; }

.auth-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  background: linear-gradient(135deg, var(--primary-50), var(--primary-100));
}

.auth-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  max-width: 1000px;
  width: 100%;
  background: var(--white);
  border-radius: var(--radius-2xl);
  overflow: hidden;
  box-shadow: var(--shadow-xl);
}

.auth-card {
  padding: 3rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.auth-header {
  text-align: center;
  margin-bottom: 2rem;
}

.auth-header h1 {
  font-size: 1.875rem;
  margin-bottom: 0.5rem;
}

.auth-header p {
  color: var(--gray-500);
}

.auth-image {
  position: relative;
}

.auth-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.image-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 3rem;
  background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
  color: var(--white);
}

.image-overlay h3 {
  font-size: 1.5rem;
  color: var(--white);
  margin-bottom: 0.5rem;
}

.auth-footer {
  margin-top: 2rem;
  text-align: center;
  font-size: 0.875rem;
}

.auth-footer a {
  color: var(--primary-600);
  font-weight: 500;
}

.btn-block {
  width: 100%;
  padding: 1rem;
  font-size: 1rem;
}

.form-group {
  margin-bottom: 1.25rem;
}

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  margin-bottom: 0.5rem;
  color: var(--gray-700);
}

.form-input {
  width: 100%;
  padding: 0.875rem 1rem;
  border: 1px solid var(--gray-300);
  border-radius: var(--radius-md);
  font-size: 0.9375rem;
  transition: all var(--transition-fast);
}

.form-input:focus {
  border-color: var(--primary-500);
  box-shadow: 0 0 0 3px var(--primary-100);
  outline: none;
}

.password-input {
  position: relative;
}

.toggle-password {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--gray-400);
  background: none;
  border: none;
  cursor: pointer;
}

.toggle-password svg {
  width: 1.25rem;
  height: 1.25rem;
}

.error-message {
  margin-top: 1rem;
  padding: 0.75rem;
  background: #fef2f2;
  color: #dc2626;
  border-radius: var(--radius-md);
  font-size: 0.875rem;
}

@media (max-width: 768px) {
  .auth-container {
    grid-template-columns: 1fr;
  }
  .auth-image {
    display: none;
  }
  .auth-card {
    padding: 2rem;
  }
}
</style>
