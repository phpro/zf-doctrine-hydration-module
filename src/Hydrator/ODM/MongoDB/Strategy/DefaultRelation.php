<?php

namespace Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy;

use Doctrine\Common\Collections\Collection;

/**
 * Class PersistentCollection.
 */
class DefaultRelation extends AbstractMongoStrategy
{
    /**
     * @param mixed $value
     *
     * @return array|mixed
     *
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
