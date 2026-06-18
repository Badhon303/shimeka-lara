<template>
  <div class="contact-page">
    <div class="page-header">
      <div class="container">
        <h1>Contact Us</h1>
        <p>We'd love to hear from you</p>
      </div>
    </div>

    <div class="container">
      <div class="contact-layout">
        <div class="contact-info">
          <h2>Get in Touch</h2>
          <p>Have questions? We're here to help!</p>
          
          <div v-if="loading" class="loading-text">Loading...</div>
          <div v-else class="info-items">
            <div v-if="contact.address" class="info-item">
              <span class="icon">📍</span>
              <div>
                <h4>Address</h4>
                <p>{{ contact.address }}</p>
              </div>
            </div>
            <div v-if="contact.phone" class="info-item">
              <span class="icon">📞</span>
              <div>
                <h4>Phone</h4>
                <p>{{ contact.phone }}</p>
              </div>
            </div>
            <div v-if="contact.email" class="info-item">
              <span class="icon">✉️</span>
              <div>
                <h4>Email</h4>
                <p>{{ contact.email }}</p>
              </div>
            </div>
          </div>
        </div>

        <form class="contact-form" @submit.prevent="submitForm">
          <h3>Send Message</h3>
          <div class="form-group">
            <label class="form-label">Your Name</label>
            <input v-model="form.name" type="text" class="form-input" required />
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input v-model="form.email" type="email" class="form-input" required />
          </div>
          <div class="form-group">
            <label class="form-label">Message</label>
            <textarea v-model="form.message" class="form-input" rows="4" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary" :disabled="sending">
            {{ sending ? 'Sending...' : 'Send Message' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const form = ref({ name: '', email: '', message: '' });
const sending = ref(false);
const contact = ref({ email: '', phone: '', address: '' });
const loading = ref(true);

async function fetchSettings() {
  try {
    const res = await axios.get('/settings');
    contact.value = {
      email: res.data.site_email || '',
      phone: res.data.site_phone || '',
      address: res.data.site_address || '',
    };
  } catch (e) {
    console.error('Failed to load contact settings:', e);
  } finally {
    loading.value = false;
  }
}

async function submitForm() {
  sending.value = true;
  try {
    await axios.post('/contact', form.value);
    if (window.$toast) window.$toast('Message sent! We will get back to you soon.', 'success');
    form.value = { name: '', email: '', message: '' };
  } catch (e) {
    if (window.$toast) window.$toast('Failed to send message. Please try again.', 'error');
  } finally {
    sending.value = false;
  }
}

onMounted(fetchSettings);
</script>

<style scoped>
.contact-page {
  min-height: 100vh;
  background: var(--gray-50);
}

.contact-layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  padding: 4rem 0;
}

.contact-info h2 {
  font-size: 1.875rem;
  margin-bottom: 0.5rem;
}

.contact-info > p {
  color: var(--gray-500);
  margin-bottom: 2rem;
}

.info-items {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.info-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
}

.info-item .icon {
  font-size: 1.5rem;
}

.info-item h4 {
  font-size: 1rem;
  margin-bottom: 0.25rem;
}

.info-item p {
  color: var(--gray-500);
}

.contact-form {
  background: var(--white);
  padding: 2rem;
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-sm);
}

.contact-form h3 {
  margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
  .contact-layout {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
}
</style>
