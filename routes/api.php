<?php

use App\Http\Controllers\API\CommercetoolsController;
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

Route::get('/test', [CommercetoolsController::class, 'posts'])->name('posts');  
Route::get('/products', [CommercetoolsController::class, 'getProducts'])->name('getProducts');  
Route::post('/customer', [CommercetoolsController::class, 'getCustomerByEmail'])->name('getCustomerByEmail');  
Route::get('/carts', [CommercetoolsController::class, 'getCarts'])->name('getCarts');  
Route::get('/carts/{cart-id}', [CommercetoolsController::class, 'getCartsId'])->name('getCartsId');  
Route::get('/cart', [CommercetoolsController::class, 'createCart'])->name('createCart');  
Route::get('/carts/line-item', [CommercetoolsController::class, 'cartAddLineItem'])->name('cartAddLineItem');  