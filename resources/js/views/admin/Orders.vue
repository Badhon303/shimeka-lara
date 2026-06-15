<template>
  <div class="admin-page">
    <div class="admin-content">
      <div class="page-header">
        <h1>Orders</h1>
        <select v-model="filterStatus" class="filter-select">
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="processing">Processing</option>
          <option value="shipped">Shipped</option>
          <option value="delivered">Delivered</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>

      <div class="stats-bar">
        <div class="stat-card"><span class="stat-value">{{ orders.length }}</span><span class="stat-label">Total</span></div>
        <div class="stat-card pending"><span class="stat-value">{{ statusCount('pending') }}</span><span class="stat-label">Pending</span></div>
        <div class="stat-card processing"><span class="stat-value">{{ statusCount('processing') }}</span><span class="stat-label">Processing</span></div>
        <div class="stat-card shipped"><span class="stat-value">{{ statusCount('shipped') }}</span><span class="stat-label">Shipped</span></div>
        <div class="stat-card delivered"><span class="stat-value">{{ statusCount('delivered') }}</span><span class="stat-label">Delivered</span></div>
      </div>

      <div class="admin-card">
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Area</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Courier</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in filteredOrders" :key="order.id">
                <td><span class="order-num">{{ order.order_number }}</span></td>
                <td>
                  <strong>{{ order.shipping_name || order.user?.name || 'Guest' }}</strong>
                  <small>{{ order.shipping_phone }}</small>
                </td>
                <td>
                  <span class="area-badge" :class="order.delivery_area">
                    {{ order.delivery_charge_label || (order.delivery_area === 'inside_dhaka' ? 'Inside' : 'Outside') }}
                  </span>
                </td>
                <td>৳{{ order.total }}</td>
                <td>
                  <span :class="['pay-badge', order.payment_status]">{{ order.payment_status }}</span>
                  <small>{{ order.payment_method }}</small>
                </td>
                <td><span :class="['status-badge', `status-${order.status}`]">{{ order.status }}</span></td>
                <td>
                  <span v-if="order.courier_name">{{ order.courier_name }}</span>
                  <span v-else class="text-muted">-</span>
                  <small v-if="order.tracking_number">#{{ order.tracking_number }}</small>
                </td>
                <td>{{ formatDate(order.created_at) }}</td>
                <td>
                  <button class="btn btn-sm" @click="viewDetail(order)">View</button>
                  <button class="btn btn-sm btn-secondary" @click="openEdit(order)">Edit</button>
                  <button class="btn btn-sm btn-outline" @click="printSlip(order)">Print</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <div v-if="showDetail" class="modal-overlay" @click.self="showDetail = false">
      <div class="modal modal-lg">
        <div class="modal-header">
          <h2>Order {{ detail?.order_number }}</h2>
          <button @click="showDetail = false" class="close-btn">&times;</button>
        </div>
        <div class="modal-body" v-if="detail">
          <div class="detail-grid">
            <div>
              <h4>Customer</h4>
              <p><strong>Name:</strong> {{ detail.shipping_name || detail.user?.name || 'Guest' }}</p>
              <p><strong>Phone:</strong> {{ detail.shipping_phone }}</p>
              <p><strong>Email:</strong> {{ detail.shipping_email }}</p>
              <p><strong>Address:</strong> {{ detail.shipping_address }}, {{ detail.shipping_city }}</p>
              <p><strong>Area:</strong> {{ detail.delivery_charge_label || (detail.delivery_area === 'inside_dhaka' ? 'Inside Dhaka' : 'Outside Dhaka') }}</p>
            </div>
            <div>
              <h4>Order Info</h4>
              <p><strong>Date:</strong> {{ formatDateTime(detail.created_at) }}</p>
              <p><strong>Status:</strong> {{ detail.status }}</p>
              <p><strong>Payment:</strong> {{ detail.payment_status }} ({{ detail.payment_method }})</p>
              <p><strong>Courier:</strong> {{ detail.courier_name || 'Not assigned' }}</p>
              <p v-if="detail.tracking_number"><strong>Tracking:</strong> {{ detail.tracking_number }}</p>
            </div>
          </div>
          <h4>Items</h4>
          <table class="items-table">
            <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th></tr></thead>
            <tbody>
              <tr v-for="item in detail.items" :key="item.id">
                <td>{{ item.product_name }} <span v-if="item.variant" class="variant-tag">{{ item.variant }}</span></td>
                <td>৳{{ item.price }}</td>
                <td>{{ item.quantity }}</td>
                <td>৳{{ item.price * item.quantity }}</td>
              </tr>
            </tbody>
          </table>
          <div class="detail-totals">
            <div class="total-row"><span>Subtotal:</span><span>৳{{ detail.subtotal }}</span></div>
            <div class="total-row"><span>Shipping:</span><span>৳{{ detail.shipping_cost }}</span></div>
            <div class="total-row"><span>Tax:</span><span>৳{{ detail.tax }}</span></div>
            <div class="total-row grand"><span>Total:</span><span>৳{{ detail.total }}</span></div>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="printSlip(detail)" class="btn btn-secondary">Print Slip</button>
          <button @click="showDetail = false" class="btn btn-primary">Close</button>
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <div v-if="showEdit" class="modal-overlay" @click.self="showEdit = false">
      <div class="modal">
        <div class="modal-header">
          <h2>Edit Order {{ edit?.order_number }}</h2>
          <button @click="showEdit = false" class="close-btn">&times;</button>
        </div>
        <div class="modal-body" v-if="edit">
          <div class="form-row">
            <div class="form-group half">
              <label>Status</label>
              <select v-model="edit.status" class="form-control">
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
            <div class="form-group half">
              <label>Payment</label>
              <select v-model="edit.payment_status" class="form-control">
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="failed">Failed</option>
                <option value="refunded">Refunded</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Courier</label>
            <select v-model="edit.courier_name" class="form-control">
              <option value="">-- Select --</option>
              <option v-for="c in couriers" :key="c" :value="c">{{ c }}</option>
            </select>
          </div>
          <div class="form-group">
            <label>Tracking Number</label>
            <input v-model="edit.tracking_number" type="text" class="form-control" />
          </div>
        </div>
        <div class="modal-footer">
          <button @click="showEdit = false" class="btn btn-secondary">Cancel</button>
          <button @click="saveOrder" class="btn btn-primary" :disabled="saving">{{ saving ? 'Saving...' : 'Save' }}</button>
        </div>
      </div>
    </div>

    <!-- Print Options Modal -->
    <div v-if="showPrintOptions" class="modal-overlay" @click.self="showPrintOptions = false">
      <div class="modal">
        <div class="modal-header">
          <h2>Print Order Slip</h2>
          <button @click="showPrintOptions = false" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <p class="print-hint">Select print format:</p>
          <div class="print-options">
            <button @click="doPrint('a4')" class="print-btn">
              <span class="print-icon">📄</span>
              <span class="print-name">A4 / Letter</span>
              <span class="print-desc">Standard invoice size</span>
            </button>
            <button @click="doPrint('pos')" class="print-btn">
              <span class="print-icon">🧾</span>
              <span class="print-name">POS / Thermal</span>
              <span class="print-desc">80mm receipt printer</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Print Slip -->
    <div v-if="slip" class="print-area">
      <div :class="['slip', printSize]" id="slip">
        <div class="slip-header">
          <h2>{{ siteSettings.site_name || 'Glow & Glam' }}</h2>
          <p>{{ siteSettings.site_address }}</p>
          <p>Phone: {{ siteSettings.site_phone }}</p>
          <hr/>
          <h3>Delivery Slip</h3>
          <p><strong>Order:</strong> {{ slip.order_number }}</p>
          <p><strong>Date:</strong> {{ formatDateTime(slip.created_at) }}</p>
        </div>
        <div class="slip-customer">
          <h4>Ship To:</h4>
          <p><strong>{{ slip.shipping_name }}</strong></p>
          <p>{{ slip.shipping_phone }}</p>
          <p>{{ slip.shipping_address }}</p>
          <p>{{ slip.shipping_city }} - {{ slip.shipping_postal_code }}</p>
          <p><strong>Area:</strong> {{ slip.delivery_charge_label || (slip.delivery_area === 'inside_dhaka' ? 'Inside Dhaka' : 'Outside Dhaka') }}</p>
        </div>
        <table class="slip-items">
          <thead><tr><th>#</th><th>Item</th><th>Qty</th><th>Price</th></tr></thead>
          <tbody>
            <tr v-for="(item, i) in slip.items" :key="item.id">
              <td>{{ i + 1 }}</td>
              <td>{{ item.product_name }}</td>
              <td>{{ item.quantity }}</td>
              <td>৳{{ item.price }}</td>
            </tr>
          </tbody>
        </table>
        <div class="slip-totals">
          <p><strong>Subtotal:</strong> ৳{{ slip.subtotal }}</p>
          <p><strong>Shipping:</strong> ৳{{ slip.shipping_cost }}</p>
          <p><strong>Tax:</strong> ৳{{ slip.tax }}</p>
          <p class="grand"><strong>Total:</strong> ৳{{ slip.total }}</p>
        </div>
        <div class="slip-footer">
          <p><strong>Payment:</strong> {{ slip.payment_status }} ({{ slip.payment_method }})</p>
          <p v-if="slip.courier_name"><strong>Courier:</strong> {{ slip.courier_name }} #{{ slip.tracking_number }}</p>
          <hr/>
          <p class="thankyou">Thank you for shopping!</p>
          <p class="developer">Developed by Metasoft Info Solutions</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const orders = ref([]);
