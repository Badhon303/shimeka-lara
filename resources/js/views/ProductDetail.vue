<template>
  <div class="product-detail-page">
    <div v-if="loading" class="loading-container">
      <div class="skeleton-detail"></div>
    </div>
    
    <div v-else-if="product" class="container">
      <!-- Breadcrumb -->
      <nav class="breadcrumb">
        <router-link to="/">Home</router-link>
        <span>/</span>
        <router-link :to="`/category/${product.category?.slug}`">{{ product.category?.name }}</router-link>
        <span>/</span>
        <span class="current">{{ product.name }}</span>
      </nav>

      <div class="product-detail">
        <!-- Image Gallery -->
        <div class="product-gallery">
          <div class="main-image">
            <img :src="displayImage" :alt="product.name" />
            <div v-if="product.discount_percentage > 0" class="discount-sticker">
              -{{ product.discount_percentage }}%
            </div>
          </div>
          <div v-if="product.images?.length > 1" class="thumbnail-list">
            <img 
              v-for="(img, index) in product.images" 
              :key="index"
              :src="img" 
              :class="['thumbnail', { active: selectedImage === img }]"
              @click="selectedImage = img"
            />
          </div>
        </div>

        <!-- Product Info -->
        <div class="product-info">
          <span class="product-brand">{{ product.brand || 'Glow & Glam' }}</span>
          <h1 class="product-title">{{ product.name }}</h1>
          
          <div class="product-rating">
            <span class="stars">{{ '★'.repeat(Math.round(product.rating || 0)) }}</span>
            <span class="rating-count">{{ product.review_count || 0 }} reviews</span>
          </div>

          <div class="product-price-section">
            <span class="current-price">৳{{ product.price }}</span>
            <span v-if="product.compare_price > product.price" class="compare-price">৳{{ product.compare_price }}</span>
            <span v-if="product.discount_percentage > 0" class="save-badge">Save ৳{{ product.compare_price - product.price }}</span>
          </div>

          <p class="product-short-desc">{{ product.short_description }}</p>

          <!-- Variants -->
          <div v-if="hasVariants" class="variants-section">
            <div v-for="(options, key) in variantOptions" :key="key" class="variant-group">
              <label>{{ formatLabel(key) }}:</label>
              <div class="variant-options">
                <!-- Color swatches -->
                <template v-if="key.toLowerCase() === 'color'">
                  <button
                    v-for="option in options"
                    :key="option"
                    :class="['color-swatch', { active: selectedVariants[key] === option, disabled: !isVariantAvailable(key, option) }]"
                    :style="{ backgroundColor: getColorHex(option) }"
                    :title="option"
                    @click="isVariantAvailable(key, option) && selectVariant(key, option)"
                  >
                    <span v-if="selectedVariants[key] === option" class="check">✓</span>
                  </button>
                </template>
                <!-- Size / Type / Shade buttons -->
                <template v-else>
                  <button
                    v-for="option in options"
                    :key="option"
                    :class="['variant-btn', { active: selectedVariants[key] === option, disabled: !isVariantAvailable(key, option) }]"
                    @click="isVariantAvailable(key, option) && selectVariant(key, option)"
                  >
                    {{ option }}
                  </button>
                </template>
              </div>
            </div>
            <p v-if="!allVariantsSelected" class="variant-hint">
              Please select all options above
            </p>
          </div>

          <!-- Selected Variant Info -->
          <div v-if="selectedVariantData" class="selected-variant-info">
            <span class="variant-price">৳{{ selectedVariantData.price }}</span>
            <span :class="['variant-stock', selectedVariantData.stock > 5 ? 'in-stock' : 'low-stock']">
              {{ selectedVariantData.stock > 0 ? selectedVariantData.stock + ' in stock' : 'Out of stock' }}
            </span>
          </div>

          <!-- Quantity -->
          <div class="quantity-section">
            <label>Quantity:</label>
            <div class="quantity-selector">
              <button @click="quantity > 1 && quantity--" :disabled="quantity <= 1">-</button>
              <span>{{ quantity }}</span>
              <button @click="canIncreaseQty && quantity++" :disabled="!canIncreaseQty">+</button>
            </div>
            <span v-if="currentStock > 0" class="stock-status in-stock">
              ✓ {{ currentStock }} in stock
            </span>
            <span v-else class="stock-status out-stock">Out of stock</span>
          </div>

          <!-- Actions -->
          <div class="product-actions">
            <button
              @click="addToCart"
              class="btn btn-primary btn-lg"
              :disabled="addingToCart || currentStock === 0 || !allVariantsSelected"
            >
              <span v-if="addingToCart">Adding...</span>
              <span v-else-if="!allVariantsSelected">Select Options</span>
              <span v-else>Add to Cart — ৳{{ totalPrice }}</span>
            </button>
            <button @click="addToWishlist" class="btn btn-outline btn-lg wishlist-btn">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
              </svg>
            </button>
          </div>

          <!-- Features -->
          <div class="product-features">
            <div class="feature">
              <span class="icon">🚚</span>
              <span>Free shipping over ৳1000</span>
            </div>
            <div class="feature">
              <span class="icon">↩️</span>
              <span>7-day easy returns</span>
            </div>
            <div class="feature">
              <span class="icon">🔒</span>
              <span>Secure payment</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="product-tabs">
        <div class="tab-buttons">
          <button 
            v-for="tab in tabs" 
            :key="tab.id"
            :class="['tab-btn', { active: activeTab === tab.id }]"
            @click="activeTab = tab.id"
          >
            {{ tab.name }}
          </button>
        </div>

        <div class="tab-content">
          <!-- Description -->
          <div v-if="activeTab === 'description'" class="tab-panel">
            <div class="description-content" v-html="product.description"></div>
          </div>

          <!-- Reviews -->
          <div v-if="activeTab === 'reviews'" class="tab-panel">
            <div v-if="product.reviews?.length === 0" class="no-reviews">
              <p>No reviews yet. Be the first to review!</p>
            </div>
            <div v-else class="reviews-list">
              <div v-for="review in product.reviews" :key="review.id" class="review-item">
                <div class="review-header">
                  <div class="reviewer-info">
                    <img :src="review.user?.avatar || '/images/avatar-placeholder.jpg'" alt="" />
                    <span>{{ review.user?.name }}</span>
                  </div>
                  <span class="review-date">{{ formatDate(review.created_at) }}</span>
                </div>
                <div class="review-rating">{{ '★'.repeat(review.rating) }}</div>
                <p class="review-text">{{ review.comment }}</p>
              </div>
            </div>
          </div>

          <!-- Shipping -->
          <div v-if="activeTab === 'shipping'" class="tab-panel">
            <div class="shipping-info">
              <h4>Shipping Information</h4>
              <p>We ship all orders within 24-48 hours. Delivery times vary based on location:</p>
              <ul>
                <li>Dhaka City: 1-2 business days</li>
                <li>Other Districts: 2-5 business days</li>
                <li>Free shipping on orders over ৳1000</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- Related Products -->
      <div v-if="relatedProducts.length" class="related-section">
        <h3>You May Also Like</h3>
        <div class="products-grid">
          <ProductCard v-for="product in relatedProducts" :key="product.id" :product="product" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import ProductCard from '../components/ProductCard.vue';
