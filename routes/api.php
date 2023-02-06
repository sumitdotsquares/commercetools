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
Route::post('/customer', [CommercetoolsApi::class, 'getCustomerByEmail'])->name('getCustomerByEmail');  
Route::get('/carts', [CommercetoolsApi::class, 'getCarts'])->name('getCarts');  
Route::get('/carts/{cart-id}', [CommercetoolsApi::class, 'getCartsId'])->name('getCartsId');  
Route::post('/carts/{cart-id}', [CommercetoolsApi::class, 'itemAddToCart'])->name('itemAddToCart');  
Route::post('/carts', [CommercetoolsApi::class, 'createCart'])->name('createCart');