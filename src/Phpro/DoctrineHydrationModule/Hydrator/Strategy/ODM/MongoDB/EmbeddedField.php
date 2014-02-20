<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2013 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace Phpro\DoctrineHydrationModule\Hydrator\Strategy\ODM\MongoDB;

use Doctrine\Common\Collections\ArrayCollection;
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
    implements ObjectManagerAwareInterface
{

    use ProvidesObjectManager;

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
                $records[$index] = $hydrator->extract($object);

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
     * TODO
     * @param mixed $value
     *
     * @return array|Collection|mixed
     */
    public function hydrate($value)
    {
        $mapping = $this->metadata->fieldMappings[$this->collectionName];
        $targetDocument = $mapping['targetDocument'];

        if (is_array($value) || $value instanceof \Iterator) {
            $result = new ArrayCollection();
            foreach ($value as $data) {

                $rc = new \ReflectionClass($targetDocument);
                $object = $rc->newInstanceWithoutConstructor();

                $hydrator = $this->getDoctrineHydrator($targetDocument);
                $hydrator->hydrate($data, $object);
                $result->add($object);

                // Todo: discriminatorMap

            }

        } else {
            $rc = new \ReflectionClass($targetDocument);
            $object = $rc->newInstanceWithoutConstructor();

            $hydrator = $this->getDoctrineHydrator($targetDocument);
            $result = $hydrator->hydrate($value, $object);
        }


        var_dump($result);exit;

        // Todo:
        throw new \Exception('Todo: hydrate embedded document');
        return parent::hydrate($result);
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
