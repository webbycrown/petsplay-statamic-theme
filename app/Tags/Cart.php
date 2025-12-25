<?php

namespace App\Tags;

use Statamic\Tags\Tags;

class Cart extends Tags
{
    protected static $handle = 'cart';
    
    /**
     * Returns cart data and subtotal for frontend display
     */
    public function index()
    {   
        // Clear coupon when viewing cart directly
        // (USE ONLY IF intentionally resetting coupons!)
        session()->put('cart_coupon', []);

        // Get cart items
        $items = session()->get('cart', []);

        // Filter valid items (must have price & quantity)
        $items = array_filter($items, fn($item) => isset($item['price'], $item['quantity']));

        // Calculate subtotal
        $subtotal = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $items));
        $count = count($items);

        // Re-index array
        $products = array_values($items);

        return [
            'products' => $products,
            'subtotal' => $subtotal,
            'count' => $count,
        ];
    }
}
