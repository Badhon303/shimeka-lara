import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';
import { useCartStore } from './cart';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const loading = ref(false);
  const error = ref(null);

  const isAuthenticated = computed(() => !!user.value);

  async function login(credentials) {
    loading.value = true;
    error.value = null;
    
    try {
      const response = await axios.post('/login', credentials);
      const { token, user: userData } = response.data;
      
      localStorage.setItem('token', token);
      localStorage.setItem('user', JSON.stringify(userData));
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

      user.value = userData;

      // Merge guest cart after login
      const cartStore = useCartStore();
      await cartStore.mergeGuestCart();

      return { success: true };
    } catch (err) {
      error.value = err.response?.data?.message || 'Login failed';
      return { success: false, error: error.value };
    } finally {
      loading.value = false;
    }
  }

  async function register(data) {
    loading.value = true;
    error.value = null;
    
    try {
      const response = await axios.post('/register', data);
      const { token, user: userData } = response.data;
      
      localStorage.setItem('token', token);
      localStorage.setItem('user', JSON.stringify(userData));
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

      user.value = userData;

      // Merge guest cart after register
      const cartStore = useCartStore();
      await cartStore.mergeGuestCart();

      return { success: true };
    } catch (err) {
      error.value = err.response?.data?.message || 'Registration failed';
      return { success: false, error: error.value };
    } finally {
      loading.value = false;
    }
  }

  async function fetchUser() {
    try {
      const response = await axios.get('/user');
      user.value = response.data;
      localStorage.setItem('user', JSON.stringify(response.data));
    } catch (err) {
      logout();
    }
  }

  async function logout() {
    try {
      await axios.post('/logout');
    } catch (err) {
      // Ignore error
    }
    
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    delete axios.defaults.headers.common['Authorization'];
    user.value = null;
  }

  async function updateProfile(data) {
    try {
      const response = await axios.put('/profile', data);
      user.value = response.data.user;
      localStorage.setItem('user', JSON.stringify(user.value));
      return { success: true };
    } catch (err) {
      return { success: false, error: err.response?.data?.message };
    }
  }

  // Initialize from localStorage
  const storedUser = localStorage.getItem('user');
  if (storedUser) {
    user.value = JSON.parse(storedUser);
  }

  return {
    user,
    loading,
    error,
    isAuthenticated,
    login,
    register,
    logout,
    fetchUser,
    updateProfile
  };
});
