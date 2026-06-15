<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Cache::remember('home_data', 60, function () {
            $featuredProducts = Product::featured()
                ->with('category')
                ->take(8)
                ->get();

            $newArrivals = Product::newArrivals()
                ->with('category')
                ->take(8)
                ->get();

            $cosmetics = Product::whereHas('category', function ($q) {
                    $q->where('type', 'cosmetics');
                })
                ->active()
                ->with('category')
                ->take(4)
                ->get();

            $dresses = Product::whereHas('category', function ($q) {
                    $q->where('type', 'dress');
                })
                ->active()
                ->with('category')
                ->take(4)
                ->get();

            $categories = Category::active()
                ->withCount('products')
                ->get();

            $banners = Banner::active()
                ->orderBy('sort_order')
                ->get();

            return [
                'featured_products' => $featuredProducts,
                'new_arrivals' => $newArrivals,
                'cosmetics' => $cosmetics,
                'dresses' => $dresses,
                'categories' => $categories,
                'banners' => $banners,
            ];
        });

        return response()->json($data);
    }
}
