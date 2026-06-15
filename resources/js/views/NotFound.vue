<template>
  <div class="not-found">
    <div class="container">
      <div class="error-content">
        <h1>404</h1>
        <h2>Page Not Found</h2>
        <p>The page you're looking for doesn't exist or has been moved.</p>
        <div class="error-actions">
          <router-link to="/" class="btn btn-primary">Go Home</router-link>
          <router-link to="/shop" class="btn btn-secondary">Continue Shopping</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.not-found {
  min-height: 70vh;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 4rem 0;
}

.error-content h1 {
  font-size: 8rem;
  font-weight: 700;
  background: linear-gradient(135deg, var(--primary-500), var(--gold-500));
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
  line-height: 1;
  margin-bottom: 1rem;
}

.error-content h2 {
  font-size: 2rem;
  margin-bottom: 1rem;
}

.error-content p {
  color: var(--gray-600);
  margin-bottom: 2rem;
  font-size: 1.125rem;
}

.error-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
}

@media (max-width: 768px) {
  .error-content h1 {
    font-size: 5rem;
  }
  .error-actions {
    flex-direction: column;
    align-items: center;
  }
}
</style>
