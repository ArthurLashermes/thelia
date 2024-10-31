<?php

declare(strict_types=1);

namespace Thelia\Api\Test\Command;

use ApiPlatform\JsonSchema\Schema;
use ApiPlatform\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use Faker\Factory;
use Propel\Runtime\Propel;
use ReflectionClass;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Test\ModelTest;
use Thelia\Config\DatabaseConfiguration;

#[AsCommand(name: 'api:generate:tests:v0', description: 'Generate tests for APIP resource')]
class CollectionTestCommand extends Command
{
    private const NAMESPACE = 'Thelia\Api\Resource';

    private const RESOURCE_TEST_DIR = __DIR__ . '/../../Resource';

    public function __construct(
        private readonly ModelTest                                  $test,
        private readonly ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
        private readonly SchemaFactoryInterface                     $schemaFactory,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $files = array_filter(scandir(self::RESOURCE_TEST_DIR), function ($file) {
            return is_file(self::RESOURCE_TEST_DIR . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php';
        });
        foreach ($files as $file) {
            $className = self::NAMESPACE . '\\' . pathinfo($file, PATHINFO_FILENAME);
            if (class_exists($className) && in_array(PropelResourceInterface::class, class_implements($className), true)) {
                $connection = Propel::getWriteConnection(DatabaseConfiguration::THELIA_CONNECTION_NAME);
                $connection->beginTransaction();
                $resourceMetadataCollection = $this->resourceMetadataCollectionFactory->create(resourceClass: $className);
                if (count($resourceMetadataCollection) < 1) {
                    continue;
                }
                $operations = [];
                foreach ($resourceMetadataCollection as $resourceMetadata) {
                    foreach ($resourceMetadata->getOperations() as $operation) {
                        $operations[] = $operation;
                    }
                }
                $this->testPost(className: $className, operations: $operations);
                $connection->rollBack();
            }
        }
        return Command::SUCCESS;
    }

    private function testPost(string $className, $operations)
    {
        $postOperations = array_filter($operations, static function ($operation) {
            return $operation instanceof Post;
        });
        foreach ($postOperations as $postOperation) {
            $schema = json_encode(
                $this->schemaFactory->buildSchema(
                    className: $className,
                    format: 'jsonld',
                    type: Schema::TYPE_INPUT,
                    operation: $postOperation,
                    forceCollection: true)
                , JSON_THROW_ON_ERROR
            );
            $schema = json_decode($schema, true);
            foreach ($schema['definitions'] as $definition) {
                $property = $definition['properties'];
            }
            $body = $this->fakeBody($property);
            $this->test->testPost(operation: $postOperation, body: $body);
        }
    }

    private function getMethod(string $method, ReflectionClass $reflection): mixed
    {
        $instance = $reflection->newInstance();
        if ($reflection->hasMethod($method)) {
            return $reflection->getMethod($method)->invoke($instance);
        }
        throw new RuntimeException('Method not found');
    }

    private function fakeBody(array $properties): array
    {
        $body = [];
        $faker = Factory::create();
        foreach ($properties as $property => $value) {
            $fakedValue = null;
            if (isset($value['format']) && $value['format'] === "iri-reference") {
                $body[$property] = "1";
                continue;
            }
            $type = $value['type'];
            if (is_array($type)) {
                $type = $value['type'][0];
            }
            switch ($type) {
                case "string":
                    $fakedValue = $faker->text(maxNbChars: 20);
                    break;
                case "boolean":
                    $fakedValue = $faker->boolean();
            }
            $body[$property] = $fakedValue;
        }
        return $body;
    }
}
