<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Cosmetics Categories
        $cosmetics = [
            [
                'name' => 'Skincare',
                'slug' => 'skincare',
                'description' => 'Premium skincare products for radiant skin',
                'type' => 'cosmetics',
                'icon' => '✨',
                'children' => [
                    ['name' => 'Face Wash', 'slug' => 'face-wash'],
                    ['name' => 'Moisturizers', 'slug' => 'moisturizers'],
                    ['name' => 'Serums', 'slug' => 'serums'],
                    ['name' => 'Sunscreen', 'slug' => 'sunscreen'],
                ]
            ],
            [
                'name' => 'Makeup',
                'slug' => 'makeup',
                'description' => 'Professional makeup products',
                'type' => 'cosmetics',
                'icon' => '💄',
                'children' => [
                    ['name' => 'Foundation', 'slug' => 'foundation'],
                    ['name' => 'Lipstick', 'slug' => 'lipstick'],
                    ['name' => 'Eyeshadow', 'slug' => 'eyeshadow'],
                    ['name' => 'Mascara', 'slug' => 'mascara'],
                ]
            ],
            [
                'name' => 'Fragrance',
                'slug' => 'fragrance',
                'description' => 'Luxury perfumes and body mists',
                'type' => 'cosmetics',
                'icon' => '🌸',
            ],
            [
                'name' => 'Hair Care',
                'slug' => 'haircare',
                'description' => 'Nourishing hair care products',
                'type' => 'cosmetics',
                'icon' => '💇‍♀️',
                'children' => [
                    ['name' => 'Shampoo', 'slug' => 'shampoo'],
                    ['name' => 'Conditioner', 'slug' => 'conditioner'],
                    ['name' => 'Hair Oil', 'slug' => 'hair-oil'],
                ]
            ],
        ];

        // Dress/Fashion Categories
        $fashion = [
            [
                'name' => 'Dresses',
                'slug' => 'dresses',
                'description' => 'Elegant dresses for every occasion',
                'type' => 'dress',
                'icon' => '👗',
                'children' => [
                    ['name' => 'Casual Dresses', 'slug' => 'casual-dresses'],
                    ['name' => 'Party Dresses', 'slug' => 'party-dresses'],
                    ['name' => 'Maxi Dresses', 'slug' => 'maxi-dresses'],
                ]
            ],
            [
                'name' => 'Tops & T-shirts',
                'slug' => 'tops',
                'description' => 'Trendy tops for your wardrobe',
                'type' => 'dress',
                'icon' => '👚',
                'children' => [
                    ['name' => 'Blouses', 'slug' => 'blouses'],
                    ['name' => 'T-shirts', 'slug' => 't-shirts'],
                    ['name' => 'Crop Tops', 'slug' => 'crop-tops'],
                ]
            ],
            [
                'name' => 'Ethnic Wear',
                'slug' => 'ethnic',
                'description' => 'Traditional Bangladeshi fashion',
                'type' => 'dress',
                'icon' => '🥻',
                'children' => [
                    ['name' => 'Sarees', 'slug' => 'sarees'],
                    ['name' => 'Kurtis', 'slug' => 'kurtis'],
                    ['name' => 'Salwar Kameez', 'slug' => 'salwar-kameez'],
                ]
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'description' => 'Fashion accessories',
                'type' => 'dress',
                'icon' => '👜',
            ],
        ];

        foreach (array_merge($cosmetics, $fashion) as $category) {
            $children = $category['children'] ?? [];
            unset($category['children']);

            $parent = Category::create($category);

            foreach ($children as $child) {
                $child['parent_id'] = $parent->id;
                $child['type'] = $category['type'];
                Category::create($child);
            }
        }
    }
}
