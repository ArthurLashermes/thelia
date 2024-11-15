<?php

namespace Thelia\Api\Test\ResourceTest\Generated;

use Thelia\Api\Test\WebTestCase;
use Faker\Factory;

class ProductDocumentTest extends WebTestCase
{
    public function test__api__admin_product_documents_get_collection(): void
{
    $client = self::$client;
    $uriTemplate = "/admin/product_documents";
    $this->login($client, $uriTemplate);
    $client->request(
        method: 'GET',
        uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
    );

    $this->assertEquals(200,  $client->getResponse()->getStatusCode());
}

public function test__api__front_product_documents_get_collection(): void
{
    $client = self::$client;
    $uriTemplate = "/front/product_documents";
    $this->login($client, $uriTemplate);
    $client->request(
        method: 'GET',
        uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
    );

    $this->assertEquals(200,  $client->getResponse()->getStatusCode());
}

//Entry point
}
