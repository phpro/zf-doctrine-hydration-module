<?php

namespace Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy;

/**
 * Class PersistentCollection.
 */
class EmbeddedReferenceField extends AbstractMongoStrategy
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function extract($value)
    {
        if (!$value) {
            return $value;
        }

        $strategy = new EmbeddedField($this->getObjectManager());
        $strategy->setClassMetadata($this->getClassMetadata());
        $strategy->setCollectionName($this->getCollectionName());
        $strategy->setObject($value);

        return $strategy->extract($value);
    }

    /**
     * @param mixed $value
     *
     * @return array|mixed
     */
    public function hydrate($value)
    {
        $strategy = new ReferencedField($this->getObjectManager());
        $strategy->setClassMetadata($this->getClassMetadata());
        $strategy->setCollectionName($this->getCollectionName());
        if ($this->getObject()) {
            $strategy->setObject($this->getObject());
        }

        return $strategy->hydrate($value);
    }
}
