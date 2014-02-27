<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2013 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy;


/**
 * Class PersistentCollection
 *
 * @package Phpro\DoctrineHydrationModule\Hydrator\Strategy\ODM\MongoDB
 */
class EmbeddedField extends AbstractMongoStrategy
{

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function extract($value)
    {
        $hydrator = $this->getDoctrineHydrator($value);
        return $hydrator->extract($value);
    }

    /**
     * @param mixed $value
     *
     * @return array|mixed
     */
    public function hydrate($value)
    {
        $mapping = $this->metadata->fieldMappings[$this->collectionName];
        $targetDocument = $mapping['targetDocument'];

        if (is_object($value)) {
            return $value;
        }

        $rc = new \ReflectionClass($targetDocument);
        $object = $rc->newInstanceWithoutConstructor();

        $hydrator = $this->getDoctrineHydrator($targetDocument);
        $hydrator->hydrate($value, $object);

        return $object;
    }

}
