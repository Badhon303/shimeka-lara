<template>
  <div id="app">
    <Navbar v-if="!isAdminRoute" />
    <AdminSidebar v-if="isAdminRoute && isAdmin" />
    <main :class="{ 'admin-main': isAdminRoute }">
      <router-view />
    </main>
    <Footer v-if="!isAdminRoute" />
    <CartDrawer />
    <Toast />
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from './stores/auth';
import Navbar from './components/Navbar.vue';
import Footer from './components/Footer.vue';
import AdminSidebar from './components/admin/AdminSidebar.vue';
import CartDrawer from './components/CartDrawer.vue';
import Toast from './components/Toast.vue';

const route = useRoute();
const authStore = useAuthStore();

const isAdminRoute = computed(() => route.path.startsWith('/admin') && route.path !== '/admin/login');
const isAdmin = computed(() => authStore.user?.is_admin);

onMounted(() => {
  // Remove preloader once Vue is mounted
  const preloader = document.getElementById('preloader');
  if (preloader) {
    preloader.style.opacity = '0';
    preloader.style.visibility = 'hidden';
    setTimeout(() => preloader.remove(), 500);
  }
});
</script>
