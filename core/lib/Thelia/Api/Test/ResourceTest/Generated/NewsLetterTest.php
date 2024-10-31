<?php

namespace Thelia\Api\Test\ResourceTest\Generated;

use Thelia\Api\Test\WebTestCase;
use Faker\Factory;

class NewsLetterTest extends WebTestCase
{
    public function test__api__admin_news_letters_post(): void
{
    $client = self::$client;
    $faker = Factory::create();
    $uriTemplate = "/admin/news_letters";
    $body = ["email" => $faker->email(),"firstname" => $faker->text(maxNbChars: 20),"lastname" => $faker->text(maxNbChars: 20),"locale" => $faker->text(maxNbChars: 20),"unsubscribed" => $faker->boolean()];
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
