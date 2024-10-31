<?php

namespace Thelia\Api\Test\ResourceTest\Generated;

use Thelia\Api\Test\WebTestCase;
use Faker\Factory;

class LangTest extends WebTestCase
{
    public function test__api__admin_languages_post(): void
{
    $client = self::$client;
    $faker = Factory::create();
    $uriTemplate = "/admin/languages";
    $body = ["id" => $faker->numberBetween(0,10),"title" => $faker->text(maxNbChars: 20),"code" => $faker->text(maxNbChars: 20),"locale" => $faker->text(maxNbChars: 20),"url" => $faker->text(maxNbChars: 20),"dateFormat" => $faker->text(maxNbChars: 20),"timeFormat" => $faker->text(maxNbChars: 20),"datetimeFormat" => $faker->text(maxNbChars: 20),"decimalSeparator" => $faker->text(maxNbChars: 20),"thousandsSeparator" => $faker->text(maxNbChars: 20),"active" => $faker->boolean(),"visible" => $faker->boolean(),"decimals" => $faker->text(maxNbChars: 20),"byDefault" => $faker->boolean(),"position" => $faker->numberBetween(0,10),"createdAt" => $faker->dateTimeThisYear()->format('Y-m-d\TH:i:s.v\Z'),"updatedAt" => $faker->dateTimeThisYear()->format('Y-m-d\TH:i:s.v\Z')];
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
