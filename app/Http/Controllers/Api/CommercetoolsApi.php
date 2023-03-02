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
                $temp_customer =  $customer;
            }
        }

        if (isset($temp_customer)) {
            return $temp_customer;
        } else {
            $body = '{ "email" : "' . $email . '", "firstName" : "' . $name . '", "lastName" : "", "password" : "' . $password . '" }';
            $customer = $this->callCT('customers', "POST", $body)->customer;

            $body = [
                "version" => $customer->version,
                "actions" => [
                    [
                        "action" => "addAddress",
                        "address" => [
                            "key" => uniqid(),
                            "title" => "My Address",
                            "salutation" => "",
                            "firstName" => $name,
                            "lastName" => "",
                            "streetName" => $address,
                            "streetNumber" => "",
                            "additionalStreetInfo" => "",
                            "postalCode" => "",
                            "city" => $city,
                            "region" => "",
                            "state" => "",
                            "country" => $country,
                        ]
                    ]
                ]
            ];

            return $this->callCT('customers/' . $customer->id, "POST", json_encode($body));
        }
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

    public function getOrdersById($order_id = null)
    {
        return $this->callCT('orders/' . $order_id);
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
        $cart = $this->callCT('carts', 'POST', $body);

        return $this->updateShippingAddressCart($cart->id);
    }

    public function customerSignIn(Request $request)
    {
        $data = $request->all();

        $body = '{
            "email" : "' . $data['email'] . '",
            "password" : "' . $data['password'] . '"
          }';

        $customer = $this->callCT('login', 'POST', $body);

        if (isset($customer->statusCode)) {
            return '400';
        } else {
            return $customer->customer;
        }

        return $customer->statusCode;
    }


    public function customerOrders()
    {

        return $orders = $this->callCT('me/orders');

        if (isset($orders->statusCode)) {
            return '400';
        } else {
            return $orders->orders;
        }

        return $orders->statusCode;
    }

    public function getOffer()
    {
        return response()->json('Under devlopment');
    }

    public function updateOrderWithCustomerId($order_id, $customer_id)
    {
        $order = $this->getOrdersById($order_id);
        $body = '{
            "version": ' . $order->version . ',
            "actions": [
                {
                    "action" : "setCustomerId",
                    "customerId" : "' . $customer_id . '"
                }
            ]
        }';
        return $this->callCT('orders/' . $order_id, 'POST',  $body);
    }

    public function updateShippingAddress($order_id, $shippingAddress = [])
    {
        $order = $this->getOrdersById($order_id);
        $body = '{
            "version": ' . $order->version . ',
            "actions": [
                {
                    "action" : "setShippingAddress",
                    "address" : {
                      "key" : "exampleKey",
                      "title" : "My Address",
                      "salutation" : "Mr.",
                      "firstName" : "Example 1",
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

        return $this->callCT('orders/' . $order_id, 'POST',  $body);
    }

    public function updateShippingAddressCart($cart_id, $shippingAddress = [])
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
                      "firstName" : "Example 1",
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

    public function getPaymentById($payment_id)
    {
        return $this->callCT('payments/' . $payment_id);
    }

    public function updateCustomerInPayment($payment_id, $customer_id)
    {
        $payment = $this->getPaymentById($payment_id);
        $body = '{
            "version": ' . $payment->version . ',
            "actions": [
                {
                    "action" : "setCustomer",
                    "customer" : {
                      "typeId" : "customer",
                      "id" : "' . $customer_id . '"
                    }
                  }
            ]
        }';
        return $this->callCT('payments/' . $payment_id, 'POST',  $body);
    }

    public function createPayment($order_id, $currencyCode, $centAmount, $timestamp)
    {
        $payments = $this->callCT('payments', 'GET');
        foreach ($payments->results as $key => $value) {
            if (isset($value->interfaceId)) {
                if ($value->interfaceId == $order_id) {
                    return $this->callCT('payments/' . $value->id, 'GET');
                }
            }
        }

        $body = '{
            "key" : "' . time() . '",
            "interfaceId" : "' . $order_id . '",
            "amountPlanned" : {
              "currencyCode" : "' . $currencyCode . '",
              "centAmount" : ' . $centAmount . '
            },
            "paymentMethodInfo" : {
              "paymentInterface" : "Super Payments",
              "method" : "Online",
              "name" : {
                "en" : "Open Banking"
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

    public function addPaymentToOrder($order_id, $payment_id)
    {
        $order = $this->getOrdersById($order_id);
        $body = '{
            "version": ' . $order->version . ',
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
        return $this->callCT('orders/' . $order_id, 'POST',  $body);
    }

    public function changePaymentState($order_id)
    {
        $order = $this->getOrdersById($order_id);
        $body = '{
            "version": ' . $order->version . ',
            "actions": [
                {
                    "action" : "changePaymentState",
                    "paymentState" : "Paid"
                  }
            ]
        }';
        return $this->callCT('orders/' . $order_id, 'POST',  $body);
    }

    public function createOrder(Request $request)
    {
        $data = $request->all();

        $currencyCode = $data['currencyCode'];
        $centAmount = $data['centAmount'];
        $timestamp = $data['timestamp'];

        $cart_id = $data['cart_id'];
        $order_id = $data['order_id'];
        $customer_id = $data['customer_id'];
        $order = $this->getOrdersById($order_id);

        $this->updateOrderWithCustomerId($order_id, $customer_id);
        $order = $this->updateShippingAddress($order_id);

        if (!isset($order->paymentInfo)) {
            $payment = $this->createPayment($order_id, $currencyCode, $centAmount, $timestamp);
            $this->updateCustomerInPayment($payment->id, $customer_id);
            $this->changePaymentState($order_id);
            return $this->addPaymentToOrder($order_id, $payment->id);
        }


        // return $this->callCT('orders', 'POST',  $body);
    }


    public function preCreateOrder(Request $request)
    {

        $cart_id = $request->cart_id;
        $cart_version = $request->version;
        $body = '{"id" : "' . $cart_id . '","version" : ' . $cart_version . ',"orderNumber" : "' . time() . '"}';

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


    public function cancelOrder($orderId = null, $paymentId = null)
    {
        $order = $this->getOrdersById($orderId);
        $body = [
            "version" => $order->version,
            "actions" => [
                [
                    "action" => "changeOrderState",
                    "orderState" => "Cancelled"
                ]
            ]
        ];
        $order =  $this->callCT('orders/' . $orderId, "POST", json_encode($body));

        // $body = [
        //     "version" => $order->version,
        //     "actions" => [
        //         [
        //             "action" => "removePayment",
        //             "payment" => [
        //                 "typeId" => "payment",
        //                 "id" => $paymentId
        //             ]
        //         ]
        //     ]
        // ];
        // $order =  $this->callCT('orders/' . $orderId, "POST", json_encode($body));

        $body = [
            "version" => $order->version,
            "actions" => [
                [
                    "action" => "changePaymentState",
                    "paymentState" => "Pending"
                ]
            ]
        ];
        return $this->callCT('orders/' . $orderId, "POST", json_encode($body));
    }
}
