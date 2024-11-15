<?php

namespace Thelia\Api\Test\ResourceTest\Generated;

use Thelia\Api\Test\WebTestCase;
use Faker\Factory;

class CartItemTest extends WebTestCase
{
    public function test__api__admin_cart_items_get_collection(): void
{
    $client = self::$client;
    $uriTemplate = "/admin/cart_items";
    $this->login($client, $uriTemplate);
    $client->request(
        method: 'GET',
        uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
    );

    $this->assertEquals(200,  $client->getResponse()->getStatusCode());
}

public function test__api__front_cart_items_post(): void
{
    $client = self::$client;
    $faker = Factory::create();
    $uriTemplate = "/front/cart_items";
    $body = ["quantity" => $faker->numberBetween(0,10),"productSaleElements" => "1"];
    $this->login($client, $uriTemplate);
    $client->request(
        method: 'POST',
        uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
        content: json_encode($body, JSON_THROW_ON_ERROR)
    );

    $this->assertEquals(201,  $client->getResponse()->getStatusCode());
}

public function test__api__front_cart_items_get_collection(): void
{
    $client = self::$client;
    $uriTemplate = "/front/cart_items";
    $this->login($client, $uriTemplate);
    $client->request(
        method: 'GET',
        uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
    );

    $this->assertEquals(200,  $client->getResponse()->getStatusCode());
}

//Entry point
}