const showDetail = ref(false);
const showEdit = ref(false);
const detail = ref(null);
const edit = ref(null);
const saving = ref(false);
const filterStatus = ref('');
const slip = ref(null);
const printSize = ref('a4');
const showPrintOptions = ref(false);
const pendingPrintOrder = ref(null);
const couriers = ref(['Pathao', 'RedX', 'Paperfly', 'Steadfast', 'eCourier']);
const siteSettings = ref({});

const filteredOrders = computed(() => {
  if (!filterStatus.value) return orders.value;
  return orders.value.filter(o => o.status === filterStatus.value);
});

function statusCount(status) {
  return orders.value.filter(o => o.status === status).length;
}

async function fetchOrders() {
  try {
    const res = await axios.get('/admin/orders?per_page=100');
    orders.value = res.data.data || [];
  } catch (err) {
    console.error('Failed to fetch orders:', err);
  }
}

async function fetchSettings() {
  try {
    const res = await axios.get('/settings');
    siteSettings.value = res.data;
  } catch (e) {}
}

function formatDate(d) {
  if (!d) return '-';
  return new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}

function formatDateTime(d) {
  if (!d) return '-';
  return new Date(d).toLocaleString('en-GB', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
}

async function viewDetail(order) {
  try {
    const res = await axios.get(`/admin/orders/${order.id}`);
    detail.value = res.data;
    showDetail.value = true;
  } catch (err) {
    if (window.$toast) window.$toast('Failed to load details', 'error');
  }
}

function openEdit(order) {
  edit.value = { ...order };
  showEdit.value = true;
}

async function saveOrder() {
  if (!edit.value) return;
  saving.value = true;
  try {
    await axios.put(`/admin/orders/${edit.value.id}/status`, {
      status: edit.value.status,
      payment_status: edit.value.payment_status,
      tracking_number: edit.value.tracking_number,
      courier_name: edit.value.courier_name,
    });
    const idx = orders.value.findIndex(o => o.id === edit.value.id);
    if (idx >= 0) orders.value[idx] = { ...orders.value[idx], ...edit.value };
    showEdit.value = false;
    if (window.$toast) window.$toast('Updated!', 'success');
  } catch (err) {
    if (window.$toast) window.$toast('Failed to update', 'error');
  } finally {
    saving.value = false;
  }
}

function printSlip(order) {
  pendingPrintOrder.value = order;
  showPrintOptions.value = true;
}

function doPrint(size) {
  printSize.value = size;
  showPrintOptions.value = false;
  const order = pendingPrintOrder.value;

  const loadAndPrint = () => {
    setTimeout(() => {
      const content = document.getElementById('slip').innerHTML;
      const w = window.open('', '_blank');
      const isPos = size === 'pos';
      const pageWidth = isPos ? '80mm' : '210mm';
      const padding = isPos ? '5mm' : '15mm';
      const fontSize = isPos ? '10px' : '13px';
      const h2Size = isPos ? '12px' : '16px';
      const h3Size = isPos ? '11px' : '14px';

      w.document.write(`<html><head><title>Slip ${order.order_number}</title><style>
        @page { size: ${pageWidth} auto; margin: 0; }
        body{font-family:Arial,sans-serif;padding:${padding};max-width:${pageWidth};margin:0 auto;font-size:${fontSize}}
        h2{font-size:${h2Size};margin:0 0 4px}h3{font-size:${h3Size};margin:4px 0}
        h4{font-size:${fontSize};margin:4px 0}
        p{margin:2px 0;font-size:${fontSize}}
        hr{border:none;border-top:1px dashed #ccc;margin:6px 0}
        table{width:100%;border-collapse:collapse;font-size:${fontSize};margin:6px 0}
        th,td{padding:3px;text-align:left;border-bottom:1px solid #eee}
        .grand{font-size:${isPos ? '12px' : '16px'};font-weight:bold;margin-top:6px}
        .thankyou{text-align:center;font-style:italic;color:#666;font-size:${fontSize}}
        .developer{text-align:center;font-size:9px;color:#999;margin-top:4px}
      </style></head><body>${content}</body></html>`);
      w.document.close();
      setTimeout(() => w.print(), 200);
      slip.value = null;
      pendingPrintOrder.value = null;
    }, 150);
  };

  if (!order.items) {
    axios.get(`/admin/orders/${order.id}`).then(r => {
      slip.value = r.data;
      loadAndPrint();
    });
  } else {
    slip.value = order;
    loadAndPrint();
  }
}

onMounted(() => { fetchOrders(); fetchSettings(); });
</script>

<style scoped>
.admin-page { min-height: 100vh; background: var(--gray-100); }
.admin-content { padding: 2rem; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.page-header h1 { font-size: 1.875rem; }
.filter-select { padding: 0.5rem 1rem; border: 1px solid var(--gray-300); border-radius: var(--radius-md); background: var(--white); }

.stats-bar { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; margin-bottom: 2rem; }
.stat-card { background: var(--white); padding: 1.25rem; border-radius: var(--radius-xl); box-shadow: var(--shadow-lg); text-align: center; border-top: 3px solid var(--gray-300); }
.stat-card.pending { border-color: #f59e0b; }
.stat-card.processing { border-color: #3b82f6; }
.stat-card.shipped { border-color: #6366f1; }
.stat-card.delivered { border-color: #10b981; }
.stat-value { display: block; font-size: 1.5rem; font-weight: 700; color: var(--gray-900); }
.stat-label { font-size: 0.75rem; color: var(--gray-500); text-transform: uppercase; }

.admin-card { background: var(--white); border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); overflow: hidden; }
.table-responsive { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
th, td { padding: 0.875rem 1rem; text-align: left; border-bottom: 1px solid var(--gray-100); white-space: nowrap; }
th { background: var(--gray-50); font-weight: 600; color: var(--gray-600); font-size: 0.75rem; text-transform: uppercase; }
td small { display: block; color: var(--gray-400); font-size: 0.75rem; }

.order-num { font-family: monospace; font-weight: 600; color: var(--primary-600); }
.area-badge { padding: 0.25rem 0.5rem; border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 500; }
.area-badge.inside_dhaka { background: #d1fae5; color: #065f46; }
.area-badge.outside_dhaka { background: #dbeafe; color: #1e40af; }
.status-badge { padding: 0.25rem 0.75rem; border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 500; text-transform: uppercase; }
.status-pending { background: #fef3c7; color: #92400e; }
.status-processing { background: #dbeafe; color: #1e40af; }
.status-shipped { background: #e0e7ff; color: #3730a3; }
.status-delivered { background: #d1fae5; color: #065f46; }
.status-cancelled { background: #fee2e2; color: #991b1b; }
.pay-badge { padding: 0.125rem 0.5rem; border-radius: var(--radius-full); font-size: 0.625rem; font-weight: 600; text-transform: uppercase; }
.pay-badge.pending { background: #fef3c7; color: #92400e; }
.pay-badge.paid { background: #d1fae5; color: #065f46; }
.pay-badge.failed { background: #fee2e2; color: #991b1b; }
.pay-badge.refunded { background: #e5e7eb; color: #374151; }

.action-btns { display: flex; gap: 0.25rem; }
.btn-sm { padding: 0.375rem 0.625rem; font-size: 0.75rem; border-radius: var(--radius-md); }
.btn-secondary { background: var(--gray-200); color: var(--gray-800); }
.btn-outline { background: var(--white); border: 1px solid var(--gray-300); color: var(--gray-600); }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 1rem; }
.modal { background: var(--white); border-radius: var(--radius-2xl); width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: var(--shadow-xl); }
.modal-lg { max-width: 700px; }
.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--gray-200); }
.close-btn { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--gray-500); }
.modal-body { padding: 1.5rem; }
.modal-footer { display: flex; justify-content: flex-end; gap: 0.75rem; padding: 1rem 1.5rem; border-top: 1px solid var(--gray-200); }

.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem; }
.detail-grid h4 { font-size: 0.875rem; text-transform: uppercase; color: var(--gray-500); margin-bottom: 0.75rem; }
.detail-grid p { margin-bottom: 0.5rem; font-size: 0.875rem; }
.items-table { width: 100%; font-size: 0.875rem; margin: 1rem 0; }
.items-table th { background: var(--gray-50); padding: 0.5rem; }
.items-table td { padding: 0.5rem; border-bottom: 1px solid var(--gray-100); }
.variant-tag { display: inline-block; background: var(--primary-50); color: var(--primary-600); padding: 0.125rem 0.375rem; border-radius: var(--radius-full); font-size: 0.625rem; margin-left: 0.25rem; }
.detail-totals { border-top: 2px solid var(--gray-200); padding-top: 1rem; margin-top: 1rem; }
.total-row { display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem; }
.total-row.grand { font-size: 1.125rem; font-weight: 700; border-top: 1px solid var(--gray-200); padding-top: 0.5rem; margin-top: 0.5rem; }

.form-group { margin-bottom: 1rem; }
.form-group.half { flex: 1; }
.form-row { display: flex; gap: 1rem; }
.form-group label { display: block; font-size: 0.875rem; font-weight: 500; color: var(--gray-700); margin-bottom: 0.25rem; }
.form-control { width: 100%; padding: 0.625rem 0.875rem; border: 1px solid var(--gray-300); border-radius: var(--radius-md); font-size: 0.875rem; }

.print-area { display: none; }

.print-hint { color: var(--gray-500); margin-bottom: 1rem; font-size: 0.875rem; }
.print-options { display: flex; flex-direction: column; gap: 0.75rem; }
.print-btn { display: flex; flex-direction: column; align-items: center; gap: 0.25rem; padding: 1.5rem; border: 2px solid var(--gray-200); border-radius: var(--radius-xl); background: var(--white); cursor: pointer; transition: all var(--transition-fast); }
.print-btn:hover { border-color: var(--primary-500); background: var(--primary-50); }
.print-icon { font-size: 2rem; }
.print-name { font-size: 1rem; font-weight: 600; color: var(--gray-800); }
.print-desc { font-size: 0.75rem; color: var(--gray-500); }

@media (max-width: 768px) {
  .stats-bar { grid-template-columns: repeat(3, 1fr); }
  .detail-grid { grid-template-columns: 1fr; }
  .admin-content { padding: 1rem; }
}
</style>
