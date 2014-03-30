<?php

namespace Phpro\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB\Strategy;
use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\EmbeddedReferenceField;
use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;


/**
 * Class EmbeddedReferenceFieldTest
 *
 * @package Phpro\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB\Strategy
 */
class EmbeddedReferenceFieldTest extends AbstractMongoStrategyTest
{
    /**
     * @return StrategyInterface
     */
    protected function createStrategy()
    {
        return new EmbeddedReferenceField();
    }
}
