<?php
    /**
     * Phpro ZF2 Library
     *
     * @link      http://fisheye.phpro.be/git/Git-Vlir-Uos.git
     * @copyright Copyright (c) 2012 PHPro
     * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
     *
     */

namespace Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as BaseHydrator;
use DoctrineModule\Stdlib\Hydrator\Strategy as DoctrineStrategy;
use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\MongoStrategyInterface;
use Traversable;

/**
 * Class DoctrineObject
 *
 * @package Phpro\DoctrineHydrationModule\Hydrator\Strategy\ODM
 */
class DoctrineObject extends BaseHydrator
{

    /**
     * @param object $object
     *
     * @throws InvalidArgumentException
     *
     * TODO: For the moment only byValue configured...
     */
    protected function prepare($object)
    {
        $associations = $this->metadata->getAssociationNames();
        foreach ($associations as $association) {

            if ($this->hasStrategy($association)) {
                continue;
            }

            $fieldMeta = $this->metadata->fieldMappings[$association];
            if ($fieldMeta['reference']) {
                $this->addStrategy($association, new Strategy\ReferencedField());
            } else {
                $this->addStrategy($association, new Strategy\ReferencedField());
            }
        }

        // Call through for DI
        parent::prepare($object);
    }

    /**
     * Make sure to only use the mongoDB ODM strategies for onMany
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

            if ($strategy instanceof MongoStrategyInterface) {
                $strategy->setObject($object);
                $this->hydrateValue($collectionName, $values);
                return;
            }

        }

        parent::toMany($object, $collectionName, $target, $values);
    }

}