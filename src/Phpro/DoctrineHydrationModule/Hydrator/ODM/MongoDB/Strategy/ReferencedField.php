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

/**
 * Class PersistentCollection
 *
 * @package Phpro\DoctrineHydrationModule\Hydrator\Strategy\ODM\MongoDB
 */
class ReferencedField extends AllowRemoveByValue
    implements ObjectManagerAwareInterface, MongoStrategyInterface
{

    use ProvidesObjectManager;

    /**
     * Hooray: The doctrine hydrator allready does this work for us!!
     * @param mixed $value
     *
     * @return mixed
     */
    public function extract($value)
    {
        return parent::extract($value);
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

        // Reference Many:
        if (is_array($value) || $value instanceof \Iterator) {
            $result = array();
            foreach ($value as $documentId) {
                $result[] = $this->hydrateSingle($targetDocument, $documentId);
            }

            // Reference One:
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

        return $this->findTargetDocument($targetDocument, $document);

    }

    /**
     * @param $targetDocument
     * @param $targetId
     *
     * @return object
     */
    protected function findTargetDocument($targetDocument, $targetId)
    {
        $repo = $this->getObjectManager()->getRepository($targetDocument);
        $document = $repo->find($targetId);
        return $document;
    }

}
