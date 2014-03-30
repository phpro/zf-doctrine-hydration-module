<?php

namespace Phpro\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB;
use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\DoctrineObject;

/**
 * Class DoctrineObjectTest
 *
 * @package Phpro\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB
 */
class DoctrineObjectTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param null $objectManager
     *
     * @return DoctrineObject
     */
    protected function createHydrator($objectManager = null)
    {
        $objectManager = $objectManager ? $objectManager : $this->getMock('Doctrine\ODM\MongoDB\DocumentManager', [], [], '', false);
        $hydrator = new DoctrineObject($objectManager);
        return $hydrator;
    }

    /**
     * @test
     */
    public function it_should_be_initializable()
    {
        $hydrator = $this->createHydrator();
        $this->assertInstanceOf('Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\DoctrineObject', $hydrator);
    }

    /**
     * @test
     */
    public function it_should_be_a_doctrine_hydrator()
    {
        $hydrator = $this->createHydrator();
        $this->assertInstanceOf('DoctrineModule\Stdlib\Hydrator\DoctrineObject', $hydrator);
    }

}
