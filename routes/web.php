<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsLetterController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use Statamic\Facades\Parse;
use Statamic\Facades\Site;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;


// Route to handle newsletter subscription form submissions
Route::get('/newsLetter', [NewsLetterController::class, 'newsLetter'])->name('newsLetter');



Route::get('/api/products', [ProductController::class, 'productFilter'])->name('productFilter');
Route::post('/product-comment', [ProductController::class, 'store'])->name('productCommentAdd');


// send mail
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('sendResetLink');

Site::all()->each(function (Statamic\Sites\Site $site) {
    Route::prefix($site->url())->group(function () {
        Route::statamic('/blog/category/{category_slug}', 'category');
    });
});

// handle reset
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('reset-password');





Route::post('/set-package-session', function (Request $request) {
    Session::put('package_id', $request->get('package_id'));
    return response()->json(['success' => true]);
});