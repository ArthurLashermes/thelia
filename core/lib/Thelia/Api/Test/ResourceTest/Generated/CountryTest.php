<?php

namespace Thelia\Api\Test\ResourceTest\Generated;

use Thelia\Api\Test\WebTestCase;
use Faker\Factory;

class CountryTest extends WebTestCase
{
    public function test__api__admin_countries_post(): void
{
    $client = self::$client;
    $faker = Factory::create();
    $uriTemplate = "/admin/countries";
    $body = ['i18ns' => ['fr_FR' => [],'en_US' => [],'es_ES' => [],'it_IT' => []],"visible" => $faker->boolean(),"isocode" => $faker->text(maxNbChars: 20),"isoalpha2" => $faker->text(maxNbChars: 20),"isoalpha3" => $faker->text(maxNbChars: 20),"hasStates" => $faker->boolean(),"needZipCode" => $faker->boolean(),"zipCodeFormat" => $faker->text(maxNbChars: 20),"byDefault" => $faker->boolean(),"shopCountry" => $faker->boolean()];
    $this->login($client, $uriTemplate);
    $client->request(
        method: 'POST',
        uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
        content: json_encode($body, JSON_THROW_ON_ERROR)
    );

    $this->assertEquals(201,  $client->getResponse()->getStatusCode());
}

public function test__api__admin_countries_get_collection(): void
{
    $client = self::$client;
    $uriTemplate = "/admin/countries";
    $this->login($client, $uriTemplate);
    $client->request(
        method: 'GET',
        uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
    );

    $this->assertEquals(200,  $client->getResponse()->getStatusCode());
}

//Entry point
}
