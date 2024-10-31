<?php

namespace Thelia\Api\Test\ResourceTest\Generated;

use Thelia\Api\Test\WebTestCase;
use Faker\Factory;

class TemplateTest extends WebTestCase
{
    public function test__api__admin_templates_post(): void
{
    $client = self::$client;
    $faker = Factory::create();
    $uriTemplate = "/admin/templates";
    $body = ['i18ns' => ['fr_FR' => ['name' => $faker->text(maxNbChars: 20),],'en_US' => ['name' => $faker->text(maxNbChars: 20),],'es_ES' => ['name' => $faker->text(maxNbChars: 20),],'it_IT' => ['name' => $faker->text(maxNbChars: 20),]],];
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
