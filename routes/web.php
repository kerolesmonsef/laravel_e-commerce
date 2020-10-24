<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaveForLaterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [LandingPageController::class, 'index'])->name('landing-page');
Route::resource("product", ProductController::class);
Route::resource("cart", CartController::class);
Route::post('/cart/switchToSaveForLater/{rowID}', [CartController::class, 'switchToSaveForLater'])->name('cart.switchToSaveForLater');

Route::post('/saveForLater/switchToCart/{product}', [SaveForLaterController::class, 'switchToCart'])->name('saveForLater.switchToCart');

Route::group(['middleware' => 'auth'], function () {
    Route::get("checkout", [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('checkout/redirect/success', [CheckoutController::class, 'success'])->name('checkout.redirect.success');
    Route::get('checkout/redirect/fail', [CheckoutController::class, 'fail'])->name('checkout.redirect.fail');
});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
