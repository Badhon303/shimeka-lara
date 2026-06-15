<template>
  <div class="cart-page">
    <div class="page-header">
      <div class="container">
        <h1>Shopping Cart</h1>
        <p v-if="cartStore.items.length > 0">
          You have {{ cartStore.itemCount }} item{{ cartStore.itemCount > 1 ? 's' : '' }} in your cart
        </p>
      </div>
    </div>

    <div class="container">
      <div v-if="cartStore.isEmpty" class="empty-cart">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
        </svg>
        <h3>Your cart is empty</h3>
        <p>Looks like you haven't added anything yet.</p>
        <router-link to="/shop" class="btn btn-primary">Continue Shopping</router-link>
      </div>

      <div v-else class="cart-layout">
        <!-- Cart Items -->
        <div class="cart-items-section">
          <div class="cart-header-row">
            <span>Product</span>
            <span>Quantity</span>
            <span>Total</span>
          </div>

          <div class="cart-items-list">
            <div v-for="item in cartStore.items" :key="item.id" class="cart-item-row">
              <div class="item-product">
                <img :src="item.product.featured_image || '/images/placeholder.jpg'" :alt="item.product.name" />
                <div class="item-info">
                  <router-link :to="`/product/${item.product.slug}`" class="item-name">
                    {{ item.product.name }}
                  </router-link>
                  <p v-if="item.variant_label" class="item-variant">{{ item.variant_label }}</p>
                  <p class="item-price">৳{{ item.display_price }}</p>
                </div>
              </div>

              <div class="item-quantity">
                <button @click="updateQuantity(item.id, item.quantity - 1)" :disabled="item.quantity <= 1">-</button>
                <span>{{ item.quantity }}</span>
                <button @click="updateQuantity(item.id, item.quantity + 1)">+</button>
              </div>

              <div class="item-total">
                <span>৳{{ item.quantity * item.display_price }}</span>
                <button @click="removeItem(item.id)" class="remove-btn">Remove</button>
              </div>
            </div>
          </div>

          <div class="cart-actions">
            <router-link to="/shop" class="btn btn-secondary">Continue Shopping</router-link>
            <button @click="clearCart" class="btn btn-outline">Clear Cart</button>
          </div>
        </div>

        <!-- Cart Summary -->
        <div class="cart-summary-section">
          <h3>Order Summary</h3>
          
          <div class="summary-row">
            <span>Subtotal</span>
            <span>৳{{ cartStore.summary.subtotal }}</span>
          </div>
          <div class="summary-row">
            <span>Tax (5%)</span>
            <span>৳{{ cartStore.summary.tax }}</span>
          </div>
          <div class="summary-row">
            <span>Shipping</span>
            <span v-if="cartStore.summary.shipping > 0">৳{{ cartStore.summary.shipping }}</span>
            <span v-else class="free-shipping">Free</span>
          </div>

          <div class="promo-code">
            <input type="text" placeholder="Enter promo code" />
            <button class="btn btn-secondary">Apply</button>
          </div>

          <div class="summary-row total">
            <span>Total</span>
            <span>৳{{ cartStore.summary.total }}</span>
          </div>

          <router-link to="/checkout" class="btn btn-primary btn-block checkout-btn">
            Proceed to Checkout
          </router-link>

          <div class="payment-icons">
            <span>🔒 Secure Checkout</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCartStore } from '../stores/cart';

const cartStore = useCartStore();

function formatVariant(variant) {
  if (!variant) return '';
  return Object.entries(variant).map(([key, value]) => `${key}: ${value}`).join(', ');
}

async function updateQuantity(itemId, quantity) {
  if (quantity < 1) return;
  await cartStore.updateQuantity(itemId, quantity);
}

async function removeItem(itemId) {
  await cartStore.removeItem(itemId);
}

async function clearCart() {
  if (confirm('Are you sure you want to clear your cart?')) {
    await cartStore.clearCart();
  }
}
</script>

<style scoped>
.cart-page {
  min-height: 100vh;
  background: var(--gray-50);
}

.empty-cart {
  text-align: center;
  padding: 5rem 2rem;
}

.empty-cart svg {
  width: 100px;
  height: 100px;
  color: var(--gray-300);
  margin-bottom: 1.5rem;
}

.empty-cart h3 {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
}

.empty-cart p {
  color: var(--gray-500);
  margin-bottom: 2rem;
}

.cart-layout {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 2rem;
  padding: 2rem 0;
}

.cart-items-section {
  background: var(--white);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
}

.cart-header-row {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  padding: 1rem 1.5rem;
  background: var(--gray-50);
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--gray-600);
}

.cart-item-row {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid var(--gray-100);
}

.item-product {
  display: flex;
  gap: 1rem;
}

.item-product img {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: var(--radius-md);
}

.item-info {
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.item-name {
  font-weight: 500;
  color: var(--gray-800);
  margin-bottom: 0.25rem;
}

.item-variant {
  font-size: 0.75rem;
  color: var(--gray-500);
}

.item-price {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--primary-600);
  margin-top: 0.25rem;
}

.item-quantity {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.item-quantity button {
  width: 32px;
  height: 32px;
  border: 1px solid var(--gray-300);
  border-radius: var(--radius-sm);
  font-weight: 600;
  transition: all var(--transition-fast);
}

.item-quantity button:hover:not(:disabled) {
  border-color: var(--primary-400);
  color: var(--primary-600);
}

.item-quantity span {
  min-width: 2rem;
  text-align: center;
  font-weight: 500;
}

.item-total {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 0.5rem;
}

.item-total span {
  font-weight: 600;
  font-size: 1.125rem;
}

.remove-btn {
  font-size: 0.75rem;
  color: #ef4444;
  text-decoration: underline;
}

.cart-actions {
  display: flex;
  justify-content: space-between;
  padding: 1.5rem;
  gap: 1rem;
}

.cart-summary-section {
  background: var(--white);
  border-radius: var(--radius-xl);
  padding: 1.5rem;
  box-shadow: var(--shadow-sm);
  height: fit-content;
  position: sticky;
  top: 100px;
}

.cart-summary-section h3 {
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--gray-200);
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.75rem;
  font-size: 0.875rem;
}

.summary-row.total {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--gray-900);
  border-top: 1px solid var(--gray-200);
  padding-top: 1rem;
  margin-top: 1rem;
}

.free-shipping {
  color: #10b981;
  font-weight: 500;
}

.promo-code {
  display: flex;
  gap: 0.5rem;
  margin: 1.5rem 0;
}

.promo-code input {
  flex: 1;
  padding: 0.75rem;
  border: 1px solid var(--gray-300);
  border-radius: var(--radius-md);
}

.promo-code button {
  padding: 0.75rem 1rem;
}

.checkout-btn {
  padding: 1rem;
  font-size: 1rem;
  font-weight: 600;
}

.payment-icons {
  text-align: center;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid var(--gray-200);
  font-size: 0.875rem;
  color: var(--gray-500);
}

@media (max-width: 768px) {
  .cart-layout {
    grid-template-columns: 1fr;
  }
  .cart-summary-section {
    position: static;
  }
  .cart-header-row {
    display: none;
  }
  .cart-item-row {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
}
</style>
