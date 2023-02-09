<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
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
        if ($cart != null) {
            return $cart->lineItems;
        } else {
            return false;
        }
    }
}

if (!function_exists('getCart')) {
    function getCart()
    {
        return Session::get('ct_cart');
    }
}

if (!function_exists('getCartItemCount')) {
    function getCartItemCount()
    {
        $cart = Session::get('ct_cart');
        if ($cart) {
            return $cart->totalLineItemQuantity;
        }
    }
}


if (!function_exists('getSuperPayOffer')) {
    function getSuperPayOffer()
    {
        return 'Hello';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => $this->getUserAgent(),
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($itemData, true),
            CURLOPT_HTTPHEADER => array(
                'content: application/json',
                'accept: application/json',
                'checkout-api-key: ' . $this->getApikey(),
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
