<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsLetterController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CheckoutController;
use Statamic\Facades\Parse;
use Statamic\Facades\Site;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;


// Route to handle newsletter subscription form submissions
Route::get('/newsLetter', [NewsLetterController::class, 'newsLetter'])->name('newsLetter');
Route::post('/registration', [AuthController::class, 'registration'])->name('registration');
Route::post('/login-user', [AuthController::class, 'login'])->name('login');
Route::post('/logout-user', [AuthController::class, 'logout'])->name('logout');


Route::get('/api/products', [ProductController::class, 'productFilter'])->name('productFilter');
Route::post('/product-comment', [ProductController::class, 'store'])->name('productCommentAdd');


// send mail
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('sendResetLink');

Site::all()->each(function (Statamic\Sites\Site $site) {
    Route::prefix($site->url())->group(function () {
        Route::statamic('/reset-password/{token}', 'reset-password');
        Route::statamic('/blog/category/{category_slug}', 'category');
    });
});

// handle reset
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('reset-password');



/*
|--------------------------------------------------------------------------
| Cart Routes
|--------------------------------------------------------------------------
| Handles add, remove, update cart items and apply/remove coupons.
*/

Route::prefix('cart')->group(function() {
    // Add product to cart
    Route::post('add',    [CartController::class, 'add'])->name('cart.add');

    // Remove product from cart
    Route::post('remove', [CartController::class, 'remove'])->name('cart.remove');

    // Update quantity in cart
    Route::post('update', [CartController::class, 'update'])->name('cart.update');

    /*
    |----------------------------------------------------------------------
    | Coupon Management
    |----------------------------------------------------------------------
    */

    // Apply coupon code
    Route::post('coupon/apply',  [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');

    // Remove applied coupon code
    Route::post('coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
});

/*
|--------------------------------------------------------------------------
| Wishlist Routes
|--------------------------------------------------------------------------
| Handles add/remove wishlist items.
*/

// Add item to wishlist
Route::post('/wishlist/add', [WishlistController::class, 'add']);

// Remove item from wishlist
Route::post('/wishlist/remove', [WishlistController::class, 'remove']);

/*
|--------------------------------------------------------------------------
| Checkout Route
|--------------------------------------------------------------------------
| Final order placement.
*/
Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder']);
Route::post('/order/razorpay/verify', [CheckoutController::class, 'verifyRazorpayPayment']);


Route::post('/set-package-session', function (Request $request) {
    Session::put('package_id', $request->get('package_id'));
    return response()->json(['success' => true]);
});