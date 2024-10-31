<?php

namespace Thelia\Api\Test\ResourceTest\Generated;

use Thelia\Api\Test\WebTestCase;
use Faker\Factory;

class AddressTest extends WebTestCase
{
    public function test__api__admin_addresses_post(): void
{
    $client = self::$client;
    $faker = Factory::create();
    $uriTemplate = "/admin/addresses";
    $body = ["label" => $faker->text(maxNbChars: 20),"firstname" => $faker->text(maxNbChars: 20),"lastname" => $faker->text(maxNbChars: 20),"address1" => $faker->text(maxNbChars: 20),"address2" => $faker->text(maxNbChars: 20),"address3" => $faker->text(maxNbChars: 20),"zipcode" => $faker->text(maxNbChars: 20),"company" => $faker->text(maxNbChars: 20),"cellphone" => $faker->text(maxNbChars: 20),"phone" => $faker->text(maxNbChars: 20),"city" => $faker->text(maxNbChars: 20),"isDefault" => $faker->boolean(),"country" => 1,"state" => 1,"customer" => 1,"customerTitle" => 1];
    $this->login($client, $uriTemplate);
    $client->request(
        method: 'POST',
        uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
        content: json_encode($body, JSON_THROW_ON_ERROR)
    );

    $this->assertEquals(201,  $client->getResponse()->getStatusCode());
}

public function test__api__front_account_addresses_post(): void
{
    $client = self::$client;
    $faker = Factory::create();
    $uriTemplate = "/front/account/addresses";
    $body = ["label" => $faker->text(maxNbChars: 20),"firstname" => $faker->text(maxNbChars: 20),"lastname" => $faker->text(maxNbChars: 20),"address1" => $faker->text(maxNbChars: 20),"address2" => $faker->text(maxNbChars: 20),"address3" => $faker->text(maxNbChars: 20),"zipcode" => $faker->text(maxNbChars: 20),"company" => $faker->text(maxNbChars: 20),"cellphone" => $faker->text(maxNbChars: 20),"phone" => $faker->text(maxNbChars: 20),"city" => $faker->text(maxNbChars: 20),"isDefault" => $faker->boolean(),"country" => 1,"state" => 1,"customer" => 1,"customerTitle" => 1];
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
