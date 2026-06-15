<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with('category');
        
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('low_stock')) {
            $query->where('stock_quantity', '<=', 10);
        }

        if ($request->has('sort')) {
            match($request->sort) {
                'price_low' => $query->orderBy('price', 'asc'),
                'price_high' => $query->orderBy('price', 'desc'),
                'name' => $query->orderBy('name', 'asc'),
                'newest' => $query->latest(),
                default => $query->latest()
            };
        } else {
            $query->latest();
        }
        
        $products = $query->paginate($request->per_page ?? 12);
        
        return response()->json($products);
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->with(['category', 'reviews' => function ($q) {
                $q->approved()->with('user');
            }])
            ->firstOrFail();
        
        $product->incrementViewCount();
        
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->take(4)
            ->get();
        
        return response()->json([
            'product' => $product,
            'related_products' => $relatedProducts
        ]);
    }

    public function featured()
    {
        $products = Product::featured()
            ->with('category')
            ->take(8)
            ->get();
        
        return response()->json($products);
    }

    public function newArrivals()
    {
        $products = Product::newArrivals()
            ->with('category')
            ->take(8)
            ->get();
        
        return response()->json($products);
    }

    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2']);
        
        $products = Product::active()
            ->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->q}%")
                  ->orWhere('description', 'like', "%{$request->q}%")
                  ->orWhere('brand', 'like', "%{$request->q}%");
            })
            ->with('category')
            ->take(10)
            ->get();
        
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'images' => 'nullable|array',
            'featured_image' => 'nullable|string',
            'attributes' => 'nullable|json',
            'variants' => 'nullable|json',
            'brand' => 'nullable|string',
            'tags' => 'nullable|string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_new' => 'boolean',
        ]);

        $product = Product::create($validated);
        
        return response()->json([
            'message' => 'Product created successfully!',
            'product' => $product
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'short_description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'sometimes|integer|min:0',
            'category_id' => 'sometimes|exists:categories,id',
            'featured_image' => 'nullable|string',
            'attributes' => 'nullable',
            'variants' => 'nullable',
            'brand' => 'nullable|string',
            'tags' => 'nullable|string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_new' => 'boolean',
        ]);

        $product->update($validated);
        
        return response()->json([
            'message' => 'Product updated successfully!',
            'product' => $product
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        
        return response()->json([
            'message' => 'Product deleted successfully!'
        ]);
    }
}
