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

class CommercetoolsController extends Controller
{
    public  $url;
    public  $client;
    public function __construct()
    {
        $this->url = url('/api');
        $this->client = new \GuzzleHttp\Client(['base_uri' => url('/api')]);
    }

    public function getProducts()
    {
        $products = [];
        $result = $this->client->get('https://commercetools.24livehost.com/api/products');

        foreach (json_decode($result->getBody())->results as $key => $value) {
            $products[] = $this->getProductByKey($value);
        }

        return $products;
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
        $cart_id = Session::get('cart_id');
        if ($cart_id == null) {
            $cart_id = $this->createCart();
        }
        $this->addItem($cart_id, $product_id);
    }

    public function addItem($cart_id, $product_id)
    {
        if ($product_id == null) {
            return;
        }
        $product = (new ResourceByProjectKeyProductsByID(["projectKey" => $this->projectKey, "ID" =>   $product_id], $this->client))->get()->execute();
        $procuct_version = $product->getVersion();
        $procuct_variant = $product->getMasterData()->getCurrent()->getMasterVariant()->getID();


        dump($product);
        dump($cart_id);
        dump($product_id);
        dd("End");
    }

    public function createCart()
    {
        $newCartDetails = (new CartDraftBuilder("EUR"))->withCurrency('EUR')->build();
        $result = $this->client->get('https://commercetools.24livehost.com/api/products');

        
        $cart = $this->apiRoot->carts()->post($newCartDetails)->execute();
        Session::put('cart_id', $cart->getId());
        return $cart->getId();
    }

    public function checkCart($cart_id = null)
    {
        if ($cart_id == null) {
            return false;
        }
        $cart = new ResourceByProjectKeyCartsByID(["projectKey" => $this->projectKey, "ID" =>   $cart_id], $this->client);
        return $cart->get()->execute();
    }
}
