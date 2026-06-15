<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Summer Glow Collection',
                'subtitle' => 'New Arrivals',
                'description' => 'Discover our summer skincare essentials for radiant skin all season long.',
                'image' => 'https://images.unsplash.com/photo-1596755389378-c31d21fd1273?w=1200&q=80',
                'link' => '/category/skincare',
                'button_text' => 'Shop Now',
                'position' => 'hero',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Elegant Dresses',
                'subtitle' => 'Up to 50% Off',
                'description' => 'Find the perfect dress for any occasion. Limited time offer!',
                'image' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=1200&q=80',
                'link' => '/category/dresses',
                'button_text' => 'View Collection',
                'position' => 'hero',
                'sort_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }
    }
}
