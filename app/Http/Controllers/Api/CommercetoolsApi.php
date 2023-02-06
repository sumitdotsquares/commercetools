<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Commercetools\Api\Client\ApiRequestBuilder;
use Commercetools\Api\Client\ClientCredentialsConfig;
use Commercetools\Api\Client\Config;
use Commercetools\Api\Models\Cart\CartDraftBuilder;
use Commercetools\Client\ClientCredentials;
use Commercetools\Client\ClientFactory;
use Illuminate\Http\Request;


class CommercetoolsApi extends Controller
{
    public  $builder;
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
        $this->builder = new ApiRequestBuilder($this->client);

        // Include the Project key with the returned Client
        $this->apiRoot = $this->builder->withProjectKey($this->projectKey);
        // dump($this->client);
    }

    public function getTest()
    {
        return response()->json(['Hello']);
    }

    public function getProducts()
    {
        return $this->callCT('products');
    }

    public function getCarts()
    {
        return $this->callCT('carts');
    }

    public function getCustomerByEmail(Request $request)
    {
        $customerEmail = $request->input('customerEmail');

        $customerToFind = $this->apiRoot
            ->customers()
            ->get()
            ->withWhere('email="' . $customerEmail . '"')
            ->execute();

        if ($customerToFind->getCount() == 0) {
            $data = 'This email address has not been registered.';
        } else {
            $data = $customerToFind->getResults()[0];
        }

        return response()->json($data);
    }

    public function getCartsId($cartId = null)
    {
        $query = $this->apiRoot
            ->carts()
            ->withId($cartId)
            ->get()
            ->execute();

        return response()->json($query);
    }

    public function createCart()
    {
        dd('sdfsd');
        $body = '{
            "currency": "EUR",
            "shipping": [],
            "customShipping": []
          }';

        return $this->callCT('carts', 'POST', $body);
    }

    public function itemAddToCart(Request $request)
    {
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
        return $this->callCT('carts/ecac2759-9a17-4be0-a432-1788ce832a03', 'POST', $body);
    }


    /**
     * Using GuzzleHttp
     *
     * @return response()
     */
    public function callCT($uri = null, $method = 'GET', $body = null)
    {
        if ($uri == null) {
            return;
        }

        $apiURL = 'https://api.' . config('commercetools.region') . '.commercetools.com/' . $this->projectKey . '/' . $uri;

        $response = $this->client->request($method, $apiURL);
        $statusCode = $response->getStatusCode();
        if ($statusCode == 200) {
            return json_decode($response->getBody(), true);
        }
    }
}
