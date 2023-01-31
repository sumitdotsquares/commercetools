<?php

namespace App\Http\Controllers;

use Commercetools\Api\Client\ApiRequestBuilder;
use Commercetools\Api\Client\ClientCredentialsConfig;
use Commercetools\Api\Client\Config;
use Commercetools\Api\Models\Product\Product;
use Commercetools\Client\ClientCredentials;
use Commercetools\Client\ClientFactory;
use GuzzleHttp\ClientInterface;

class CommercetoolsController extends Controller
{
    public  $apiRoot;
    public function __construct()
    {
        // $commercetools_configuration = \Config::get('commercetools');

        /** @var string $clientId */
        /** @var string $clientSecret */
        $authConfig = new ClientCredentialsConfig(
            new ClientCredentials('rjFkOncgHwI2oinpL3hIfByE', 'o_92qm52CQJB89Ww64wfCBNtrkY76mza'),
            [],
            'https://auth.us-central1.gcp.commercetools.com/oauth/token'
        );

        $client = ClientFactory::of()->createGuzzleClient(
            new Config([], 'https://api.us-central1.gcp.commercetools.com'),
            $authConfig
        );

        /** @var ClientInterface $client */
        $builder = new ApiRequestBuilder($client);

        // Include the Project key with the returned Client
        $this->apiRoot = $builder->withProjectKey('super-payments');
    }

    public function getProducts()
    {
        $findProductByKey = $this->apiRoot->products();
  

        // Output the Project name
        return $findProductByKey();
    }
}
