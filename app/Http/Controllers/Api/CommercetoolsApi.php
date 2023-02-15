<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Illuminate\Support\Facades\Log;

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

    public function getCustomerByEmail(Request $request)
    {
        $customers = $this->callCT('customers');
        foreach ($customers->results as $customer) {
            if ($customer->email == $request->email) {
                return $customer;
            }
        }
        return response()->json(false);
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
        return $this->callCT('carts/' . $cart_id);
    }

    public function itemAddToCart(Request $request)
    {
        $data = $request->all();

        $body = [
            "version" => (int) $data['version'],
            "actions" => [
                [
                    "action" => "addLineItem",
                    "productId" => $data['productId'],
                    "variantId" => (int) $data['variantId'],
                    "quantity" => (int) $data['quantity']
                ]
            ]
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.us-central1.gcp.commercetools.com/super-payments/carts/' . $data['cart_id'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->accessToken
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
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

    public function getOffer()
    {
        return response()->json('Under devlopment');
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
        // dd($body);
        try {
            $request = new \GuzzleHttp\Psr7\Request($method, $apiURL, $headers, $body);
            $res = $this->client->sendAsync($request)->wait();
        } catch (\GuzzleHttp\Exception\RequestException $e) {

            if ($e->hasResponse()) {
                $res = $e->getResponse();
            }
        }
        return json_decode($res->getBody());
    }

    public function superpaymentsSuccess(Request $request)
    {
        die('Hello');
        $request_body = $request->all();
        Log::debug('Sumit');
        Log::debug(json_encode($request_body));
        return response()->json($request_body);
    }
}
