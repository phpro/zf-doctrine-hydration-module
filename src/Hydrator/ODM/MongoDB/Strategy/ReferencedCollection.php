<?php

namespace Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy;

use Doctrine\Common\Collections\Collection;

/**
 *
 * Class PersistentCollection
 */
class ReferencedCollection extends AbstractMongoStrategy
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function extract($value)
    {
        $strategy = new ReferencedField($this->getObjectManager());
        $strategy->setClassMetadata($this->getClassMetadata());
        $strategy->setCollectionName($this->getCollectionName());

        $result = array();
        if ($value) {
            foreach ($value as $key => $record) {
                $strategy->setObject($record);
                $result[$key] = $strategy->extract($record);
            }
        }

        return $result;
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

        $result = array();
        if ($value) {
            foreach ($value as $documentId) {
                $result[] = $this->hydrateSingle($targetDocument, $documentId);
            }
        }

        return $this->hydrateCollection($result);
    }

    /**
     *
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
}
