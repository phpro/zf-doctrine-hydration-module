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
use LogicException;

/**
 * Class PersistentCollection
 *
 * @package Phpro\DoctrineHydrationModule\Hydrator\Strategy\ODM\MongoDB
 */
class ReferencedCollection extends AllowRemoveByValue
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
                // Todo: find identifier field in mapping:
                $records[] = $object->getId();
            }

            $value = $records;
        }

        return parent::extract($value);
    }

    /**
     * @param mixed $value
     *
     * @return array|Collection|mixed
     */
    public function hydrate($value)
    {
        $mapping = $this->object->getMapping();
        var_dump($mapping);
        exit;




        return parent::hydrate($value);
    }


}
