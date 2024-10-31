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
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Test\ModelTest;
use Thelia\Config\DatabaseConfiguration;
use Thelia\Model\Lang;
use Thelia\Model\LangQuery;

#[AsCommand(name: 'api:generate:tests', description: 'Generate tests for APIP resource')]
class TestGenerationCommand extends Command
{
    private const NAMESPACE = 'Thelia\Api\Resource';

    private const RESOURCE_TEST_DIR = __DIR__ . '/../../Resource';

    public function __construct(
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
        if (!is_dir(__DIR__ . '/../ResourceTest/Generated/')) {
            mkdir(__DIR__ . '/../ResourceTest/Generated/', 0777, true);
        }
        foreach ($files as $file) {
            $className = self::NAMESPACE . '\\' . pathinfo($file, PATHINFO_FILENAME);
            if (class_exists($className) && in_array(PropelResourceInterface::class, class_implements($className), true)) {
                $resourceMetadataCollection = $this->resourceMetadataCollectionFactory->create(resourceClass: $className);
                if (count($resourceMetadataCollection) < 1) {
                    continue;
                }
                $destinationClassName = pathinfo($file, PATHINFO_FILENAME);
                $destinationClassNewName = $destinationClassName . 'Test.php';
                $destinationClassPath = __DIR__ . '/../ResourceTest/Generated/' . $destinationClassNewName;
                $this->copyModelClass(
                    sourceClass: __DIR__ . '/../ResourceTest/Model/ModelTestGeneration.php',
                    destinationClassName: $destinationClassPath,
                    destinationClassNewName: $destinationClassNewName
                );

                foreach ($resourceMetadataCollection as $resourceMetadata) {
                    foreach ($resourceMetadata->getOperations() as $operation) {
                        if ($operation instanceof Post) {
                            $this->createPostTest($operation, $destinationClassPath, $className);
                        }
                    }
                }
            }
        }
        return Command::SUCCESS;
    }

    private function copyModelClass(string $sourceClass, string $destinationClassName, string $destinationClassNewName)
    {
        if (!file_exists($sourceClass)) {
            throw new RuntimeException(sprintf("The source file %s does not exist.", $sourceClass));
        }
        $content = file_get_contents($sourceClass);
        $content = str_replace('class ModelTestGeneration', 'class ' . substr($destinationClassNewName, 0, strrpos($destinationClassNewName, '.')), $content);
        $content = str_replace("Thelia\Api\Test\ResourceTest\Model", "Thelia\Api\Test\ResourceTest\Generated", $content);

        file_put_contents($destinationClassName, $content);
    }

