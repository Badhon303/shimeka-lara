<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::whereNull('parent_id')->with('children')->get();

        // Cosmetic Products
        $cosmeticProducts = [
            // Skincare
            [
                'name' => 'Glow Brightening Face Wash',
                'price' => 450,
                'compare_price' => 550,
                'category_slug' => 'face-wash',
                'description' => 'Gentle face wash with vitamin C for bright, glowing skin. Removes impurities while maintaining natural moisture balance.',
                'brand' => 'Glow & Glam',
                'stock' => 50,
                'featured' => true,
                'new' => false,
            ],
            [
                'name' => 'Hydrating Rose Water Toner',
                'price' => 380,
                'compare_price' => 480,
                'category_slug' => 'face-wash',
                'description' => 'Pure rose water toner that hydrates and refreshes skin. Alcohol-free formula suitable for all skin types.',
                'brand' => 'Rose Beauty',
                'stock' => 40,
                'featured' => false,
                'new' => true,
            ],
            [
                'name' => 'Anti-Aging Night Cream',
                'price' => 890,
                'compare_price' => 1200,
                'category_slug' => 'moisturizers',
                'description' => 'Rich night cream with retinol and collagen. Reduces fine lines and wrinkles while you sleep.',
                'brand' => 'Youth Elixir',
                'stock' => 25,
                'featured' => true,
                'new' => false,
            ],
            [
                'name' => 'Vitamin C Glow Serum',
                'price' => 1200,
                'compare_price' => 1500,
                'category_slug' => 'serums',
                'description' => 'High potency vitamin C serum for radiant skin. Reduces dark spots and evens skin tone.',
                'brand' => 'Glow & Glam',
                'stock' => 30,
                'featured' => true,
                'new' => true,
            ],
            [
                'name' => 'SPF 50 Sunscreen Lotion',
                'price' => 650,
                'compare_price' => 800,
                'category_slug' => 'sunscreen',
                'description' => 'Broad spectrum SPF 50 sunscreen. Non-greasy formula perfect for daily use.',
                'brand' => 'SunShield',
                'stock' => 60,
                'featured' => false,
                'new' => false,
            ],
            // Makeup
            [
                'name' => 'Matte Perfection Foundation',
                'price' => 950,
                'compare_price' => 1200,
                'category_slug' => 'foundation',
                'description' => 'Long-lasting matte foundation with full coverage. Available in multiple shades.',
                'brand' => 'Glam Studio',
                'stock' => 35,
                'featured' => true,
                'new' => false,
            ],
            [
                'name' => 'Velvet Kiss Lipstick - Red Passion',
                'price' => 550,
                'compare_price' => 700,
                'category_slug' => 'lipstick',
                'description' => 'Long-wearing velvet matte lipstick in classic red. Richly pigmented and moisturizing.',
                'brand' => 'Lip Luxe',
                'stock' => 45,
                'featured' => true,
                'new' => true,
            ],
            [
                'name' => 'Nude Palette Eyeshadow',
                'price' => 1450,
                'compare_price' => 1800,
                'category_slug' => 'eyeshadow',
                'description' => '12 shade nude eyeshadow palette. Highly pigmented colors for everyday glam.',
                'brand' => 'Eye Magic',
                'stock' => 20,
                'featured' => true,
                'new' => false,
            ],
            [
                'name' => 'Volume Boost Mascara',
                'price' => 480,
                'compare_price' => 650,
                'category_slug' => 'mascara',
                'description' => 'Waterproof mascara for voluminous lashes. Smudge-proof formula.',
                'brand' => 'Lash Queen',
                'stock' => 55,
                'featured' => false,
                'new' => true,
            ],
            // Fragrance
            [
                'name' => 'Midnight Rose Perfume',
                'price' => 2500,
                'compare_price' => 3200,
                'category_slug' => 'fragrance',
                'description' => 'Elegant floral fragrance with notes of rose, jasmine, and sandalwood. Long-lasting scent.',
                'brand' => 'Glam Perfumes',
                'stock' => 15,
                'featured' => true,
                'new' => false,
            ],
            [
                'name' => 'Fresh Blossom Body Mist',
                'price' => 350,
                'compare_price' => 450,
                'category_slug' => 'fragrance',
                'description' => 'Light and refreshing body mist with floral notes. Perfect for daily wear.',
                'brand' => 'Fresh Scents',
                'stock' => 70,
                'featured' => false,
                'new' => true,
            ],
            // Hair Care
            [
                'name' => 'Argan Oil Shampoo',
                'price' => 580,
                'compare_price' => 750,
                'category_slug' => 'shampoo',
                'description' => 'Nourishing shampoo with Moroccan argan oil. Repairs damaged hair and adds shine.',
                'brand' => 'Hair Luxe',
                'stock' => 40,
                'featured' => false,
                'new' => false,
            ],
            [
                'name' => 'Coconut Hair Oil',
                'price' => 320,
                'compare_price' => 400,
                'category_slug' => 'hair-oil',
                'description' => 'Pure coconut oil for deep hair nourishment. Promotes hair growth and prevents breakage.',
                'brand' => 'Nature Care',
                'stock' => 80,
                'featured' => true,
                'new' => false,
            ],
        ];

        // Dress/Fashion Products
        $fashionProducts = [
            // Dresses
            [
                'name' => 'Floral Print Maxi Dress',
                'price' => 1890,
                'compare_price' => 2500,
                'category_slug' => 'maxi-dresses',
                'description' => 'Beautiful floral print maxi dress with flowing silhouette. Perfect for summer outings.',
                'brand' => 'Glam Fashion',
                'stock' => 20,
                'featured' => true,
                'new' => true,
                'attributes' => ['size' => ['S', 'M', 'L', 'XL'], 'color' => ['Blue', 'Pink', 'Yellow']],
            ],
            [
                'name' => 'Elegant Black Evening Dress',
                'price' => 3500,
                'compare_price' => 4500,
                'category_slug' => 'party-dresses',
                'description' => 'Sophisticated black evening dress with lace details. Perfect for parties and events.',
                'brand' => 'Elegance',
                'stock' => 12,
                'featured' => true,
                'new' => false,
                'attributes' => ['size' => ['S', 'M', 'L', 'XL'], 'color' => ['Black']],
            ],
            [
                'name' => 'Casual Denim Dress',
                'price' => 1450,
                'compare_price' => 1900,
                'category_slug' => 'casual-dresses',
                'description' => 'Comfortable denim dress for everyday wear. Features button front and pockets.',
                'brand' => 'Denim Co',
                'stock' => 25,
                'featured' => false,
                'new' => true,
                'attributes' => ['size' => ['S', 'M', 'L', 'XL'], 'color' => ['Blue', 'Light Blue']],
            ],
            // Tops
            [
                'name' => 'Silk Embroidered Blouse',
                'price' => 1200,
                'compare_price' => 1600,
                'category_slug' => 'blouses',
                'description' => 'Elegant silk blouse with hand embroidery. Perfect for formal occasions.',
                'brand' => 'Ethnic Touch',
                'stock' => 18,
                'featured' => true,
                'new' => false,
                'attributes' => ['size' => ['S', 'M', 'L', 'XL'], 'color' => ['White', 'Cream']],
            ],
            [
                'name' => 'Graphic Print T-shirt',
                'price' => 450,
                'compare_price' => 650,
                'category_slug' => 't-shirts',
                'description' => 'Trendy graphic print cotton t-shirt. Comfortable and stylish for casual wear.',
                'brand' => 'Trendy Tees',
                'stock' => 50,
                'featured' => false,
                'new' => true,
                'attributes' => ['size' => ['S', 'M', 'L', 'XL'], 'color' => ['White', 'Black', 'Pink']],
            ],
            // Ethnic Wear
            [
                'name' => 'Banarasi Silk Saree',
                'price' => 5500,
                'compare_price' => 7500,
                'category_slug' => 'sarees',
                'description' => 'Luxurious Banarasi silk saree with intricate zari work. Perfect for weddings.',
                'brand' => 'Heritage Sarees',
                'stock' => 8,
                'featured' => true,
                'new' => false,
                'attributes' => ['color' => ['Red', 'Green', 'Blue', 'Purple']],
            ],
            [
                'name' => 'Cotton Printed Kurti',
                'price' => 890,
                'compare_price' => 1200,
                'category_slug' => 'kurtis',
                'description' => 'Comfortable cotton kurti with block print. Ideal for daily wear.',
                'brand' => 'Daily Wear',
                'stock' => 35,
                'featured' => false,
                'new' => true,
                'attributes' => ['size' => ['S', 'M', 'L', 'XL', 'XXL'], 'color' => ['Pink', 'Yellow', 'Green']],
            ],
            [
                'name' => 'Designer Salwar Kameez',
                'price' => 2800,
                'compare_price' => 3800,
                'category_slug' => 'salwar-kameez',
                'description' => 'Beautiful designer salwar kameez with intricate embroidery.',
                'brand' => 'Desi Fashion',
                'stock' => 15,
                'featured' => true,
                'new' => true,
                'attributes' => ['size' => ['S', 'M', 'L', 'XL'], 'color' => ['Pink', 'Blue', 'Green']],
            ],
            // Accessories
            [
                'name' => 'Pearl Stud Earrings',
                'price' => 350,
                'compare_price' => 500,
                'category_slug' => 'accessories',
                'description' => 'Elegant pearl stud earrings. Classic design for everyday wear.',
                'brand' => 'Jewel Box',
                'stock' => 40,
                'featured' => false,
                'new' => true,
            ],
            [
                'name' => 'Leather Handbag',
                'price' => 2200,
                'compare_price' => 3000,
                'category_slug' => 'accessories',
                'description' => 'Stylish leather handbag with multiple compartments. Perfect for daily use.',
                'brand' => 'Bag Charm',
                'stock' => 20,
                'featured' => true,
                'new' => false,
                'attributes' => ['color' => ['Black', 'Brown', 'Tan']],
            ],
        ];

        foreach (array_merge($cosmeticProducts, $fashionProducts) as $data) {
            $category = Category::where('slug', $data['category_slug'])->first();
            
            if (!$category) {
                continue;
            }

            $attributes = $data['attributes'] ?? null;
            unset($data['attributes']);

            Product::create([
                'name' => $data['name'],
                'slug' => \Str::slug($data['name']),
                'description' => $data['description'],
                'short_description' => substr($data['description'], 0, 100) . '...',
                'price' => $data['price'],
                'compare_price' => $data['compare_price'],
                'stock_quantity' => $data['stock'],
                'category_id' => $category->id,
                'brand' => $data['brand'],
                'is_featured' => $data['featured'],
                'is_new' => $data['new'],
                'is_active' => true,
                'attributes' => $attributes,
                'featured_image' => 'https://via.placeholder.com/400x400/ec4899/ffffff?text=' . urlencode($data['name']),
            ]);
        }
    }
}
