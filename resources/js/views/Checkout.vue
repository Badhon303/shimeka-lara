<template>
  <div class="checkout-page">
    <div class="page-header">
      <div class="container">
        <h1>Checkout</h1>
        <p>Complete your order</p>
      </div>
    </div>

    <div class="container">
      <!-- Guest Checkout Options Modal -->
      <div v-if="showGuestModal && !authStore.isAuthenticated" class="guest-modal-overlay">
        <div class="guest-modal">
          <h2>How would you like to checkout?</h2>
          <p>Choose an option to continue with your order</p>
          
          <div class="checkout-options">
            <button @click="goToLogin" class="checkout-option">
              <span class="option-icon">🔐</span>
              <span class="option-title">Sign In</span>
              <span class="option-desc">Already have an account? Sign in for faster checkout</span>
            </button>
            
            <button @click="goToRegister" class="checkout-option">
              <span class="option-icon">✨</span>
              <span class="option-title">Create Account</span>
              <span class="option-desc">Create an account to track orders and earn rewards</span>
            </button>
            
            <button @click="continueAsGuest" class="checkout-option guest">
              <span class="option-icon">🛒</span>
              <span class="option-title">Continue as Guest</span>
              <span class="option-desc">Checkout without creating an account</span>
            </button>
          </div>
        </div>
      </div>

      <div v-if="orderPlaced" class="order-success">
        <div class="success-icon">🎉</div>
        <h2>Order Placed Successfully!</h2>
        <p>Thank you for your purchase. Your order number is <strong>{{ orderNumber }}</strong></p>
        <p>We've sent a confirmation email to {{ form.shipping_email }}</p>
        <p class="track-hint">You can track your order using the <router-link to="/track">Track Order</router-link> page.</p>
        <div class="success-actions">
          <router-link v-if="authStore.isAuthenticated" to="/orders" class="btn btn-primary">View My Orders</router-link>
          <router-link to="/shop" class="btn btn-secondary">Continue Shopping</router-link>
        </div>
      </div>

      <form v-else @submit.prevent="placeOrder" class="checkout-layout">
        <!-- Left Column - Shipping & Payment -->
        <div class="checkout-main">
          <!-- Shipping Information -->
          <div class="checkout-section">
            <h3><span class="step">1</span> Shipping Information</h3>
            
            <div class="form-grid">
              <div class="form-group">
                <label class="form-label">Full Name *</label>
                <input v-model="form.shipping_name" type="text" class="form-input" required />
              </div>
              
              <div class="form-group">
                <label class="form-label">Email *</label>
                <input v-model="form.shipping_email" type="email" class="form-input" required />
              </div>
              
              <div class="form-group">
                <label class="form-label">Phone Number *</label>
                <input v-model="form.shipping_phone" type="tel" class="form-input" required />
              </div>
              
              <div class="form-group full-width">
                <label class="form-label">Address *</label>
                <textarea v-model="form.shipping_address" class="form-input" rows="2" required></textarea>
              </div>
              
              <div class="form-group">
                <label class="form-label">City *</label>
                <input v-model="form.shipping_city" type="text" class="form-input" required />
              </div>
              
              <div class="form-group">
                <label class="form-label">Postal Code *</label>
                <input v-model="form.shipping_postal_code" type="text" class="form-input" required />
              </div>
            </div>
          </div>

          <!-- Delivery Area -->
          <div class="checkout-section">
            <h3><span class="step">2</span> Delivery Area</h3>
            <div class="delivery-options">
              <label class="delivery-option" :class="{ active: form.delivery_area === 'inside_dhaka' }">
                <input type="radio" v-model="form.delivery_area" value="inside_dhaka" />
                <div class="delivery-info">
                  <span class="delivery-name">Inside Dhaka</span>
                  <span class="delivery-charge">
                    <span v-if="settings.free_shipping_threshold > 0 && cartStore.summary.subtotal >= settings.free_shipping_threshold" class="free">FREE</span>
                    <span v-else>৳{{ settings.delivery_charge_inside_dhaka }}</span>
                  </span>
                </div>
              </label>
              <label class="delivery-option" :class="{ active: form.delivery_area === 'outside_dhaka' }">
                <input type="radio" v-model="form.delivery_area" value="outside_dhaka" />
                <div class="delivery-info">
                  <span class="delivery-name">Outside Dhaka</span>
                  <span class="delivery-charge">
                    <span v-if="settings.free_shipping_threshold > 0 && cartStore.summary.subtotal >= settings.free_shipping_threshold" class="free">FREE</span>
                    <span v-else>৳{{ settings.delivery_charge_outside_dhaka }}</span>
                  </span>
                </div>
              </label>
            </div>
            <p v-if="settings.free_shipping_threshold > 0 && cartStore.summary.subtotal >= settings.free_shipping_threshold" class="free-shipping-msg">🎉 Your order qualifies for free shipping!</p>
          </div>

          <!-- Payment Method -->
          <div class="checkout-section">
            <h3><span class="step">3</span> Payment Method</h3>
            
            <div class="payment-options">
              <label class="payment-option" :class="{ active: form.payment_method === 'cash_on_delivery' }">
                <input type="radio" v-model="form.payment_method" value="cash_on_delivery" />
                <span class="payment-icon">💵</span>
                <div class="payment-info">
                  <span class="payment-name">Cash on Delivery</span>
                  <span class="payment-desc">Pay when you receive your order</span>
                </div>
              </label>
              
            </div>
          </div>

          <!-- Order Notes -->
          <div class="checkout-section">
            <h3>Order Notes (Optional)</h3>
            <textarea v-model="form.notes" class="form-input" rows="3" placeholder="Special instructions for delivery..."></textarea>
          </div>
        </div>

        <!-- Right Column - Order Summary -->
        <div class="checkout-sidebar">
          <div class="order-summary">
            <h3>Order Summary</h3>
            
            <div class="order-items">
              <div v-for="item in cartStore.items" :key="item.id" class="order-item">
                <img :src="item.product?.featured_image || '/images/placeholder.jpg'" :alt="item.product?.name || 'Product'" />
                <div class="order-item-info">
                  <h4>{{ item.product?.name || item.name || 'Product' }}</h4>
                  <p v-if="item.variant_label" class="variant-label">{{ item.variant_label }}</p>
                  <p>Qty: {{ item.quantity }}</p>
                </div>
                <span class="order-item-price">৳{{ item.quantity * (item.display_price || item.product?.price || 0) }}</span>
              </div>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-row">
              <span>Subtotal</span>
              <span>৳{{ cartStore.summary.subtotal }}</span>
            </div>
            <div class="summary-row">
              <span>Shipping ({{ form.delivery_area === 'inside_dhaka' ? 'Inside Dhaka' : 'Outside Dhaka' }})</span>
              <span v-if="cartStore.summary.subtotal >= settings.free_shipping_threshold" class="free">Free</span>
              <span v-else>৳{{ computedShipping }}</span>
            </div>
            <div class="summary-row">
              <span>Tax</span>
              <span>৳{{ cartStore.summary.tax }}</span>
            </div>

            <!-- Coupon Section -->
            <div v-if="coupon.discount > 0" class="summary-row discount">
              <span>Discount ({{ coupon.code }})</span>
              <span>-৳{{ coupon.discount }}</span>
            </div>
            <div class="coupon-section">
              <div class="coupon-input-row">
                <input v-model="couponCode" placeholder="Enter coupon code" class="form-input" :disabled="couponLoading" />
                <button @click="applyCoupon" type="button" class="btn btn-secondary" :disabled="couponLoading || !couponCode">
                  {{ couponLoading ? '...' : 'Apply' }}
                </button>
              </div>
              <p v-if="couponError" class="coupon-error">{{ couponError }}</p>
              <p v-if="coupon.discount > 0" class="coupon-success">Coupon applied!</p>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-row total">
              <span>Total</span>
              <span>৳{{ computedTotal }}</span>
            </div>

            <button type="submit" class="btn btn-primary btn-block place-order-btn" :disabled="placingOrder">
              <span v-if="placingOrder">Processing...</span>
              <span v-else>Place Order</span>
            </button>

            <p class="secure-note">
              <span>🔒</span> Your information is secure
            </p>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useCartStore } from '../stores/cart';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';

