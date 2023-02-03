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
use Illuminate\Support\Facades\Session;

class CommercetoolsController extends Controller
{
    public  $apiRoot;
    public  $client;
    public $projectKey;
    public function __construct()
    {
        $this->projectKey = config('commercetools.projectKey');
        /** @var string $clientId */
        /** @var string $clientSecret */
        $authConfig = new ClientCredentialsConfig(
            new ClientCredentials(config('commercetools.clientID'), config('commercetools.secret')),
            [],
            'https://auth.' . config('commercetools.region') . '.commercetools.com/oauth/token'
        );

        $this->client = ClientFactory::of()->createGuzzleClient(
            new Config([], 'https://api.' . config('commercetools.region') . '.commercetools.com'),
            $authConfig
        );

        /** @var ClientInterface $client */
        $builder = new ApiRequestBuilder($this->client);

        // Include the Project key with the returned Client
        $this->apiRoot = $builder->withProjectKey($this->projectKey);
    }

    public function getProducts()
    {
        $products = [];
        // Query a Product by its Key
        $findProductByKey = $this->apiRoot
            ->products()
            ->get()
            ->execute()->getResults();

        foreach ($findProductByKey as $key => $value) {
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

        $procuct['id'] = $findProductByKey->getId();
        $procuct['name'] = $findProductByKey->getMasterData()->getCurrent()->getName()[config('commercetools.lang')];
        $procuct['image'] = $findProductByKey->getMasterData()->getCurrent()->getMasterVariant()->getImages()[0]->getUrl();
        $procuct['price'] = $findProductByKey->getMasterData()->getCurrent()->getMasterVariant()->getprices()[0]->getValue();
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
        if($product_id == null){
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
