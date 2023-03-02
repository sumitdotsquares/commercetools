<?php

namespace App\Http\Controllers;

use Commercetools\Api\Client\ApiRequestBuilder;
use Commercetools\Api\Client\ClientCredentialsConfig;
use Commercetools\Api\Client\Config;
use Commercetools\Api\Client\Resource\ResourceByProjectKeyCartsByID;
use Commercetools\Api\Client\Resource\ResourceByProjectKeyProductsByID;
use Commercetools\Api\Models\Cart\CartDraftBuilder;
use Commercetools\Client\ClientCredentials;
use Commercetools\Client\ClientFactory;
use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Routing\UrlGenerato;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommercetoolsController extends Controller
{
    public  $url;
    public  $client;
    public function __construct()
    {
        $this->url = url('/api');
        $this->client = new \GuzzleHttp\Client();
    }

    public function getProducts()
    {
        $products = Session::get('ct_products');
        if ($products == null) {
            $products = [];
            $result = $this->client->get(url('/api') . '/products');

            foreach (json_decode($result->getBody())->results as $key => $value) {
                $products[] = $this->getProductByKey($value);
            }

            Session::put('ct_products', $products);
        }

        return $products;
    }

    public function getProductsById($product_id = null)
    {
        if ($product_id == null) {
            return;
        }

        $request = new \GuzzleHttp\Psr7\Request('GET', url('/api') . '/products' . '/' . $product_id);
        $result = $this->client->sendAsync($request)->wait();
        return json_decode($result->getBody());
    }

    public function getCarts()
    {
        $cart_id = Session::get('ct_cart')->id;
        $request = new \GuzzleHttp\Psr7\Request('GET', url('/api') . '/carts' . '/' . $cart_id);
        $result = $this->client->sendAsync($request)->wait();
        return json_decode($result->getBody());
    }

    public function getCustomerByEmail(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        $name = $request->name;
        $address = $request->address;
        $city = $request->city;
        $country = $request->country;
        $request = new \GuzzleHttp\Psr7\Request('POST', url('/api') . '/customer-sign-in');
        $result = $this->client->sendAsync($request, ['form_params' => [
            "email" => $email,
            "password" => $password,
            "name" => $name,
            "address" => $address,
            "city" => $city,
            "country" => $country,
        ]])->wait();
        $result = json_decode($result->getBody());

        Session::put('ct_customer', $result);
        return response()->json($result);
    }

    public function getCartsById($cart_id = null)
    {
        if ($cart_id == null) {
            return;
        }

        $request = new \GuzzleHttp\Psr7\Request('GET', url('/api') . '/carts' . '/' . $cart_id);
        $result = $this->client->sendAsync($request)->wait();
        return json_decode($result->getBody());
    }

    public function getProductByKey($findProductByKey = null)
    {
        $procuct = [];
        if ($findProductByKey == null) {
            return;
        }

        $procuct['id'] = $findProductByKey->id;
        $procuct['name'] = $findProductByKey->masterData->current->name->en;
        $procuct['image'] = $findProductByKey->masterData->current->masterVariant->images[0]->url;
        $procuct['price'] = $findProductByKey->masterData->current->masterVariant->prices[0]->value;
        return $procuct;
    }

    public function addToCart($product_id = null)
    {
        $cart = Session::get('ct_cart');
        if (!isset($cart->version)) {
            $request = new \GuzzleHttp\Psr7\Request('POST', url('/api') . '/carts');
            $result = $this->client->sendAsync($request)->wait();
            $cart = json_decode($result->getBody());
            Session::put('ct_cart', json_decode($result->getBody()));
        }


        $product = $this->getProductsById($product_id);
        $cart = $this->getCartsById($cart->id);

        $body = [
            "cart_id" => $cart->id,
            "version" => $cart->version,
            "action" => "addLineItem",
            "productId" => $product_id,
            "variantId" => $product->lastVariantId,
            "quantity" => 1
        ];

        $request = new \GuzzleHttp\Psr7\Request('POST', url('/api') . '/add-to-cart');
        $result = $this->client->sendAsync($request, ['form_params' => $body])->wait();
        $result = json_decode($result->getBody());
        $cart = $this->getCartsById($cart->id);
        Session::put('ct_cart', $cart);

        return $result;
    }


    function preCreateOrder()
    {
        $cart = Session::get('ct_cart');
        $cart = $this->getCartsById($cart->id);
        $order = Session::get('ct_order');
        if ($order == null) {
            $body = [
                "cart_id" => $cart->id,
                "version" => $cart->version,
            ];

            $request = new \GuzzleHttp\Psr7\Request('POST', url('/api') . '/pre-create-order');
            $result = $this->client->sendAsync($request, ['form_params' => $body])->wait();
            $order = json_decode($result->getBody());
            Session::put('ct_order', json_decode($result->getBody()));
        }

        return $order;
    }

    public function loginCustomer($email, $password)
    {
        $body = [
            "email" => $email,
            "password" => $password
        ];

        $request = new \GuzzleHttp\Psr7\Request('POST', url('/api') . '/customer-sign-in');
        $result = $this->client->sendAsync($request, ['form_params' => $body])->wait();
        $result = json_decode($result->getBody());

        if ($result != '400') {
            Session::put('ct_customer', $result);
            return redirect()->route('userDashboard');
        } else {
            return 'Please provide correct username and password!';
        }
    }

    public function superpaymentsSuccess()
    {
        $session_data = Session::all();

        if (!isset($session_data['ct_cart'])) {
            return view('pages.ordersuccess');
        }
        
        $suparpayment = DB::table('suparpayment')
            ->where('externalReference', $session_data['ct_order']->orderNumber)
            ->first();

        $body = [
            "cart_id" => $session_data['ct_cart']->id,
            "order_id" => $session_data['ct_order']->id,
            "customer_id" => $session_data['ct_customer']->id,
            "eventType" => $suparpayment->eventType,
            "transactionId" => $suparpayment->transactionId,
            "transactionReference" => $suparpayment->transactionReference,
            "transactionStatus" => $suparpayment->transactionStatus,
            "transactionAmount" => $suparpayment->transactionAmount,
            "externalReference" => $suparpayment->externalReference,
            "currencyCode" => config('commercetools.currencyCode'),
            "centAmount" => $session_data['ct_suparpay_offer']->calculation->amountAfterSavings,
            "timestamp" => date(DATE_ATOM, strtotime($suparpayment->c_data)),
        ];

        $insertData = [
            "cart_id" => $session_data['ct_cart']->id,
            "order_id" => $session_data['ct_order']->id,
            "customer_id" => $session_data['ct_customer']->id,
            "externalReference" => $suparpayment->externalReference,
            "centAmount" => $session_data['ct_suparpay_offer']->calculation->amountAfterSavings,
            "timestamp" =>  $body['timestamp']
        ];

        DB::table('orders')->insert($insertData);

        $request = new \GuzzleHttp\Psr7\Request('POST', url('/api') . '/create-order');
        $result = $this->client->sendAsync($request, ['form_params' => $body])->wait();
        $result = json_decode($result->getBody());

        Session::put('ct_order', $result);
        Session::put('ct_cart', []);
        Session::put('ct_suparpay_offer', []);
        Session::put('ct_suparpay_offer_id', []);
        Session::put('ct_suparpay_payments', []);
        Session::put('ct_order', []);
        $output['cart_item_count'] = 0;
        return view('pages.ordersuccess',  $output);
    }

    public function superpaymentsRefund()
    {
        $session_data = Session::all();

        if (!$session_data['ct_cart']) {
            return redirect()->route('shop');
        }
        $suparpayment = DB::table('suparpayment')
            ->where('externalReference', $session_data['ct_order']->orderNumber)
            ->first();

        $body = [
            "cart_id" => $session_data['ct_cart']->id,
            "order_id" => $session_data['ct_order']->id,
            "customer_id" => $session_data['ct_customer']->id,
            "eventType" => $suparpayment->eventType,
            "transactionId" => $suparpayment->transactionId,
            "transactionReference" => $suparpayment->transactionReference,
            "transactionStatus" => $suparpayment->transactionStatus,
            "transactionAmount" => $suparpayment->transactionAmount,
            "externalReference" => $suparpayment->externalReference,
            "currencyCode" => config('commercetools.currencyCode'),
            "centAmount" => $session_data['ct_suparpay_offer']->calculation->amountAfterSavings,
            "timestamp" => date(DATE_ATOM, strtotime($suparpayment->c_data)),
        ];

        $insertData = [
            "cart_id" => $session_data['ct_cart']->id,
            "order_id" => $session_data['ct_order']->id,
            "customer_id" => $session_data['ct_customer']->id,
            "externalReference" => $suparpayment->externalReference,
            "centAmount" => $session_data['ct_suparpay_offer']->calculation->amountAfterSavings,
            "timestamp" =>  $body['timestamp']
        ];

        DB::table('orders')->insert($insertData);

        $request = new \GuzzleHttp\Psr7\Request('POST', url('/api') . '/create-order');
        $result = $this->client->sendAsync($request, ['form_params' => $body])->wait();
        $result = json_decode($result->getBody());

        Session::put('ct_order', $result);
        Session::put('ct_cart', []);
        Session::put('ct_suparpay_offer', []);
        Session::put('ct_suparpay_offer_id', []);
        Session::put('ct_suparpay_payments', []);
        Session::put('ct_order', []);
        $output['cart_item_count'] = 0;
        return view('pages.ordersuccess',  $output);
    }
}
