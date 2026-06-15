<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with('items.product')
            ->latest()
            ->paginate(10);
        
        return response()->json($orders);
    }

    public function show(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->with('items.product')
            ->findOrFail($id);
        
        return response()->json($order);
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_email' => 'required|email',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_postal_code' => 'required|string',
            'delivery_area' => 'required|in:inside_dhaka,outside_dhaka',
            'payment_method' => 'required|in:cash_on_delivery,card',
            'notes' => 'nullable|string',
            'coupon_code' => 'nullable|string',
        ]);

        $cartItems = Cart::where('user_id', $request->user()->id)
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'Cart is empty!'
            ], 422);
        }

        // Check stock availability
        foreach ($cartItems as $item) {
            if ($item->product->stock_quantity < $item->quantity) {
                return response()->json([
                    'message' => "Not enough stock for {$item->product->name}!"
                ], 422);
            }
        }

        DB::beginTransaction();
        
        try {
            $subtotal = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });

            $tax = $subtotal * 0.05;
            $freeThreshold = Setting::get('free_shipping_threshold', 1000);
            $deliveryCharge = $request->delivery_area === 'inside_dhaka'
                ? Setting::get('delivery_charge_inside_dhaka', 60)
                : Setting::get('delivery_charge_outside_dhaka', 120);
            $shipping = $subtotal >= $freeThreshold ? 0 : $deliveryCharge;

            // Apply coupon if provided
            $discount = 0;
            $couponCode = null;
            if ($request->coupon_code) {
                $coupon = Coupon::where('code', $request->coupon_code)->first();
                if ($coupon && $coupon->isValid($subtotal)) {
                    $discount = $coupon->calculateDiscount($subtotal);
                    $couponCode = $coupon->code;
                    $coupon->increment('usage_count');
                }
            }

            $total = $subtotal + $tax + $shipping - $discount;

            $order = Order::create([
                'user_id' => $request->user()->id,
                'status' => 'pending',
                'payment_status' => $request->payment_method === 'cash_on_delivery' ? 'pending' : 'paid',
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shipping,
                'coupon_code' => $couponCode,
                'discount_amount' => $discount,
                'total' => $total,
                'shipping_name' => $request->shipping_name,
                'shipping_email' => $request->shipping_email,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_postal_code' => $request->shipping_postal_code,
                'delivery_area' => $request->delivery_area,
                'delivery_charge_label' => $request->delivery_area === 'inside_dhaka' ? 'Inside Dhaka' : 'Outside Dhaka',
                'notes' => $request->notes,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'variant' => $item->variant,
                ]);

                // Reduce stock
                $item->product->decrement('stock_quantity', $item->quantity);
            }

            // Clear cart
            Cart::where('user_id', $request->user()->id)->delete();

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully!',
                'order' => $order->load('items')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to place order. Please try again.'
            ], 500);
        }
    }

    // Guest checkout - no auth required
    public function guestStore(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_email' => 'required|email',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_postal_code' => 'required|string',
            'delivery_area' => 'required|in:inside_dhaka,outside_dhaka',
            'payment_method' => 'required|in:cash_on_delivery,card',
            'notes' => 'nullable|string',
            'coupon_code' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.name' => 'required|string',
            'items.*.variant' => 'nullable',
        ]);

        // Check stock availability
        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            if ($product->stock_quantity < $item['quantity']) {
                return response()->json([
                    'message' => "Not enough stock for {$product->name}!"
                ], 422);
            }
        }

        DB::beginTransaction();

        try {
            // Create or find guest user by email so orders are linked
            $user = \App\Models\User::firstOrCreate(
                ['email' => $request->shipping_email],
                [
                    'name' => $request->shipping_name,
                    'phone' => $request->shipping_phone,
                    'password' => bcrypt(uniqid()),
                    'is_admin' => false,
                ]
            );

            $subtotal = collect($request->items)->sum(function ($item) {
                return $item['quantity'] * $item['price'];
            });

            $tax = $subtotal * 0.05;
            $freeThreshold = Setting::get('free_shipping_threshold', 1000);
            $deliveryCharge = $request->delivery_area === 'inside_dhaka'
                ? Setting::get('delivery_charge_inside_dhaka', 60)
                : Setting::get('delivery_charge_outside_dhaka', 120);
            $shipping = $subtotal >= $freeThreshold ? 0 : $deliveryCharge;

            // Apply coupon if provided
            $discount = 0;
            $couponCode = null;
            if ($request->coupon_code) {
                $coupon = Coupon::where('code', $request->coupon_code)->first();
                if ($coupon && $coupon->isValid($subtotal)) {
                    $discount = $coupon->calculateDiscount($subtotal);
                    $couponCode = $coupon->code;
                    $coupon->increment('usage_count');
                }
            }

            $total = $subtotal + $tax + $shipping - $discount;

            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'payment_status' => $request->payment_method === 'cash_on_delivery' ? 'pending' : 'paid',
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shipping,
                'coupon_code' => $couponCode,
                'discount_amount' => $discount,
                'total' => $total,
                'shipping_name' => $request->shipping_name,
                'shipping_email' => $request->shipping_email,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_postal_code' => $request->shipping_postal_code,
                'delivery_area' => $request->delivery_area,
                'delivery_charge_label' => $request->delivery_area === 'inside_dhaka' ? 'Inside Dhaka' : 'Outside Dhaka',
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'variant' => isset($item['variant']) ? json_encode($item['variant']) : null,
                ]);

                // Reduce stock
                $product = Product::find($item['product_id']);
                $product->decrement('stock_quantity', $item['quantity']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully!',
                'order' => $order->load('items')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to place order. Please try again.'
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:100',
            'courier_name' => 'nullable|string|max:100',
            'courier_charge' => 'nullable|numeric|min:0',
        ]);

        $order = Order::findOrFail($id);
        $updateData = ['status' => $request->status];

        if ($request->has('tracking_number')) {
            $updateData['tracking_number'] = $request->tracking_number;
        }
        if ($request->has('courier_name')) {
            $updateData['courier_name'] = $request->courier_name;
        }
        if ($request->has('courier_charge')) {
            $updateData['courier_charge'] = $request->courier_charge;
        }

        $order->update($updateData);

        if ($request->status === 'shipped' && !$order->shipped_at) {
            $order->update(['shipped_at' => now()]);
        }

        if ($request->status === 'delivered' && !$order->delivered_at) {
            $order->update(['delivered_at' => now()]);
        }

        return response()->json([
            'message' => 'Order status updated!',
            'order' => $order
        ]);
    }

    // Admin order detail
    public function adminShow($id)
    {
        $order = Order::with(['items', 'user'])->findOrFail($id);
        return response()->json($order);
    }

    // Public tracking - no auth required
    public function track(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
        ]);

        $order = Order::where('order_number', $request->order_number)
            ->with('items.product')
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'order' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'tracking_number' => $order->tracking_number,
                'courier_name' => $order->courier_name,
                'courier_charge' => $order->courier_charge,
                'shipping_name' => $order->shipping_name,
                'shipping_address' => $order->shipping_address,
                'shipping_city' => $order->shipping_city,
                'shipping_phone' => $order->shipping_phone,
                'total' => $order->total,
                'created_at' => $order->created_at,
                'shipped_at' => $order->shipped_at,
                'delivered_at' => $order->delivered_at,
                'items' => $order->items->map(function($item) {
                    return [
                        'product_name' => $item->product_name ?? ($item->product->name ?? ''),
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
                }),
            ]
        ]);
    }

    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon) {
            return response()->json(['valid' => false, 'message' => 'Invalid coupon code'], 422);
        }

        if (!$coupon->isValid($request->subtotal)) {
            $reason = 'Coupon is not valid';
            if ($coupon->expires_at && $coupon->expires_at->isPast()) $reason = 'Coupon has expired';
            elseif ($coupon->usage_limit !== null && $coupon->usage_count >= $coupon->usage_limit) $reason = 'Coupon usage limit reached';
            elseif ($request->subtotal < $coupon->min_order_amount) $reason = 'Minimum order amount is ৳' . $coupon->min_order_amount;
            return response()->json(['valid' => false, 'message' => $reason], 422);
        }

        $discount = $coupon->calculateDiscount($request->subtotal);

        return response()->json([
            'valid' => true,
            'coupon' => [
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'discount' => $discount,
            ],
        ]);
    }
}
