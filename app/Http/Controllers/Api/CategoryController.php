<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::active()
            ->with(['children' => function ($q) {
                $q->active();
            }])
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
        
        return response()->json($categories);
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->with(['children', 'parent'])
            ->firstOrFail();
        
        return response()->json($category);
    }

    public function products($slug, Request $request)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $query = $category->products()->active();
        
        if ($request->has('sort')) {
            match($request->sort) {
                'price_low' => $query->orderBy('price', 'asc'),
                'price_high' => $query->orderBy('price', 'desc'),
                'name' => $query->orderBy('name', 'asc'),
                default => $query->latest()
            };
        }
        
        $products = $query->paginate($request->per_page ?? 12);
        
        return response()->json([
            'category' => $category,
            'products' => $products
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'icon' => 'nullable|string',
            'type' => 'required|in:cosmetics,dress',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        $category = Category::create($validated);
        
        return response()->json([
            'message' => 'Category created successfully!',
            'category' => $category
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'type' => 'sometimes|in:cosmetics,dress',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);
        
        return response()->json([
            'message' => 'Category updated successfully!',
            'category' => $category
        ]);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        if ($category->products()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete category with products!'
            ], 422);
        }
        
        $category->delete();
        
        return response()->json([
            'message' => 'Category deleted successfully!'
        ]);
    }
}
