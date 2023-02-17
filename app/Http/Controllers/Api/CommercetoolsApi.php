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
        $email = $request->email;
        $password = $request->password;
        $name = $request->name;
        $address = $request->address;
        $city = $request->city;
        $country = $request->country;

        foreach ($customers->results as $customer) {
            if ($customer->email == $request->email) {
                return $customer;
            }
        }

        $body = '{
            "email" : "' . $email . '",
            "firstName" : "' . $name . '",
            "lastName" : "",
            "password" : "' . $password . '",
          }';
        return $request->all();
        return $customer = $this->callCT('customers', "POST", $body);

        $body = '{
            "version": ' . $customer->version . ',
            "actions": [
                {
                    "action" : "addAddress",
                    "address" : {
                      "key" : "' . uniqid() . '",
                      "title" : "My Address",
                      "salutation" : "Mr.",
                      "firstName" : "' . $name . '",
                      "lastName" : "",
                      "streetName" : "' . $address . '",
                      "streetNumber" : "",
                      "additionalStreetInfo" : "",
                      "postalCode" : "",
                      "city" : "' . $city . '",
                      "region" : "",
                      "state" : "",
                      "country" : "' . $country . '",
                    }
                  }
            ]
        }';

        return $this->callCT('customers/' . $customer->id, "POST", $body);

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

    public function updateCartWithCustomerId($cart_id, $customer_id)
    {
        $cart = $this->getCartsById($cart_id);
        $body = '{
            "version": ' . $cart->version . ',
            "actions": [
                {
                    "action" : "setCustomerId",
                    "customerId" : "' . $customer_id . '"
                }
            ]
        }';
        return $this->callCT('carts/' . $cart_id, 'POST',  $body);
    }

    public function updateShippingAddress($cart_id, $shippingAddress = [])
    {
        $cart = $this->getCartsById($cart_id);
        $body = '{
            "version": ' . $cart->version . ',
            "actions": [
                {
                    "action" : "setShippingAddress",
                    "address" : {
                      "key" : "exampleKey",
                      "title" : "My Address",
                      "salutation" : "Mr.",
                      "firstName" : "Example",
                      "lastName" : "Person",
                      "streetName" : "Example Street",
                      "streetNumber" : "4711",
                      "additionalStreetInfo" : "Backhouse",
                      "postalCode" : "80933",
                      "city" : "Exemplary City",
                      "region" : "Exemplary Region",
                      "state" : "Exemplary State",
                      "country" : "DE",
                      "company" : "My Company Name",
                      "department" : "Sales",
                      "building" : "Hightower 1",
                      "apartment" : "247",
                      "pOBox" : "2471",
                      "phone" : "+49 89 12345678",
                      "mobile" : "+49 171 2345678",
                      "email" : "email@example.com",
                      "fax" : "+49 89 12345679",
                      "additionalAddressInfo" : "no additional Info",
                      "externalId" : "Information not needed"
                    }
                  }
            ]
        }';

        return $this->callCT('carts/' . $cart_id, 'POST',  $body);
    }

    public function createPayment($cart_id, $currencyCode, $centAmount, $timestamp)
    {
        $payments = $this->callCT('payments', 'GET');
        foreach ($payments->results as $key => $value) {
            if (isset($value->interfaceId)) {
                if ($value->interfaceId == $cart_id) {
                    return $this->callCT('payments/' . $value->id, 'GET');
                }
            }
        }

        $body = '{
            "key" : "' . time() . '",
            "interfaceId" : "' . $cart_id . '",
            "amountPlanned" : {
              "currencyCode" : "' . $currencyCode . '",
              "centAmount" : ' . $centAmount . '
            },
            "paymentMethodInfo" : {
              "paymentInterface" : "SUPARPAYMENT",
              "method" : "ONLINE",
              "name" : {
                "en" : "Net Banking"
              }
            },
            "transactions" : [ {
              "timestamp" : "' . $timestamp . '",
              "type" : "Charge",
              "amount" : {
                "currencyCode" : "' . $currencyCode . '",
                "centAmount" : ' . $centAmount . '
              },
              "state" : "Success"
            } ]
          }';
        return $this->callCT('payments', 'POST',  $body);
    }

    public function addPaymentToCart($cart_id, $payment_id)
    {
        $cart = $this->getCartsById($cart_id);
        $body = '{
            "version": ' . $cart->version . ',
            "actions": [
                {
                    "action" : "addPayment",
                    "payment" : {
                      "id" : "' . $payment_id . '",
                      "typeId" : "payment"
                    }
                  }
            ]
        }';
        return $this->callCT('carts/' . $cart_id, 'POST',  $body);
    }

    public function createOrder(Request $request)
    {
        $data = $request->all();
        $currencyCode = $data['currencyCode'];
        $centAmount = $data['centAmount'];
        $timestamp = $data['timestamp'];

        $cart_id = $data['cart_id'];
        $customer_id = $data['customer_id'];
        $cart = $this->getCartsById($cart_id);

        if (!isset($cart->shippingAddress)) {
            $this->updateCartWithCustomerId($cart_id, $customer_id);
            $cart = $this->updateShippingAddress($cart_id);
        }

        if (!isset($cart->paymentInfo)) {
            $payment = $this->createPayment($cart_id, $currencyCode, $centAmount, $timestamp);
            $cart = $this->addPaymentToCart($cart_id, $payment->id);
        }

        $body = '{
            "id" : "' . $cart->id . '",
            "version" : ' . $cart->version . ',
            "orderNumber" : "' . time() . '"
          }';

        return $this->callCT('orders', 'POST',  $body);
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
}
