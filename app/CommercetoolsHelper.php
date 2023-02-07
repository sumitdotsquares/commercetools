<?php

use Illuminate\Support\Facades\Session;

if (!function_exists('pr')) {
    function pr($data)
    {
       echo '<pre>';
       print_r($data);
       echo '</pre>';
    }
}

if (!function_exists('getCartItems')) {
    function getCartItemsForCheckout()
    {
        $cart = Session::get('ct_cart');
        if ($cart) {
            return $cart->lineItems;
        }
    }
}

if (!function_exists('getCartItemCount')) {
    function getCartItemCount()
    {
        $cart = Session::get('ct_cart');
        if ($cart) {
            return sizeof($cart->lineItems);
        }
    }
}