import { useCartStore } from '../stores/cart';
import { useAuthStore } from '../stores/auth';

const route = useRoute();
const cartStore = useCartStore();
const authStore = useAuthStore();

const product = ref(null);
const relatedProducts = ref([]);
const loading = ref(true);
const addingToCart = ref(false);
const quantity = ref(1);
const selectedImage = ref(null);
const selectedVariants = ref({});
const activeTab = ref('description');

const tabs = [
  { id: 'description', name: 'Description' },
  { id: 'reviews', name: `Reviews (${product.value?.review_count || 0})` },
  { id: 'shipping', name: 'Shipping' }
];

const hasVariants = computed(() => {
  return product.value?.attributes && Object.keys(product.value.attributes).length > 0;
});

const variantOptions = computed(() => {
  // Use product.attributes for available options (e.g. {size: ['S','M'], color: ['Blue']})
  return product.value?.attributes || {};
});

// Find the matching variant from product.variants based on selected options
const selectedVariantData = computed(() => {
  if (!product.value?.variants?.length || !allVariantsSelected.value) return null;
  const sv = selectedVariants.value;
  return product.value.variants.find(v => {
    return Object.keys(sv).every(key => v[key] === sv[key]);
  }) || null;
});

// Show color-specific image if a color variant is selected
const displayImage = computed(() => {
  // If a color is selected and that variant has an image, show it
  const color = selectedVariants.value?.color;
  if (color && product.value?.variants) {
    const match = product.value.variants.find(v => v.color === color && v.image);
    if (match?.image) return match.image;
  }
  // For lipstick/shade
  const shade = selectedVariants.value?.shade;
  if (shade && product.value?.variants) {
    const match = product.value.variants.find(v => v.shade === shade && v.image);
    if (match?.image) return match.image;
  }
  return selectedImage.value || product.value?.featured_image;
});

const allVariantsSelected = computed(() => {
  if (!hasVariants.value) return true;
  const keys = Object.keys(variantOptions.value);
  return keys.every(key => selectedVariants.value[key] !== undefined);
});

const currentPrice = computed(() => {
  if (selectedVariantData.value) return selectedVariantData.value.price;
  return product.value?.price || 0;
});

const currentStock = computed(() => {
  if (selectedVariantData.value) return selectedVariantData.value.stock;
  return product.value?.stock_quantity || 0;
});

const totalPrice = computed(() => currentPrice.value * quantity.value);

const canIncreaseQty = computed(() => {
  return quantity.value < currentStock.value;
});

function formatLabel(key) {
  return key.charAt(0).toUpperCase() + key.slice(1);
}

