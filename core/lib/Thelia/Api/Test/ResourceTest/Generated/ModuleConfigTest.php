<?php

namespace Thelia\Api\Test\ResourceTest\Generated;

use Thelia\Api\Test\WebTestCase;
use Faker\Factory;

class ModuleConfigTest extends WebTestCase
{
    public function test__api__admin_module_configs_post(): void
{
    $client = self::$client;
    $faker = Factory::create();
    $uriTemplate = "/admin/module_configs";
    $body = ["module" => ["id" => 1],'i18ns' => ['fr_FR' => ['value' => $faker->text(maxNbChars: 20),],'en_US' => ['value' => $faker->text(maxNbChars: 20),],'es_ES' => ['value' => $faker->text(maxNbChars: 20),],'it_IT' => ['value' => $faker->text(maxNbChars: 20),]],"name" => $faker->text(maxNbChars: 20)];
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
