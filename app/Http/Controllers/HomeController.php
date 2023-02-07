<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CommercetoolsController as CT;

class HomeController extends Controller
{
    public function index()
    {
        $ct = new CT();
        $output['products'] = $ct->getProducts();
        // $output['carts'] = $ct->getCarts();
        return view('pages.home', $output);
    }

    public function addTocart($product_id = null)
    {
        if ($product_id != null) {
            $ct = new CT();
            $ct->addToCart($product_id);
        }

        dd('HomeController');    
        return redirect()->route('shop');
    }
}
