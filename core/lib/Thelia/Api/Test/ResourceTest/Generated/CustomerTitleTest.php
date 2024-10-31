<?php

namespace Thelia\Api\Test\ResourceTest\Generated;

use Thelia\Api\Test\WebTestCase;
use Faker\Factory;

class CustomerTitleTest extends WebTestCase
{
    public function test__api__admin_customer_titles_post(): void
{
    $client = self::$client;
    $faker = Factory::create();
    $uriTemplate = "/admin/customer_titles";
    $body = ['i18ns' => ['fr_FR' => ['short' => $faker->text(maxNbChars: 20),'long' => $faker->text(maxNbChars: 20),],'en_US' => ['short' => $faker->text(maxNbChars: 20),'long' => $faker->text(maxNbChars: 20),],'es_ES' => ['short' => $faker->text(maxNbChars: 20),'long' => $faker->text(maxNbChars: 20),],'it_IT' => ['short' => $faker->text(maxNbChars: 20),'long' => $faker->text(maxNbChars: 20),]],"position" => $faker->text(maxNbChars: 20),"byDefault" => $faker->numberBetween(0,10)];
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
