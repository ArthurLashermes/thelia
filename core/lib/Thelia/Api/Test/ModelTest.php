<?php

namespace Thelia\Api\Test;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use http\Exception\RuntimeException;

class ModelTest extends WebTestCase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function testPost(Post $operation, array $body): void
    {
        $client = self::createClient();
        $uriTemplate = $operation->getUriTemplate();
        $this->login($client,$uriTemplate);
        $method = $operation->getMethod();
        $client->request(
            method: $method,
            uri: sprintf('%s%s', $_ENV['API_BASE_URL'], $uriTemplate),
            content : json_encode($body, JSON_THROW_ON_ERROR)
        );

        $this->assertEquals(201,  $client->getResponse()->getStatusCode());
    }

    public function testGet(Get $operation,int $id): void
    {
        $client = self::createClient();
        $uriTemplate = $operation->getUriTemplate();
        $this->login($client,$uriTemplate);

        $method = $operation->getMethod();
        $client->request(
            method: $method,
            uri: sprintf('%s%s', $_ENV('API_BASE_URL'), $uriTemplate),
        );

        $this->assertEquals(200,  $client->getResponse()->getStatusCode());
    }


    public function testGetCollection(GetCollection $operation): void
    {
        $client = self::createClient();
        $uriTemplate = $operation->getUriTemplate();
        $this->login($client,$uriTemplate);

        $method = $operation->getMethod();
        $client->request(
            method: $method,
            uri: sprintf('%s%s', $_ENV('API_BASE_URL'), $uriTemplate),
        );
        $this->assertEquals(200,  $client->getResponse()->getStatusCode());
    }


    public function testDelete(Delete $operation,$id): void
    {
        $client = self::createClient();

        $uriTemplate = $operation->getUriTemplate();
        $this->login($client,$uriTemplate);

        $method = $operation->getMethod();
        $client->request(
            method: $method,
            uri: sprintf('%s%s', $_ENV('API_BASE_URL'), $uriTemplate),
        );

        $this->assertEquals(200,  $client->getResponse()->getStatusCode());
    }

    private function login($client, string $uri): void
    {
        if (str_contains($uri, '/admin')) {
            $this->loginAdmin($client);
            return;
        }
        if(str_contains($uri, '/front')) {
            $this->loginCustomer($client);
            return;
        }
        throw new RuntimeException(sprintf('Cannot log for the route %s', $uri));
    }
}
