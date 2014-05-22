<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2013 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy;

use Doctrine\Common\Collections\Collection;
use DoctrineModule\Persistence\ProvidesObjectManager;
use DoctrineModule\Stdlib\Hydrator;

/**
 * Class PersistentCollection
 *
 * @package Phpro\DoctrineHydrationModule\Hydrator\Strategy\ODM\MongoDB
 */
class DefaultRelation extends AbstractMongoStrategy
{

    /**
     * @param mixed $value
     *
     * @return array|mixed
     * @throws \Exception
     */
    public function extract($value)
    {
        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return array|Collection|mixed
     */
    public function hydrate($value)
    {
        // Beware of the collection strategies:
        $collection = $this->collectionName;
        if ($this->metadata->isCollectionValuedAssociation($collection)) {
            $value = $this->hydrateCollection($value);
        }

        return $value;
    }

}
