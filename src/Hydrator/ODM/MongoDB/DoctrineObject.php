<?php

namespace Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB;

use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as BaseHydrator;
use DoctrineModule\Stdlib\Hydrator\Strategy as DoctrineStrategy;
use InvalidArgumentException;
use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\AbstractMongoStrategy;
use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\DateTimeField;

/**
 * Class DoctrineObject.
 */
class DoctrineObject extends BaseHydrator
{
    /**
     * TODO: For the moment only byValue configured...
     *
     * @throws InvalidArgumentException
     */
    protected function prepareStrategies()
    {
        $this->prepareFieldStrategies();
        $this->prepareAssociationStrategies();

        // Call through for DI
        parent::prepareStrategies();
    }

    /**
     * Add custom strategies to specific field types.
     */
    protected function prepareFieldStrategies()
    {
        $fields = $this->metadata->getFieldNames();
        foreach ($fields as $field) {
            if ($this->hasStrategy($field) || in_array($field, $this->metadata->getAssociationNames())) {
                continue;
            }

            $fieldMeta = $this->metadata->fieldMappings[$field];
            if (in_array($fieldMeta['type'], array('date', 'timestamp'))) {
                $isTimestamp = ($fieldMeta['type'] == 'timestamp');
                $this->addStrategy($field, new DateTimeField($isTimestamp));
            }
        }
    }

    /**
     * Add custom strategies to association fields.
     */
    protected function prepareAssociationStrategies()
    {
        $associations = $this->metadata->getAssociationNames();
        foreach ($associations as $association) {
            // Add meta data to existing collections:
            if ($this->hasStrategy($association)) {
                $strategy = $this->getStrategy($association);
                $this->injectAssociationStrategyDependencies($strategy, $association);
                continue;
            }

            // In uni-directional relationships the association mapping might not be available.
            // No strategy will be automatically added.
            if (!isset($this->metadata->fieldMappings[$association])) {
                continue;
            }

            // Create new strategy based on type of filed
            $fieldMeta = $this->metadata->fieldMappings[$association];
            $reference = isset($fieldMeta['reference']) && $fieldMeta['reference'];
            $embedded = isset($fieldMeta['embedded']) && $fieldMeta['embedded'];
            $isCollection = $this->metadata->isCollectionValuedAssociation($association);
            $strategy = null;

            if ($isCollection) {
                if ($reference) {
                    $strategy = new Strategy\ReferencedCollection($this->objectManager);
                } elseif ($embedded) {
                    $strategy = new Strategy\EmbeddedCollection($this->objectManager);
                }
            } else {
                if ($reference) {
                    $strategy = new Strategy\ReferencedField($this->objectManager);
                } elseif ($embedded) {
                    $strategy = new Strategy\EmbeddedField($this->objectManager);
                }
            }

            // Add meta data
            if ($strategy) {
                $this->injectAssociationStrategyDependencies($strategy, $association);
                $this->addStrategy($association, $strategy);
            }
        }
    }

    /**
     * Inject dependencies to strategy that is injected in a later state.
     *
     * @param $strategy
     * @param $association
     */
    protected function injectAssociationStrategyDependencies($strategy, $association)
    {
        if ($strategy instanceof DoctrineStrategy\AbstractCollectionStrategy) {
            $strategy->setCollectionName($association);
            $strategy->setClassMetadata($this->metadata);
        }

        if ($strategy instanceof ObjectManagerAwareInterface) {
            $strategy->setObjectManager($this->objectManager);
        }
    }

    /**
     * Make sure to only use the mongoDB ODM strategies for onMany.
     *
     * @param object $object
     * @param mixed  $collectionName
     * @param string $target
     * @param mixed  $values
     */
    protected function toMany($object, $collectionName, $target, $values)
    {
        if ($this->hasStrategy($collectionName)) {
            $strategy = $this->getStrategy($collectionName);

            if ($strategy instanceof AbstractMongoStrategy) {
                $strategy->setObject($object);
                $this->hydrateValue($collectionName, $values, $values);

                return;
            }
        }

        parent::toMany($object, $collectionName, $target, $values);
    }
}
