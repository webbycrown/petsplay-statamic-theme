<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Coupons\CouponService;
use Statamic\Facades\AssetContainer;
use Statamic\Facades\Scope;
use Illuminate\Validation\ValidationException;

class CartController
{
    /**
     * Add an item to the cart.
     *
     * Logic:
     * 1. Read item details from request and normalize quantity/price.
     * 2. If item already exists in cart → increase quantity.
     * 3. Otherwise, add as a new cart entry.
     * 4. Recalculate subtotal based on all cart items.
     * 5. If a coupon is already applied, recalculate discount dynamically.
     * 6. Compute total = subtotal - discount + delivery.
     * 7. Save updated cart, discount, and total back to session.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
    */   

    public function add(Request $request)
    {   

        $item = $request->only(['product_id','slug','title', 'price', 'quantity','image','short_description','stock','size']);

        $item['quantity'] = (int) ($item['quantity'] ?? 1);
        $item['price'] = (float) $item['price'];

        $cart = session()->get('cart', []);

        // Check if the product already exists → increment quantity
        $itemExists = false;
        foreach ($cart as &$cartItem) {
            if (isset($cartItem['product_id']) && $cartItem['product_id'] == $item['product_id']) {
                $cartItem['quantity'] += $item['quantity'];
                $itemExists = true;
                break;
            }
        }
        unset($cartItem);

        // Add new item if not existing
        if (!$itemExists) {
            $cart[] = $item;
        }

        session()->put('cart', $cart);

        // Calculate totals
        $items    = array_filter($cart, fn($item) => isset($item['price'], $item['quantity']));
        $items    = array_values($items);
        $subtotal = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $items));

        // Recalculate discount from coupon (if any)
        $coupon   = session()->get('cart_coupon');

        $discount = 0;
        if ($coupon) {
            $couponService = app(\WebbyCrown\CommerceSuite\Services\Coupons\CouponService::class);
            $discount      = $couponService->calculateDiscount($coupon, $subtotal);
        }
        session()->put('cart_discount', $discount);

        $delivery = (float) session()->get('cart_delivery', 0);
        $total    = max(0, $subtotal - $discount + $delivery);
        session()->put('cart_total', $total);

        $totalQuantity = collect($items)->count();

        return response()->json([
            'success'        => true,
            'items'          => $items,
            'count'          => count($items),
            'subtotal'       => round($subtotal, 2),
            'discount'       => round($discount, 2),
            'delivery'       => round($delivery, 2),
            'total'          => round($total, 2),
            'applied_coupon' => $coupon,
            'total_quantity' => $totalQuantity,
        ]);
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request)
    {
        // Get the product ID that we want to remove
        $id   = $request->get('product_id');

        // Get current cart from session
        $cart = session()->get('cart', []);

        // Remove the item that matches the given product_id
        $cart = array_filter($cart, fn($item) => isset($item['product_id']) && $item['product_id'] != $id);

        // Reindex array to avoid gaps in indexes
        $cart = array_values($cart);

        // Save updated cart back to session
        session()->put('cart', $cart);

        // Prepare items array (only items with price & quantity)
        $items    = array_filter($cart, fn($item) => isset($item['price'], $item['quantity']));
        $items    = array_values($items);

        // Calculate subtotal (sum of price * quantity)
        $subtotal = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $items));

        // NEW: recalc discount from stored coupon
        $coupon   = session()->get('cart_coupon');
        
        // Default discount is 0
        $discount = 0;

        // If coupon exists → recalculate discount based on new subtotal
        if ($coupon) {
            $couponService = app(\WebbyCrown\CommerceSuite\Services\Coupons\CouponService::class);
            $discount      = $couponService->calculateDiscount($coupon, $subtotal);
        }

        // Save updated discount to session
        session()->put('cart_discount', $discount);

        // Get delivery charge from session (default 0)
        $delivery = (float) session()->get('cart_delivery', 0);

        // Final total = subtotal - discount + delivery
        $total    = max(0, $subtotal - $discount + $delivery);

        // Return updated cart info as JSON
        return response()->json([
            'success'        => true,
            'items'          => $items,
            'count'          => count($items),
            'subtotal'       => round($subtotal, 2),
            'discount'       => round($discount, 2),
            'delivery'       => round($delivery, 2),
            'total'          => round($total, 2),
            'applied_coupon' => $coupon,
            'is_empty'       => count($items) === 0, // If cart becomes empty
        ]);
    }

    /**
        * Update quantity of an item in the cart
    */
    public function update(Request $request)
    {
        $id       = $request->get('product_id');
        $quantity = (int) $request->get('quantity', 1);

        // Get existing cart
        $cart     = session()->get('cart', []);
        
        // Update selected product quantity
        foreach ($cart as &$item) {
            if (isset($item['product_id']) && $item['product_id'] == $id) {
                $item['quantity'] = max(1, $quantity); // prevent 0 or negative
                break;
            }
        }

        // cleanup reference
        unset($item);

        // Save updated cart
        session()->put('cart', $cart);

        // Recompute subtotal
        $items    = array_filter($cart, fn($item) => isset($item['price'], $item['quantity']));
        $items    = array_values($items);


        // Recalculate discount if coupon exists
        $subtotal = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $items));

        // NEW: recalc discount from stored coupon
        $coupon   = session()->get('cart_coupon');
        $discount = 0;
        if ($coupon) {
            $couponService = app(\WebbyCrown\CommerceSuite\Services\Coupons\CouponService::class);
            $discount      = $couponService->calculateDiscount($coupon, $subtotal);
        }
        session()->put('cart_discount', $discount);

        // Add delivery
        $delivery = (float) session()->get('cart_delivery', 0);

        // Final total
        $total    = max(0, $subtotal - $discount + $delivery);
        session()->put('cart_total', $total);

        $totalQuantity = collect($items)->count();

        return response()->json([
            'success'        => true,
            'items'          => $items,
            'count'          => count($items),
            'subtotal'       => round($subtotal, 2),
            'discount'       => round($discount, 2),
            'delivery'       => round($delivery, 2),
            'total'          => round($total, 2),
            'applied_coupon' => $coupon,
            'total_quantity' => $totalQuantity,

        ]);
    }

    public function applyCoupon(Request $request, CouponService $coupons){
        
        // Get coupon code from request
        $code = $request->input('code', '');

        // Fetch current cart from session
        $cart = session()->get('cart', []);

        // Filter only valid cart items (must contain price & quantity)
        $items = array_filter($cart, fn($item) => isset($item['price'], $item['quantity']));
        $items = array_values($items);

        // Calculate subtotal for all cart items
        $subtotal = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $items));

        // Prevent applying coupon on empty cart
        if ($subtotal <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty.',
            ], 400);
        }

        // Validate coupon from service layer
        $coupon = $coupons->findValid($code, $subtotal);

        // Coupon not found or expired
        if (! $coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired coupon code.',
            ], 400);
        }

        // Calculate discount amount using coupon service
        $discount = $coupons->calculateDiscount($coupon, $subtotal);

        // Get delivery charge from session
        $delivery = (float) session()->get('cart_delivery', 0);

        // Total = subtotal - discount + delivery
        $total    = max(0, $subtotal - $discount + $delivery);

        // Store coupon info in session
        session()->put('cart_coupon', $coupon);
        session()->put('cart_discount', $discount);
        session()->put('cart_total', $total);

        $totalQuantity = collect($items)->sum('quantity');
        // Return updated cart summary
        return response()->json([
            'success'        => true,
            'message'        => 'Coupon applied successfully.',
            'items'          => $items,
            'count'          => count($items),
            'subtotal'       => round($subtotal, 2),
            'discount'       => round($discount, 2),
            'delivery'       => round($delivery, 2),
            'total'          => round($total, 2),
            'applied_coupon' => $coupon,
            'total_quantity' => $totalQuantity,
        ]);
    }

    public function removeCoupon(){

        // Remove applied coupon & discount from session
        session()->forget('cart_coupon');
        session()->forget('cart_discount');

        // Fetch cart items or empty array
        $cart  = session()->get('cart', []);

        // Keep only valid items containing price & quantity
        $items = array_filter($cart, fn($item) => isset($item['price'], $item['quantity']));

        // Reset array keys
        $items = array_values($items);

        // Calculate subtotal = sum(price * qty)
        $subtotal = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $items));

        // Get delivery charge (if any)
        $delivery = (float) session()->get('cart_delivery', 0);

        // Total = subtotal + delivery (never negative)
        $total    = max(0, $subtotal + $delivery);

        // Return updated cart response
        return response()->json([
            'success'        => true,
            'message'        => 'Coupon removed.',
            'items'          => $items,
            'count'          => count($items),
            'subtotal'       => round($subtotal, 2),
            'discount'       => 0,
            'delivery'       => round($delivery, 2),
            'total'          => round($total, 2),
            'applied_coupon' => null,
        ]);
    }
}
