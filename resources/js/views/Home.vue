<template>
  <div class="home">
    <!-- Hero Slider -->
    <section class="hero-slider">
      <div class="hero-slides" :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
        <div v-for="(slide, i) in heroSlides" :key="i" class="hero-slide">
          <div class="hero-content">
            <div class="hero-text">
              <span class="hero-subtitle">{{ slide.subtitle || 'New Collection' }}</span>
              <h1>{{ slide.title || 'Discover Your Beauty & Style' }}</h1>
              <p>{{ slide.description || 'Explore our premium cosmetics and fashion collection.' }}</p>
              <div class="hero-buttons" v-if="slide.button_text">
                <router-link :to="slide.button_link || '/shop'" class="btn btn-primary">
                  {{ slide.button_text }}
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.25rem; height: 1.25rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                  </svg>
                </router-link>
              </div>
            </div>
            <div class="hero-image">
              <img :src="slide.image || 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=800&q=80'" :alt="slide.title || 'Hero'" />
            </div>
          </div>
        </div>
      </div>
      <div class="hero-dots" v-if="heroSlides.length > 1">
        <button v-for="(_, i) in heroSlides" :key="i" :class="['hero-dot', { active: currentSlide === i }]" @click="goToSlide(i)"></button>
      </div>
      <button v-if="heroSlides.length > 1" class="hero-arrow hero-prev" @click="prevSlide">&#8249;</button>
      <button v-if="heroSlides.length > 1" class="hero-arrow hero-next" @click="nextSlide">&#8250;</button>
    </section>

    <!-- Categories Section -->
    <section class="section">
      <div class="container">
        <div class="section-header">
          <span class="section-subtitle">Browse By</span>
          <h2 class="section-title">Shop by Category</h2>
        </div>
        <div class="categories-grid">
          <router-link
            v-for="category in leafCategories"
            :key="category.id"
            :to="`/category/${category.slug}`"
            class="category-card"
            :class="category.type"
          >
            <img :src="category.image || '/images/placeholder.jpg'" :alt="category.name" />
            <div class="category-overlay">
              <h3>{{ category.name }}</h3>
              <p>{{ category.products_count || 0 }} Products</p>
            </div>
          </router-link>
        </div>
        <!-- Mobile horizontal category scroll -->
        <div class="categories-scroll mobile-only">
          <router-link
            v-for="category in leafCategories"
            :key="category.id"
            :to="`/category/${category.slug}`"
            class="category-pill"
          >
            <img :src="category.image || '/images/placeholder.jpg'" :alt="category.name" />
            <span>{{ category.name }}</span>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section class="section featured-section">
      <div class="container">
        <div class="section-header">
          <span class="section-subtitle">Curated For You</span>
          <h2 class="section-title">Featured Products</h2>
        </div>
        <div class="products-grid">
          <ProductCard 
            v-for="product in featuredProducts" 
            :key="product.id"
            :product="product"
          />
        </div>
        <div class="products-scroll mobile-only">
          <ProductCard 
            v-for="product in featuredProducts" 
            :key="product.id"
            :product="product"
          />
        </div>
        <div class="text-center mt-8">
          <router-link to="/shop" class="btn btn-outline">View All Products</router-link>
        </div>
      </div>
    </section>

    <!-- Cosmetics Section -->
    <section class="section cosmetics-section">
      <div class="container">
        <div class="split-section">
          <div class="split-image">
            <img :src="homeBanners.cosmetics || 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=800&q=80'" alt="Cosmetics" />
          </div>
          <div class="split-content">
            <span class="section-subtitle">Beauty Essentials</span>
            <h2>Premium Cosmetics</h2>
            <p>Discover our range of high-quality cosmetics from top brands. From everyday essentials to luxury makeup, enhance your natural beauty.</p>
            <div class="cosmetics-products">
              <ProductCard 
                v-for="product in cosmetics" 
                :key="product.id"
                :product="product"
                compact
              />
            </div>
            <router-link to="/category/skincare" class="btn btn-primary mt-4">Shop Cosmetics</router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Dress Collection -->
    <section class="section fashion-section">
      <div class="container">
        <div class="split-section reverse">
          <div class="split-content">
            <span class="section-subtitle">Trendy Fashion</span>
            <h2>Stunning Dresses</h2>
            <p>Find the perfect dress for any occasion. From casual everyday wear to elegant evening gowns, express your unique style.</p>
            <div class="fashion-products">
              <ProductCard 
                v-for="product in dresses" 
                :key="product.id"
                :product="product"
                compact
              />
            </div>
            <router-link to="/category/dresses" class="btn btn-primary mt-4">Shop Dresses</router-link>
          </div>
          <div class="split-image">
            <img :src="homeBanners.fashion || 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=800&q=80'" alt="Dresses" />
          </div>
        </div>
      </div>
    </section>

    <!-- New Arrivals -->
    <section class="section">
      <div class="container">
        <div class="section-header">
          <span class="section-subtitle">Just Landed</span>
          <h2 class="section-title">New Arrivals</h2>
        </div>
        <div class="products-grid">
          <ProductCard 
            v-for="product in newArrivals" 
            :key="product.id"
            :product="product"
          />
        </div>
      </div>
    </section>

    <!-- Features Banner -->
    <section class="section features-section">
      <div class="container">
        <div class="features-grid">
          <div v-for="(feature, i) in homeFeatures" :key="i" class="feature-item">
            <div class="feature-icon">{{ feature.icon }}</div>
            <h4>{{ feature.title }}</h4>
            <p>{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import ProductCard from '../components/ProductCard.vue';

const categories = ref([]);
const featuredProducts = ref([]);
const newArrivals = ref([]);
const cosmetics = ref([]);
const dresses = ref([]);
const loading = ref(true);
const currentSlide = ref(0);
const slideInterval = ref(null);

const defaultHeroSlides = [
  {
    subtitle: 'New Collection 2024',
    title: 'Discover Your Beauty & Style',
    description: 'Explore our premium cosmetics and fashion collection. From skincare essentials to trendy dresses, find everything you need to glow.',
    button_text: 'Shop Now',
    button_link: '/shop',
    image: 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=800&q=80'
  },
  {
    subtitle: 'Summer Sale',
    title: 'Up to 50% Off',
    description: 'Get amazing discounts on our best-selling products. Limited time offer!',
    button_text: 'Shop Sale',
    button_link: '/shop',
    image: 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=800&q=80'
  },
  {
    subtitle: 'New Arrivals',
    title: 'Ethnic Wear Collection',
    description: 'Beautiful sarees, kurtis and salwar kameez for every occasion.',
    button_text: 'Explore',
    button_link: '/category/ethnic',
    image: 'https://images.unsplash.com/photo-1610189012906-4f8545f4a31c?w=800&q=80'
  }
];

const heroSlides = ref([...defaultHeroSlides]);
const homeBanners = ref({ cosmetics: '', fashion: '' });
const defaultHomeFeatures = [
  { icon: '🚚', title: 'Free Shipping', description: 'On orders over ৳1000' },
  { icon: '💵', title: 'Cash on Delivery', description: 'Pay when you receive' },
  { icon: '🎁', title: 'Gift Wrapping', description: 'Beautiful gift packages' },
  { icon: '↩️', title: 'Easy Returns', description: '7-day return policy' }
];
const homeFeatures = ref([...defaultHomeFeatures]);

const leafCategories = computed(() => {
  return categories.value;
});

function nextSlide() {
  currentSlide.value = (currentSlide.value + 1) % heroSlides.value.length;
}

function prevSlide() {
  currentSlide.value = (currentSlide.value - 1 + heroSlides.value.length) % heroSlides.value.length;
}

function goToSlide(i) {
  currentSlide.value = i;
  resetSlideTimer();
}

function resetSlideTimer() {
  if (slideInterval.value) clearInterval(slideInterval.value);
  slideInterval.value = setInterval(nextSlide, 5000);
}

async function fetchHomeData() {
  try {
    const [homeRes, settingsRes] = await Promise.all([
      axios.get('/'),
      axios.get('/settings').catch(() => ({ data: {} }))
    ]);

    const data = homeRes.data;
    categories.value = data.categories || [];
    featuredProducts.value = data.featured_products || [];
    newArrivals.value = data.new_arrivals || [];
    cosmetics.value = data.cosmetics || [];
    dresses.value = data.dresses || [];

    // Load hero slides from settings if available
    if (settingsRes.data.hero_slides && settingsRes.data.hero_slides.length > 0) {
      heroSlides.value = settingsRes.data.hero_slides;
    }
    // Load home banner images from settings
    if (settingsRes.data.home_banners) {
      homeBanners.value = settingsRes.data.home_banners;
    }
    // Load home features from settings
    if (settingsRes.data.home_features && settingsRes.data.home_features.length > 0) {
      homeFeatures.value = settingsRes.data.home_features;
    }
  } catch (err) {
    console.error('Failed to fetch home data:', err);
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  fetchHomeData();
  resetSlideTimer();
});
</script>

<style scoped>
.category-card {
  position: relative;
  border-radius: var(--radius-xl);
  overflow: hidden;
  aspect-ratio: 1;
  box-shadow: var(--shadow-md);
  transition: all var(--transition-base);
}

.category-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-xl);
}

