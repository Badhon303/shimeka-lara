<template>
  <div class="admin-page">
    <div class="admin-content">
      <div class="page-header">
        <h1>Reports</h1>
        <select v-model="period" @change="loadReports" class="filter-select">
          <option value="week">Last 7 Days</option>
          <option value="month">Last 30 Days</option>
          <option value="year">Last 365 Days</option>
        </select>
      </div>

      <!-- Sales Summary -->
      <div class="stats-bar" v-if="summary">
        <div class="stat-card">
          <span class="stat-value">৳{{ Math.round(summary.total_revenue || 0) }}</span>
          <span class="stat-label">Revenue</span>
        </div>
        <div class="stat-card">
          <span class="stat-value">{{ summary.total_orders || 0 }}</span>
          <span class="stat-label">Orders</span>
        </div>
        <div class="stat-card">
          <span class="stat-value">৳{{ Math.round(summary.average_order || 0) }}</span>
          <span class="stat-label">Avg Order</span>
        </div>
      </div>

      <!-- Order Status Chart -->
      <div class="reports-grid">
        <div class="report-card">
          <h3>Order Status</h3>
          <div class="chart-list">
            <div v-for="(count, status) in orderReport.status_counts" :key="status" class="chart-row">
              <span :class="['status-dot', status]"></span>
              <span class="chart-label">{{ status }}</span>
              <div class="chart-bar-bg"><div class="chart-bar" :style="{ width: barPercent(count, totalOrders) + '%' }"></div></div>
              <span class="chart-value">{{ count }}</span>
            </div>
          </div>
        </div>

        <div class="report-card">
          <h3>Payment Status</h3>
          <div class="chart-list">
            <div v-for="(count, status) in orderReport.payment_counts" :key="status" class="chart-row">
              <span :class="['status-dot', status]"></span>
              <span class="chart-label">{{ status }}</span>
              <div class="chart-bar-bg"><div class="chart-bar" :style="{ width: barPercent(count, totalOrders) + '%' }"></div></div>
              <span class="chart-value">{{ count }}</span>
            </div>
          </div>
        </div>

        <div class="report-card full-width">
          <h3>Top Selling Products</h3>
          <table class="report-table">
            <thead>
              <tr><th>Rank</th><th>Product</th><th>Units Sold</th></tr>
            </thead>
            <tbody>
              <tr v-for="(item, i) in orderReport.top_products" :key="i">
                <td>#{{ i + 1 }}</td>
                <td>{{ item.product_name }}</td>
                <td>{{ item.total_sold }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="report-card full-width">
          <h3>Sales Timeline</h3>
          <div class="timeline">
            <div v-for="day in salesReport.sales" :key="day.date" class="timeline-row">
              <span class="timeline-date">{{ day.date }}</span>
              <div class="timeline-bar-bg">
                <div class="timeline-bar" :style="{ width: barPercent(day.revenue, maxRevenue) + '%' }"></div>
              </div>
              <span class="timeline-rev">৳{{ Math.round(day.revenue) }}</span>
              <span class="timeline-count">{{ day.orders }} orders</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const period = ref('month');
const salesReport = ref({ sales: [], summary: {} });
const orderReport = ref({ status_counts: {}, payment_counts: {}, top_products: [] });
const summary = ref(null);

const totalOrders = computed(() => {
  return Object.values(orderReport.value.status_counts).reduce((a, b) => a + b, 0);
});

const maxRevenue = computed(() => {
  if (!salesReport.value.sales.length) return 1;
  return Math.max(...salesReport.value.sales.map(d => d.revenue));
});

function barPercent(value, total) {
  if (!total) return 0;
  return Math.min((value / total) * 100, 100);
}

async function loadReports() {
  try {
    const [sales, orders] = await Promise.all([
      axios.get(`/admin/reports/sales?period=${period.value}`),
      axios.get(`/admin/reports/orders?period=${period.value}`),
    ]);
    salesReport.value = sales.data;
    orderReport.value = orders.data;
    summary.value = sales.data.summary;
  } catch (e) {
    console.error('Failed to load reports:', e);
  }
}

onMounted(loadReports);
</script>

<style scoped>
.admin-page { min-height: 100vh; background: var(--gray-100); }
.admin-content { padding: 2rem; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.page-header h1 { font-size: 1.875rem; }
.filter-select { padding: 0.5rem 1rem; border: 1px solid var(--gray-300); border-radius: var(--radius-md); background: var(--white); }

.stats-bar { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem; }
.stat-card { background: var(--white); padding: 1.25rem; border-radius: var(--radius-xl); box-shadow: var(--shadow-lg); text-align: center; }
.stat-value { display: block; font-size: 1.5rem; font-weight: 700; color: var(--gray-900); }
.stat-label { font-size: 0.75rem; color: var(--gray-500); text-transform: uppercase; }

.reports-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
.report-card { background: var(--white); border-radius: var(--radius-xl); padding: 1.5rem; box-shadow: var(--shadow-lg); }
.report-card.full-width { grid-column: 1 / -1; }
.report-card h3 { font-size: 1.125rem; margin-bottom: 1.25rem; color: var(--gray-800); }

.chart-list { display: flex; flex-direction: column; gap: 0.75rem; }
.chart-row { display: flex; align-items: center; gap: 0.75rem; }
.status-dot { width: 10px; height: 10px; border-radius: var(--radius-full); flex-shrink: 0; }
.status-dot.pending { background: #f59e0b; }
.status-dot.processing { background: #3b82f6; }
.status-dot.shipped { background: #6366f1; }
.status-dot.delivered { background: #10b981; }
.status-dot.cancelled { background: #ef4444; }
.status-dot.paid { background: #10b981; }
.chart-label { width: 80px; font-size: 0.875rem; text-transform: capitalize; }
.chart-bar-bg { flex: 1; height: 8px; background: var(--gray-100); border-radius: var(--radius-full); overflow: hidden; }
.chart-bar { height: 100%; background: var(--primary-500); border-radius: var(--radius-full); }
.chart-value { width: 40px; text-align: right; font-size: 0.875rem; font-weight: 600; }

.report-table { width: 100%; font-size: 0.875rem; }
.report-table th { text-align: left; padding: 0.5rem; border-bottom: 2px solid var(--gray-200); font-weight: 600; }
.report-table td { padding: 0.5rem; border-bottom: 1px solid var(--gray-100); }

.timeline { display: flex; flex-direction: column; gap: 0.5rem; }
.timeline-row { display: flex; align-items: center; gap: 0.75rem; }
.timeline-date { width: 80px; font-size: 0.75rem; color: var(--gray-500); }
.timeline-bar-bg { flex: 1; height: 20px; background: var(--gray-100); border-radius: var(--radius-md); overflow: hidden; }
.timeline-bar { height: 100%; background: linear-gradient(90deg, var(--primary-500), var(--primary-400)); border-radius: var(--radius-md); }
.timeline-rev { width: 70px; text-align: right; font-size: 0.875rem; font-weight: 600; }
.timeline-count { width: 70px; font-size: 0.75rem; color: var(--gray-500); }

@media (max-width: 768px) {
  .reports-grid { grid-template-columns: 1fr; }
  .stats-bar { grid-template-columns: 1fr; }
  .admin-content { padding: 1rem; }
}
</style>
