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
class ReferencedField extends AbstractMongoStrategy
{

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function extract($value)
    {
        if (!is_object($value)) {
            return $value;
        }

        $idField = $this->metadata->getIdentifier();
        $getter = 'get' . ucfirst($idField);

        // Validate object:
        $rc = new \ReflectionClass($value);
        if (!$rc->hasMethod($getter)) {
            return $value;
        }

        return $value->$getter();
    }

    /**
     * @param mixed $value
     *
     * @return array|Collection|mixed
     */
    public function hydrate($value)
    {
        if (is_object($value)) {
            return $value;
        }

        $mapping = $this->metadata->fieldMappings[$this->collectionName];
        $targetDocument = $mapping['targetDocument'];

        return $this->findTargetDocument($targetDocument, $value);
    }

}
