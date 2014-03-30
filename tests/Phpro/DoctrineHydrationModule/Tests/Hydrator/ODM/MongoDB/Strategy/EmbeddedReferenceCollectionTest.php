<?php

namespace Phpro\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB\Strategy;
use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\EmbeddedReferenceCollection;
use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;


/**
 * Class EmbeddedReferenceCollectionTest
 *
 * @package Phpro\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB\Strategy
 */
class EmbeddedReferenceCollectionTest extends AbstractMongoStrategyTest
{

    /**
     * @return StrategyInterface
     */
    protected function createStrategy()
    {
        return new EmbeddedReferenceCollection();
    }

}
