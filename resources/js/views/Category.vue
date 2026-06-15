<template>
  <div class="category-page">
    <div class="page-header">
      <div class="container">
        <h1>{{ category?.name || 'Category' }}</h1>
        <p>{{ category?.description }}</p>
      </div>
    </div>

    <div class="container">
      <div v-if="loading" class="loading-grid">
        <div v-for="i in 8" :key="i" class="skeleton-card"></div>
      </div>

      <div v-else>
        <div class="toolbar">
          <p>{{ products.length }} products</p>
          <select v-model="sortBy" @change="sortProducts">
            <option value="newest">Newest</option>
            <option value="price_low">Price: Low to High</option>
            <option value="price_high">Price: High to Low</option>
          </select>
        </div>

        <div v-if="products.length === 0" class="empty-state">
          <p>No products found in this category.</p>
        </div>

        <div v-else class="products-grid">
          <ProductCard v-for="product in products" :key="product.id" :product="product" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import ProductCard from '../components/ProductCard.vue';

const route = useRoute();
const category = ref({});
const products = ref([]);
const loading = ref(true);
const sortBy = ref('newest');

async function fetchCategory() {
  loading.value = true;
  
  try {
    const response = await axios.get(`/category/${route.params.slug}/products`);
    category.value = response.data.category;
    products.value = response.data.products.data || [];
  } catch (err) {
    console.error('Failed to fetch category:', err);
  } finally {
    loading.value = false;
  }
}

function sortProducts() {
  const sorted = [...products.value];
  switch (sortBy.value) {
    case 'price_low':
      sorted.sort((a, b) => a.price - b.price);
      break;
    case 'price_high':
      sorted.sort((a, b) => b.price - a.price);
      break;
    case 'newest':
    default:
      sorted.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
  }
  products.value = sorted;
}

watch(() => route.params.slug, fetchCategory);

onMounted(fetchCategory);
</script>

<style scoped>
.category-page {
  min-height: 100vh;
  background: var(--gray-50);
}

.loading-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
  padding: 2rem 0;
}

.skeleton-card {
  aspect-ratio: 3/4;
  background: linear-gradient(90deg, var(--gray-100), var(--gray-200), var(--gray-100));
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: var(--radius-xl);
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

.toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 2rem 0;
}

.toolbar select {
  padding: 0.5rem 1rem;
  border: 1px solid var(--gray-300);
  border-radius: var(--radius-md);
  background: var(--white);
}

.empty-state {
  text-align: center;
  padding: 4rem;
  color: var(--gray-500);
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
  padding-bottom: 4rem;
}
</style>
