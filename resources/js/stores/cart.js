import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

const GUEST_CART_KEY = 'guest_cart';

function getGuestCart() {
  try {
    return JSON.parse(localStorage.getItem(GUEST_CART_KEY)) || { items: [], summary: { subtotal: 0, tax: 0, shipping: 0, total: 0, item_count: 0 } };
  } catch {
    return { items: [], summary: { subtotal: 0, tax: 0, shipping: 0, total: 0, item_count: 0 } };
  }
}

function saveGuestCart(cart) {
  localStorage.setItem(GUEST_CART_KEY, JSON.stringify(cart));
}

function clearGuestCart() {
  localStorage.removeItem(GUEST_CART_KEY);
}

function enrichItem(item) {
  return {
    ...item,
    variant_label: formatVariantLabel(item.variant),
    display_price: item.variant_price ?? item.product?.price ?? item.price ?? 0
  };
}

function formatVariantLabel(variant) {
  if (!variant) return '';
  try {
    const v = typeof variant === 'string' ? JSON.parse(variant) : variant;
    return Object.entries(v).map(([k, val]) => `${k.charAt(0).toUpperCase() + k.slice(1)}: ${val}`).join(', ');
  } catch {
    return '';
  }
}

function recalcSummary(cartItems) {
  const subtotal = cartItems.reduce((sum, item) => sum + (item.quantity * (item.variant_price ?? item.product?.price ?? item.price ?? 0)), 0);
  const tax = subtotal * 0.05;
  const shipping = subtotal > 1000 ? 0 : 100;
  return { subtotal, tax, shipping, total: subtotal + tax + shipping, item_count: cartItems.reduce((sum, item) => sum + item.quantity, 0) };
}

export const useCartStore = defineStore('cart', () => {
  const items = ref([]);
  const summary = ref({
    subtotal: 0,
    tax: 0,
    shipping: 0,
    total: 0,
    item_count: 0
  });
  const loading = ref(false);
  const isDrawerOpen = ref(false);

  const itemCount = computed(() => summary.value.item_count || 0);
  const cartTotal = computed(() => summary.value.total || 0);
  const isEmpty = computed(() => items.value.length === 0);

  // Check if user is logged in (has token)
  const isLoggedIn = () => !!localStorage.getItem('token');

  async function fetchCart() {
    if (!isLoggedIn()) {
      // Guest: load from localStorage
      const guest = getGuestCart();
      items.value = (guest.items || []).map(enrichItem);
      summary.value = recalcSummary(items.value);
      return;
    }
    // Logged in: load from server
    try {
      const response = await axios.get('/cart');
      items.value = (response.data.items || []).map(enrichItem);
      summary.value = response.data.summary;
    } catch (err) {
      console.error('Failed to fetch cart:', err);
    }
  }

  async function addToCart(product, quantity = 1, variant = null, variantPrice = null) {
    loading.value = true;
    const price = variantPrice || product.price;

    if (!isLoggedIn()) {
      // Guest: save to localStorage
      const guest = getGuestCart();
      const existingIndex = guest.items.findIndex(i =>
        i.product_id === product.id && JSON.stringify(i.variant) === JSON.stringify(variant)
      );

      const cartItem = existingIndex >= 0 ? guest.items[existingIndex] : {
        id: 'guest_' + Date.now() + Math.random().toString(36).slice(2),
        product_id: product.id,
        product: { id: product.id, name: product.name, slug: product.slug, price: price, featured_image: product.featured_image },
        quantity: 0,
        variant: variant,
        variant_price: price,
        price: price
      };

      if (existingIndex >= 0) {
        cartItem.quantity += quantity;
      } else {
        cartItem.quantity = quantity;
        guest.items.push(cartItem);
      }

      saveGuestCart(guest);
      await fetchCart();
      openDrawer();
      loading.value = false;
      return { success: true, message: 'Added to cart!' };
    }

    // Logged in: server
    try {
      const response = await axios.post('/cart/add', {
        product_id: product.id,
        quantity,
        variant: variant ? JSON.stringify(variant) : null
      });
      await fetchCart();
      openDrawer();
      return { success: true, message: 'Added to cart!' };
    } catch (err) {
      return { success: false, message: err.response?.data?.message || 'Failed to add to cart' };
    } finally {
      loading.value = false;
    }
  }

  async function updateQuantity(itemId, quantity) {
    if (!isLoggedIn()) {
      const guest = getGuestCart();
      const idx = guest.items.findIndex(i => i.id === itemId);
      if (idx >= 0) {
        guest.items[idx].quantity = quantity;
        if (quantity <= 0) guest.items.splice(idx, 1);
        saveGuestCart(guest);
        await fetchCart();
      }
      return { success: true };
    }
    try {
      await axios.put(`/cart/update/${itemId}`, { quantity });
      await fetchCart();
      return { success: true };
    } catch (err) {
      return { success: false, message: err.response?.data?.message };
    }
  }

  async function removeItem(itemId) {
    if (!isLoggedIn()) {
      const guest = getGuestCart();
      guest.items = guest.items.filter(i => i.id !== itemId);
      saveGuestCart(guest);
      await fetchCart();
      return { success: true };
    }
    try {
      await axios.delete(`/cart/remove/${itemId}`);
      await fetchCart();
      return { success: true };
    } catch (err) {
      return { success: false };
    }
  }

  async function clearCart() {
    if (!isLoggedIn()) {
      clearGuestCart();
      items.value = [];
      summary.value = { subtotal: 0, tax: 0, shipping: 0, total: 0, item_count: 0 };
      return { success: true };
    }
    try {
      await axios.delete('/cart/clear');
      items.value = [];
      summary.value = { subtotal: 0, tax: 0, shipping: 0, total: 0, item_count: 0 };
      return { success: true };
    } catch (err) {
      return { success: false };
    }
  }

  // Merge guest cart to server when user logs in
  async function mergeGuestCart() {
    if (!isLoggedIn()) return;
    const guest = getGuestCart();
    if (!guest.items || guest.items.length === 0) return;

    for (const item of guest.items) {
      try {
        await axios.post('/cart/add', {
          product_id: item.product_id,
          quantity: item.quantity,
          variant: item.variant ? JSON.stringify(item.variant) : null
        });
      } catch (e) {
        console.error('Failed to merge cart item:', e);
      }
    }
    clearGuestCart();
    await fetchCart();
  }

  function openDrawer() {
    isDrawerOpen.value = true;
  }

  function closeDrawer() {
    isDrawerOpen.value = false;
  }

  function toggleDrawer() {
    isDrawerOpen.value = !isDrawerOpen.value;
  }

  return {
    items,
    summary,
    loading,
    isDrawerOpen,
    itemCount,
    cartTotal,
    isEmpty,
    fetchCart,
    addToCart,
    updateQuantity,
    removeItem,
    clearCart,
    mergeGuestCart,
    openDrawer,
    closeDrawer,
    toggleDrawer
  };
});
