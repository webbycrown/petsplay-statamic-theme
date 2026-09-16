<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\NewsLetterController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Statamic\Facades\Site;

Route::get('/newsLetter', [NewsLetterController::class, 'newsLetter'])->name('newsLetter');
Route::get('/api/products', [ProductController::class, 'productFilter'])->name('productFilter');
Route::post('/product-comment', [ProductController::class, 'store'])->name('productCommentAdd');

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('sendResetLink');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('reset-password');

Site::all()->each(function (Statamic\Sites\Site $site) {
    Route::prefix($site->url())->group(function () {
        Route::statamic('/blog/category/{category_slug}', 'category');
    });
});

Route::post('/set-package-session', function (Request $request) {
    Session::put('package_id', $request->get('package_id'));

    return response()->json(['success' => true]);
});

Route::post('/customer/register', [AuthController::class, 'registration'])->name('customer.register');
Route::post('/customer/login', [AuthController::class, 'login'])->name('customer.login');
Route::get('/customer/logout', [AuthController::class, 'logout'])->name('customer.logout');

Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
Route::post('/wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');

Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
