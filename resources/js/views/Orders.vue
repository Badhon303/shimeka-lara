<template>
  <div class="orders-page">
    <div class="page-header">
      <div class="container">
        <h1>My Orders</h1>
        <p>Track and manage your orders</p>
      </div>
    </div>

    <div class="container">
      <div v-if="loading" class="loading-state">
        <div v-for="i in 3" :key="i" class="skeleton-order"></div>
      </div>

      <div v-else-if="orders.length === 0" class="empty-orders">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
        </svg>
        <h3>No orders yet</h3>
        <p>When you make your first purchase, it will appear here.</p>
        <router-link to="/shop" class="btn btn-primary">Start Shopping</router-link>
      </div>

      <div v-else class="orders-list">
        <div v-for="order in orders" :key="order.id" class="order-card">
          <div class="order-header">
            <div class="order-info">
              <span class="order-number">{{ order.order_number }}</span>
              <span class="order-date">{{ formatDate(order.created_at) }}</span>
            </div>
            <span :class="['order-status', `status-${order.status}`]">
              {{ order.status }}
            </span>
          </div>

          <div class="order-items">
            <div v-for="item in order.items" :key="item.id" class="order-item">
              <img :src="item.product?.featured_image || '/images/placeholder.jpg'" :alt="item.product_name" />
              <div class="item-details">
                <h4>{{ item.product_name }}</h4>
                <p v-if="item.variant">{{ formatVariant(item.variant) }}</p>
                <p>Qty: {{ item.quantity }} × ৳{{ item.price }}</p>
              </div>
            </div>
          </div>

          <div class="order-footer">
            <div class="order-total">
              <span>Total:</span>
              <strong>৳{{ order.total }}</strong>
            </div>
            <div class="order-actions">
              <button class="btn btn-secondary" @click="viewOrder(order.id)">View Details</button>
              <button v-if="order.status === 'delivered'" class="btn btn-primary">Write Review</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const orders = ref([]);
const loading = ref(true);

async function fetchOrders() {
  try {
    const response = await axios.get('/orders');
    orders.value = response.data.data || [];
  } catch (err) {
    console.error('Failed to fetch orders:', err);
  } finally {
    loading.value = false;
  }
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  });
}

function formatVariant(variant) {
  if (!variant) return '';
  return Object.entries(variant).map(([key, value]) => `${key}: ${value}`).join(', ');
}

function viewOrder(id) {
  // Navigate to order detail
  console.log('View order:', id);
}

onMounted(fetchOrders);
</script>

<style scoped>
.orders-page {
  min-height: 100vh;
  background: var(--gray-50);
}

.empty-orders {
  text-align: center;
  padding: 5rem 2rem;
}

.empty-orders svg {
  width: 80px;
  height: 80px;
  color: var(--gray-300);
  margin-bottom: 1.5rem;
}

.loading-state {
  padding: 2rem 0;
}

.skeleton-order {
  height: 200px;
  background: linear-gradient(90deg, var(--gray-100), var(--gray-200), var(--gray-100));
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: var(--radius-xl);
  margin-bottom: 1rem;
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

.orders-list {
  padding: 2rem 0;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.order-card {
  background: var(--white);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  background: var(--gray-50);
  border-bottom: 1px solid var(--gray-200);
}

.order-info {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.order-number {
  font-weight: 600;
  color: var(--gray-900);
}

.order-date {
  font-size: 0.875rem;
  color: var(--gray-500);
}

.order-status {
  padding: 0.375rem 1rem;
  border-radius: var(--radius-full);
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.status-pending {
  background: #fef3c7;
  color: #92400e;
}

.status-processing {
  background: #dbeafe;
  color: #1e40af;
}

.status-shipped {
  background: #e0e7ff;
  color: #3730a3;
}

.status-delivered {
  background: #d1fae5;
  color: #065f46;
}

.status-cancelled {
  background: #fee2e2;
  color: #991b1b;
}

.order-items {
  padding: 1rem 1.5rem;
}

.order-item {
  display: flex;
  gap: 1rem;
  padding: 1rem 0;
  border-bottom: 1px solid var(--gray-100);
}

.order-item:last-child {
  border-bottom: none;
}

.order-item img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: var(--radius-md);
}

.item-details h4 {
  font-size: 1rem;
  margin-bottom: 0.25rem;
}

.item-details p {
  font-size: 0.875rem;
  color: var(--gray-500);
}

.order-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  border-top: 1px solid var(--gray-200);
  background: var(--gray-50);
}

.order-total {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.order-total strong {
  font-size: 1.25rem;
  color: var(--gray-900);
}

.order-actions {
  display: flex;
  gap: 0.75rem;
}
</style>
