<?php

namespace Phpro\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB\Strategy;
use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

/**
 * Class AbstractMongoStrategyTest
 *
 * @package Phpro\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB\Strategy
 */
abstract class AbstractMongoStrategyTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return StrategyInterface
     */
    abstract protected function createStrategy();

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function stubObjectManager()
    {
        $objectManager = $this->getMock('Doctrine\ODM\MongoDB\DocumentManager', [], [], '', false);
        return $objectManager;
    }

    /**
     * @test
     */
    public function it_should_be_a_mongodb_strategy()
    {
        $strategy = $this->createStrategy();
        $this->assertInstanceOf('Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\AbstractMongoStrategy', $strategy);
    }

    /**
     * @test
     */
    public function it_should_be_a_collection_strategy()
    {
        $strategy = $this->createStrategy();
        $this->assertInstanceOf('DoctrineModule\Stdlib\Hydrator\Strategy\AbstractCollectionStrategy', $strategy);
    }

    /**
     * @test
     */
    public function it_should_know_an_object_manager()
    {
        $strategy = $this->createStrategy();
        $this->assertInstanceOf('DoctrineModule\Persistence\ObjectManagerAwareInterface', $strategy);
    }

    /**
     * @test
     */
    public function it_should_have_an_object_manager()
    {
        $objectManager = $this->stubObjectManager();
        $strategy = $this->createStrategy();

        $strategy->setObjectManager($objectManager);
        $this->assertEquals($objectManager, $strategy->getObjectManager());
    }
}
