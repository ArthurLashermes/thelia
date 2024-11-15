<?php

namespace Thelia\Api\Test\ResourceTest\Generated;

use Thelia\Api\Test\WebTestCase;
use Faker\Factory;

class CustomerTest extends WebTestCase
{
    public function test__api__admin_customers_post(): void
{
    $client = self::$client;
    $faker = Factory::create();
    $uriTemplate = "/admin/customers";
    $body = ["addresses" => [["label" => $faker->text(maxNbChars: 20),"firstname" => $faker->text(maxNbChars: 20),"lastname" => $faker->text(maxNbChars: 20),"address1" => $faker->text(maxNbChars: 20),"address2" => $faker->text(maxNbChars: 20),"address3" => $faker->text(maxNbChars: 20),"zipcode" => $faker->text(maxNbChars: 20),"company" => $faker->text(maxNbChars: 20),"cellphone" => $faker->text(maxNbChars: 20),"phone" => $faker->text(maxNbChars: 20),"city" => $faker->text(maxNbChars: 20),"isDefault" => $faker->boolean(),"country" => "1","state" => "1","customerTitle" => "1"],],"customerTitle" => "1","lang" => "1","firstname" => $faker->text(maxNbChars: 20),"lastname" => $faker->text(maxNbChars: 20),"email" => $faker->email(),"password" => $faker->text(maxNbChars: 20),"reseller" => $faker->boolean(),"discount" => $faker->randomFloat(2)];
    $this->login($client, $uriTemplate);
    $client->request(
        method: 'POST',
        uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
        content: json_encode($body, JSON_THROW_ON_ERROR)
    );

    $this->assertEquals(201,  $client->getResponse()->getStatusCode());
}

public function test__api__admin_customers_get_collection(): void
{
    $client = self::$client;
    $uriTemplate = "/admin/customers";
    $this->login($client, $uriTemplate);
    $client->request(
        method: 'GET',
        uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
    );

    $this->assertEquals(200,  $client->getResponse()->getStatusCode());
}

//Entry point
}
