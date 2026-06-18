<template>
  <div class="admin-page">
    <div class="admin-content">
      <div class="page-header">
        <h1>Contact Submissions</h1>
        <p>Messages from customers</p>
      </div>

      <div v-if="loading" class="loading-state">Loading...</div>

      <div v-else-if="contacts.length === 0" class="empty-state">
        <p>No contact submissions yet.</p>
      </div>

      <div v-else class="contacts-list">
        <div
          v-for="contact in contacts"
          :key="contact.id"
          :class="['contact-card', { unread: !contact.read }]"
        >
          <div class="contact-header">
            <div class="contact-meta">
              <h4>{{ contact.name }}</h4>
              <span class="contact-email">{{ contact.email }}</span>
            </div>
            <div class="contact-actions">
              <span v-if="!contact.read" class="badge badge-new">New</span>
              <span class="contact-date">{{ formatDate(contact.created_at) }}</span>
              <button class="icon-btn" @click="deleteContact(contact.id)" title="Delete">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
              </button>
            </div>
          </div>
          <p class="contact-message">{{ contact.message }}</p>
          <div v-if="!contact.read" class="contact-footer">
            <button class="btn btn-sm btn-secondary" @click="markRead(contact.id)">Mark as Read</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const contacts = ref([]);
const loading = ref(true);

async function fetchContacts() {
  try {
    const res = await axios.get('/admin/contacts');
    contacts.value = res.data.data || [];
  } catch (e) {
    console.error('Failed to fetch contacts:', e);
  } finally {
    loading.value = false;
  }
}

async function markRead(id) {
  try {
    await axios.put(`/admin/contacts/${id}/read`);
    const contact = contacts.value.find(c => c.id === id);
    if (contact) contact.read = true;
    if (window.$toast) window.$toast('Marked as read', 'success');
  } catch (e) {
    if (window.$toast) window.$toast('Failed to mark as read', 'error');
  }
}

async function deleteContact(id) {
  if (!confirm('Are you sure you want to delete this message?')) return;
  try {
    await axios.delete(`/admin/contacts/${id}`);
    contacts.value = contacts.value.filter(c => c.id !== id);
    if (window.$toast) window.$toast('Deleted', 'success');
  } catch (e) {
    if (window.$toast) window.$toast('Failed to delete', 'error');
  }
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
}

onMounted(fetchContacts);
</script>

<style scoped>
.contacts-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.contact-card {
  background: var(--white);
  border-radius: var(--radius-xl);
  padding: 1.5rem;
  box-shadow: var(--shadow-sm);
  border-left: 3px solid transparent;
}

.contact-card.unread {
  border-left-color: var(--primary-500);
  background: #fff1f2;
}

.contact-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.75rem;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.contact-meta h4 {
  margin: 0 0 0.25rem 0;
  font-size: 1rem;
}

.contact-email {
  font-size: 0.875rem;
  color: var(--gray-500);
}

.contact-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.contact-date {
  font-size: 0.75rem;
  color: var(--gray-400);
}

.contact-message {
  color: var(--gray-700);
  line-height: 1.6;
  margin: 0;
  white-space: pre-wrap;
}

.contact-footer {
  margin-top: 1rem;
  display: flex;
  gap: 0.5rem;
}

.badge-new {
  background: var(--primary-500);
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: var(--radius-full);
  font-size: 0.625rem;
  font-weight: 600;
  text-transform: uppercase;
}

.icon-btn {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: var(--radius-md);
  color: var(--gray-400);
}

.icon-btn:hover {
  background: #fee2e2;
  color: #dc2626;
}

.icon-btn svg {
  width: 1rem;
  height: 1rem;
}

.empty-state {
  text-align: center;
  padding: 4rem;
  color: var(--gray-500);
}

.loading-state {
  text-align: center;
  padding: 4rem;
  color: var(--gray-500);
}

.btn-sm {
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
}
</style>
