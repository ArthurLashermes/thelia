<?php

namespace Thelia\Api\Test\ResourceTest\Generated;

use Thelia\Api\Test\WebTestCase;
use Faker\Factory;

class OrderProductTaxTest extends WebTestCase
{
    public function test__api__admin_order_product_taxes_post(): void
{
    $client = self::$client;
    $faker = Factory::create();
    $uriTemplate = "/admin/order_product_taxes";
    $body = ["orderProduct" => 1,"title" => $faker->text(maxNbChars: 20),"description" => $faker->text(maxNbChars: 20),"amount" => $faker->randomFloat(2),"promoAmount" => $faker->randomFloat(2)];
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