const router = useRouter();
const cartStore = useCartStore();
const authStore = useAuthStore();

const orderPlaced = ref(false);
const orderNumber = ref('');
const placingOrder = ref(false);
const cartLoading = ref(true);
const showGuestModal = ref(true);

function goToLogin() {
  router.push('/login?redirect=/checkout');
}

function goToRegister() {
  router.push('/register?redirect=/checkout');
}

function continueAsGuest() {
  showGuestModal.value = false;
}

const settings = ref({
  delivery_charge_inside_dhaka: 60,
  delivery_charge_outside_dhaka: 120,
  free_shipping_threshold: 1000,
});

const form = ref({
  shipping_name: authStore.user?.name || '',
  shipping_email: authStore.user?.email || '',
  shipping_phone: authStore.user?.phone || '',
  shipping_address: authStore.user?.address || '',
  shipping_city: authStore.user?.city || '',
  shipping_postal_code: authStore.user?.postal_code || '',
  delivery_area: 'inside_dhaka',
  payment_method: 'cash_on_delivery',
  notes: ''
});

const computedShipping = computed(() => {
  const subtotal = cartStore.summary.subtotal;
  if (settings.value.free_shipping_threshold > 0 && subtotal >= settings.value.free_shipping_threshold) return 0;
  return form.value.delivery_area === 'inside_dhaka'
    ? settings.value.delivery_charge_inside_dhaka
    : settings.value.delivery_charge_outside_dhaka;
});

