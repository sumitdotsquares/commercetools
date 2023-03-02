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
        if (isset($cart->totalLineItemQuantity)) {
            return $cart->totalLineItemQuantity;
        } else {
            return 0;
        }
    }
}


if (!function_exists('getSuperPayOffer')) {
    function getSuperPayOffer()
    {
        $cart = Session::get('ct_cart');
        $cart_items = $cart->lineItems;
        $minorUnitAmountTotal = 0;
        $items = [];
        for ($i = 0; $i < count($cart->lineItems); $i++) {
            $minorUnitAmount = (int) $cart_items[$i]->totalPrice->centAmount;
            $minorUnitAmountTotal +=  $minorUnitAmount;
            $items[] = [
                'name' =>  $cart_items[$i]->name->en,
                'quantity' =>  $cart_items[$i]->quantity,
                'url' => $cart_items[$i]->variant->images[0]->url,
                'minorUnitAmount' =>  $minorUnitAmount
            ];
        }

        $itemData = [
            'minorUnitAmount' => $minorUnitAmountTotal,
            'cart' => [
                'id' => $cart->id,
                'items' =>  $items

            ],
            'page' => 'Checkout',
            'output' => 'both',
            'test' => true
        ];


        $url = config('commercetools.SUPAR_API_URL') . '/offers';
        $getApikey = config('commercetools.SUPAR_API_KEY');

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

        if ($response === false) {
            echo 'Curl error: ' . curl_error($curl);
        } else {
            $response = json_decode($response);
            Session::put('ct_suparpay_offer', $response);
            Session::put('ct_suparpay_offer_id', $response->cashbackOfferId);
            return $response;
        }

        curl_close($curl);
    }
}

if (!function_exists('getSuperPayment')) {
    function getSuperPayment($order_id)
    {
        $APP_URL = config('commercetools.APP_URL');
        $cart = Session::get('ct_cart');
        $session_data = Session::all();
        $minorUnitAmount = $session_data['ct_cart']->totalPrice->centAmount;
        $ct_customer =  Session::get('ct_customer');
        $ct_suparpay_offer_id = Session::get('ct_suparpay_offer_id');
        $url = config('commercetools.SUPAR_API_URL') . '/payments';
        $getApikey = config('commercetools.SUPAR_API_KEY');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "cashbackOfferId": "' . $ct_suparpay_offer_id . '",
                "successUrl": "' . $APP_URL . '/superpayments/success",
                "cancelUrl": "' . $APP_URL . '/checkout",
                "failureUrl": "' . $APP_URL . '/checkout",
                "minorUnitAmount": ' . $minorUnitAmount . ',
                "currency": "GBP",
                "externalReference": "' . $order_id . '"
                }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Referer: ' . $APP_URL,
                'checkout-api-key: ' . $getApikey
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);
        Session::put('ct_suparpay_payments', $response);
        return $response;
    }
}

if (!function_exists('refundSuperPayment')) {
    function refundSuperPayment($transactionId = null, $minorUnitAmount = null, $orderId = null)
    {
        $APP_URL = config('commercetools.APP_URL');
        $url = config('commercetools.SUPAR_API_URL') . '/refunds';
        $getApikey = config('commercetools.SUPAR_API_KEY');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                    "currency": "GBP",
                    "transactionId": "' . $transactionId . '",
                    "minorUnitAmount": ' . $minorUnitAmount . ',
                    "externalReference": "'.$orderId.'"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Referer: ' . $APP_URL,
                'checkout-api-key: ' . $getApikey
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);
        return $response;
    }
}


if (!function_exists('checkRefundSuperPayment')) {
    function checkRefundSuperPayment($transactionId)
    {
        $APP_URL = config('commercetools.APP_URL');
        $url = config('commercetools.SUPAR_API_URL') . '/refunds/' . $transactionId;
        $getApikey = config('commercetools.SUPAR_API_KEY');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',

            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Referer: ' . $APP_URL,
                'checkout-api-key: ' . $getApikey
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);
        return $response;
    }
}


if (!function_exists('getPaymentSuperPayment')) {
    function getPaymentSuperPayment()
    {
        $APP_URL = config('commercetools.APP_URL');
        $url = config('commercetools.SUPAR_API_URL') . '/payments/search?pageSize=100';
        $getApikey = config('commercetools.SUPAR_API_KEY');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',

            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Referer: ' . $APP_URL,
                'checkout-api-key: ' . $getApikey
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);
        return $response;
    }
}


if (!function_exists('formatAmount')) {
    function formatAmount($dollars = 0)
    {
        echo config('commercetools.currencySymbol') . ' ' . sprintf('%0.2f', $dollars);
    }
}

if (!function_exists('getCustomer')) {
    function getCustomer()
    {
        if (isset(Session::get('ct_customer')->id)) {
            return true;
        } else {
            return false;
        }
    }
}
