<?php

namespace Thelia\Api\Test\ResourceTest\Generated;

use Thelia\Api\Test\WebTestCase;
use Faker\Factory;

class OrderProductTest extends WebTestCase
{
    public function test__api__admin_order_products_post(): void
{
    $client = self::$client;
    $faker = Factory::create();
    $uriTemplate = "/admin/order_products";
    $body = ["productRef" => $faker->text(maxNbChars: 20),"productSaleElementsRef" => $faker->text(maxNbChars: 20),"productSaleElementsId" => $faker->numberBetween(0,10),"title" => $faker->text(maxNbChars: 20),"chapo" => $faker->text(maxNbChars: 20),"description" => $faker->text(maxNbChars: 20),"postscriptum" => $faker->text(maxNbChars: 20),"quantity" => $faker->numberBetween(0,10),"price" => $faker->randomFloat(2),"promoPrice" => $faker->randomFloat(2),"wasNew" => $faker->boolean(),"wasInPromo" => $faker->boolean(),"weight" => $faker->text(maxNbChars: 20),"eanCode" => $faker->text(maxNbChars: 20),"taxRuleTitle" => $faker->text(maxNbChars: 20),"taxRuleDescription" => $faker->text(maxNbChars: 20),"parent" => $faker->numberBetween(0,10),"virtual" => $faker->boolean(),"virtualDocument" => $faker->boolean()];
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