    private function createPostTest(Post $operation, string $destinationClassPath, string $className): void
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
        $body = $this->getFakeBody($operation, $className);
        if (!$body) {
            return;
        }
        $postTestTemplate = str_replace('BODY_VALUE', $body, $postTestTemplate);
        $content = str_replace('//Entry point', $postTestTemplate, $content);
        file_put_contents($destinationClassPath, $content);
    }

    private function i18nsManagement(string $className, Post $operation): string
    {
        $reflector = new \ReflectionClass($className);
        $i18nResource = $this->getMethod('getI18nResourceClass', $reflector);
        $reflector = new \ReflectionClass($i18nResource);
        $properties = [];
        foreach ($reflector->getProperties() as $property) {
            foreach ($property->getAttributes(Groups::class) as $groupAttribute) {
                $contextGroups = $operation->getDenormalizationContext()['groups'];
                if (isset($groupAttribute->getArguments()[0])) {
                    $propertyGroups = $groupAttribute->getArguments()[0];
                }
                if (isset($groupAttribute->getArguments()['groups'])) {
                    $propertyGroups = $groupAttribute->getArguments()['groups'];
                }
                $isInContext = !empty(array_intersect($contextGroups, $propertyGroups));
                if ($isInContext) {
                    $properties[] = $property->getName();
                }
            }
        }
        $langs = LangQuery::create()->filterByActive(1)->find();
        $i18nsString = "'i18ns'";
        $i18nsString .= ' => [';
        /** @var Lang $lang */
        foreach ($langs as $lang) {
            $i18nsString .= "'" . $lang->getLocale() . "' => [";
            foreach ($properties as $property) {
                $i18nsString .= "'" . $property . "' => \$faker->text(maxNbChars: 20),";
            }
            $i18nsString .= '],';
        }
        $i18nsString = rtrim($i18nsString, ',');
        $i18nsString .= '],';
        return $i18nsString;
    }

    private function getMethod(string $method, ReflectionClass $reflection): mixed
    {
        $instance = $reflection->newInstance();
        if ($reflection->hasMethod($method)) {
            return $reflection->getMethod($method)->invoke($instance);
        }
        throw new RuntimeException('Method not found');
    }

    private function getFakeBody(Post $operation, string $className, bool $isRecursive= false): ?string
    {
        $schema = json_encode(
            $this->schemaFactory->buildSchema(
                className: $className,
                format: 'jsonld',
                type: Schema::TYPE_INPUT,
                operation: $operation,
                forceCollection: true),
            JSON_THROW_ON_ERROR
        );
        $schema = json_decode($schema, true, 512, JSON_THROW_ON_ERROR);
        foreach ($schema['definitions'] as $index => $definition) {
            if (!isset($definition['properties'])) {
                return null;
            }
            $properties = $definition['properties'];
            unset($schema['definitions'][$index]);
            break;
        }
        $body = '[';
        $line = '';
        if (count($schema['definitions']) > 0) {
            foreach ($schema['definitions'] as $index => $definition) {
                $propertyNames = [lcfirst(explode('.', $index)[0])];
                $propertyRealNames = $this->findRelationField($className,'Thelia\Api\Resource\\'.explode('.', $index)[0]);
                if (!empty($propertyRealNames)){
                    $propertyNames = $propertyRealNames;
                }
                foreach ($propertyNames as $propertyName) {
                    if (isset($definition['properties']['id'])) {
                        $body .= '"' . $propertyName . '" => ';
                        $body .= '["' . 'id' . '"' . ' => ' . "1" . '],';
                    } else {
                        $body .= '"' . $propertyName . '" => ['.$this->getFakeBody(
                                operation: $operation,
                                className: 'Thelia\Api\Resource\\'.explode('.', $index)[0],
                                isRecursive: true).'],';
                    }
                }
            }
        }
        foreach ($properties as $property => $value) {
            if (isset($value['format']) && $value['format'] === "iri-reference") {
                $property = '"' . $property . '"';
                $line .= $property . ' => ' . "1" . ',';
                continue;
            }
            if ($property === "i18ns") {
                $i18ns = $this->i18nsManagement(className: $className, operation: $operation);
                $body .= $i18ns;
                continue;
            }
            if (isset($value['$ref'])) {
                continue;
            }
            if (!isset($value['type'])) {
                return null;
            }
            $type = $value['type'];
            if (is_array($type)) {
                $type = $value['type'][0];
            }
            switch ($type) {
                case "string":
                    $fakedValue = '$faker->text(maxNbChars: 20)';
                    break;
                case "boolean":
                    $fakedValue = '$faker->boolean()';
                    break;
                case "integer":
                    $fakedValue = '$faker->numberBetween(0,10)';
                    break;
                case "number":
                    $fakedValue = '$faker->randomFloat(2)';
                    break;
                default:
                    continue 2;
            }
            if ($type === "string" && $property === "email") {
                $fakedValue = '$faker->email()';
            }
            if (isset($value['format']) && $value['format'] === "date-time"){
                $fakedValue = '$faker->dateTimeThisYear()->format(\'Y-m-d\\TH:i:s.v\\Z\')';
            }
            $property = '"' . $property . '"';
            $line .= $property . ' => ' . $fakedValue . ',';
        }
        $line = rtrim($line, ',');
        if (!$isRecursive){
            $body .= $line . '];';
        }
        if ($isRecursive){
            $body .= $line . '],';
        }
        return $body;
    }

    function findRelationField(string $className, string $targetResourceClass): array
    {
        $names = [];
        $reflectionClass = new ReflectionClass($className);
        foreach ($reflectionClass->getProperties() as $property) {
            $attributes = $property->getAttributes();
            foreach ($attributes as $attribute) {
                if ($attribute->getName() === Relation::class) {
                    $args = $attribute->getArguments();
                    if (isset($args['targetResource']) && $args['targetResource'] === $targetResourceClass) {
                        $names []= $property->getName();
                    }
                }
            }
        }
        return $names;
    }
}
