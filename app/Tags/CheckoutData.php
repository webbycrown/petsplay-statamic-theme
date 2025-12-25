<?php

namespace App\Tags;

use Statamic\Tags\Tags;
use Statamic\Facades\GlobalSet;

class CheckoutData extends Tags
{
    protected static $handle = 'checkout_data';

    /**
      * Handle {{ checkout_data }} tag
      *
      * Returns cart items + subtotal + discount + total
      */
    public function index()
    {   
        // Cart array from session (always ensure array)
        $cart = session()->get('cart', []);
        
        /**
         * Calculate subtotal safely:
         * - Ignore items missing price or quantity
         * - Convert values to float/int
         */
        $subtotal = array_sum(array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));

        // Discount from session (fallback 0)
        $discount = session()->get('cart_discount');

        // Total stored from CartController
        $cart_total = session()->get('cart_total',0);

        return [
            'items'     => $cart,
            'subtotal'  => $subtotal,
            'total'     => ( $cart_total == 0 ) ? $subtotal : $cart_total,
            'discount'  => $discount
        ];
    }
}
