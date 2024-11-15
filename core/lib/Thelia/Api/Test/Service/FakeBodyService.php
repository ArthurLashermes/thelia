<?php

namespace Thelia\Api\Test\Service;

use ApiPlatform\JsonSchema\Schema;
use ApiPlatform\JsonSchema\SchemaFactoryInterface;
use ApiPlatform\Metadata\Post;
use RuntimeException;
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Model\Lang;
use Thelia\Model\LangQuery;
use ReflectionClass;
readonly class FakeBodyService
{
    public function __construct(
        private readonly SchemaFactoryInterface $schemaFactory,
    )
    {
    }

    public function getFakeBody(Post $operation, string $className, bool $isRecursive = false): ?string
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
                $propertyRealNames = $this->findRelationField($className, 'Thelia\Api\Resource\\' . explode('.', $index)[0]);
                if (!empty($propertyRealNames)) {
                    $propertyNames = $propertyRealNames;
                }
                foreach ($propertyNames as $propertyName) {
                    if (isset($definition['properties']['id'])) {
                        $id = $this->getId(className: $className,propertyName: $propertyName);
                        if (!$id){
                            continue;
                        }
                        $body .= '"' . $propertyName . '" => ';
                        $body .= '["' . 'id' . '"' . ' => ' . $id . '],';
                    } else {
                        $body .= '"' . $propertyName . '" => [' . $this->getFakeBody(
                                operation: $operation,
                                className: 'Thelia\Api\Resource\\' . explode('.', $index)[0],
                                isRecursive: true) . '],';
                    }
                }
            }
        }
        foreach ($properties as $property => $value) {
            if (isset($value['format']) && $value['format'] === "iri-reference") {
                $id = '"'.$this->getId(className: $className,propertyName: $property).'"';
                $property = '"' . $property . '"';
                $line .= $property . ' => ' . $id . ',';
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
            if (isset($value['format']) && $value['format'] === "date-time") {
                $fakedValue = '$faker->dateTimeThisYear()->format(\'Y-m-d\\TH:i:s.v\\Z\')';
            }
            $property = '"' . $property . '"';
            $line .= $property . ' => ' . $fakedValue . ',';
        }
        $line = rtrim($line, ',');
        if (!$isRecursive) {
            $body .= $line . '];';
        }
        if ($isRecursive) {
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
                        $names [] = $property->getName();
                    }
                }
            }
        }
        return $names;
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

    private function getTypeWithProperty(string $className, string $propertyName): ?string
    {
        $reflectionClass = new ReflectionClass($className);
        if (!$reflectionClass->hasProperty($propertyName)) {
            return null;
        }
        $property = $reflectionClass->getProperty($propertyName);
        return $property->getType();
    }

    private function getId(string $className, string $propertyName) :?int
    {
        $type = $this->getTypeWithProperty($className,$propertyName);
        if (!$type){
            return null;
        }
        if (str_contains($type,'?')){
            $type = str_replace('?','',$type);
        }
        $tableMap = $type::getPropelRelatedTableMap();
        $query = $tableMap->getClassName() . 'Query';
        $query = $query::create();
        $model = $query->findOne();
        if ($model === null){
            throw new \Exception(sprintf('You must define at least one row in the test database for the %s table', $tableMap->getName()));
        }
        if (method_exists($model, 'getId')) {
            return $model->getId();
        }
        return 1;
    }


}
