<?php

namespace Thelia\Api\Test\Service;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;

readonly class GenerateFunctionTestService
{
    public function __construct(
        private FakeBodyService $fakeBodyService,
    )
    {
    }

    public function createPostTest(Post $operation, string $destinationClassPath, string $className): void
    {
        $content = file_get_contents($destinationClassPath);
        $postTestTemplate = <<<EOD
        public function FUNCTION_NAME(): void
        {
            \$client = self::\$client;
            \$faker = Factory::create();
            \$uriTemplate = URI_VALUE
            \$body = BODY_VALUE
            \$this->login(\$client, \$uriTemplate);
            \$client->request(
                method: 'POST',
                uri: sprintf('%s%s', \$_ENV['API_BASE_URL'], \$uriTemplate),
                content: json_encode(\$body, JSON_THROW_ON_ERROR)
            );

            \$this->assertEquals(201,  \$client->getResponse()->getStatusCode());
        }

        //Entry point
        EOD;
        $uriTemplateValue = '"' . $operation->getUriTemplate() . '";';
        $functionName = str_replace('/', '_', "test_" . $operation->getName());
        $postTestTemplate = str_replace('URI_VALUE', $uriTemplateValue, $postTestTemplate);
        $postTestTemplate = str_replace('FUNCTION_NAME', $functionName, $postTestTemplate);
        $body = $this->fakeBodyService->getFakeBody($operation, $className);
        if (!$body) {
            return;
        }
        $postTestTemplate = str_replace('BODY_VALUE', $body, $postTestTemplate);
        $content = str_replace('//Entry point', $postTestTemplate, $content);
        file_put_contents($destinationClassPath, $content);
    }


    public function createGetCollectionTest(GetCollection $operation, string $destinationClassPath): void
    {
        $content = file_get_contents($destinationClassPath);
        $getCollectionTestTemplate = <<<EOD
        public function FUNCTION_NAME(): void
        {
            \$client = self::\$client;
            \$uriTemplate = URI_VALUE
            \$this->login(\$client, \$uriTemplate);
            \$client->request(
                method: 'GET',
                uri: sprintf('%s%s', \$_ENV['API_BASE_URL'], \$uriTemplate),
            );

            \$this->assertEquals(200,  \$client->getResponse()->getStatusCode());
        }

        //Entry point
        EOD;
        $uriTemplateValue = '"' . $operation->getUriTemplate() . '";';
        $functionName = str_replace('/', '_', "test_" . $operation->getName());
        $getCollectionTestTemplate = str_replace('URI_VALUE', $uriTemplateValue, $getCollectionTestTemplate);
        $getCollectionTestTemplate = str_replace('FUNCTION_NAME', $functionName, $getCollectionTestTemplate);
        $content = str_replace('//Entry point', $getCollectionTestTemplate, $content);
        file_put_contents($destinationClassPath, $content);
    }

    public function createGetTest(Get $operation, string $destinationClassPath): void
    {
        $content = file_get_contents($destinationClassPath);
        $getTestTemplate = <<<EOD
        public function FUNCTION_NAME(): void
        {
            \$client = self::\$client;
            \$uriTemplate = URI_VALUE
            \$this->login(\$client, \$uriTemplate);
            \$client->request(
                method: 'GET',
                uri: sprintf('%s%s', \$_ENV['API_BASE_URL'], \$uriTemplate),
            );

            \$this->assertEquals(200,  \$client->getResponse()->getStatusCode());
        }

        //Entry point
        EOD;
        $uriTemplateValue = '"' . $operation->getUriTemplate() . '";';
        $uriTemplateValue = str_replace('{id}', '1', $uriTemplateValue);
        $name = $operation->getName();
        $name = str_replace(array('{', '}'), '', $name);
        $functionName = str_replace('/', '_', "test_" . $name);
        $getTestTemplate = str_replace('URI_VALUE', $uriTemplateValue, $getTestTemplate);
        $getTestTemplate = str_replace('FUNCTION_NAME', $functionName, $getTestTemplate);
        $content = str_replace('//Entry point', $getTestTemplate, $content);
        file_put_contents($destinationClassPath, $content);
    }
}
