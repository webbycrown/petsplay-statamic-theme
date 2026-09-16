<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Statamic\Facades\Entry;

class CheckoutController
{
    public function placeOrder(Request $request)
    {
        $cart = session()->get('cart', []);
        $cartCoupon = session()->get('cart_coupon', []);

        if (empty($cart)) {
            return response()->json(['error' => 'Cart is empty'], 422);
        }

        $authorId = session('author_id');
        if (! $authorId) {
            return response()->json([
                'status' => false,
                'type' => 'unauthorized',
                'message' => 'Please sign in to continue.',
                'redirect' => '/sign-in',
            ]);
        }

        try {
            $request->validate([
                'name' => 'required',
                'address' => 'required',
                'city' => 'required',
                'state' => 'required',
                'zip' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'type' => 'validation',
                'errors' => $e->errors(),
                'message' => 'Please fix the errors below.',
            ]);
        }

        $customer = Entry::query()
            ->where('collection', 'customer')
            ->where('id', $authorId)
            ->first();

        if (! $customer) {
            return response()->json([
                'status' => false,
                'type' => 'unauthorized',
                'message' => 'Please sign in to continue.',
                'redirect' => '/sign-in',
            ]);
        }

        $paymentMethod = $request->input('payment_method', 'cash-on-delivery');
        if (in_array($paymentMethod, ['stripe', 'razorpay'], true)) {
            return response()->json([
                'status' => false,
                'message' => 'Card checkout is not included in this kit. Use cash on delivery to send a demo order inquiry.',
            ], 422);
        }

        $customer->set('title', $request->name ?? $customer->get('title'));
        $customer->set('email', $request->email ?? $customer->get('email'));
        $customer->set('address', $request->address ?? $customer->get('address'));
        $customer->set('city', $request->city ?? $customer->get('city'));
        $customer->set('state', $request->state ?? $customer->get('state'));
        $customer->set('zip_code', $request->zip ?? $customer->get('zip_code'));
        $customer->set('notes', $request->notes ?? $customer->get('notes'));
        $customer->save();

        $products = $request->input('products', []);
        if (is_string($products)) {
            $products = json_decode($products, true) ?? [];
        }
        if (! is_array($products) || $products === []) {
            $products = $cart;
        }

        $couponId = $cartCoupon['coupon_id'] ?? 0;
        $discount = (float) ($request->discount ?? session('cart_discount', 0));
        $shipping = (float) ($request->s_charge ?? 0);

        foreach ($products as $product) {
            $productId = $product['product_id'] ?? $product['id'] ?? null;
            $qty = (float) ($product['quantity'] ?? 0);
            $price = (float) ($product['price'] ?? 0);
            if (! $productId || $qty <= 0) {
                continue;
            }

            $catalog = Entry::query()
                ->where('collection', 'products')
                ->where('id', $productId)
                ->first();
            if ($catalog) {
                $catalog->set('stock', max(0, (int) $catalog->get('stock') - (int) $qty));
                $catalog->save();
            }

            Entry::make()->collection('orders')->data([
                'title' => 'order-' . Str::random(8),
                'payment_method' => 'cash-on-delivery',
                'order_status' => 'inquiry',
                'product_id' => $productId,
                'price' => $price,
                'qty' => $qty,
                'total' => $price * $qty,
                'size' => $product['size'] ?? null,
                'coupon_id' => $couponId,
                'author_id' => $authorId,
                'shipping_charge' => $shipping,
                'discount' => $discount,
            ])->save();
        }

        session()->put('cart', []);
        session()->forget(['cart_coupon', 'cart_discount', 'cart_total']);

        return response()->json([
            'status' => true,
            'message' => 'Order inquiry placed. This kit does not charge cards.',
            'redirect' => '/',
        ]);
    }
}
