<template>
  <div id="app">
    <Navbar v-if="!isAdminRoute" />
    <AdminSidebar v-if="isAdminRoute && isAdmin" :class="{ open: sidebarOpen }" />
    <div v-if="isAdminRoute && isAdmin" class="admin-sidebar-overlay" :class="{ show: sidebarOpen }" @click="sidebarOpen = false"></div>
    <button v-if="isAdminRoute && isAdmin" class="admin-mobile-toggle" @click="sidebarOpen = !sidebarOpen">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
      </svg>
    </button>
    <!-- Admin Desktop Top Bar -->
    <header v-if="isAdminRoute && isAdmin" class="admin-top-bar">
      <div class="top-bar-left">
        <button class="sidebar-toggle" @click="sidebarOpen = !sidebarOpen">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
        </button>
        <h2 class="page-title">{{ pageTitle }}</h2>
      </div>
      <div class="top-bar-right">
        <router-link to="/" class="top-bar-link">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
          Store
        </router-link>
        <div class="user-menu" v-if="authStore.isAuthenticated">
          <span class="user-name">{{ authStore.user?.name || 'Admin' }}</span>
          <button @click="logout" class="logout-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
            Logout
          </button>
        </div>
      </div>
    </header>

    <main :class="{ 'admin-main': isAdminRoute }">
      <router-view />
    </main>
    <Footer v-if="!isAdminRoute" />

    <!-- Mobile Bottom Nav (customer) -->
    <nav v-if="!isAdminRoute" class="mobile-bottom-nav">
      <router-link to="/" class="mob-nav-item" active-class="active">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
        <span>Home</span>
      </router-link>
      <router-link to="/shop" class="mob-nav-item" active-class="active">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m-9.856-13.5c-.089.356-.532.536-.854.42a6.685 6.685 0 0 0-2.42-.42c-1.09 0-2.1.3-2.97.816-.334.2-.777.02-.866-.418a6.68 6.68 0 0 1 .91-4.72c.834-1.304 2.168-2.19 3.68-2.44 1.513-.25 3.046.14 4.19 1.17a.75.75 0 0 1 0 1.114c-1.13 1.02-2.65 1.41-4.16 1.16z" /></svg>
        <span>Shop</span>
      </router-link>
      <router-link to="/cart" class="mob-nav-item" active-class="active">
        <span class="nav-icon-wrap">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
          <span v-if="cartCount > 0" class="nav-badge">{{ cartCount }}</span>
        </span>
        <span>Cart</span>
      </router-link>
      <router-link to="/wishlist" class="mob-nav-item" active-class="active">
        <span class="nav-icon-wrap">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" /></svg>
          <span v-if="wishlistCount > 0" class="nav-badge">{{ wishlistCount }}</span>
        </span>
        <span>Wishlist</span>
      </router-link>
      <router-link to="/profile" class="mob-nav-item" active-class="active">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
        <span>Account</span>
      </router-link>
    </nav>

    <!-- Mobile Bottom Nav (admin) -->
    <nav v-if="isAdminRoute && isAdmin" class="admin-mobile-nav">
      <router-link to="/admin" class="mob-nav-item" active-class="active">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
        <span>Dash</span>
      </router-link>
      <router-link to="/admin/products" class="mob-nav-item" active-class="active">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /></svg>
        <span>Products</span>
      </router-link>
      <router-link to="/admin/orders" class="mob-nav-item" active-class="active">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V19.5a2.25 2.25 0 0 0 2.25 2.25h.75a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08" /></svg>
        <span>Orders</span>
      </router-link>
      <button @click="showAdminMore = true" class="mob-nav-item" :class="{ active: showAdminMore }">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
        <span>More</span>
      </button>
    </nav>

    <!-- Admin Mobile More Menu Sheet -->
    <div v-if="showAdminMore" class="admin-more-overlay" @click="showAdminMore = false"></div>
    <div v-if="showAdminMore" class="admin-more-sheet">
      <div class="admin-more-header">
        <h4>Menu</h4>
        <button @click="showAdminMore = false" class="close-more">&times;</button>
      </div>
      <div class="admin-more-grid">
        <router-link to="/admin" class="more-item" @click="showAdminMore = false">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
          <span>Dashboard</span>
        </router-link>
        <router-link to="/admin/products" class="more-item" @click="showAdminMore = false">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /></svg>
          <span>Products</span>
        </router-link>
        <router-link to="/admin/orders" class="more-item" @click="showAdminMore = false">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V19.5a2.25 2.25 0 0 0 2.25 2.25h.75a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08" /></svg>
          <span>Orders</span>
        </router-link>
        <router-link to="/admin/categories" class="more-item" @click="showAdminMore = false">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.489l3.272-1.636a2.25 2.25 0 0 0 1.114-1.987V5.25A2.25 2.25 0 0 0 18 3H9.568Z" /></svg>
          <span>Categories</span>
        </router-link>
        <router-link to="/admin/users" class="more-item" @click="showAdminMore = false">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
          <span>Users</span>
        </router-link>
        <router-link to="/admin/coupons" class="more-item" @click="showAdminMore = false">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H4.5a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A7.125 7.125 0 0 0 4.875 12M12 4.875a7.125 7.125 0 0 1 7.125 7.125M12 4.875v14.25" /></svg>
          <span>Coupons</span>
        </router-link>
        <router-link to="/admin/reports" class="more-item" @click="showAdminMore = false">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
          <span>Reports</span>
        </router-link>
        <router-link to="/admin/couriers" class="more-item" @click="showAdminMore = false">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25M15 11.25a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V11.25Zm-9 0h.008v.008H9V11.25Z" /></svg>
          <span>Couriers</span>
        </router-link>
        <router-link to="/admin/low-stock" class="more-item" @click="showAdminMore = false">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
          <span>Low Stock</span>
        </router-link>
        <router-link to="/admin/hero-slider" class="more-item" @click="showAdminMore = false">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6v12a2.25 2.25 0 002.25 2.25z" /></svg>
          <span>Hero Slider</span>
        </router-link>
        <router-link to="/admin/home-banners" class="more-item" @click="showAdminMore = false">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.077-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.388 1.62a15.998 15.998 0 001.128-5.78 2.25 2.25 0 00-2.245-2.4 4.5 4.5 0 00-8.4 2.245c0 .399.077.78.22 1.128m0 0a15.998 15.998 0 01-3.388 1.62m5.043.025a15.994 15.994 0 01-1.622 3.395m-3.388-1.62a15.998 15.998 0 00-1.128 5.78 2.25 2.25 0 002.245 2.4 4.5 4.5 0 008.4-2.245c0-.399-.077-.78-.22-1.128z" /></svg>
          <span>Banners</span>
        </router-link>
        <router-link to="/admin/settings" class="more-item" @click="showAdminMore = false">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.298-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" /></svg>
          <span>Settings</span>
        </router-link>
      </div>
      <div class="admin-more-footer">
        <button @click="logout" class="more-logout">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
          Logout
        </button>
      </div>
    </div>

    <CartDrawer />
    <Toast />
  </div>
