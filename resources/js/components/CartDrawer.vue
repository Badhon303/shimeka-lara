<template>
  <Transition name="drawer">
    <div v-if="cartStore.isDrawerOpen" class="cart-overlay" @click.self="cartStore.closeDrawer">
      <div class="cart-drawer">
        <div class="cart-header">
          <h3>Shopping Cart ({{ cartStore.itemCount }})</h3>
          <div class="cart-header-actions">
            <button v-if="!cartStore.isEmpty" @click="clearCart" class="clear-btn" title="Clear Cart">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
              </svg>
              Clear
            </button>
            <button @click="cartStore.closeDrawer" class="close-btn">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <div v-if="cartStore.isEmpty" class="cart-empty">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
          </svg>
          <p>Your cart is empty</p>
          <router-link to="/shop" @click="cartStore.closeDrawer" class="btn-primary">
            Continue Shopping
          </router-link>
        </div>

        <template v-else>
          <div class="cart-items">
            <div v-for="item in cartStore.items" :key="item.id" class="cart-item">
              <img 
                :src="item.product.featured_image || '/images/placeholder.jpg'" 
                :alt="item.product.name"
                class="item-image"
              />
              <div class="item-details">
                <h4 class="item-name">{{ item.product.name }}</h4>
                <p v-if="item.variant_label" class="item-variant">{{ item.variant_label }}</p>
                <p class="item-price">৳{{ item.display_price }}</p>
                <div class="item-quantity">
                  <button @click="updateQty(item.id, item.quantity - 1)" :disabled="item.quantity <= 1">-</button>
                  <span>{{ item.quantity }}</span>
                  <button @click="updateQty(item.id, item.quantity + 1)">+</button>
                </div>
              </div>
              <button @click="removeItem(item.id)" class="remove-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

          <div class="cart-summary">
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
              <span>{{ cartStore.summary.shipping === 0 ? 'Free' : '৳' + cartStore.summary.shipping }}</span>
            </div>
            <div class="summary-row total">
              <span>Total</span>
              <span>৳{{ cartStore.summary.total }}</span>
            </div>
            
            <router-link to="/checkout" @click="cartStore.closeDrawer" class="checkout-btn">
              Proceed to Checkout
            </router-link>
            <router-link to="/cart" @click="cartStore.closeDrawer" class="view-cart-btn">
              View Cart
            </router-link>
          </div>
        </template>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { useCartStore } from '../stores/cart';

const cartStore = useCartStore();

async function updateQty(itemId, quantity) {
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
