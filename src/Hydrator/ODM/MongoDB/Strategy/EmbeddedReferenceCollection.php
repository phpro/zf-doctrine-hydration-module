<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2013 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy;

use Doctrine\Common\Collections\Collection;
use DoctrineModule\Persistence\ProvidesObjectManager;
use DoctrineModule\Stdlib\Hydrator;
use Doctrine\ODM\MongoDB\PersistentCollection as MongoDbPersistentCollection;

/**
 * Class PersistentCollection
 *
 * @package Phpro\DoctrineHydrationModule\Hydrator\Strategy\ODM\MongoDB
 */
class EmbeddedReferenceCollection extends AbstractMongoStrategy
{

    /**
     * @param mixed $value
     *
     * @return array|mixed
     * @throws \Exception
     */
    public function extract($value)
    {
        if (!$value) {
            return $value;
        }

        $strategy = new EmbeddedCollection($this->getObjectManager());
        $strategy->setClassMetadata($this->getClassMetadata());
        $strategy->setCollectionName($this->getCollectionName());
        $strategy->setObject($value);

        return $strategy->extract($value);
    }

    /**
     * @param mixed $value
     *
     * @return array|Collection|mixed
     */
    public function hydrate($value)
    {
        $strategy = new ReferencedCollection($this->getObjectManager());
        $strategy->setClassMetadata($this->getClassMetadata());
        $strategy->setCollectionName($this->getCollectionName());
        if ($this->getObject()) {
            $strategy->setObject($this->getObject());
        }
        return $strategy->hydrate($value);
    }

}
