# Glow & Glam - Cosmetics & Fashion E-commerce

A modern, premium e-commerce platform for cosmetics and fashion built with Laravel, Vue.js, and MySQL.

## Features

### Customer Features
- **Beautiful Modern UI** - Rose gold and elegant theme
- **Product Categories** - Cosmetics (Skincare, Makeup, Fragrance, Hair Care) & Fashion (Dresses, Tops, Ethnic Wear, Accessories)
- **Shopping Cart** - Add to cart, update quantities, remove items
- **User Authentication** - Register, Login, Profile management
- **Order Management** - Place orders, track status, order history
- **Search & Filter** - Find products easily
- **Responsive Design** - Works on all devices

### Admin Features
- **Dashboard** - View statistics, recent orders, low stock alerts
- **Product Management** - CRUD operations for products
- **Category Management** - Manage product categories
- **Order Management** - View and update order status
- **User Management** - View registered users

## Tech Stack

- **Backend:** Laravel 10, PHP 8.1+
- **Frontend:** Vue.js 3, Vite, Pinia
- **Database:** MySQL
- **Authentication:** Laravel Sanctum
- **Styling:** Custom CSS with CSS Variables

## Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js 18+ and NPM
- MySQL

### Step 1: Clone and Install Dependencies

```bash
cd /home/javed/Desktop/javed/ecommerce
composer install
npm install
```

### Step 2: Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file with your database credentials:
```
DB_DATABASE=ecommerce_glow_glam
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 3: Database Setup

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE ecommerce_glow_glam;"

# Run migrations
php artisan migrate

# Seed dummy data
php artisan db:seed
```

### Step 4: Build Frontend

```bash
npm run build
```

For development:
```bash
npm run dev
```

### Step 5: Start Server

```bash
php artisan serve
```

Visit: http://localhost:8000

## Default Login Credentials

### Admin Account
- Email: `admin@glowglam.com`
- Password: `password`

### Test User Accounts
- Email: `fatima@example.com`
- Password: `password`

(Other test users: ayesh@example.com, nusrat@example.com, sabina@example.com, rina@example.com)

## Project Structure

```
ecommerce/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/          # API Controllers
│   │   │   └── ...
│   │   └── Middleware/
│   └── Models/               # Eloquent Models
├── database/
│   ├── migrations/           # Database Migrations
│   └── seeders/              # Database Seeders
├── resources/
│   ├── js/
│   │   ├── components/       # Vue Components
│   │   ├── views/            # Vue Views/Pages
│   │   ├── stores/           # Pinia Stores
│   │   └── router/           # Vue Router
│   ├── css/                  # Stylesheets
│   └── views/                # Blade Templates
├── routes/
│   ├── api.php              # API Routes
│   └── web.php              # Web Routes
└── config/                  # Configuration Files
```

## API Endpoints

### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user
- `GET /api/user` - Get current user

### Products
- `GET /api/products` - List all products
- `GET /api/products/featured` - Featured products
- `GET /api/products/new` - New arrivals
- `GET /api/product/{slug}` - Get single product

### Categories
- `GET /api/categories` - List all categories
- `GET /api/category/{slug}` - Get category with products

### Cart (Authenticated)
- `GET /api/cart` - Get cart items
- `POST /api/cart/add` - Add item to cart
- `PUT /api/cart/update/{id}` - Update quantity
- `DELETE /api/cart/remove/{id}` - Remove item

### Orders (Authenticated)
- `GET /api/orders` - Get user orders
- `POST /api/orders` - Place order
- `GET /api/orders/{id}` - Get order details

### Admin (Admin Only)
- `GET /api/admin/dashboard` - Dashboard stats
- `GET /api/admin/users` - List users
- `GET /api/admin/orders` - List all orders

## Dummy Data Included

The seeders create:
- 1 Admin user
- 5 Sample customers
- 12 Categories (4 cosmetics, 4 fashion with subcategories)
- 20+ Products (cosmetics and dresses)
- 2 Hero banners

## Theme Colors

- **Primary:** Rose Pink (#ec4899)
- **Gold Accent:** #f59e0b
- **Cosmetics Theme:** Soft pinks, lavenders, peaches
- **Fashion Theme:** Navy, emerald, burgundy, teal

## License

MIT License

## Support

For issues or questions, please create an issue in the repository.

---

**Made with love for cosmetics and fashion lovers!**
