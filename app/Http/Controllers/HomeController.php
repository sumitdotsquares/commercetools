<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CommercetoolsController as CT;

class HomeController extends Controller
{
    public function index()
    {
        $ct = new CT();
        $output['products'] = $ct->getProducts();
        $output['cart_item_count'] = getCartItemCount();
        return view('pages.home', $output);
    }

    public function addTocart($product_id = null)
    {
        if ($product_id != null) {
            $ct = new CT();
            $ct->addToCart($product_id);
        }

        return redirect()->route('shop');
    }

    public function checkout()
    {
        $output['cart_items'] = getCartItemsForCheckout();
        $output['cart_item_count'] = getCartItemCount();
        return view('pages.checkout', $output);
    }
}
