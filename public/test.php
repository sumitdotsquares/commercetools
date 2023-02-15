<?php

$curl = curl_init();


curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.superpayments.com/v2/offers",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => '{"minorUnitAmount":10000,"cart":{"id":"cart101","items":[{"name":"Amazing boots","url":"https://www.merchant.com/product1.html","quantity":3,"minorUnitAmount":10000}]},"page":"Checkout","output":"both","scheme":"orange","test":true}',
    CURLOPT_HTTPHEADER => array(
        'content: application/json',
        'accept: application/json',
        'checkout-api-key: PSK_mXO-nafkIq1zhuoGcik41VExMi1QLgtxtUcQyJQl',
        'Content-Type: application/json'
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    echo $response;
}
