<?php

use App\Http\Controllers\API\CommercetoolsApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', [CommercetoolsApi::class, 'getTest'])->name('getTest');  

Route::get('/products', [CommercetoolsApi::class, 'getProducts'])->name('getProducts');  
Route::get('/products/{id}', [CommercetoolsApi::class, 'getProductsById'])->name('getProductsById');  

Route::post('/customer', [CommercetoolsApi::class, 'getCustomerByEmail'])->name('getCustomerByEmail');  
Route::get('/carts', [CommercetoolsApi::class, 'getCarts'])->name('getCarts');  
Route::post('/carts', [CommercetoolsApi::class, 'createCart'])->name('createCart');
Route::get('/carts/{id}', [CommercetoolsApi::class, 'getCartsById'])->name('getCartsById');  
Route::any('/add-to-cart', [CommercetoolsApi::class, 'itemAddToCart'])->name('itemAddToCart');
Route::any('/get-offer', [CommercetoolsApi::class, 'getOffer'])->name('getOffer');
Route::any('/update-cart-with-customer-id', [CommercetoolsApi::class, 'updateCartWithCustomerId'])->name('updateCartWithCustomerId');
Route::any('/update-carts-shipping-address', [CommercetoolsApi::class, 'updateShippingAddress'])->name('updateShippingAddress');
Route::post('/create-order', [CommercetoolsApi::class, 'createOrder'])->name('createOrder');


