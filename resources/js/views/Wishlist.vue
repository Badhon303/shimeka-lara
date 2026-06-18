<template>
  <div class="wishlist-page">
    <div class="page-header">
      <div class="container">
        <h1>My Wishlist</h1>
        <p>Your saved items</p>
      </div>
    </div>

    <div class="container">
      <div v-if="wishlist.length === 0" class="empty-wishlist">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
        </svg>
        <h3>Your wishlist is empty</h3>
        <p>Save items you love for later</p>
        <router-link to="/shop" class="btn btn-primary">Explore Products</router-link>
      </div>

      <div v-else class="products-grid">
        <ProductCard v-for="product in wishlist" :key="product.id" :product="product" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import ProductCard from '../components/ProductCard.vue';

const wishlist = ref([]);
const loading = ref(true);

function loadWishlist() {
  const stored = localStorage.getItem('wishlist');
  if (stored) {
    try {
      wishlist.value = JSON.parse(stored);
    } catch (e) {
      wishlist.value = [];
    }
  } else {
    wishlist.value = [];
  }
}

onMounted(() => {
  loadWishlist();
  loading.value = false;
  window.addEventListener('wishlist-update', loadWishlist);
});
</script>

<style scoped>
.wishlist-page {
  min-height: 100vh;
  background: var(--gray-50);
}

.empty-wishlist {
  text-align: center;
  padding: 5rem 2rem;
}

.empty-wishlist svg {
  width: 80px;
  height: 80px;
  color: var(--gray-300);
  margin-bottom: 1.5rem;
}

.empty-wishlist h3 {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
}

.empty-wishlist p {
  color: var(--gray-500);
  margin-bottom: 2rem;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
  padding: 2rem 0;
}
</style>
