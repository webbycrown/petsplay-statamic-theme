<?php

namespace App\Tags;

use Statamic\Tags\Tags;

class Wishlist extends Tags
{   
    /**
     * This is the tag handle:
     * {{ wishlist }} ... {{ /wishlist }}
     */
    protected static $handle = 'wishlist';

    /**
     * Main tag: {{ wishlist }}  
     * Returns the wishlist items stored in session.
     */
    public function index()
    {   
        // Retrieve wishlist from session (or empty array if not set)
        $wishlist = session()->get('wishlist', []);

        // Statamic expects a loop variable for tag iteration
        return [
            'loop' => $wishlist
        ];
    }
}
