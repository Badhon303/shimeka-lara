<template>
  <div class="admin-page">
    <div class="admin-content">
      <div class="page-header">
        <h1>Categories</h1>
        <button class="btn btn-primary">Add Category</button>
      </div>

      <div class="admin-card">
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Type</th>
              <th>Products</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="category in categories" :key="category.id">
              <td>{{ category.name }}</td>
              <td>{{ category.type }}</td>
              <td>{{ category.products_count || 0 }}</td>
              <td>
                <span :class="['badge', category.is_active ? 'active' : 'inactive']">
                  {{ category.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td>
                <button class="btn btn-sm">Edit</button>
                <button class="btn btn-sm btn-danger">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const categories = ref([]);

onMounted(async () => {
  try {
    const response = await axios.get('/categories');
    categories.value = response.data || [];
  } catch (err) {
    console.error('Failed to fetch categories:', err);
  }
});
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
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 1.875rem;
}

.admin-card {
  background: var(--white);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid var(--gray-100);
}

th {
  background: var(--gray-50);
  font-weight: 500;
  color: var(--gray-600);
}

.badge {
  padding: 0.25rem 0.75rem;
  border-radius: var(--radius-full);
  font-size: 0.75rem;
  font-weight: 500;
}

.badge.active {
  background: #d1fae5;
  color: #065f46;
}

.badge.inactive {
  background: #fee2e2;
  color: #991b1b;
}

.btn-sm {
  padding: 0.375rem 0.75rem;
  font-size: 0.75rem;
}

.btn-danger {
  background: #ef4444;
  color: white;
  margin-left: 0.5rem;
}
</style>
