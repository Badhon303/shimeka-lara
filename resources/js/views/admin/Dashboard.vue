<template>
  <div class="admin-page">
    <div class="admin-content">
      <div class="page-header">
        <h1>Dashboard</h1>
        <p>Welcome back, {{ authStore.user?.name }}</p>
      </div>

      <div v-if="loading" class="loading-grid">
        <div v-for="i in 4" :key="i" class="skeleton-stat"></div>
      </div>

      <div v-else class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon users">👥</div>
          <div class="stat-info">
            <h3>Total Users</h3>
            <p class="stat-value">{{ stats.total_users }}</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon orders">📦</div>
          <div class="stat-info">
            <h3>Total Orders</h3>
            <p class="stat-value">{{ stats.total_orders }}</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon products">🛍️</div>
          <div class="stat-info">
            <h3>Products</h3>
            <p class="stat-value">{{ stats.total_products }}</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon revenue">💰</div>
          <div class="stat-info">
            <h3>This Month</h3>
            <p class="stat-value">৳{{ formatNumber(stats.month_revenue) }}</p>
          </div>
        </div>
      </div>

      <div class="admin-grid">
        <!-- Recent Orders -->
        <div class="admin-card">
          <div class="card-header">
            <h3>Recent Orders</h3>
            <router-link to="/admin/orders">View All</router-link>
          </div>
          <div class="orders-table">
            <table>
              <thead>
                <tr>
                  <th>Order</th>
                  <th>Customer</th>
                  <th>Total</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="order in recentOrders" :key="order.id">
                  <td>{{ order.order_number }}</td>
                  <td>{{ order.user?.name }}</td>
                  <td>৳{{ order.total }}</td>
                  <td>
                    <span :class="['status-badge', `status-${order.status}`]">
                      {{ order.status }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Low Stock Products -->
        <div class="admin-card">
          <div class="card-header">
            <h3>Low Stock Alert</h3>
            <router-link to="/admin/products">Manage Products</router-link>
          </div>
          <div class="low-stock-list">
            <div v-for="product in lowStockProducts" :key="product.id" class="low-stock-item">
              <img :src="product.featured_image || '/images/placeholder.jpg'" :alt="product.name" />
              <div class="product-info">
                <h4>{{ product.name }}</h4>
                <p>Only {{ product.stock_quantity }} left in stock</p>
              </div>
              <router-link :to="`/admin/products/${product.id}/edit`" class="btn btn-sm">
                Restock
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../../stores/auth';
import axios from 'axios';

const authStore = useAuthStore();

const stats = ref({
  total_users: 0,
  total_orders: 0,
  total_products: 0,
  total_categories: 0,
  pending_orders: 0,
  processing_orders: 0,
  completed_orders: 0,
  today_revenue: 0,
  month_revenue: 0
});
const recentOrders = ref([]);
const lowStockProducts = ref([]);
const loading = ref(true);

async function fetchDashboardData() {
  try {
    const response = await axios.get('/admin/dashboard');
    const data = response.data;
    
    stats.value = data.stats;
    recentOrders.value = data.recent_orders || [];
    lowStockProducts.value = data.low_stock_products || [];
  } catch (err) {
    console.error('Failed to fetch dashboard data:', err);
  } finally {
    loading.value = false;
  }
}

function formatNumber(num) {
  return num?.toLocaleString('en-US') || '0';
}

onMounted(fetchDashboardData);
</script>

<style scoped>
.admin-page {
  min-height: 100vh;
  background: var(--gray-100);
}

.admin-content {
  padding: 2rem;
}

.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 1.875rem;
  margin-bottom: 0.25rem;
}

.page-header p {
  color: var(--gray-500);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: var(--white);
  padding: 1.5rem;
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-sm);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: var(--radius-lg);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.75rem;
}

.stat-icon.users { background: #dbeafe; }
.stat-icon.orders { background: #d1fae5; }
.stat-icon.products { background: #fce7f3; }
.stat-icon.revenue { background: #fef3c7; }

.stat-info h3 {
  font-size: 0.875rem;
  color: var(--gray-500);
  font-weight: 500;
  margin-bottom: 0.25rem;
}

.stat-value {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--gray-900);
}

.admin-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1.5rem;
}

.admin-card {
  background: var(--white);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--gray-200);
}

.card-header h3 {
  font-size: 1.125rem;
}

.card-header a {
  font-size: 0.875rem;
  color: var(--primary-600);
}

.orders-table {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 1rem 1.5rem;
  text-align: left;
  font-size: 0.875rem;
}

th {
  font-weight: 500;
  color: var(--gray-500);
  background: var(--gray-50);
}

td {
  border-bottom: 1px solid var(--gray-100);
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: var(--radius-full);
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.status-pending { background: #fef3c7; color: #92400e; }
.status-processing { background: #dbeafe; color: #1e40af; }
.status-shipped { background: #e0e7ff; color: #3730a3; }
.status-delivered { background: #d1fae5; color: #065f46; }

.low-stock-list {
  padding: 1rem;
}

.low-stock-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border-bottom: 1px solid var(--gray-100);
}

.low-stock-item:last-child {
  border-bottom: none;
}

.low-stock-item img {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: var(--radius-md);
}

.low-stock-item .product-info {
  flex: 1;
}

.low-stock-item h4 {
  font-size: 0.875rem;
  margin-bottom: 0.25rem;
}

.low-stock-item p {
  font-size: 0.75rem;
  color: #dc2626;
}

.btn-sm {
  padding: 0.5rem 1rem;
  font-size: 0.75rem;
  background: var(--primary-500);
  color: var(--white);
  border-radius: var(--radius-md);
}

@media (max-width: 1024px) {
  .admin-grid {
    grid-template-columns: 1fr;
  }
}
</style>
