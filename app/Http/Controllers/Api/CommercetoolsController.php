<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Commercetools\Api\Client\ApiRequestBuilder;
use Commercetools\Api\Client\ClientCredentialsConfig;
use Commercetools\Api\Client\Config;
use Commercetools\Api\Client\Resource\ResourceByProjectKeyCartsByID;
use Commercetools\Api\Client\Resource\ResourceByProjectKeyProductsByID;
use Commercetools\Api\Models\Cart\CartDraftBuilder;
use Commercetools\Api\Models\Cart\CartUpdate;
use Commercetools\Api\Models\Cart\CartUpdateActionBuilder;
use Commercetools\Api\Models\Cart\CartUpdateActionCollection;
use Commercetools\Api\Models\Cart\CartUpdateBuilder;
use Commercetools\Api\Models\Cart\LineItemDraftBuilder;
use Commercetools\Client\ClientCredentials;
use Commercetools\Client\ClientFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Promise;

class CommercetoolsController extends Controller
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
        $newCartDetails = (new CartDraftBuilder("EUR"))->withCurrency('EUR')->build();

        $query = $this->apiRoot
            ->carts()
            ->post($newCartDetails)
            ->execute();

        return response()->json($query);
    }

    /**
     * body: {
     *     version: version,
     *     actions: [
     *       {
     *         action: "addLineItem",
     *         productId: productId,
     *         variantId: variantId
     *       }
     *     ],
     * }
     */
    public function cartAddLineItem(Request $request)
    {
        $data = $request->all();
        $params = (new CartUpdateActionCollection([
            "action" => "addLineItem",
            "productId" => "4df38248-fb6e-4133-9f21-04ca5ce61bb7",
            "variantId" => 1,
            "quantity" => 1
        ]));
        dump($params);
        $params = (new CartUpdateActionBuilder())
            ->build();
        dump($params);
        $query = $this->apiRoot
            ->carts()
            ->withId('ecac2759-9a17-4be0-a432-1788ce832a03')

            ->post();
        // ->execute();
        dd($query);




        return response()->json($query);
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