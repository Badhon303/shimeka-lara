<template>
  <div class="about-page">
    <div class="page-header">
      <div class="container">
        <h1>About Glow & Glam</h1>
        <p>Your trusted destination for beauty and fashion</p>
      </div>
    </div>

    <div class="container">
      <div class="about-content">
        <div class="about-section">
          <div class="about-image">
            <img src="https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=800&q=80" alt="About Us" />
          </div>
          <div class="about-text">
            <h2>Our Story</h2>
            <p>Founded in 2020, Glow & Glam has become Bangladesh's premier destination for cosmetics and fashion. We believe everyone deserves to feel beautiful and confident.</p>
            <p>Our carefully curated collection features the finest skincare products, makeup, fragrances, and trendy fashion pieces that celebrate your unique style.</p>
          </div>
        </div>

        <div class="values-section">
          <h2>Our Values</h2>
          <div class="values-grid">
            <div class="value-card">
              <span class="icon">✨</span>
              <h3>Quality First</h3>
              <p>We only offer products from trusted brands that meet our high standards.</p>
            </div>
            <div class="value-card">
              <span class="icon">💚</span>
              <h3>Customer Love</h3>
              <p>Your satisfaction is our priority. We're here to help you look and feel your best.</p>
            </div>
            <div class="value-card">
              <span class="icon">🌱</span>
              <h3>Sustainability</h3>
              <p>We're committed to eco-friendly practices and sustainable beauty solutions.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.about-page {
  min-height: 100vh;
  background: var(--gray-50);
}

.about-content {
  padding: 4rem 0;
}

.about-section {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
  margin-bottom: 4rem;
}

.about-image img {
  width: 100%;
  border-radius: var(--radius-2xl);
  box-shadow: var(--shadow-xl);
}

.about-text h2 {
  font-size: 2rem;
  margin-bottom: 1rem;
}

.about-text p {
  color: var(--gray-600);
  margin-bottom: 1rem;
  line-height: 1.7;
}

.values-section {
  text-align: center;
}

.values-section h2 {
  font-size: 2rem;
  margin-bottom: 2rem;
}

.values-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
}

.value-card {
  background: var(--white);
  padding: 2rem;
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-sm);
}

.value-card .icon {
  font-size: 3rem;
  margin-bottom: 1rem;
  display: block;
}

.value-card h3 {
  font-size: 1.25rem;
  margin-bottom: 0.5rem;
}

.value-card p {
  color: var(--gray-500);
  font-size: 0.875rem;
}

@media (max-width: 768px) {
  .about-section {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  .values-grid {
    grid-template-columns: 1fr;
  }
}
</style>
