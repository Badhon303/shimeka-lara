<template>
  <div class="product-card" :class="{ compact: compact }">
    <div class="product-image">
      <img :src="product.featured_image || '/images/placeholder.jpg'" :alt="product.name" />
      
      <div class="product-badges">
        <span v-if="product.is_new" class="product-badge badge-new">New</span>
        <span v-if="product.discount_percentage > 0" class="product-badge badge-sale">-{{ product.discount_percentage }}%</span>
        <span v-if="product.is_featured" class="product-badge badge-featured">Featured</span>
        <router-link v-if="hasVariants" :to="`/product/${product.slug}`" class="product-badge badge-options">Options</router-link>
      </div>

      <div class="product-actions">
        <button @click="quickView" class="action-btn" title="Quick View">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
          </svg>
        </button>
        <button @click="addToWishlist" class="action-btn" title="Add to Wishlist">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
          </svg>
        </button>
      </div>

      <button @click="addToCart" class="add-to-cart-btn" :disabled="loading">
        <span v-if="loading">...</span>
        <span v-else-if="hasVariants">Select Options</span>
        <span v-else>Add to Cart</span>
      </button>
    </div>

    <div class="product-info">
      <router-link :to="`/category/${product.category?.slug}`" class="product-category">
        {{ product.category?.name }}
      </router-link>
      <router-link :to="`/product/${product.slug}`" class="product-name-link">
        {{ product.name }}
      </router-link>
      <div class="product-rating" v-if="product.rating > 0">
        <span class="stars">{{ '★'.repeat(Math.round(product.rating)) }}</span>
        <span class="rating-count">({{ product.review_count }})</span>
      </div>
      <div class="product-price">
        <span class="price-current">৳{{ product.price }}</span>
        <span v-if="product.compare_price > product.price" class="price-original">৳{{ product.compare_price }}</span>
      </div>
    </div>

    <!-- Quick View Modal -->
    <div v-if="showQuickView" class="quickview-overlay" @click.self="showQuickView = false">
      <div class="quickview-modal">
        <button @click="showQuickView = false" class="quickview-close">&times;</button>
        <div class="quickview-body">
          <img :src="product.featured_image || '/images/placeholder.jpg'" :alt="product.name" />
          <div class="quickview-details">
            <router-link :to="`/category/${product.category?.slug}`" class="qv-category">{{ product.category?.name }}</router-link>
            <h3>{{ product.name }}</h3>
            <div class="qv-price">
              <span class="qv-current">৳{{ product.price }}</span>
              <span v-if="product.compare_price > product.price" class="qv-original">৳{{ product.compare_price }}</span>
            </div>
            <p class="qv-desc">{{ product.short_description || product.description }}</p>
            <div v-if="hasVariants" class="qv-variants">
              <span class="qv-variant-badge">Has Options - View Product</span>
            </div>
            <div class="qv-actions">
              <button @click="addToCart" class="btn btn-primary" :disabled="loading">
                <span v-if="loading">...</span>
                <span v-else-if="hasVariants">Select Options</span>
                <span v-else>Add to Cart</span>
              </button>
              <button @click="goToProduct" class="btn btn-secondary">View Details</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useCartStore } from '../stores/cart';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const props = defineProps({
  product: { type: Object, required: true },
  compact: { type: Boolean, default: false }
});

const cartStore = useCartStore();
const authStore = useAuthStore();
const router = useRouter();
const loading = ref(false);
const showQuickView = ref(false);

const hasVariants = computed(() => {
  return props.product?.attributes && Object.keys(props.product.attributes).length > 0;
});

async function addToCart() {
  if (hasVariants.value) {
    router.push(`/product/${props.product.slug}`);
    return;
  }
  loading.value = true;
  const result = await cartStore.addToCart(props.product, 1);
  loading.value = false;

  if (window.$toast) {
    window.$toast(result.message, result.success ? 'success' : 'error');
  }
}

function addToWishlist() {
  if (!authStore.isAuthenticated) {
    router.push('/login');
    return;
  }
  if (window.$toast) {
    window.$toast('Added to wishlist!', 'success');
  }
}

function quickView() {
  showQuickView.value = true;
}

function goToProduct() {
  showQuickView.value = false;
  setTimeout(() => {
    router.push(`/product/${props.product.slug}`);
  }, 150);
}
</script>

