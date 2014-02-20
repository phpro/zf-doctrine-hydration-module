<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2013 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace Phpro\DoctrineHydrationModule\Hydrator\Strategy\ODM\MongoDB;

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
class PersistentCollection extends AllowRemoveByValue
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
        if ($value instanceof MongoDbPersistentCollection) {
            $mapping = $value->getMapping();
            $records = [];
            foreach ($value as $index => $object) {
                $hydrator = $this->getDoctrineHydrator($object);
                $records[$index] = $hydrator->extract($object);

                // Add discrimator field if it can be found.
                if (isset($mapping['discriminatorMap'])) {
                    $discriminatorName = array_search(get_class($object), $mapping['discriminatorMap']);
                    if ($discriminatorName) {
                        $records[$index][$mapping['discriminatorField']] = $discriminatorName;
                    }
                }

            }

            $value = $records;
        }

        return parent::extract($value);
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
