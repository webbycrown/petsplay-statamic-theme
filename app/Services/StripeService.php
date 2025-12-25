<?php

namespace WebbyCrown\CommerceSuite\Services;

use Statamic\Facades\GlobalSet;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Checkout\Session as StripeSession;
use Stripe\PaymentIntent;
use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Illuminate\Support\Str;
use Session;
use Exception;
use Log;

class StripeService
{   
    /**
     * Get Stripe Secret Key from Statamic Global Set
     *
     * @return string
     */

    private static function getStripeSecretKey(): string
    {   
        // Fetch global settings (product_page set)
    	$settings = GlobalSet::find('product_page')->inCurrentSite()->data();

        // Get all payment methods
    	$payments = $settings->get('payments', []);

        // Find Stripe settings
    	$stripe = collect($payments)->firstWhere('payment_option', 'stripe');

    	return $stripe['key_secret'] ?? '';
    }

    /**
     * Create Stripe Checkout Payment Intent
     *
     * @param float $amount
     * @param int $coupon_id
     * @param string|null $message
     * @param array $products
     * @param int $author_id
     * @return array
     * @throws Exception
     */
    public static function createPaymentIntent($amount, $coupon_id = 0, $message = null, $products, $author_id, $discount, $s_charge): array
    {
    	try {
            // Validate amount
    		if (!$amount) {
    			throw new Exception('Amount is required.');
    		}
            // Get Stripe secret key
            $keySecret = self::getStripeSecretKey();
            Stripe::setApiKey($keySecret);

            // Create Stripe Checkout Session
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => ['name' => 'Test Product'],
                        'unit_amount' => (int) ($amount * 100), // in cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',

                 // Redirect URLs
                'success_url' => url('/order/stripe/verify?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => url('/checkout'),
            ]);

            // Store session ID into PHP session
            Session::put(['stripe_session_id' =>  $session->id]);

            /**
             * Create Orders in Statamic Entries
             */

            foreach ($products as $product) {

                $product_id = $product['product_id'] ?? null;
                $size       = $product['size'] ?? null;
                $price      = (float)($product['price'] ?? 0);
                $qty        = (float)($product['quantity'] ?? 0);
                $total      = (float)$price * (float)$qty;

                // Skip invalid items
                if (!$product_id || $qty <= 0) {
                    continue; 
                }

                // Fetch product entry
                $product = Entry::query()->where('collection', 'products')->where('id', $product_id)->first();

                // Reduce stock if product exists
                if ($product) {
                    $currentStock = (int)$product->get('stock');
                    $newStock = max(0, $currentStock - (int)$qty);

                    $product->set('stock', $newStock);

                    $product->save();
                }

                // Create order entry
                Entry::make()->collection('orders')
                ->data([
                    'title'          => 'order-' . Str::random(8),
                    'payment_method' => 'stripe',
                    'order_status'   => 'created',
                    'product_id'     => $product_id,
                    'price'          => $price,
                    'qty'            => $qty,
                    'total'          => $total,
                    'size'           => $size,
                    'coupon_id'      => $coupon_id,
                    'author_id'      => $author_id,
                    'shipping_charge'=> $s_charge,
                    'discount'       => $discount,
                    'payment_id'     => $session->id,
                ])->save();
            }

            // Return success response with checkout URL
            return [
                'checkout_url'      => $session->url,
                'paymentIntentId'   => $session->id,
            ];

        } catch (\Exception $e) {
            \Log::error('Stripe error: ' . $e->getMessage());
            throw new Exception('Stripe checkout creation failed: ' . $e->getMessage());
        }
    }


    /**
     * Verify Stripe Checkout Payment
     *
     * @param string $sessionId
     * @return array
     * @throws Exception
     */
    public static function verifyPayment(string $sessionId): array
    {
        if (!$sessionId) {
            throw new Exception('Session ID is required.');
        }

        $keySecret = self::getStripeSecretKey();
        Stripe::setApiKey($keySecret);

        try {
            // Retrieve checkout session
            $session = StripeSession::retrieve($sessionId);

            if (!$session || !$session->payment_intent) {
                return ['status' => 'error', 'message' => 'Invalid session.'];
            }

            // Retrieve payment intent
            $intent = PaymentIntent::retrieve($session->payment_intent);

            // On success update order
            if ($intent && $intent->status === 'succeeded') {
                $orders = Entry::query()
                    ->where('collection', 'orders')
                    ->where('payment_id', $session->id)
                    ->first();

                    if( count($orders) > 0 ){
                        // Update all matching orders
                    	foreach ($orders as $order) {
                    		$order->set('order_status', 'paid')->save();
                    	}
                    }

                // Clear session
                Session::forget('stripe_session_id');

                return ['status' => 'success', 'message' => 'Payment verified successfully.'];
            }

            // Payment failed
            return ['status' => 'error', 'message' => 'Payment not completed.'];
        } catch (\Exception $e) {
            \Log::error('Stripe verification failed: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}