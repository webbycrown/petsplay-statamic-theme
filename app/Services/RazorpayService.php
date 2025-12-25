<?php

namespace App\Services;

use Statamic\Facades\GlobalSet;
use Razorpay\Api\Api;
use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Illuminate\Support\Str;
use Acme\Coupons\Components\Coupons as CouponsComponent;
use Session;
use Exception;
use Log;

class RazorpayService
{

	/**
     * Get Razorpay Key ID for frontend
     *
     * Reads the Statamic GlobalSet: product_page → payments → razorpay
     *
     * @return string
     */
	public static function getRazorpayKey()
	{
		$settings = GlobalSet::find('product_page')->inCurrentSite()->data();

        // payments is a replicator field in global set
    	$payments = $settings->get('payments', []);

        // find row with payment_option = razorpay
    	$stripe = collect($payments)->firstWhere('payment_option', 'razorpay');

    	return $stripe['key_id'] ?? '';
	}

    /**
     * Create Razorpay API Instance
     *
     * @return Api
     * @throws Exception
     */
	private static function getRazorpayApi(): Api
    {
    	$settings = GlobalSet::find('product_page')->inCurrentSite()->data();

    	$payments = $settings->get('payments', []);

    	$stripe = collect($payments)->firstWhere('payment_option', 'razorpay');


    	$keyId = $stripe['key_id'] ?? '';
        $keySecret = $stripe['key_secret'] ?? '';

        // Safety check
        if (!$keyId || !$keySecret) {
            throw new Exception('Razorpay keys are missing.');
        }

        // Create Razorpay API client
        return new Api($keyId, $keySecret);
    }

    /**
     * Create Razorpay Order & Store Local Order Entries
     *
     * @param float $amount
     * @param int $coupon_id
     * @param string|null $message
     * @param array $products
     * @param int $author_id
     * @return array
     * @throws Exception
     */
    public static function createPaymentIntent($amount, $coupon_id = 0, $message = null, $products, $author_id,$discount,$s_charge): array
    {
        if (!$amount) {
            throw new Exception('Amount is required.');
        }

        // Initialize API
        $api = self::getRazorpayApi();
        
         /**
         * Create Razorpay Order
         * Razorpay expects amount in paise → multiply by 100.
         * (sandbox: using static 1 USD = 100 cents)
         */
        $razorpayOrder = $api->order->create([
            'receipt' => 'rcpt_' . time(),
            'amount' => (int) ($amount * 100), // Convert to paise
            'currency' => 'USD',
            'payment_capture' => 1,
        ]);

        /**
         * Loop through purchased products
         * Update stock
         * Create local order entries in Statamic
         */
        foreach ($products as $product) {

            $product_id = $product['product_id'] ?? null;
            $size       = $product['size'] ?? null;
            $price      = (float)($product['price'] ?? 0);
            $qty        = (float)($product['quantity'] ?? 0);
            $total      = (float)$price * (float)$qty;

            // Skip invalid rows
            if (!$product_id || $qty <= 0) {
                continue; 
            }

            // Reduce stock
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

            // Create order entry in Statamic
            Entry::make()->collection('orders')
            ->data([
                'title'          => 'order-' . Str::random(8),
                'payment_method' => 'razorpay',
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
                'payment_id'     => $razorpayOrder['id'],
            ])->save();
        }

        /**
         * Return data to frontend for Razorpay Checkout
         */
        return [
            'success' => true,
            'order_id' => $razorpayOrder['id'],
            'amount' => $amount,
            'key_id' => self::getRazorpayKey(),
        ];
    }

    /**
     * Verify Razorpay Payment Signature & Update Local Orders
     *
     * @param string $order_id
     * @param string $payment_id
     * @param string $signature
     * @return array
     */
    public static function verifyRazorpayPayment($order_id, $payment_id, $signature)
    {	
    	try{
    		$api = self::getRazorpayApi();

            // Validate Razorpay signature
    		$api->utility->verifyPaymentSignature([
    			'razorpay_order_id' => $order_id,
    			'razorpay_payment_id' => $payment_id,
    			'razorpay_signature' => $signature
    		]);


    		// Update local order entries
    		$orders = Entry::query()
    		->where('collection', 'orders')
    		->where('payment_id', $order_id)
    		->get();
    		if( count($orders) > 0 ){
    			foreach ($orders as $order) {
    				$order->set('order_status', 'paid')->save();
    			}
    			
    		}

    		return ['status' => 'success', 'message' => 'Razorpay payment verified.'];

    	} catch (Exception $e) {
    		Log::error('Razorpay verification failed: ' . $e->getMessage());
    		return ['status' => 'error', 'message' => $e->getMessage()];
    	}
    }
}