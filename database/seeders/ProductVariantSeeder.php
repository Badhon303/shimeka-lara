<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        // Helper for colored placeholder images
        $img = function($name, $bg, $fg = 'FFFFFF') {
            $text = urlencode(str_replace(' ', '+', substr($name, 0, 20)));
            return "https://placehold.co/400x400/{$bg}/{$fg}?text={$text}";
        };

        // ============== COSMETICS ==============
        // Skincare
        $this->updateOrCreate('Glow Brightening Face Wash', [
            'featured_image' => $img('Glow Face Wash', 'F472B6'),
            'attributes' => ['shade' => ['Original', 'Sensitive', 'Acne Care']],
            'variants' => [
                ['shade' => 'Original', 'price' => 450, 'stock' => 30],
                ['shade' => 'Sensitive', 'price' => 480, 'stock' => 15],
                ['shade' => 'Acne Care', 'price' => 500, 'stock' => 5],
            ],
        ]);

        $this->updateOrCreate('Hydrating Rose Water Toner', [
            'featured_image' => $img('Rose Toner', 'F9A8D4'),
            'attributes' => ['size' => ['100ml', '200ml']],
            'variants' => [
                ['size' => '100ml', 'price' => 380, 'stock' => 25],
                ['size' => '200ml', 'price' => 650, 'stock' => 15],
            ],
        ]);

        $this->updateOrCreate('Anti-Aging Night Cream', [
            'featured_image' => $img('Night Cream', 'C084FC'),
            'attributes' => ['size' => ['50ml', '100ml']],
            'variants' => [
                ['size' => '50ml', 'price' => 890, 'stock' => 20],
                ['size' => '100ml', 'price' => 1500, 'stock' => 10],
            ],
        ]);

        $this->updateOrCreate('Vitamin C Glow Serum', [
            'featured_image' => $img('Vitamin C Serum', 'FBBF24'),
            'attributes' => ['size' => ['15ml', '30ml']],
            'variants' => [
                ['size' => '15ml', 'price' => 1200, 'stock' => 20],
                ['size' => '30ml', 'price' => 2000, 'stock' => 10],
            ],
        ]);

        $this->updateOrCreate('SPF 50 Sunscreen Lotion', [
            'featured_image' => $img('Sunscreen SPF50', 'FCD34D'),
            'attributes' => ['type' => ['Normal', 'Matte', 'Tinted']],
            'variants' => [
                ['type' => 'Normal', 'price' => 650, 'stock' => 40],
                ['type' => 'Matte', 'price' => 700, 'stock' => 25],
                ['type' => 'Tinted', 'price' => 750, 'stock' => 15],
            ],
        ]);

        // Makeup
        $this->updateOrCreate('Matte Perfection Foundation', [
            'featured_image' => $img('Foundation', 'FDA4AF'),
            'attributes' => ['shade' => ['Fair', 'Medium', 'Tan', 'Deep']],
            'variants' => [
                ['shade' => 'Fair', 'price' => 950, 'stock' => 15],
                ['shade' => 'Medium', 'price' => 950, 'stock' => 20],
                ['shade' => 'Tan', 'price' => 950, 'stock' => 12],
                ['shade' => 'Deep', 'price' => 950, 'stock' => 8],
            ],
        ]);

        $this->updateOrCreate('Velvet Kiss Lipstick - Red Passion', [
            'featured_image' => $img('Red Lipstick', 'EF4444'),
            'attributes' => ['shade' => ['Red Passion', 'Pink Bloom', 'Nude Glow', 'Berry Kiss']],
            'variants' => [
                ['shade' => 'Red Passion', 'price' => 550, 'stock' => 20, 'image' => $img('Red Passion', 'EF4444')],
                ['shade' => 'Pink Bloom', 'price' => 550, 'stock' => 18, 'image' => $img('Pink Bloom', 'EC4899')],
                ['shade' => 'Nude Glow', 'price' => 550, 'stock' => 22, 'image' => $img('Nude Glow', 'D4A574')],
                ['shade' => 'Berry Kiss', 'price' => 550, 'stock' => 15, 'image' => $img('Berry Kiss', 'A855F7')],
            ],
        ]);

        $this->updateOrCreate('Nude Palette Eyeshadow', [
            'featured_image' => $img('Eyeshadow Palette', 'D4A574'),
            'attributes' => ['variant' => ['Classic Nude', 'Warm Rose', 'Cool Smoke']],
            'variants' => [
                ['variant' => 'Classic Nude', 'price' => 1450, 'stock' => 15],
                ['variant' => 'Warm Rose', 'price' => 1450, 'stock' => 12],
                ['variant' => 'Cool Smoke', 'price' => 1450, 'stock' => 8],
            ],
        ]);

        $this->updateOrCreate('Volume Boost Mascara', [
            'featured_image' => $img('Mascara', '111827'),
            'attributes' => ['type' => ['Waterproof', 'Washable']],
            'variants' => [
                ['type' => 'Waterproof', 'price' => 480, 'stock' => 35],
                ['type' => 'Washable', 'price' => 450, 'stock' => 20],
            ],
        ]);

        // Fragrance
        $this->updateOrCreate('Midnight Rose Perfume', [
            'featured_image' => $img('Midnight Rose', '7C3AED'),
            'attributes' => ['size' => ['30ml', '50ml', '100ml']],
            'variants' => [
                ['size' => '30ml', 'price' => 2500, 'stock' => 10],
                ['size' => '50ml', 'price' => 3500, 'stock' => 8],
                ['size' => '100ml', 'price' => 5000, 'stock' => 5],
            ],
        ]);

        $this->updateOrCreate('Fresh Blossom Body Mist', [
            'featured_image' => $img('Body Mist', 'A7F3D0'),
            'attributes' => ['scent' => ['Rose', 'Jasmine', 'Lavender']],
            'variants' => [
                ['scent' => 'Rose', 'price' => 350, 'stock' => 30],
                ['scent' => 'Jasmine', 'price' => 350, 'stock' => 25],
                ['scent' => 'Lavender', 'price' => 350, 'stock' => 20],
            ],
        ]);

        // Hair Care
        $this->updateOrCreate('Argan Oil Shampoo', [
            'featured_image' => $img('Argan Shampoo', 'FDE68A'),
            'attributes' => ['size' => ['200ml', '400ml']],
            'variants' => [
                ['size' => '200ml', 'price' => 580, 'stock' => 25],
                ['size' => '400ml', 'price' => 950, 'stock' => 15],
            ],
        ]);

        $this->updateOrCreate('Coconut Hair Oil', [
            'featured_image' => $img('Coconut Oil', 'FEF3C7'),
            'attributes' => ['size' => ['100ml', '200ml']],
            'variants' => [
                ['size' => '100ml', 'price' => 320, 'stock' => 50],
                ['size' => '200ml', 'price' => 550, 'stock' => 30],
            ],
        ]);

        // ============== FASHION / DRESSES ==============
        $this->updateOrCreate('Floral Print Maxi Dress', [
            'featured_image' => $img('Floral Maxi', 'F0FDF4'),
            'attributes' => ['size' => ['S', 'M', 'L', 'XL'], 'color' => ['Blue', 'Pink', 'Yellow']],
            'variants' => [
                ['size' => 'S', 'color' => 'Blue', 'price' => 1890, 'stock' => 5, 'image' => $img('Blue Floral', '3B82F6')],
                ['size' => 'M', 'color' => 'Blue', 'price' => 1890, 'stock' => 8, 'image' => $img('Blue Floral', '3B82F6')],
                ['size' => 'L', 'color' => 'Blue', 'price' => 1890, 'stock' => 6, 'image' => $img('Blue Floral', '3B82F6')],
                ['size' => 'XL', 'color' => 'Blue', 'price' => 1890, 'stock' => 3, 'image' => $img('Blue Floral', '3B82F6')],
                ['size' => 'S', 'color' => 'Pink', 'price' => 1890, 'stock' => 4, 'image' => $img('Pink Floral', 'EC4899')],
                ['size' => 'M', 'color' => 'Pink', 'price' => 1890, 'stock' => 7, 'image' => $img('Pink Floral', 'EC4899')],
                ['size' => 'L', 'color' => 'Pink', 'price' => 1890, 'stock' => 5, 'image' => $img('Pink Floral', 'EC4899')],
                ['size' => 'XL', 'color' => 'Pink', 'price' => 1890, 'stock' => 2, 'image' => $img('Pink Floral', 'EC4899')],
                ['size' => 'S', 'color' => 'Yellow', 'price' => 1890, 'stock' => 3, 'image' => $img('Yellow Floral', 'EAB308')],
                ['size' => 'M', 'color' => 'Yellow', 'price' => 1890, 'stock' => 5, 'image' => $img('Yellow Floral', 'EAB308')],
                ['size' => 'L', 'color' => 'Yellow', 'price' => 1890, 'stock' => 4, 'image' => $img('Yellow Floral', 'EAB308')],
                ['size' => 'XL', 'color' => 'Yellow', 'price' => 1890, 'stock' => 2, 'image' => $img('Yellow Floral', 'EAB308')],
            ],
        ]);

        $this->updateOrCreate('Elegant Black Evening Dress', [
            'featured_image' => $img('Black Evening', '111827'),
            'attributes' => ['size' => ['S', 'M', 'L', 'XL']],
            'variants' => [
                ['size' => 'S', 'price' => 3500, 'stock' => 3],
                ['size' => 'M', 'price' => 3500, 'stock' => 5],
                ['size' => 'L', 'price' => 3500, 'stock' => 4],
                ['size' => 'XL', 'price' => 3500, 'stock' => 2],
            ],
        ]);

        $this->updateOrCreate('Casual Denim Dress', [
            'featured_image' => $img('Denim Dress', '60A5FA'),
            'attributes' => ['size' => ['S', 'M', 'L', 'XL'], 'color' => ['Blue', 'Light Blue']],
            'variants' => [
                ['size' => 'S', 'color' => 'Blue', 'price' => 1450, 'stock' => 8],
                ['size' => 'M', 'color' => 'Blue', 'price' => 1450, 'stock' => 12],
                ['size' => 'L', 'color' => 'Blue', 'price' => 1450, 'stock' => 10],
                ['size' => 'XL', 'color' => 'Blue', 'price' => 1450, 'stock' => 5],
                ['size' => 'S', 'color' => 'Light Blue', 'price' => 1450, 'stock' => 6],
                ['size' => 'M', 'color' => 'Light Blue', 'price' => 1450, 'stock' => 9],
                ['size' => 'L', 'color' => 'Light Blue', 'price' => 1450, 'stock' => 7],
                ['size' => 'XL', 'color' => 'Light Blue', 'price' => 1450, 'stock' => 4],
            ],
        ]);

        $this->updateOrCreate('Silk Embroidered Blouse', [
            'featured_image' => $img('Silk Blouse', 'FEF9C3'),
            'attributes' => ['size' => ['S', 'M', 'L', 'XL'], 'color' => ['White', 'Cream']],
            'variants' => [
                ['size' => 'S', 'color' => 'White', 'price' => 1200, 'stock' => 6],
                ['size' => 'M', 'color' => 'White', 'price' => 1200, 'stock' => 10],
                ['size' => 'L', 'color' => 'White', 'price' => 1200, 'stock' => 8],
                ['size' => 'XL', 'color' => 'White', 'price' => 1200, 'stock' => 4],
                ['size' => 'S', 'color' => 'Cream', 'price' => 1200, 'stock' => 5],
                ['size' => 'M', 'color' => 'Cream', 'price' => 1200, 'stock' => 8],
                ['size' => 'L', 'color' => 'Cream', 'price' => 1200, 'stock' => 6],
                ['size' => 'XL', 'color' => 'Cream', 'price' => 1200, 'stock' => 3],
            ],
        ]);

        $this->updateOrCreate('Graphic Print T-shirt', [
            'featured_image' => $img('Graphic Tee', 'E5E7EB'),
            'attributes' => ['size' => ['S', 'M', 'L', 'XL'], 'color' => ['White', 'Black', 'Pink']],
            'variants' => [
                ['size' => 'S', 'color' => 'White', 'price' => 450, 'stock' => 15],
                ['size' => 'M', 'color' => 'White', 'price' => 450, 'stock' => 20],
                ['size' => 'L', 'color' => 'White', 'price' => 450, 'stock' => 18],
                ['size' => 'XL', 'color' => 'White', 'price' => 450, 'stock' => 10],
                ['size' => 'S', 'color' => 'Black', 'price' => 450, 'stock' => 12],
                ['size' => 'M', 'color' => 'Black', 'price' => 450, 'stock' => 18],
                ['size' => 'L', 'color' => 'Black', 'price' => 450, 'stock' => 15],
                ['size' => 'XL', 'color' => 'Black', 'price' => 450, 'stock' => 8],
                ['size' => 'S', 'color' => 'Pink', 'price' => 450, 'stock' => 10],
                ['size' => 'M', 'color' => 'Pink', 'price' => 450, 'stock' => 15],
                ['size' => 'L', 'color' => 'Pink', 'price' => 450, 'stock' => 12],
                ['size' => 'XL', 'color' => 'Pink', 'price' => 450, 'stock' => 6],
            ],
        ]);

        // Ethnic
        $this->updateOrCreate('Banarasi Silk Saree', [
            'featured_image' => $img('Silk Saree', 'DC2626'),
            'attributes' => ['color' => ['Red', 'Green', 'Blue', 'Purple']],
            'variants' => [
                ['color' => 'Red', 'price' => 5500, 'stock' => 3],
                ['color' => 'Green', 'price' => 5500, 'stock' => 2],
                ['color' => 'Blue', 'price' => 5500, 'stock' => 4],
                ['color' => 'Purple', 'price' => 5500, 'stock' => 2],
            ],
        ]);

        $this->updateOrCreate('Cotton Printed Kurti', [
            'featured_image' => $img('Cotton Kurti', 'FEF3C7'),
            'attributes' => ['size' => ['S', 'M', 'L', 'XL', 'XXL'], 'color' => ['Pink', 'Yellow', 'Green']],
            'variants' => [
                ['size' => 'S', 'color' => 'Pink', 'price' => 890, 'stock' => 8],
                ['size' => 'M', 'color' => 'Pink', 'price' => 890, 'stock' => 12],
                ['size' => 'L', 'color' => 'Pink', 'price' => 890, 'stock' => 10],
                ['size' => 'XL', 'color' => 'Pink', 'price' => 890, 'stock' => 6],
                ['size' => 'XXL', 'color' => 'Pink', 'price' => 890, 'stock' => 4],
                ['size' => 'S', 'color' => 'Yellow', 'price' => 890, 'stock' => 6],
                ['size' => 'M', 'color' => 'Yellow', 'price' => 890, 'stock' => 10],
                ['size' => 'L', 'color' => 'Yellow', 'price' => 890, 'stock' => 8],
                ['size' => 'XL', 'color' => 'Yellow', 'price' => 890, 'stock' => 5],
                ['size' => 'XXL', 'color' => 'Yellow', 'price' => 890, 'stock' => 3],
                ['size' => 'S', 'color' => 'Green', 'price' => 890, 'stock' => 7],
                ['size' => 'M', 'color' => 'Green', 'price' => 890, 'stock' => 11],
                ['size' => 'L', 'color' => 'Green', 'price' => 890, 'stock' => 9],
                ['size' => 'XL', 'color' => 'Green', 'price' => 890, 'stock' => 5],
                ['size' => 'XXL', 'color' => 'Green', 'price' => 890, 'stock' => 3],
            ],
        ]);

        $this->updateOrCreate('Designer Salwar Kameez', [
            'featured_image' => $img('Salwar Kameez', 'DBEAFE'),
            'attributes' => ['size' => ['S', 'M', 'L', 'XL'], 'color' => ['Pink', 'Blue', 'Green']],
            'variants' => [
                ['size' => 'S', 'color' => 'Pink', 'price' => 2800, 'stock' => 5],
                ['size' => 'M', 'color' => 'Pink', 'price' => 2800, 'stock' => 8],
                ['size' => 'L', 'color' => 'Pink', 'price' => 2800, 'stock' => 6],
                ['size' => 'XL', 'color' => 'Pink', 'price' => 2800, 'stock' => 3],
                ['size' => 'S', 'color' => 'Blue', 'price' => 2800, 'stock' => 4],
                ['size' => 'M', 'color' => 'Blue', 'price' => 2800, 'stock' => 7],
                ['size' => 'L', 'color' => 'Blue', 'price' => 2800, 'stock' => 5],
                ['size' => 'XL', 'color' => 'Blue', 'price' => 2800, 'stock' => 2],
                ['size' => 'S', 'color' => 'Green', 'price' => 2800, 'stock' => 4],
                ['size' => 'M', 'color' => 'Green', 'price' => 2800, 'stock' => 6],
                ['size' => 'L', 'color' => 'Green', 'price' => 2800, 'stock' => 4],
                ['size' => 'XL', 'color' => 'Green', 'price' => 2800, 'stock' => 2],
            ],
        ]);

        // Accessories
        $this->updateOrCreate('Pearl Stud Earrings', [
            'featured_image' => $img('Pearl Earrings', 'F3F4F6'),
            'attributes' => ['type' => ['Silver', 'Gold Plated']],
            'variants' => [
                ['type' => 'Silver', 'price' => 350, 'stock' => 25],
                ['type' => 'Gold Plated', 'price' => 450, 'stock' => 18],
            ],
        ]);

        $this->updateOrCreate('Leather Handbag', [
            'featured_image' => $img('Leather Bag', '92400E'),
            'attributes' => ['color' => ['Black', 'Brown', 'Tan']],
            'variants' => [
                ['color' => 'Black', 'price' => 2200, 'stock' => 8],
                ['color' => 'Brown', 'price' => 2200, 'stock' => 6],
                ['color' => 'Tan', 'price' => 2200, 'stock' => 6],
            ],
        ]);
    }

    private function updateOrCreate(string $name, array $data): void
    {
        $product = Product::where('name', $name)->first();
        if ($product) {
            $product->update($data);
        }
    }
}
