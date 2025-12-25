<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WishlistController
{
    public function add(Request $request)
    {

        $wishlist = session()->get('wishlist', []);

        $product = $request->only([
            'product_id', 'title', 'image', 'slug', 'short_description', 'price', 'status', 'url','stock','size'
        ]);

        $product['quantity'] = (int) $request->get('quantity', 1);

        $itemExists = false;
        foreach ($wishlist as &$wishlistItem) {
            if (isset($wishlistItem['product_id']) && $wishlistItem['product_id'] == $product['product_id']) {

              $wishlistItem['quantity'] += $product['quantity'];
                $itemExists = true;
                break;
            }
        }
        unset($wishlistItem);


        if (!$itemExists) {
            $wishlist[] = $product;
        }

        session()->put('wishlist', array_values($wishlist));

        $totalQuantity = collect($wishlist)->count();
        
        return response()->json(['success' => true, 'wishlist' => $wishlist]);
    }

    // public function remove(Request $request)
    // {

    //     $wishlist = session()->get('wishlist', []);

    //     $wishlist = array_filter($wishlist, function($item) use ($request) {
    //         return $item['product_id'] !== $request->get('product_id');
    //     });

    //     session()->put('wishlist', array_values($wishlist));

    //     return response()->json(['success' => true, 'wishlist' => $wishlist, 'is_empty' => count($wishlist) === 0]);
    // }


    public function remove(Request $request){


    $wishlist = session()->get('wishlist', []);

    $wishlist = array_filter($wishlist, function ($item) use ($request) {
        return $item['product_id'] !== $request->get('product_id');
    });

    $wishlist = array_values($wishlist);
    session()->put('wishlist', $wishlist);

    // ✅ Total quantity after removal
    $totalQuantity = collect($wishlist)->count();


    return response()->json([
        'success' => true,
        'wishlist' => $wishlist,
        'totalQuantity' => $totalQuantity,
        'is_empty' => count($wishlist) === 0
    ]);
}
}
