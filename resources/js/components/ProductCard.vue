<template>
  <div class="product-card" :class="{ compact: compact }">
    <div class="product-image">
      <img :src="product.featured_image || '/images/placeholder.jpg'" :alt="product.name" />
      
      <div class="product-badges">
        <span v-if="product.is_new" class="product-badge badge-new">New</span>
        <span v-if="product.discount_percentage > 0" class="product-badge badge-sale">-{{ product.discount_percentage }}%</span>
        <span v-if="product.is_featured" class="product-badge badge-featured">Featured</span>
        <router-link v-if="hasVariants" :to="`/product/${product.slug}`" class="product-badge badge-options">View</router-link>
      </div>

      <div class="product-actions">
        <button @click="toggleWishlist" class="action-btn" :class="{ active: isInWishlist }" :title="isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist'">
          <svg xmlns="http://www.w3.org/2000/svg" :fill="isInWishlist ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
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

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
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

const hasVariants = computed(() => {
  return props.product?.attributes && Object.keys(props.product.attributes).length > 0;
});

const inWishlist = ref(false);
function checkWishlist() {
  const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
  inWishlist.value = wishlist.some(item => item.id === props.product.id);
}
const isInWishlist = computed(() => inWishlist.value);

onMounted(() => {
  checkWishlist();
  window.addEventListener('wishlist-update', checkWishlist);
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

function toggleWishlist() {
  const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
  const index = wishlist.findIndex(item => item.id === props.product.id);
  
  if (index >= 0) {
    wishlist.splice(index, 1);
    localStorage.setItem('wishlist', JSON.stringify(wishlist));
    if (window.$toast) window.$toast('Removed from wishlist', 'info');
  } else {
    wishlist.push({
      id: props.product.id,
      name: props.product.name,
      slug: props.product.slug,
      price: props.product.price,
      featured_image: props.product.featured_image,
      category: props.product.category
    });
    localStorage.setItem('wishlist', JSON.stringify(wishlist));
    if (window.$toast) window.$toast('Added to wishlist!', 'success');
  }
  // Trigger reactivity across components
  window.dispatchEvent(new CustomEvent('wishlist-update'));
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

.action-btn.active {
  background: #ef4444;
  color: white;
  border-color: #ef4444;
}

/* Mobile: Always show action buttons */
@media (max-width: 768px) {
  .product-actions {
    opacity: 1;
    transform: translateY(0);
    bottom: 0.5rem;
    padding: 0.5rem;
    background: none;
    justify-content: flex-end;
  }
  .action-btn {
    width: 36px;
    height: 36px;
    box-shadow: var(--shadow-md);
  }
  .add-to-cart-btn {
    opacity: 1;
    transform: translateY(0);
    position: relative;
    padding: 0.625rem;
    font-size: 0.875rem;
  }
}
</style>
