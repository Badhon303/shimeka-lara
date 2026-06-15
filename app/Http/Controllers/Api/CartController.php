<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = Cart::where('user_id', $request->user()->id)
            ->with('product')
            ->get();

        // Enrich cart items with variant price
        $cartItems->each(function ($item) {
            $variant = $item->variant ? json_decode($item->variant, true) : null;
            $item->variant_price = $this->getVariantPrice($item->product, $variant);
            $item->variant_label = $this->formatVariantLabel($variant);
        });

        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * ($item->variant_price ?? $item->product->price);
        });

        $tax = $subtotal * 0.05;
        $shipping = $subtotal > 1000 ? 0 : 100;
        $total = $subtotal + $tax + $shipping;

        return response()->json([
            'items' => $cartItems,
            'summary' => [
                'subtotal' => round($subtotal, 2),
                'tax' => round($tax, 2),
                'shipping' => round($shipping, 2),
                'total' => round($total, 2),
                'item_count' => $cartItems->sum('quantity')
            ]
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'variant' => 'nullable',
        ]);

        $product = Product::findOrFail($request->product_id);
        $variantData = $request->variant ? (is_string($request->variant) ? json_decode($request->variant, true) : $request->variant) : null;

        // Check variant-specific stock
        $stockQty = $this->getVariantStock($product, $variantData);
        $variantPrice = $this->getVariantPrice($product, $variantData);

        if ($stockQty < $request->quantity) {
            return response()->json([
                'message' => 'Not enough stock available for this variant!'
            ], 422);
        }

        $variantJson = $variantData ? json_encode($variantData) : null;
        $existingCart = Cart::where('user_id', $request->user()->id)
            ->where('product_id', $request->product_id)
            ->where('variant', $variantJson)
            ->first();

        if ($existingCart) {
            $newQuantity = $existingCart->quantity + $request->quantity;

            if ($stockQty < $newQuantity) {
                return response()->json([
                    'message' => 'Not enough stock available!'
                ], 422);
            }

            $existingCart->update(['quantity' => $newQuantity]);
            $cart = $existingCart;
        } else {
            $cart = Cart::create([
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'variant' => $variantJson,
            ]);
        }

        return response()->json([
            'message' => 'Product added to cart!',
            'cart_item' => $cart->load('product')
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        $product = $cart->product;
        $variant = $cart->variant ? json_decode($cart->variant, true) : null;
        $stockQty = $this->getVariantStock($product, $variant);

        if ($stockQty < $request->quantity) {
            return response()->json([
                'message' => 'Not enough stock available for this variant!'
            ], 422);
        }

        $cart->update(['quantity' => $request->quantity]);

        return response()->json([
            'message' => 'Cart updated!',
            'cart_item' => $cart->load('product')
        ]);
    }

    private function getVariantStock($product, $variantData)
    {
        if (!$variantData || !$product->variants) return $product->stock_quantity;
        $variants = is_string($product->variants) ? json_decode($product->variants, true) : $product->variants;
        if (!is_array($variants)) return $product->stock_quantity;
        foreach ($variants as $v) {
            if ($this->variantMatches($v, $variantData)) {
                return $v['stock'] ?? 0;
            }
        }
        return $product->stock_quantity;
    }

    private function getVariantPrice($product, $variantData)
    {
        if (!$variantData || !$product->variants) return $product->price;
        $variants = is_string($product->variants) ? json_decode($product->variants, true) : $product->variants;
        if (!is_array($variants)) return $product->price;
        foreach ($variants as $v) {
            if ($this->variantMatches($v, $variantData)) {
                return $v['price'] ?? $product->price;
            }
        }
        return $product->price;
    }

    private function variantMatches($variant, $selected)
    {
        foreach ($selected as $key => $value) {
            if (($variant[$key] ?? null) !== $value) return false;
        }
        return true;
    }

    private function formatVariantLabel($variant)
    {
        if (!$variant) return '';
        return collect($variant)->map(function ($v, $k) {
            return ucfirst($k) . ': ' . $v;
        })->implode(', ');
    }

    public function remove(Request $request, $id)
    {
        $cart = Cart::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        $cart->delete();

        return response()->json([
            'message' => 'Item removed from cart!'
        ]);
    }

    public function clear(Request $request)
    {
        Cart::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'message' => 'Cart cleared!'
        ]);
    }
}
