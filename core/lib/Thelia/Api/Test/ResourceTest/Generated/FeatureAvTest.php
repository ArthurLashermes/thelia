<?php

namespace Thelia\Api\Test\ResourceTest\Generated;

use Thelia\Api\Test\WebTestCase;
use Faker\Factory;

class FeatureAvTest extends WebTestCase
{
    public function test__api__admin_feature_avs_post(): void
{
    $client = self::$client;
    $faker = Factory::create();
    $uriTemplate = "/admin/feature_avs";
    $body = ["feature" => ["id" => 1],'i18ns' => ['fr_FR' => ['title' => $faker->text(maxNbChars: 20),'description' => $faker->text(maxNbChars: 20),'chapo' => $faker->text(maxNbChars: 20),'postscriptum' => $faker->text(maxNbChars: 20),],'en_US' => ['title' => $faker->text(maxNbChars: 20),'description' => $faker->text(maxNbChars: 20),'chapo' => $faker->text(maxNbChars: 20),'postscriptum' => $faker->text(maxNbChars: 20),],'es_ES' => ['title' => $faker->text(maxNbChars: 20),'description' => $faker->text(maxNbChars: 20),'chapo' => $faker->text(maxNbChars: 20),'postscriptum' => $faker->text(maxNbChars: 20),],'it_IT' => ['title' => $faker->text(maxNbChars: 20),'description' => $faker->text(maxNbChars: 20),'chapo' => $faker->text(maxNbChars: 20),'postscriptum' => $faker->text(maxNbChars: 20),]],"position" => $faker->numberBetween(0,10)];
    $this->login($client, $uriTemplate);
    $client->request(
        method: 'POST',
        uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
        content: json_encode($body, JSON_THROW_ON_ERROR)
    );

    $this->assertEquals(201,  $client->getResponse()->getStatusCode());
}

public function test__api__admin_feature_avs_get_collection(): void
{
    $client = self::$client;
    $uriTemplate = "/admin/feature_avs";
    $this->login($client, $uriTemplate);
    $client->request(
        method: 'GET',
        uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
    );

    $this->assertEquals(200,  $client->getResponse()->getStatusCode());
}

public function test__api__front_feature_avs_get_collection(): void
{
    $client = self::$client;
    $uriTemplate = "/front/feature_avs";
    $this->login($client, $uriTemplate);
    $client->request(
        method: 'GET',
        uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
    );

    $this->assertEquals(200,  $client->getResponse()->getStatusCode());
}

//Entry point
}