const couponCode = ref('');
const coupon = ref({ code: '', discount: 0 });
const couponLoading = ref(false);
const couponError = ref('');

const computedTotal = computed(() => {
  return cartStore.summary.subtotal + cartStore.summary.tax + computedShipping.value - coupon.value.discount;
});

async function fetchSettings() {
  try {
    const response = await axios.get('/settings');
    settings.value = { ...settings.value, ...response.data };
  } catch (e) {
    console.error('Failed to fetch settings:', e);
  }
}

async function applyCoupon() {
  couponLoading.value = true;
  couponError.value = '';
  try {
    const response = await axios.post('/coupon/validate', {
      code: couponCode.value,
      subtotal: cartStore.summary.subtotal
    });
    if (response.data.valid) {
      coupon.value = {
        code: response.data.coupon.code,
        discount: response.data.coupon.discount
      };
    }
  } catch (err) {
    coupon.value = { code: '', discount: 0 };
    couponError.value = err.response?.data?.message || 'Invalid coupon';
  } finally {
    couponLoading.value = false;
  }
}

onMounted(async () => {
  await Promise.all([cartStore.fetchCart(), fetchSettings()]);
  cartLoading.value = false;
  if (cartStore.isEmpty) {
    router.push('/cart');
  }
});

function getOrderItems() {
  return cartStore.items.map(item => ({
    product_id: item.product_id || item.product?.id,
    name: item.product?.name || item.name || 'Product',
    price: item.display_price || item.product?.price || 0,
    quantity: item.quantity,
    variant: item.variant || null
  }));
}

async function placeOrder() {
  placingOrder.value = true;

  try {
    const payload = {
      ...form.value,
      items: getOrderItems(),
      coupon_code: coupon.value.code || null
    };

    const isGuest = !authStore.isAuthenticated;
    const endpoint = isGuest ? '/guest-orders' : '/orders';
    const response = await axios.post(endpoint, payload);

    orderNumber.value = response.data.order.order_number;
    orderPlaced.value = true;
    cartStore.clearCart();
  } catch (err) {
    if (window.$toast) {
      window.$toast(err.response?.data?.message || 'Failed to place order', 'error');
    }
  } finally {
    placingOrder.value = false;
  }
}
</script>

<style scoped>
.guest-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.guest-modal {
  background: var(--white);
  border-radius: var(--radius-2xl);
  padding: 2.5rem;
  max-width: 500px;
  width: 100%;
  text-align: center;
  box-shadow: var(--shadow-xl);
}

.guest-modal h2 {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
  color: var(--gray-900);
}

.guest-modal > p {
  color: var(--gray-500);
  margin-bottom: 2rem;
}

.checkout-options {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.checkout-option {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1.5rem;
  border: 2px solid var(--gray-200);
  border-radius: var(--radius-xl);
  background: var(--white);
  cursor: pointer;
  transition: all var(--transition-fast);
  text-align: center;
}

.checkout-option:hover {
  border-color: var(--primary-500);
  background: var(--primary-50);
}

.checkout-option.guest {
  border-style: dashed;
}

.option-icon {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.option-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--gray-900);
  margin-bottom: 0.25rem;
}

.option-desc {
  font-size: 0.875rem;
  color: var(--gray-500);
}
</style>
