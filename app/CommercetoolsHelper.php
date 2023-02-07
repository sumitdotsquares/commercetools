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
        if ($cart) {
            return $cart->lineItems;
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
        dump(getCart()->totalPrice->centAmount / 100);

        $POSTFIELDS = [
            "minorUnitAmount" => 10000,
            "cart" => [
                "id" => "cart101",
                "items" => [
                    [
                        "name" => "Im a product",
                        "quantity" => 2,
                        "minorUnitAmount" => 10000,
                        "url" => "https:\\/\\/www.dev-site-2x6137.wixdev-sites.org\\/product-page\\/i-m-a-product-8"
                    ],
                    [
                        "name" => "Amazing boots",
                        "quantity" => 3,
                        "minorUnitAmount" => 10000,
                        "url" => "https://www.merchant.com/product1.html"
                    ]
                ]
            ],
            "page" => "Checkout",
            "output" => "both",
            "test" => true
        ];
        $POSTFIELDS = json_encode($POSTFIELDS);
        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json',
            'Referer' => 'https://www.staging.superpayments.com',
            'checkout-api-key' => 'PSK_mXO-nafkIq1zhuoGcik41VExMi1QLgtxtUcQyJQl'
        ];
        $body = '{
            "minorUnitAmount": 10000,
            "cart": {
                "id": "cart101",
                "items": [
                {
                    "name": "Im a product",
                    "quantity": 2,
                    "minorUnitAmount": 10000,
                    "url": "https://www.dev-site-2x6137.wixdev-sites.org/product-page/i-m-a-product-8"
                },
                {
                    "name": "Amazing boots",
                    "quantity": 3,
                    "minorUnitAmount": 10000,
                    "url": "https://www.merchant.com/product1.html"
                }
                ]
            },
            "page": "Checkout",
            "output": "both",
            "test": true
        }';
        $request = new Request('POST', 'https://api.staging.superpayments.com/v2/offers', $headers, $body);
        $res = $client->sendAsync($request)->wait();
      dd(json_encode($res->getBody()));
        
    }
}