.category-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.category-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 1.5rem;
  color: var(--white);
}

.category-overlay h3 {
  font-size: 1.5rem;
  color: var(--white);
  margin-bottom: 0.25rem;
}

.category-overlay p {
  font-size: 0.875rem;
  opacity: 0.9;
}

.category-card.cosmetics .category-overlay {
  background: linear-gradient(to top, rgba(219, 39, 119, 0.8), transparent);
}

.category-card.dress .category-overlay {
  background: linear-gradient(to top, rgba(30, 58, 138, 0.8), transparent);
}

.featured-section {
  background: linear-gradient(to bottom, var(--white), var(--primary-50));
}

.cosmetics-section {
  background: linear-gradient(135deg, #fff5f7 0%, #ffeef2 100%);
}

.fashion-section {
  background: linear-gradient(135deg, #f0f9ff 0%, #f5f3ff 100%);
}

.split-section {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
}

.split-section.reverse {
  direction: rtl;
}

.split-section.reverse > * {
  direction: ltr;
}

.split-image img {
  width: 100%;
  height: 500px;
  object-fit: cover;
  border-radius: var(--radius-2xl);
  box-shadow: var(--shadow-xl);
}

.split-content h2 {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.split-content p {
  font-size: 1.125rem;
  color: var(--gray-600);
  margin-bottom: 2rem;
}

.cosmetics-products,
.fashion-products {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.features-section {
  background: var(--gray-900);
  color: var(--white);
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2rem;
}

.feature-item {
  text-align: center;
  padding: 2rem;
}

.feature-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.feature-item h4 {
  font-size: 1.125rem;
  color: var(--white);
  margin-bottom: 0.5rem;
}

.feature-item p {
  font-size: 0.875rem;
  color: var(--gray-400);
}

.hero-subtitle {
  display: inline-block;
  padding: 0.5rem 1rem;
  background: var(--gold-400);
  color: var(--gray-900);
  font-size: 0.875rem;
  font-weight: 600;
  border-radius: var(--radius-full);
  margin-bottom: 1rem;
}

.mt-4 { margin-top: 1rem; }
.mt-8 { margin-top: 2rem; }
.text-center { text-align: center; }

@media (max-width: 768px) {
  .split-section {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  .split-section.reverse {
    direction: ltr;
  }
  .cosmetics-products,
  .fashion-products {
    grid-template-columns: 1fr;
  }
  .features-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .split-image img {
    height: 300px;
  }
}
</style>
