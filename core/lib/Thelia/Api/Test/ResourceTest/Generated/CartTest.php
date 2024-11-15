<?php

namespace Thelia\Api\Test\ResourceTest\Generated;

use Thelia\Api\Test\WebTestCase;
use Faker\Factory;

class CartTest extends WebTestCase
{
    public function test__api__admin_carts_post(): void
{
    $client = self::$client;
    $faker = Factory::create();
    $uriTemplate = "/admin/carts";
    $body = ["token" => $faker->text(maxNbChars: 20),"customer" => "1","discount" => $faker->randomFloat(2)];
    $this->login($client, $uriTemplate);
    $client->request(
        method: 'POST',
        uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
        content: json_encode($body, JSON_THROW_ON_ERROR)
    );

    $this->assertEquals(201,  $client->getResponse()->getStatusCode());
}

public function test__api__admin_carts_get_collection(): void
{
    $client = self::$client;
    $uriTemplate = "/admin/carts";
    $this->login($client, $uriTemplate);
    $client->request(
        method: 'GET',
        uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
    );

    $this->assertEquals(200,  $client->getResponse()->getStatusCode());
}

public function test__api__front_carts_post(): void
{
    $client = self::$client;
    $faker = Factory::create();
    $uriTemplate = "/front/carts";
    $body = ["token" => $faker->text(maxNbChars: 20),"customer" => "1","discount" => $faker->randomFloat(2)];
    $this->login($client, $uriTemplate);
    $client->request(
        method: 'POST',
        uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
        content: json_encode($body, JSON_THROW_ON_ERROR)
    );

    $this->assertEquals(201,  $client->getResponse()->getStatusCode());
}

//Entry point
}
