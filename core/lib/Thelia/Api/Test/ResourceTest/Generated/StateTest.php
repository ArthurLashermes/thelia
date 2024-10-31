<?php

namespace Thelia\Api\Test\ResourceTest\Generated;

use Thelia\Api\Test\WebTestCase;
use Faker\Factory;

class StateTest extends WebTestCase
{
    public function test__api__admin_states_post(): void
{
    $client = self::$client;
    $faker = Factory::create();
    $uriTemplate = "/admin/states";
    $body = ['i18ns' => ['fr_FR' => [],'en_US' => [],'es_ES' => [],'it_IT' => []],"visible" => $faker->boolean(),"isocode" => $faker->text(maxNbChars: 20),"country" => 1];
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
