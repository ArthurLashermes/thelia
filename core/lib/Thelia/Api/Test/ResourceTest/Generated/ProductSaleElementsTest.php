<?php

namespace Thelia\Api\Test\ResourceTest\Generated;

use Thelia\Api\Test\WebTestCase;
use Faker\Factory;

class ProductSaleElementsTest extends WebTestCase
{
    public function test__api__admin_product_sale_elements_post(): void
{
    $client = self::$client;
    $faker = Factory::create();
    $uriTemplate = "/admin/product_sale_elements";
    $body = ["product" => ["id" => 1],"productPrices" => [["currency" => ["id" => 1],"price" => $faker->randomFloat(2),"promoPrice" => $faker->randomFloat(2)],],"currency" => ["id" => 1],"attributeCombinations" => [["attribute" => 1,"attributeAv" => 1],],"ref" => $faker->text(maxNbChars: 20),"quantity" => $faker->numberBetween(0,10),"promo" => $faker->boolean(),"newness" => $faker->boolean(),"weight" => $faker->randomFloat(2),"isDefault" => $faker->boolean(),"eanCode" => $faker->text(maxNbChars: 20)];
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
