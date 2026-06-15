<template>
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-grid">
        <!-- Brand -->
        <div class="footer-brand">
          <router-link to="/" class="footer-logo">
            <span class="logo-glow">Glow</span>
            <span class="logo-amp">&</span>
            <span class="logo-glam">Glam</span>
          </router-link>
          <p class="footer-tagline">Your Destination for Beauty & Fashion</p>
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
            <li><router-link to="/category/skincare">Skincare</router-link></li>
            <li><router-link to="/category/makeup">Makeup</router-link></li>
            <li><router-link to="/category/dresses">Dresses</router-link></li>
            <li><router-link to="/category/tops">Tops & T-shirts</router-link></li>
            <li><router-link to="/category/ethnic">Ethnic Wear</router-link></li>
          </ul>
        </div>

        <!-- Customer Service -->
        <div class="footer-section">
          <h4>Customer Service</h4>
          <ul>
            <li><router-link to="/contact">Contact Us</router-link></li>
            <li><router-link to="/faq">FAQs</router-link></li>
            <li><router-link to="/shipping">Shipping Info</router-link></li>
            <li><router-link to="/returns">Returns & Exchanges</router-link></li>
            <li><router-link to="/size-guide">Size Guide</router-link></li>
          </ul>
        </div>

        <!-- About -->
        <div class="footer-section">
          <h4>About</h4>
          <ul>
            <li><router-link to="/about">About Us</router-link></li>
            <li><router-link to="/careers">Careers</router-link></li>
            <li><router-link to="/press">Press</router-link></li>
            <li><router-link to="/sustainability">Sustainability</router-link></li>
          </ul>
        </div>

        <!-- Newsletter -->
        <div class="footer-section newsletter">
          <h4>Stay in the Glow</h4>
          <p>Subscribe for exclusive offers and beauty tips!</p>
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
        <p>&copy; {{ new Date().getFullYear() }} Glow & Glam. All rights reserved.</p>
        <div class="payment-methods">
          <span>We Accept:</span>
          <div class="payment-icons">
            <span class="payment-icon">💳</span>
            <span class="payment-icon">📱</span>
            <span class="payment-icon">💰</span>
          </div>
        </div>
      </div>
      <div class="footer-developer">
        <p>Developed by <a href="https://metasoftinfo.com" target="_blank" rel="noopener noreferrer">Metasoft Info Solutions</a></p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue';

const email = ref('');
const loading = ref(false);
const message = ref('');
const messageType = ref('');

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
</script>