</template>

<script setup>
import { computed, ref, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from './stores/auth';
import { useCartStore } from './stores/cart';
import Navbar from './components/Navbar.vue';
import Footer from './components/Footer.vue';
import AdminSidebar from './components/admin/AdminSidebar.vue';
import CartDrawer from './components/CartDrawer.vue';
import Toast from './components/Toast.vue';

const route = useRoute();
const authStore = useAuthStore();
const cartStore = useCartStore();
const sidebarOpen = ref(false);

const isAdminRoute = computed(() => route.path.startsWith('/admin') && route.path !== '/admin/login');
const isAdmin = computed(() => authStore.user?.is_admin);
const showAdminMore = ref(false);

const pageTitle = computed(() => {
  const titles = {
    '/admin': 'Dashboard',
    '/admin/products': 'Products',
    '/admin/low-stock': 'Low Stock',
    '/admin/categories': 'Categories',
    '/admin/orders': 'Orders',
    '/admin/couriers': 'Couriers',
    '/admin/users': 'Users',
    '/admin/reports': 'Reports',
    '/admin/coupons': 'Coupons',
    '/admin/hero-slider': 'Hero Slider',
    '/admin/home-banners': 'Home Banners',
    '/admin/home-features': 'Home Features',
    '/admin/settings': 'Settings',
  };
  return titles[route.path] || 'Admin';
});

function logout() {
  authStore.logout();
  window.location.href = '/';
}

const cartCount = computed(() => cartStore.itemCount);
const wishlistCount = ref(0);
function updateWishlistCount() {
  const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
  wishlistCount.value = wishlist.length;
}

onMounted(() => {
  updateWishlistCount();
  window.addEventListener('wishlist-update', updateWishlistCount);
});

// Close sidebar on route change
watch(() => route.path, () => {
  sidebarOpen.value = false;
});
</script>

<style>
/* Mobile Bottom Navigation */
.mobile-bottom-nav, .admin-mobile-nav {
  display: none;
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: var(--white);
  border-top: 1px solid var(--gray-200);
  z-index: 100;
  padding-bottom: env(safe-area-inset-bottom);
}
.mob-nav-item {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 0.5rem 0.25rem;
  gap: 0.25rem;
  color: var(--gray-500);
  font-size: 0.625rem;
  font-weight: 500;
  transition: color 0.15s;
}
.mob-nav-item svg {
  width: 1.5rem;
  height: 1.5rem;
}
.mob-nav-item.active {
  color: var(--primary-600);
}
.nav-icon-wrap { position: relative; display: inline-flex; }
.nav-badge {
  position: absolute;
  top: -6px;
  right: -8px;
  min-width: 18px;
  height: 18px;
  background: #ef4444;
  color: white;
  font-size: 0.625rem;
  font-weight: 700;
  border-radius: 9999px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 4px;
  line-height: 1;
}

/* Admin Top Bar */
.admin-top-bar {
  display: none;
  position: fixed;
  top: 0;
  left: 260px;
  right: 0;
  height: 60px;
  background: var(--white);
  border-bottom: 1px solid var(--gray-200);
  z-index: 50;
  align-items: center;
  justify-content: space-between;
  padding: 0 1.5rem;
}
.top-bar-left { display: flex; align-items: center; gap: 1rem; }
.sidebar-toggle {
  display: none;
  width: 40px;
  height: 40px;
  align-items: center;
  justify-content: center;
  border-radius: var(--radius-md);
  background: var(--gray-100);
}
.sidebar-toggle svg { width: 1.25rem; height: 1.25rem; }
.page-title { font-size: 1.25rem; font-weight: 600; color: var(--gray-800); margin: 0; }
.top-bar-right { display: flex; align-items: center; gap: 1rem; }
.top-bar-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: var(--radius-md);
  color: var(--gray-600);
  font-size: 0.875rem;
  font-weight: 500;
}
.top-bar-link:hover { background: var(--gray-100); color: var(--gray-800); }
.top-bar-link svg { width: 1.125rem; height: 1.125rem; }
.user-menu { display: flex; align-items: center; gap: 0.75rem; }
.user-name { font-size: 0.875rem; font-weight: 500; color: var(--gray-700); }
.logout-btn {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.5rem 0.875rem;
  background: var(--gray-100);
  border-radius: var(--radius-md);
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--gray-700);
}
.logout-btn:hover { background: #fee2e2; color: #dc2626; }
.logout-btn svg { width: 1rem; height: 1rem; }

/* Admin main layout offset for sidebar */
.admin-main { margin-left: 260px; padding-top: 60px; }

/* Admin mobile toggle */
.admin-mobile-toggle {
  display: none;
  position: fixed;
  top: 1rem;
  left: 1rem;
  z-index: 90;
  width: 44px;
  height: 44px;
  background: var(--white);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-md);
  align-items: center;
  justify-content: center;
}
.admin-mobile-toggle svg { width: 1.5rem; height: 1.5rem; }

