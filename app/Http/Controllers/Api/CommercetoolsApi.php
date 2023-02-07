<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class CommercetoolsApi extends Controller
{
    public  $client;
    public $accessToken;
    public $projectKey;

    public function __construct()
    {
        $this->projectKey = config('commercetools.projectKey');
        $this->client = new Client();
        $headers = [
            'Authorization' => 'Basic ' . base64_encode(config('commercetools.clientID') . ":" . config('commercetools.secret'))
        ];
        $body = '';
        $request = new \GuzzleHttp\Psr7\Request('POST', 'https://auth.' . config('commercetools.region') . '.commercetools.com/oauth/token?grant_type=client_credentials', $headers, $body);
        $res = $this->client->sendAsync($request)->wait();

        $this->accessToken = json_decode($res->getBody())->access_token;
    }

    public function getTest()
    {
        return response()->json(['Hello']);
    }

    public function getProductsById($product_id = null)
    {
        return $this->callCT('products/' . $product_id);
    }

    public function getProducts()
    {
        return $this->callCT('products');
    }

    public function getCarts()
    {
        return $this->callCT('carts');
    }

    public function getCartsById($cart_id = null)
    {
        return $this->callCT('carts/'.$cart_id);
    }

    public function itemAddToCart(Request $request)
    {
        $data = $request->all();
        return response()->json($data);

        dd('Hello');
        $body = '{
            "version": 1,
            "actions": [
              {
                "action": "addLineItem",
                "productId": "4df38248-fb6e-4133-9f21-04ca5ce61bb7",
                "variantId": 1,
                "quantity": 1
              }
            ]
          }';

        dd($body);
        dd($this->callCT('carts/7b58ef7e-c28d-4e62-b62e-d1a301275ea2', 'POST', $body));

        return $this->callCT('carts');
    }

    public function createCart()
    {
        $body = '{
            "currency": "EUR",
            "shipping": [],
            "customShipping": []
          }';
        return $this->callCT('carts', 'POST', $body);
    }

    public function callCT($uri = null, $method = 'GET', $body = null)
    {
        if ($uri == null) {
            return;
        }

        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken
        ];

        $apiURL = 'https://api.' . config('commercetools.region') . '.commercetools.com/' . $this->projectKey . '/' . $uri;

        $request = new \GuzzleHttp\Psr7\Request($method, $apiURL, $headers, $body);
        $res = $this->client->sendAsync($request)->wait();

        return json_decode($res->getBody());
    }
}
