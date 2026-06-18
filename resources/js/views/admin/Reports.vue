<template>
  <div class="admin-page">
    <div class="admin-content">
      <div class="page-header">
        <div>
          <h1>Reports</h1>
          <p class="report-site">Sʜɪᴍᴇᴋᴀ Sales Report</p>
        </div>
        <div class="report-actions">
          <select v-model="period" @change="onPeriodChange" class="filter-select">
            <option value="week">Last 7 Days</option>
            <option value="month">Last 30 Days</option>
            <option value="year">Last 365 Days</option>
            <option value="custom">Custom Range</option>
          </select>
          <button @click="printReport" class="btn btn-outline btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.001 42.001 0 0 1 5.997 0m-5.997 0a42 42 0 0 1 5.997 0M6.72 13.829l-.857 3.429a.75.75 0 0 0 .726.942H17.41a.75.75 0 0 0 .726-.942l-.857-3.429M9.75 13.5V9.75m0 0h4.5m-4.5 0v-.562c0-.863.697-1.562 1.562-1.562h.938c.865 0 1.562.699 1.562 1.562v.562m-4.5 0h4.5M3 17.25v1.5a2.25 2.25 0 0 0 2.25 2.25h13.5A2.25 2.25 0 0 0 21 18.75v-1.5M3 17.25V6.75A2.25 2.25 0 0 1 5.25 4.5h13.5A2.25 2.25 0 0 1 21 6.75v10.5" /></svg>
            Print
          </button>
          <button @click="exportCSV" class="btn btn-primary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
            Export CSV
          </button>
        </div>
      </div>

      <!-- Custom Date Range -->
      <div v-if="period === 'custom'" class="date-range-bar">
        <label>From: <input v-model="startDate" type="date" class="form-control" @change="loadReports" /></label>
        <label>To: <input v-model="endDate" type="date" class="form-control" @change="loadReports" /></label>
        <button @click="loadReports" class="btn btn-secondary btn-sm">Apply</button>
      </div>

      <!-- Print Header -->
      <div class="print-header">
        <h1>Sʜɪᴍᴇᴋᴀ Sales Report</h1>
        <p>Generated on {{ new Date().toLocaleDateString() }}</p>
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

      <!-- Print Footer -->
      <div class="print-footer">
        <p>Generated by Sʜɪᴍᴇᴋᴀ Admin System</p>
        <p>Developed by <strong>Metasoft Info Solutions</strong></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const period = ref('month');
const startDate = ref('');
const endDate = ref('');
const salesReport = ref({ sales: [], summary: {} });
const orderReport = ref({ status_counts: {}, payment_counts: {}, top_products: [] });
const summary = ref(null);

function onPeriodChange() {
  if (period.value !== 'custom') {
    loadReports();
  }
}

function printReport() {
  window.print();
}

function exportCSV() {
  // Build CSV content
  let csv = 'Date,Revenue,Orders\n';
  for (const row of salesReport.value.sales) {
    csv += `${row.date},${row.revenue},${row.orders}\n`;
  }
  csv += '\nStatus,Count\n';
  for (const [status, count] of Object.entries(orderReport.value.status_counts)) {
    csv += `${status},${count}\n`;
  }
  csv += '\nProduct,Units Sold\n';
  for (const item of orderReport.value.top_products) {
    csv += `${item.product_name},${item.total_sold}\n`;
  }

  const blob = new Blob([csv], { type: 'text/csv' });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `shimeka-report-${new Date().toISOString().split('T')[0]}.csv`;
  a.click();
  window.URL.revokeObjectURL(url);
}

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
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
.page-header h1 { font-size: 1.875rem; margin: 0; }
.report-site { font-size: 0.875rem; color: var(--gray-500); margin: 0.25rem 0 0; }
.report-actions { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
.filter-select { padding: 0.5rem 1rem; border: 1px solid var(--gray-300); border-radius: var(--radius-md); background: var(--white); }

.date-range-bar { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; padding: 1rem; background: var(--white); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); flex-wrap: wrap; }
.date-range-bar label { display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--gray-700); }
.date-range-bar input { padding: 0.375rem 0.75rem; border: 1px solid var(--gray-300); border-radius: var(--radius-md); }

.print-header, .print-footer { display: none; }

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
  .report-actions { width: 100%; justify-content: flex-start; }
  .date-range-bar { flex-direction: column; align-items: stretch; }
}

@media print {
  @page { margin: 1cm; size: auto; }
  .admin-sidebar, .admin-mobile-toggle, .admin-mobile-nav, .admin-top-bar,
  .admin-more-sheet, .admin-more-overlay, .admin-sidebar-overlay,
  .report-actions, .date-range-bar { display: none !important; }
  .admin-page { background: white !important; min-height: auto !important; }
  .admin-content { padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
  .report-card { box-shadow: none; border: 1px solid #ddd; break-inside: avoid; page-break-inside: avoid; }
  .stats-bar { grid-template-columns: repeat(3, 1fr); gap: 1rem; }
  .print-header { display: block !important; text-align: center; margin: 0 0 1rem 0; }
  .print-header h1 { font-size: 1.25rem; margin: 0 0 0.25rem 0; }
  .print-footer { display: block !important; border-top: 1px solid #ddd; padding-top: 0.5rem; margin-top: 1rem; font-size: 0.7rem; color: #666; text-align: center; }
  .timeline-bar { background: #333 !important; }
  .page-header h1 { display: none; }
  .report-site { display: none; }
  .page-header { margin-bottom: 0; }
  body, #app { padding: 0 !important; margin: 0 !important; }
}
</style>
