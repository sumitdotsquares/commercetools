<?php

use App\Http\Controllers\CommercetoolsController;
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

Route::get('/phpinfo', [HomeController::class, 'phpinfo'])->name('phpinfo');
Route::get('/', [HomeController::class, 'index'])->name('shop');
Route::get('/add-to-cart/{id}', [HomeController::class, 'addTocart'])->name('add-to-cart');   
Route::get('/checkout', [HomeController::class, 'checkout'])->name('checkout');   
Route::get('/process-checkout', [HomeController::class, 'processCheckout'])->name('processCheckout');   
Route::post('/customer', [CommercetoolsController::class, 'getCustomerByEmail'])->name('getCustomerByEmail');  
Route::any('/superpayments/success', [CommercetoolsController::class, 'superpaymentsSuccess']);  
Route::any('/superpayments/cancel', [CommercetoolsController::class, 'superpaymentsSuccess']);  
Route::any('/superpayments/fail', [CommercetoolsController::class, 'superpaymentsSuccess']);  
Route::any('/superpayments/payments', [HomeController::class, 'webhook']);  
 

Route::any('/reset-session', [HomeController::class, 'resetSession']);  
Route::get('/user-dashboard', [HomeController::class, 'userDashboard'])->name('userDashboard');  
Route::any('/login', [HomeController::class, 'login'])->name('login');    
Route::any('/logout', [HomeController::class, 'logout'])->name('logout');    

