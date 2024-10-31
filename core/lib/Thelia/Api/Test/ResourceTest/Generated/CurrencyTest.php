<?php

namespace Thelia\Api\Test\ResourceTest\Generated;

use Thelia\Api\Test\WebTestCase;
use Faker\Factory;

class CurrencyTest extends WebTestCase
{
    public function test__api__admin_currencies_post(): void
{
    $client = self::$client;
    $faker = Factory::create();
    $uriTemplate = "/admin/currencies";
    $body = ['i18ns' => ['fr_FR' => [],'en_US' => [],'es_ES' => [],'it_IT' => []],"code" => $faker->text(maxNbChars: 20),"symbol" => $faker->text(maxNbChars: 20),"format" => $faker->text(maxNbChars: 20),"rate" => $faker->randomFloat(2)];
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
