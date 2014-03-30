<?php

namespace Phpro\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB\Strategy;
use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\ReferencedCollection;
use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;


/**
 * Class ReferencedCollectionTest
 *
 * @package Phpro\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB\Strategy
 */
class ReferencedCollectionTest extends AbstractMongoStrategyTest
{
    /**
     * @return StrategyInterface
     */
    protected function createStrategy()
    {
        return new ReferencedCollection();
    }
}
