<template>
  <div class="track-page">
    <div class="page-header">
      <div class="container">
        <h1>Track Your Order</h1>
        <p>Enter your order number to check status</p>
      </div>
    </div>

    <div class="container">
      <div class="track-form">
        <div class="search-box">
          <input
            v-model="orderNumber"
            type="text"
            placeholder="Enter order number (e.g., ORD-2024-0001)"
            @keyup.enter="trackOrder"
          />
          <button @click="trackOrder" :disabled="loading" class="btn btn-primary">
            <span v-if="loading">Searching...</span>
            <span v-else>Track Order</span>
          </button>
        </div>

        <!-- Error -->
        <div v-if="error" class="error-message">
          {{ error }}
        </div>

        <!-- Order Result -->
        <div v-if="order" class="order-result">
          <div class="order-header">
            <div>
              <h2>Order {{ order.order_number }}</h2>
              <p class="order-date">Placed on {{ formatDate(order.created_at) }}</p>
            </div>
            <span :class="['status-badge', `status-${order.status}`]">
              {{ order.status }}
            </span>
          </div>

          <!-- Tracking Info -->
          <div v-if="order.tracking_number" class="tracking-info">
            <h3>Shipping Details</h3>
            <div class="tracking-details">
              <div class="detail-row">
                <span class="label">Courier:</span>
                <span class="value">{{ order.courier_name || 'N/A' }}</span>
              </div>
              <div class="detail-row">
                <span class="label">Tracking ID:</span>
                <span class="value track-id">{{ order.tracking_number }}</span>
              </div>
              <div v-if="order.courier_charge > 0" class="detail-row">
                <span class="label">Delivery Charge:</span>
                <span class="value">৳{{ order.courier_charge }}</span>
              </div>
            </div>
          </div>

          <!-- Timeline -->
          <div class="timeline">
            <div :class="['timeline-step', { active: isStepActive('pending') }]">
              <div class="step-icon">📋</div>
              <div class="step-label">Order Placed</div>
              <div class="step-date">{{ formatDate(order.created_at) }}</div>
            </div>
            <div :class="['timeline-step', { active: isStepActive('processing') }]">
              <div class="step-icon">📦</div>
              <div class="step-label">Processing</div>
            </div>
            <div :class="['timeline-step', { active: isStepActive('shipped') }]">
              <div class="step-icon">🚚</div>
              <div class="step-label">Shipped</div>
              <div v-if="order.shipped_at" class="step-date">{{ formatDate(order.shipped_at) }}</div>
            </div>
            <div :class="['timeline-step', { active: isStepActive('delivered') }]">
              <div class="step-icon">✅</div>
              <div class="step-label">Delivered</div>
              <div v-if="order.delivered_at" class="step-date">{{ formatDate(order.delivered_at) }}</div>
            </div>
          </div>

          <!-- Items -->
          <div class="order-items">
            <h3>Order Items</h3>
            <div v-for="(item, index) in order.items" :key="index" class="item-row">
              <span class="item-name">{{ item.product_name }}</span>
              <span class="item-qty">x{{ item.quantity }}</span>
              <span class="item-price">৳{{ item.price }}</span>
            </div>
          </div>

          <!-- Shipping Address -->
          <div class="shipping-info">
            <h3>Shipping Address</h3>
            <p><strong>{{ order.shipping_name }}</strong></p>
            <p>{{ order.shipping_address }}</p>
            <p>{{ order.shipping_city }}</p>
            <p>Phone: {{ order.shipping_phone }}</p>
          </div>

          <!-- Total -->
          <div class="order-total">
            <strong>Total: ৳{{ order.total }}</strong>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const orderNumber = ref('');
const order = ref(null);
const loading = ref(false);
const error = ref(null);

const statusOrder = ['pending', 'processing', 'shipped', 'delivered'];

function isStepActive(stepStatus) {
  if (!order.value) return false;
  const currentIndex = statusOrder.indexOf(order.value.status);
  const stepIndex = statusOrder.indexOf(stepStatus);
  return stepIndex <= currentIndex;
}

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric', month: 'short', day: 'numeric'
  });
}

