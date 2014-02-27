<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2013 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy;

use Doctrine\Common\Collections\Collection;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use DoctrineModule\Stdlib\Hydrator;
use DoctrineModule\Stdlib\Hydrator\Strategy\AllowRemoveByValue;
use Doctrine\ODM\MongoDB\PersistentCollection as MongoDbPersistentCollection;

/**
 * Class PersistentCollection
 *
 * @package Phpro\DoctrineHydrationModule\Hydrator\Strategy\ODM\MongoDB
 */
class EmbeddedField extends AllowRemoveByValue
    implements ObjectManagerAwareInterface, MongoStrategyInterface
{

    use ProvidesObjectManager;

    /**
     *
     * @param ObjectManager $objectManager Possibly injected by hydrator factory
     */
    public function __construct($objectManager = null)
    {
        if ($objectManager) {
            $this->setObjectManager($objectManager);
        }
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function extract($value)
    {
        // Embedded Many
        if ($value instanceof MongoDbPersistentCollection) {
            $mapping = $value->getMapping();
            $result = [];
            foreach ($value as $index => $object) {
                $hydrator = $this->getDoctrineHydrator($object);
                $result[$index] = $hydrator->extract($object);

                // Add discrimator field if it can be found.
                if (isset($mapping['discriminatorMap'])) {
                    $discriminatorName = array_search(get_class($object), $mapping['discriminatorMap']);
                    if ($discriminatorName) {
                        $result[$index][$mapping['discriminatorField']] = $discriminatorName;
                    }
                }

            }

        // Embedded One:
        } else {
            $hydrator = $this->getDoctrineHydrator($value);
            $result = $hydrator->extract($value);
        }

        return parent::extract($result);
    }

    /**
     * @param mixed $value
     *
     * @return array|Collection|mixed
     */
    public function hydrate($value)
    {
        $mapping = $this->metadata->fieldMappings[$this->collectionName];
        $targetDocument = $mapping['targetDocument'];
        $discriminator = ($mapping ['discriminatorField']) ? $mapping ['discriminatorField'] : false;
        $discriminatorMap = ($mapping['discriminatorMap']) ? $mapping['discriminatorMap'] : array();

        if (is_array($value) || $value instanceof \Iterator) {
            $result = array();
            foreach ($value as $data) {
                // Use configured discriminator as discriminator class:
                if ($discriminator && is_array($data)) {
                    if (isset($data[$discriminator]) && isset($discriminatorMap[$data[$discriminator]])) {
                        $targetDocument = $discriminatorMap[$data[$discriminator]];
                    }
                }

                $result[] = $this->hydrateSingle($targetDocument, $data);
            }
        } else {
            $result = $this->hydrateSingle($targetDocument, $value);
        }

        return parent::hydrate($result);
    }

    /**
     * @param $targetDocument
     * @param $document
     *
     * @return object
     */
    protected function hydrateSingle($targetDocument, $document)
    {
        if (is_object($document)) {
            return $document;
        }

        $rc = new \ReflectionClass($targetDocument);
        $object = $rc->newInstanceWithoutConstructor();

        $hydrator = $this->getDoctrineHydrator($targetDocument);
        $hydrator->hydrate($document, $object);

        return $object;
    }


    /**
     * @param $document
     *
     * @return Hydrator\DoctrineObject
     */
    protected function getDoctrineHydrator($document)
    {
        if (is_object($document)) {
            $document = get_class($document);
        }

        $hydrator = new Hydrator\DoctrineObject($this->getObjectManager(), $document);
        return $hydrator;
    }

}