function getColorHex(colorName) {
  const map = {
    'red': '#EF4444', 'green': '#22C55E', 'blue': '#3B82F6', 'yellow': '#EAB308',
    'pink': '#EC4899', 'purple': '#A855F7', 'orange': '#F97316', 'black': '#111827',
    'white': '#F9FAFB', 'cream': '#FEF3C7', 'brown': '#92400E', 'tan': '#D4A574',
    'navy': '#1E3A5F', 'teal': '#0D9488', 'maroon': '#7F1D1D', 'grey': '#6B7280',
    'light blue': '#BFDBFE', 'lightblue': '#BFDBFE',
  };
  return map[colorName.toLowerCase()] || '#CBD5E1';
}

function isVariantAvailable(key, option) {
  if (!product.value?.variants?.length) return true;
  // If no other variant selected yet, all are available
  const otherSelections = { ...selectedVariants.value };
  delete otherSelections[key];
  const otherKeys = Object.keys(otherSelections).filter(k => otherSelections[k] !== undefined);
  if (otherKeys.length === 0) return true;
  // Check if any variant exists with this option + other selections
  return product.value.variants.some(v => {
    if (v[key] !== option) return false;
    return otherKeys.every(k => v[k] === otherSelections[k]);
  });
}

async function fetchProduct() {
  loading.value = true;
  
  try {
    const response = await axios.get(`/product/${route.params.slug}`);
    product.value = response.data.product;
    relatedProducts.value = response.data.related_products;
    tabs[1].name = `Reviews (${product.value.review_count || 0})`;
  } catch (err) {
    console.error('Failed to fetch product:', err);
  } finally {
    loading.value = false;
  }
}

function selectVariant(key, value) {
  selectedVariants.value = { ...selectedVariants.value, [key]: value };
  // Reset quantity if current qty exceeds new variant stock
  if (selectedVariantData.value && quantity.value > selectedVariantData.value.stock) {
    quantity.value = Math.max(1, selectedVariantData.value.stock);
  }
}

async function addToCart() {
  if (hasVariants.value && !allVariantsSelected.value) {
    if (window.$toast) window.$toast('Please select all options', 'error');
    return;
  }

  addingToCart.value = true;

  const variantData = hasVariants.value ? selectedVariants.value : null;
  const variantPrice = selectedVariantData.value?.price || product.value.price;
  const result = await cartStore.addToCart(product.value, quantity.value, variantData, variantPrice);

  if (window.$toast) {
    window.$toast(result.message, result.success ? 'success' : 'error');
  }

  addingToCart.value = false;
}

function addToWishlist() {
  if (!authStore.isAuthenticated) {
    if (window.$toast) window.$toast('Please login first', 'error');
    return;
  }
  if (window.$toast) window.$toast('Added to wishlist!', 'success');
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

onMounted(fetchProduct);
</script>

<style scoped>
.variants-section {
  margin-bottom: 1.5rem;
}

.variant-group {
  margin-bottom: 1rem;
}

.variant-group label {
  display: block;
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--gray-700);
  margin-bottom: 0.5rem;
  text-transform: capitalize;
}

.variant-options {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.variant-btn {
  padding: 0.5rem 1.25rem;
  border: 1px solid var(--gray-300);
  border-radius: var(--radius-md);
  background: var(--white);
  font-size: 0.875rem;
  font-weight: 500;
  transition: all var(--transition-fast);
  cursor: pointer;
}

.variant-btn:hover:not(.disabled) {
  border-color: var(--primary-500);
  color: var(--primary-600);
}

.variant-btn.active {
  border-color: var(--primary-500);
  background: var(--primary-500);
  color: var(--white);
  box-shadow: 0 0 0 3px var(--primary-100);
}

.variant-btn.disabled {
  opacity: 0.4;
  cursor: not-allowed;
  pointer-events: none;
  text-decoration: line-through;
  background: var(--gray-100);
}

.color-swatch {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 2px solid var(--gray-300);
  cursor: pointer;
  position: relative;
  transition: all var(--transition-fast);
  flex-shrink: 0;
}

.color-swatch:hover:not(.disabled) {
  border-color: var(--primary-500);
  transform: scale(1.1);
}

.color-swatch.active {
  border-color: var(--primary-500);
  box-shadow: 0 0 0 3px var(--primary-100);
}

.color-swatch.disabled {
  opacity: 0.3;
  cursor: not-allowed;
  pointer-events: none;
}

.color-swatch .check {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 0.875rem;
  font-weight: 700;
  text-shadow: 0 1px 3px rgba(0,0,0,0.5);
}

.selected-variant-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem 1rem;
  background: var(--gray-50);
  border-radius: var(--radius-lg);
  margin-bottom: 1rem;
  border: 1px solid var(--gray-200);
}

.variant-price {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--primary-600);
}

.variant-stock {
  font-size: 0.875rem;
  font-weight: 500;
}

.variant-stock.in-stock {
  color: #059669;
}

.variant-stock.low-stock {
  color: #d97706;
}

.variant-hint {
  color: var(--primary-600);
  font-size: 0.875rem;
  margin-top: 0.5rem;
  font-weight: 500;
}

.quantity-selector button:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
</style>
