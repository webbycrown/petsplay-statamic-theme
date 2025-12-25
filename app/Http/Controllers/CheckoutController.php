<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use App\Services\Coupons\CouponService;
use Illuminate\Support\Str;
use App\Services\StripeService;
use App\Services\RazorpayService;

class CheckoutController
{
    public function placeOrder(Request $request){

        $cart = session()->get('cart', []);
        $cart_coupon = session()->get('cart_coupon', []);
        

        if (empty($cart)) {
            return response()->json(['error' => 'Cart is empty'], 422);
        }

        // ----------------------------
        // 1. VALIDATION
        // ----------------------------
        try {
            $request->validate([
                'name'    => 'required',
                'address' => 'required',
                'city'    => 'required',
                'state'   => 'required',
                'zip'     => 'required',
                'notes'   => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'type'   => 'validation',
                'errors' => $e->errors(),
                'message'=> 'Please fix the errors below.',
            ]);
        }

        // ----------------------------
        // 2. CLEAN INPUT EXTRACTION
        // ----------------------------
        $author_id      = $request->input('author_id', '');
        $payment_method = $request->input('payment_method', '');
        $amount         = (float)$request->input('amount', 0);

        // products might be JSON or array → normalize
        $products = $request->input('products', []);

        if (is_string($products)) {
            $products = json_decode($products, true) ?? [];
        }

        if (!is_array($products)) {
            $products = [];
        }

        // ----------------------------
        // 3. UPDATE CUSTOMER PROFILE
        // ----------------------------
        $customer = Entry::query()
        ->where('collection', 'customer')
        ->where('id', $author_id)
        ->first();

        if (!$customer) {
            return response()->json([
                'status'    => false,
                'type'      => 'unauthorized',
                'message'   => 'Please sign in to continue.',
                'redirect'  => '/sign-in'
            ]);
        }
        $discount = $request->discount ?? 0;
        $s_charge = $request->s_charge ?? 0;

        $customer->data([
            'title'    => $request->name ?? $customer->title,
            'email'    => $request->email ?? $customer->email,
            'password' => $customer->password,
            'address'  => $request->address ?? $customer->address,
            'city'     => $request->city ?? $customer->city,
            'state'    => $request->state ?? $customer->state,
            'zip_code' => $request->zip ?? $customer->zip,
            'notes'    => $request->notes ?? $customer->notes,
            'number'   => $customer->number,
        ]);
        $customer->save();
        $coupon_id = $cart_coupon['coupon_id'] ?? 0;
        if ($payment_method === 'stripe') {
            $stripe = StripeService::createPaymentIntent($amount, $coupon_id, $request->notes, $products, $author_id, $discount, $s_charge);
            session()->put('cart', []);
            return response()->json($stripe);
        }

        if( $payment_method === 'razorpay' ){
           $razorpay = RazorpayService::createPaymentIntent($amount, $coupon_id, $request->notes, $products, $author_id, $discount,$s_charge);
            session()->put('cart', []);
            return response()->json($razorpay);
        }

        if( $payment_method === 'cash-on-delivery' ){
        // ----------------------------
        // 4. CREATE ORDERS
        // ----------------------------
            foreach ($products as $product) {

                $product_id = $product['product_id'] ?? null;
                $size       = $product['size'] ?? null;
                $price      = (float)($product['price'] ?? 0);
                $qty        = (float)($product['quantity'] ?? 0);
                $total      = (float)$price * (float)$qty;

                if (!$product_id || $qty <= 0) {
                // skip invalid item
                    continue; 
                }

                $product = Entry::query()
                ->where('collection', 'products')
                ->where('id', $product_id)
                ->first();
                if ($product) {
                    $currentStock = (int)$product->get('stock');
                    $newStock = max(0, $currentStock - (int)$qty);

                    // Correct way: set() then save()
                    $product->set('stock', $newStock);

                    $product->save();
                }

                Entry::make()->collection('orders')
                ->data([
                    'title'          => 'order-' . Str::random(8),
                    'payment_method' => 'cash-on-delivery',
                    'order_status'   => 'pending',
                    'product_id'     => $product_id,
                    'price'          => $price,
                    'qty'            => $qty,
                    'total'          => $total,
                    'size'           => $size,
                    'coupon_id'      => $coupon_id,
                    'author_id'      => $author_id,
                    'shipping_charge'=> $s_charge,
                    'discount'       => $discount,
                ])->save();
            }

            // ----------------------------
            // If payment = stripe → call createStripePayment()
            // ----------------------------

            session()->put('cart', []);
            return response()->json([
                'status'  => true,
                'message' => 'Order placed successfully',
                'redirect'  => '/'
            ]);

        }
    }

     /**
     * STRIPE – CREATE PAYMENT
     */
    public function createStripePayment(Request $request)
    {
        $amount  = $request->amount;
        $orderId = $request->order_id;

        $session = StripeService::createPaymentIntent($amount, $orderId);

        return response()->json($session);
    }

     /**
     * STRIPE – VERIFY PAYMENT AFTER SUCCESS
     */
    public function verifyPayment(Request $request)
    {
        $sessionId = $request->session_id;

        $result = StripeService::verifyPayment($sessionId);

        if ($result['status'] === 'success') {
            return redirect('/')->with('success', 'Payment completed');
        }

        return redirect('/checkout')->with('error', $result['message']);
    }

    public function verifyRazorpayPayment( Request $request ){

        $order_id = $request->get('order_id') ? $request->get('order_id') : '';
        $payment_id = $request->get('payment_id') ? $request->get('payment_id') : '';
        $signature = $request->get('signature') ? $request->get('signature') : '';
        $result = RazorpayService::verifyRazorpayPayment($order_id, $payment_id, $signature);

        if ($result['status'] === 'success') {
            return response()->json($result);
        }

        return response()->json($result, 400);
    }

}