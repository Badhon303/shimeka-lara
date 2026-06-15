<template>
  <div class="shop-page">
    <div class="page-header">
      <div class="container">
        <h1>Shop All Products</h1>
        <p>Discover our collection of cosmetics and fashion</p>
      </div>
    </div>

    <div class="container">
      <div class="shop-layout">
        <!-- Filters Sidebar -->
        <aside class="filters-sidebar">
          <div class="filter-section">
            <h4>Categories</h4>
            <div class="filter-options">
              <label v-for="category in categories" :key="category.id" class="filter-option">
                <input type="checkbox" :value="category.slug" v-model="selectedCategories">
                <span>{{ category.name }}</span>
                <span class="count">({{ category.products_count }})</span>
              </label>
            </div>
          </div>

          <div class="filter-section">
            <h4>Price Range</h4>
            <div class="price-range">
              <input type="range" v-model="maxPrice" :max="10000" min="0" step="100">
              <div class="price-labels">
                <span>৳0</span>
                <span>৳{{ maxPrice }}</span>
              </div>
            </div>
          </div>

          <div class="filter-section">
            <button @click="applyFilters" class="btn btn-primary w-full">Apply Filters</button>
            <button @click="clearFilters" class="btn btn-secondary w-full mt-2">Clear</button>
          </div>
        </aside>

        <!-- Products Grid -->
        <div class="products-area">
          <div class="toolbar">
            <p>{{ products.total || 0 }} products found</p>
            <select v-model="sortBy" @change="applySort">
              <option value="newest">Newest First</option>
              <option value="price_low">Price: Low to High</option>
              <option value="price_high">Price: High to Low</option>
              <option value="name">Name</option>
            </select>
          </div>

          <div v-if="loading" class="loading-grid">
            <div v-for="i in 8" :key="i" class="skeleton-card"></div>
          </div>

          <div v-else-if="products.data?.length === 0" class="empty-state">
            <p>No products found matching your criteria.</p>
          </div>

          <div v-else class="products-grid">
            <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
          </div>

          <!-- Pagination -->
          <div v-if="products.last_page > 1" class="pagination">
            <button 
              v-for="page in products.last_page" 
              :key="page"
              @click="goToPage(page)"
              :class="['page-btn', { active: page === products.current_page }]"
            >
              {{ page }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import ProductCard from '../components/ProductCard.vue';

const route = useRoute();
const router = useRouter();

const categories = ref([]);
const products = ref({});
const loading = ref(true);
const selectedCategories = ref([]);
const maxPrice = ref(10000);
const sortBy = ref('newest');
const currentPage = ref(1);

async function fetchCategories() {
  try {
    const response = await axios.get('/categories');
    categories.value = response.data;
  } catch (err) {
    console.error('Failed to fetch categories:', err);
  }
}

async function fetchProducts() {
  loading.value = true;
  
  try {
    const params = {
      page: currentPage.value,
      per_page: 12,
      sort: sortBy.value,
    };
    
    if (selectedCategories.value.length > 0) {
      params.category = selectedCategories.value[0];
    }
    
    if (maxPrice.value < 10000) {
      params.max_price = maxPrice.value;
    }
    
    const response = await axios.get('/products', { params });
    products.value = response.data;
  } catch (err) {
    console.error('Failed to fetch products:', err);
  } finally {
    loading.value = false;
  }
}

function applyFilters() {
  currentPage.value = 1;
  fetchProducts();
}

function clearFilters() {
  selectedCategories.value = [];
  maxPrice.value = 10000;
  sortBy.value = 'newest';
  fetchProducts();
}

function applySort() {
  fetchProducts();
}

function goToPage(page) {
  currentPage.value = page;
  fetchProducts();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

onMounted(() => {
  fetchCategories();
  fetchProducts();
});
</script>

<style scoped>
.page-header {
  background: linear-gradient(135deg, var(--primary-50), var(--primary-100));
  padding: 4rem 0;
  text-align: center;
  margin-bottom: 3rem;
}

.page-header h1 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.page-header p {
  color: var(--gray-600);
  font-size: 1.125rem;
}

.shop-layout {
  display: grid;
  grid-template-columns: 260px 1fr;
  gap: 2rem;
}

.filters-sidebar {
  position: sticky;
  top: 100px;
  height: fit-content;
}

.filter-section {
  background: var(--white);
  padding: 1.5rem;
  border-radius: var(--radius-xl);
  margin-bottom: 1rem;
  box-shadow: var(--shadow-sm);
}

.filter-section h4 {
  font-size: 1rem;
  margin-bottom: 1rem;
}

.filter-option {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 0;
  cursor: pointer;
}

.filter-option input {
  width: 18px;
  height: 18px;
  accent-color: var(--primary-500);
}

.filter-option .count {
  color: var(--gray-400);
  font-size: 0.875rem;
}

.price-range input {
  width: 100%;
  margin-bottom: 0.5rem;
}

.price-labels {
  display: flex;
  justify-content: space-between;
  font-size: 0.875rem;
  color: var(--gray-500);
}

.w-full {
  width: 100%;
}

.mt-2 {
  margin-top: 0.5rem;
}

.toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--gray-200);
}

.toolbar select {
  padding: 0.5rem 1rem;
  border: 1px solid var(--gray-300);
  border-radius: var(--radius-md);
  background: var(--white);
}

.loading-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
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

.empty-state {
  text-align: center;
  padding: 4rem;
  color: var(--gray-500);
}

.pagination {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 3rem;
}

.page-btn {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid var(--gray-300);
  border-radius: var(--radius-md);
  background: var(--white);
  transition: all var(--transition-fast);
}

.page-btn:hover,
.page-btn.active {
  background: var(--primary-500);
  color: var(--white);
  border-color: var(--primary-500);
}

@media (max-width: 768px) {
  .shop-layout {
    grid-template-columns: 1fr;
  }
  .filters-sidebar {
    position: static;
  }
}
</style>
