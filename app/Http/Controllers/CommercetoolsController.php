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
        $request = new \GuzzleHttp\Psr7\Request('POST', url('/api') . '/customer');
        $result = $this->client->sendAsync($request, ['form_params' => ['email' => $email]])->wait();
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
        if ($cart == null) {
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
}
