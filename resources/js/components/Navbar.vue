<template>
  <nav class="navbar">
    <div class="navbar-container">
      <!-- Logo -->
      <router-link to="/" class="logo">
        <img v-if="siteLogo" :src="siteLogo" :alt="siteName" class="logo-img" />
        <template v-else>
          <span class="logo-glow">{{ siteName }}</span>
        </template>
      </router-link>

      <!-- Desktop Navigation -->
      <div class="nav-links desktop-only">
        <router-link to="/" class="nav-link" active-class="active">Home</router-link>
        <div v-if="cosmeticsCategories.length > 0" class="dropdown">
          <span class="nav-link">Cosmetics</span>
          <div class="dropdown-menu">
            <router-link
              v-for="cat in cosmeticsCategories"
              :key="cat.id"
              :to="`/category/${cat.slug}`"
            >{{ cat.name }}</router-link>
          </div>
        </div>
        <div v-if="fashionCategories.length > 0" class="dropdown">
          <span class="nav-link">Fashion</span>
          <div class="dropdown-menu">
            <router-link
              v-for="cat in fashionCategories"
              :key="cat.id"
              :to="`/category/${cat.slug}`"
            >{{ cat.name }}</router-link>
          </div>
        </div>
        <router-link to="/shop" class="nav-link" active-class="active">Shop All</router-link>
        <router-link to="/track" class="nav-link" active-class="active">Track Order</router-link>
      </div>

      <!-- Icons -->
      <div class="nav-icons">
        <button class="icon-btn search-btn" @click="showSearch = true">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
          </svg>
        </button>

        <router-link v-if="authStore.isAuthenticated" to="/wishlist" class="icon-btn">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
          </svg>
        </router-link>

        <button class="icon-btn cart-btn" @click="cartStore.toggleDrawer()">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
          </svg>
          <span v-if="cartStore.itemCount > 0" class="cart-badge">{{ cartStore.itemCount }}</span>
        </button>

        <router-link to="/track" class="icon-btn" title="Track Order">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
          </svg>
        </router-link>

        <div v-if="authStore.isAuthenticated" class="dropdown user-dropdown">
          <button class="icon-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
          </button>
          <div class="dropdown-menu right">
            <router-link to="/profile">My Profile</router-link>
            <router-link to="/orders">My Orders</router-link>
            <router-link v-if="authStore.user?.is_admin" to="/admin">Admin Panel</router-link>
            <hr />
            <a href="#" @click.prevent="logout">Logout</a>
          </div>
        </div>

      <router-link v-else to="/login" class="icon-btn">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
          </svg>
        </router-link>

        <button class="icon-btn mobile-only" @click="mobileMenuOpen = !mobileMenuOpen">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Menu -->
    <div v-if="mobileMenuOpen" class="mobile-menu">
      <router-link to="/" @click="mobileMenuOpen = false">Home</router-link>
      <router-link to="/shop" @click="mobileMenuOpen = false">Shop All</router-link>
      <router-link to="/track" @click="mobileMenuOpen = false">Track Order</router-link>
      <div v-if="cosmeticsCategories.length > 0" class="mobile-category">
        <strong>Cosmetics</strong>
        <router-link
          v-for="cat in cosmeticsCategories"
          :key="cat.id"
          :to="`/category/${cat.slug}`"
          @click="mobileMenuOpen = false"
        >{{ cat.name }}</router-link>
      </div>
      <div v-if="fashionCategories.length > 0" class="mobile-category">
        <strong>Fashion</strong>
        <router-link
          v-for="cat in fashionCategories"
          :key="cat.id"
          :to="`/category/${cat.slug}`"
          @click="mobileMenuOpen = false"
        >{{ cat.name }}</router-link>
      </div>
    </div>

    <!-- Search Modal -->
    <div v-if="showSearch" class="search-modal" @click.self="showSearch = false">
      <div class="search-container">
        <input 
          v-model="searchQuery" 
          @input="handleSearch"
          type="text" 
          placeholder="Search products..." 
          class="search-input"
          ref="searchInput"
        />
        <button class="close-search" @click="showSearch = false">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
          </svg>
        </button>
        
        <div v-if="searchResults.length > 0" class="search-results">
          <router-link 
            v-for="product in searchResults" 
            :key="product.id"
            :to="`/product/${product.slug}`"
            @click="showSearch = false"
            class="search-result-item"
          >
            <img :src="product.featured_image || '/images/placeholder.jpg'" :alt="product.name" />
            <div>
              <h4>{{ product.name }}</h4>
              <p>৳{{ product.price }}</p>
            </div>
          </router-link>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, watch, nextTick, computed, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useCartStore } from '../stores/cart';
import { useRouter } from 'vue-router';
import axios from 'axios';

const authStore = useAuthStore();
const cartStore = useCartStore();
const router = useRouter();

const categories = ref([]);
const siteName = ref('Glow & Glam');
const siteLogo = ref('');

const cosmeticsCategories = computed(() =>
  categories.value.filter(c => c.type === 'cosmetics')
);
const fashionCategories = computed(() =>
  categories.value.filter(c => c.type === 'dress')
);

async function fetchCategories() {
  try {
    const response = await axios.get('/categories');
    categories.value = response.data || [];
  } catch (err) {
    console.error('Failed to fetch categories:', err);
  }
}

async function fetchSettings() {
  try {
    const response = await axios.get('/settings');
    const data = response.data;
    if (data.site_name) siteName.value = data.site_name;
    if (data.site_logo) siteLogo.value = data.site_logo;
  } catch (err) {
    console.error('Failed to fetch settings:', err);
  }
}

const mobileMenuOpen = ref(false);
const showSearch = ref(false);
const searchQuery = ref('');
const searchResults = ref([]);
const searchInput = ref(null);

let searchTimeout = null;

watch(showSearch, (val) => {
  if (val) {
    nextTick(() => searchInput.value?.focus());
  }
});

function handleSearch() {
  clearTimeout(searchTimeout);

  if (searchQuery.value.length < 2) {
    searchResults.value = [];
    return;
  }

  searchTimeout = setTimeout(async () => {
    try {
      const response = await axios.get(`/products/search?q=${searchQuery.value}`);
      searchResults.value = response.data;
    } catch (err) {
      searchResults.value = [];
    }
  }, 300);
}

function logout() {
  authStore.logout();
  router.push('/');
}

onMounted(() => {
  fetchCategories();
  fetchSettings();
});
</script>
