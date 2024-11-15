<?php

namespace Thelia\Api\Test\ResourceTest\Generated;

use Thelia\Api\Test\WebTestCase;
use Faker\Factory;

class AttributeTest extends WebTestCase
{
    public function test__api__admin_attributes_post(): void
{
    $client = self::$client;
    $faker = Factory::create();
    $uriTemplate = "/admin/attributes";
    $body = ['i18ns' => ['fr_FR' => ['title' => $faker->text(maxNbChars: 20),'description' => $faker->text(maxNbChars: 20),'chapo' => $faker->text(maxNbChars: 20),'postscriptum' => $faker->text(maxNbChars: 20),],'en_US' => ['title' => $faker->text(maxNbChars: 20),'description' => $faker->text(maxNbChars: 20),'chapo' => $faker->text(maxNbChars: 20),'postscriptum' => $faker->text(maxNbChars: 20),],'es_ES' => ['title' => $faker->text(maxNbChars: 20),'description' => $faker->text(maxNbChars: 20),'chapo' => $faker->text(maxNbChars: 20),'postscriptum' => $faker->text(maxNbChars: 20),],'it_IT' => ['title' => $faker->text(maxNbChars: 20),'description' => $faker->text(maxNbChars: 20),'chapo' => $faker->text(maxNbChars: 20),'postscriptum' => $faker->text(maxNbChars: 20),]],"position" => $faker->numberBetween(0,10)];
    $this->login($client, $uriTemplate);
    $client->request(
        method: 'POST',
        uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
        content: json_encode($body, JSON_THROW_ON_ERROR)
    );

    $this->assertEquals(201,  $client->getResponse()->getStatusCode());
}

public function test__api__admin_attributes_get_collection(): void
{
    $client = self::$client;
    $uriTemplate = "/admin/attributes";
    $this->login($client, $uriTemplate);
    $client->request(
        method: 'GET',
        uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
    );

    $this->assertEquals(200,  $client->getResponse()->getStatusCode());
}

public function test__api__front_attributes_get_collection(): void
{
    $client = self::$client;
    $uriTemplate = "/front/attributes";
    $this->login($client, $uriTemplate);
    $client->request(
        method: 'GET',
        uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
    );

    $this->assertEquals(200,  $client->getResponse()->getStatusCode());
}

//Entry point
}