/* Admin More Overlay & Sheet */
.admin-more-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  z-index: 200;
}
.admin-more-sheet {
  display: none;
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: var(--white);
  border-radius: 1.25rem 1.25rem 0 0;
  z-index: 210;
  max-height: 75vh;
  overflow-y: auto;
  padding: 1rem;
  animation: slideUp 0.25s ease;
}
@keyframes slideUp {
  from { transform: translateY(100%); }
  to { transform: translateY(0); }
}
.admin-more-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 0.75rem;
  margin-bottom: 0.75rem;
  border-bottom: 1px solid var(--gray-200);
}
.admin-more-header h4 { font-size: 1rem; font-weight: 600; margin: 0; }
.close-more {
  width: 32px; height: 32px;
  display: flex; align-items: center; justify-content: center;
  border-radius: var(--radius-full);
  background: var(--gray-100);
  font-size: 1.25rem; color: var(--gray-600);
}
.admin-more-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.75rem;
}
.more-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.375rem;
  padding: 0.875rem 0.5rem;
  border-radius: var(--radius-lg);
  background: var(--gray-50);
  color: var(--gray-700);
  font-size: 0.75rem;
  font-weight: 500;
  text-align: center;
}
.more-item svg { width: 1.5rem; height: 1.5rem; color: var(--primary-500); }
.more-item:active { background: var(--primary-50); }
.admin-more-footer { margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid var(--gray-200); }
.more-logout {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  width: 100%;
  padding: 0.75rem;
  background: #fee2e2;
  color: #dc2626;
  border-radius: var(--radius-lg);
  font-size: 0.875rem;
  font-weight: 500;
}
.more-logout svg { width: 1.125rem; height: 1.125rem; }

@media (max-width: 768px) {
  .mobile-bottom-nav, .admin-mobile-nav { display: flex; }
  #app { padding-bottom: 64px; }
  .admin-top-bar { display: none; }
  .admin-main { margin-left: 0; padding-top: 0; }
  .admin-mobile-toggle { display: flex; }
  .admin-sidebar-overlay { display: none; }
  .admin-sidebar-overlay.show { display: block; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 80; }
  .admin-more-overlay { display: block; }
  .admin-more-sheet { display: block; }
}

@media (min-width: 769px) {
  .admin-top-bar { display: flex; }
  .admin-sidebar { position: fixed; top: 0; left: 0; bottom: 0; width: 260px; }
}
</style>