async function trackOrder() {
  if (!orderNumber.value.trim()) {
    error.value = 'Please enter an order number';
    return;
  }

  loading.value = true;
  error.value = null;
  order.value = null;

  try {
    const response = await axios.get('/track', {
      params: { order_number: orderNumber.value.trim() }
    });
    order.value = response.data.order;
  } catch (err) {
    error.value = err.response?.data?.message || 'Order not found. Please check the number and try again.';
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.track-page {
  min-height: 100vh;
  background: var(--gray-50);
}

.track-form {
  max-width: 800px;
  margin: 0 auto;
  padding: 3rem 1rem;
}

.search-box {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.search-box input {
  flex: 1;
  padding: 1rem 1.25rem;
  border: 2px solid var(--gray-300);
  border-radius: var(--radius-lg);
  font-size: 1rem;
  transition: border-color var(--transition-fast);
}

.search-box input:focus {
  outline: none;
  border-color: var(--primary-500);
}

.search-box button {
  padding: 1rem 2rem;
  font-size: 1rem;
  font-weight: 600;
}

.error-message {
  background: #fee2e2;
  color: #991b1b;
  padding: 1rem;
  border-radius: var(--radius-lg);
  text-align: center;
  margin-bottom: 1.5rem;
}

.order-result {
  background: var(--white);
  border-radius: var(--radius-2xl);
  box-shadow: var(--shadow-lg);
  overflow: hidden;
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  background: var(--gray-50);
  border-bottom: 1px solid var(--gray-200);
}

.order-header h2 {
  font-size: 1.5rem;
  margin-bottom: 0.25rem;
}

.order-date {
  color: var(--gray-500);
  font-size: 0.875rem;
}

.status-badge {
  padding: 0.5rem 1rem;
  border-radius: var(--radius-full);
  font-size: 0.875rem;
  font-weight: 600;
  text-transform: uppercase;
}

.status-pending { background: #fef3c7; color: #92400e; }
.status-processing { background: #dbeafe; color: #1e40af; }
.status-shipped { background: #d1fae5; color: #065f46; }
.status-delivered { background: #dcfce7; color: #15803d; }
.status-cancelled { background: #fee2e2; color: #991b1b; }

.tracking-info {
  padding: 1.5rem 2rem;
  border-bottom: 1px solid var(--gray-200);
}

.tracking-info h3 {
  font-size: 1.125rem;
  margin-bottom: 1rem;
}

.tracking-details {
  display: grid;
  gap: 0.75rem;
}

.detail-row {
  display: flex;
  gap: 1rem;
}

.detail-row .label {
  color: var(--gray-500);
  min-width: 140px;
}

.detail-row .value {
  font-weight: 500;
}

.track-id {
  font-family: monospace;
  background: var(--gray-100);
  padding: 0.25rem 0.5rem;
  border-radius: var(--radius-sm);
}

.timeline {
  display: flex;
  justify-content: space-between;
  padding: 2rem;
  position: relative;
}

.timeline::before {
  content: '';
  position: absolute;
  top: 2.5rem;
  left: 2rem;
  right: 2rem;
  height: 2px;
  background: var(--gray-200);
  z-index: 0;
}

.timeline-step {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  position: relative;
  z-index: 1;
}

.timeline-step .step-icon {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: var(--gray-200);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  transition: all var(--transition-fast);
}

.timeline-step.active .step-icon {
  background: var(--primary-500);
}

.timeline-step .step-label {
  font-size: 0.75rem;
  font-weight: 500;
  color: var(--gray-500);
}

.timeline-step.active .step-label {
  color: var(--primary-600);
  font-weight: 600;
}

.step-date {
  font-size: 0.625rem;
  color: var(--gray-400);
}

.order-items {
  padding: 1.5rem 2rem;
  border-bottom: 1px solid var(--gray-200);
}

.order-items h3 {
  font-size: 1.125rem;
  margin-bottom: 1rem;
}

.item-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid var(--gray-100);
}

.item-row:last-child {
  border-bottom: none;
}

.item-name {
  flex: 1;
}

.item-qty {
  color: var(--gray-500);
  margin: 0 1rem;
}

.item-price {
  font-weight: 600;
}

.shipping-info {
  padding: 1.5rem 2rem;
  border-bottom: 1px solid var(--gray-200);
}

.shipping-info h3 {
  font-size: 1.125rem;
  margin-bottom: 1rem;
}

.shipping-info p {
  color: var(--gray-600);
  margin-bottom: 0.25rem;
}

.order-total {
  padding: 1.5rem 2rem;
  text-align: right;
  font-size: 1.25rem;
  color: var(--primary-600);
}

@media (max-width: 640px) {
  .search-box {
    flex-direction: column;
  }
  .timeline::before {
    display: none;
  }
  .timeline {
    flex-wrap: wrap;
    gap: 1rem;
  }
}
</style>
