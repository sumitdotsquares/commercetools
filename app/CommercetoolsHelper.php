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
        $itemData = [
            'minorUnitAmount' => 105,
            'cart' => [
                'id' => '5569854',
                'items' => [
                    [
                        'name' => 'Test products',
                        'quantity' => 1,
                        'url' => 'https://m243extensions.projectstatus.in/test-products.html',
                        'minorUnitAmount' => 100
                    ]
        
                ]
        
            ],
        
            'page' => 'Checkout',
            'output' => 'both',
            'test' => true
        ];
        $url = 'https://api.staging.superpayments.com/v2/offers';
        $getApikey = 'PSK_mXO-nafkIq1zhuoGcik41VExMi1QLgtxtUcQyJQl';
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
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
                'checkout-api-key: ' . $getApikey,
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);
        echo $response->cashbackOfferId;
        echo $response->content->description;
    }
}
