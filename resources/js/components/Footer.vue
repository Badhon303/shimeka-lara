<template>
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-grid">
        <!-- Brand -->
        <div class="footer-brand">
          <router-link to="/" class="footer-logo">
            <img v-if="siteLogo" :src="siteLogo" :alt="siteName" class="footer-logo-img" />
            <template v-else>
              <span class="logo-glow">{{ siteName }}</span>
            </template>
          </router-link>
          <p class="footer-tagline">Your Destination for Cosmetics & Fashion</p>
          <div class="social-links">
            <a href="#" target="_blank" rel="noopener"><i class="icon-facebook"></i></a>
            <a href="#" target="_blank" rel="noopener"><i class="icon-instagram"></i></a>
            <a href="#" target="_blank" rel="noopener"><i class="icon-twitter"></i></a>
            <a href="#" target="_blank" rel="noopener"><i class="icon-youtube"></i></a>
          </div>
        </div>

        <!-- Shop Links -->
        <div class="footer-section">
          <h4>Shop</h4>
          <ul>
            <li v-for="cat in categories" :key="cat.id">
              <router-link :to="`/category/${cat.slug}`">{{ cat.name }}</router-link>
            </li>
          </ul>
        </div>

        <!-- Customer Service -->
        <div class="footer-section">
          <h4>Customer Service</h4>
          <ul>
            <li><router-link to="/contact">Contact Us</router-link></li>
            <li><router-link to="/about">About Us</router-link></li>
          </ul>
        </div>

        <!-- Newsletter -->
        <div class="footer-section newsletter">
          <h4>Stay Updated</h4>
          <p>Subscribe for exclusive offers and latest updates!</p>
          <form @submit.prevent="subscribe" class="newsletter-form">
            <input 
              v-model="email" 
              type="email" 
              placeholder="Enter your email" 
              required
            />
            <button type="submit" :disabled="loading">
              {{ loading ? '...' : 'Subscribe' }}
            </button>
          </form>
          <p v-if="message" :class="['subscribe-message', messageType]">{{ message }}</p>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; {{ new Date().getFullYear() }} {{ siteName }}. All rights reserved.</p>
        <div class="payment-methods">
          <span class="cod-badge">� Cash on Delivery</span>
        </div>
      </div>
      <div class="footer-developer">
        <p>Developed by <a href="https://metasoftinfo.com" target="_blank" rel="noopener noreferrer">Metasoft Info Solutions</a></p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const email = ref('');
const loading = ref(false);
const message = ref('');
const messageType = ref('');
const categories = ref([]);
const siteName = ref('Sʜɪᴍᴇᴋᴀ');
const siteLogo = ref('');

async function fetchCategories() {
  try {
    const res = await axios.get('/categories');
    categories.value = res.data || [];
  } catch (err) {
    console.error('Failed to fetch footer categories:', err);
  }
}

async function fetchSettings() {
  try {
    const res = await axios.get('/settings');
    const data = res.data;
    if (data.site_name) siteName.value = data.site_name;
    if (data.site_logo) siteLogo.value = data.site_logo;
  } catch (err) {
    console.error('Failed to fetch footer settings:', err);
  }
}

async function subscribe() {
  loading.value = true;
  message.value = '';

  // Simulate API call
  setTimeout(() => {
    message.value = 'Thank you for subscribing!';
    messageType.value = 'success';
    email.value = '';
    loading.value = false;
  }, 1000);
}

onMounted(() => {
  fetchCategories();
  fetchSettings();
});
</script>