<style scoped>
.product-card.compact .product-image {
  aspect-ratio: 3/4;
}

.product-card.compact .product-name {
  font-size: 0.875rem;
}

.product-badges {
  position: absolute;
  top: 0.75rem;
  left: 0.75rem;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.product-rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}

.stars {
  color: #fbbf24;
  font-size: 0.875rem;
}

.rating-count {
  font-size: 0.75rem;
  color: var(--gray-500);
}

.product-category {
  display: block;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--primary-500);
  margin-bottom: 0.25rem;
}

.product-name-link {
  display: block;
  font-size: 1rem;
  font-weight: 500;
  color: var(--gray-800);
  margin-bottom: 0.5rem;
  line-height: 1.4;
  text-decoration: none;
}

.product-name-link:hover {
  color: var(--primary-600);
}

.add-to-cart-btn {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 0.875rem;
  background: var(--primary-500);
  color: var(--white);
  font-weight: 600;
  opacity: 0;
  transform: translateY(100%);
  transition: all var(--transition-base);
  z-index: 20;
  pointer-events: auto;
}

.product-card:hover .add-to-cart-btn {
  opacity: 1;
  transform: translateY(0);
}

.add-to-cart-btn:hover:not(:disabled) {
  background: var(--primary-600);
}

.add-to-cart-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.product-actions {
  position: absolute;
  bottom: 3.5rem;
  left: 0;
  right: 0;
  padding: 1rem;
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  opacity: 0;
  transform: translateY(20px);
  transition: all var(--transition-base);
  background: linear-gradient(to top, rgba(0,0,0,0.5), transparent);
  z-index: 20;
  pointer-events: auto;
}

.product-card:hover .product-actions {
  opacity: 1;
  transform: translateY(0);
}

.action-btn {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--white);
  border-radius: var(--radius-full);
  color: var(--gray-700);
  transition: all var(--transition-fast);
  pointer-events: auto;
  cursor: pointer;
  z-index: 25;
  position: relative;
}

.action-btn:hover {
  background: var(--primary-500);
  color: var(--white);
}

.action-btn svg {
  width: 1.25rem;
  height: 1.25rem;
  pointer-events: none;
}

/* Quick View Modal */
.quickview-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 2000; padding: 1rem; }
.quickview-modal { background: var(--white); border-radius: var(--radius-2xl); width: 100%; max-width: 700px; max-height: 90vh; overflow-y: auto; position: relative; box-shadow: var(--shadow-2xl); }
.quickview-close { position: absolute; top: 1rem; right: 1rem; width: 32px; height: 32px; background: var(--gray-100); border: none; border-radius: var(--radius-full); font-size: 1.25rem; cursor: pointer; z-index: 10; }
.quickview-close:hover { background: var(--gray-200); }
.quickview-body { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
.quickview-body img { width: 100%; height: 100%; object-fit: cover; min-height: 300px; }
.quickview-details { padding: 1.5rem; display: flex; flex-direction: column; justify-content: center; }
.qv-category { font-size: 0.75rem; text-transform: uppercase; color: var(--primary-500); letter-spacing: 0.05em; margin-bottom: 0.5rem; text-decoration: none; }
.quickview-details h3 { font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--gray-900); }
.qv-price { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; }
.qv-current { font-size: 1.25rem; font-weight: 700; color: var(--primary-600); }
.qv-original { font-size: 1rem; color: var(--gray-400); text-decoration: line-through; }
.qv-desc { font-size: 0.875rem; color: var(--gray-600); line-height: 1.6; margin-bottom: 1rem; }
.qv-variant-badge { display: inline-block; padding: 0.375rem 0.75rem; background: var(--primary-50); color: var(--primary-600); border-radius: var(--radius-full); font-size: 0.75rem; margin-bottom: 1rem; }
.qv-actions { display: flex; gap: 0.75rem; }
.qv-actions .btn { flex: 1; padding: 0.75rem; text-align: center; border-radius: var(--radius-md); font-weight: 600; }
.qv-actions .btn-primary { background: var(--primary-500); color: var(--white); }
.qv-actions .btn-secondary { background: var(--gray-100); color: var(--gray-800); }

@media (max-width: 640px) {
  .quickview-body { grid-template-columns: 1fr; }
  .quickview-body img { min-height: 200px; }
}
</style>
