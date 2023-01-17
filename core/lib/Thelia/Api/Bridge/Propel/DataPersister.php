<?php

namespace Thelia\Api\Bridge\Propel;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use ApiPlatform\Core\Exception\InvalidResourceException;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Resource\TranslatableResourceInterface;

class DataPersister implements DataPersisterInterface
{

    public function supports($data): bool
    {
        return \is_object($data) && is_subclass_of($data, PropelResourceInterface::class);
    }

    /**
     * @param PropelResourceInterface $data
     * @return object|void
     * @throws \ReflectionException
     * @throws InvalidResourceException
     */
    public function persist($data)
    {
        $propelModelClass = $data::getPropelModelClass();

        /** @var ModelCriteria $queryClass */
        $queryClass = $propelModelClass.'Query';
        $propelModel = $data->getId() ? $queryClass::create()->filterById($data->getId())->findOne() : new $propelModelClass();

        if (null === $propelModel) {
            throw new InvalidResourceException('Invalid resource, can\'t find or create a propel model.');
        }

        foreach (get_class_methods($propelModel) as $methodName) {
            if (!str_starts_with($methodName, 'set')) {
                continue;
            }

            $possibleGetters = [
                'get'.ucfirst(substr($methodName, 3)),
                'is'.ucfirst(substr($methodName, 3)),
            ];

            $availableMethods = array_filter(array_intersect($possibleGetters, get_class_methods($data)));

            if (empty($availableMethods)) {
                continue;
            }

            $reflectionMethod = new \ReflectionMethod($propelModel, $methodName);
            $parameters = $reflectionMethod->getParameters();

            if (!isset($parameters[0])) {
                continue;
            }

            $value = null;
            while (!empty($availableMethods) && ($value === null || empty($theliaValue))) {
                $method = array_pop($availableMethods);
                $value = $data->$method();
            }

            if (null !== $parameters[0]->getType() && $parameters[0]->getType()->__toString() == \gettype($value)) {
                continue;
            }

            $propelModel->$methodName($value);
        }

        if (is_subclass_of($data, TranslatableResourceInterface::class)) {
            foreach ($data->getI18ns() as $i18n) {
                $i18nGetters = array_filter(get_class_methods($i18n), function ($method) {return str_starts_with($method, 'get');});
                usort($i18nGetters, function ($a, $b) {return $a !== 'getLocale'; });
                foreach ($i18nGetters as $i18nGetter) {
                    if ($i18nGetter === 'getId') {
                        continue;
                    }
                    $propelSetter = substr_replace($i18nGetter, 's', 0, 1);
                    if (method_exists($propelModel, $propelSetter)) {
                        $propelModel->$propelSetter($i18n->$i18nGetter());
                    }
                }
            }
        }

        $propelModel->save();

        $data->setId($propelModel->getId());
    }

    public function remove($data)
    {
        // TODO: Implement remove() method.
    }
}