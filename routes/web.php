<?php

use App\Http\Controllers\HomeController;
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

Route::get('/', [HomeController::class, 'index'])->name('shop');
Route::get('/add-to-cart/{id}', [HomeController::class, 'addTocart'])->name('add-to-cart');   
Route::get('/checkout', [HomeController::class, 'checkout'])->name('checkout');   