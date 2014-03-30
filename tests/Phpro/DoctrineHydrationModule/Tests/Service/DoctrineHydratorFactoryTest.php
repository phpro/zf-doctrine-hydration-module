<?php

namespace Phpro\DoctrineHydrationModule\Tests\Service;

use Phpro\DoctrineHydrationModule\Service\DoctrineHydratorFactory;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Hydrator\HydratorPluginManager;

/**
 * Class DoctrineHydratorFactoryTest
 *
 * @package Phpro\DoctrineHydrationModule\Tests\Service
 */
class DoctrineHydratorFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var array
     */
    protected $serviceConfig;

    /**
     * @var HydratorPluginManager
     */
    protected $hydratorManager;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * Setup the service manager
     */
    protected function setUp()
    {
        $this->serviceConfig = require(TEST_BASE_PATH . '/config/module.config.php');

        $this->serviceManager = new ServiceManager();
        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setService('Config', $this->serviceConfig);
        $this->serviceManager->setService('custom.strategy', $this->getMock('Zend\Stdlib\Hydrator\Strategy\StrategyInterface'));

        $this->hydratorManager = $this->getMock('Zend\Stdlib\Hydrator\HydratorPluginManager');
        $this->hydratorManager
            ->expects($this->any())
            ->method('getServiceLocator')
            ->will($this->returnValue($this->serviceManager));
    }

    /**
     * @param $objectManagerClass
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function stubObjectManager($objectManagerClass)
    {
        $objectManager = $this->getMock($objectManagerClass, [], [], '', false);
        $this->serviceManager->setService('doctrine.default.object-manager', $objectManager);
        return $objectManager;
    }

    /**
     * @test
     */
    public function it_should_be_initializable()
    {
        $factory = new DoctrineHydratorFactory();
        $this->assertInstanceOf('Phpro\DoctrineHydrationModule\Service\DoctrineHydratorFactory', $factory);
    }

    /**
     * @test
     */
    public function it_should_be_an_abstract_factory()
    {
        $factory = new DoctrineHydratorFactory();
        $this->assertInstanceOf('Zend\ServiceManager\AbstractFactoryInterface', $factory);
    }

    /**
     * @test
     */
    public function it_should_know_which_services_it_can_create()
    {
        // $this->stubObjectManager('Doctrine\Common\Persistence\ObjectManager');
        $factory = new DoctrineHydratorFactory();

        $result = $factory->canCreateServiceWithName($this->hydratorManager, 'customhydrator', 'custom-hydrator');
        $this->assertTrue($result);

        $result = $factory->canCreateServiceWithName($this->hydratorManager, 'invalidhydrator', 'invalid-hydrator');
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function it_should_create_a_custom_ORM_hydrator()
    {
        $this->stubObjectManager('Doctrine\ORM\EntityManager');

        $factory = new DoctrineHydratorFactory();
        $hydrator = $factory->createServiceWithName($this->hydratorManager, 'customhydrator', 'custom-hydrator');

        $this->assertInstanceOf('Phpro\DoctrineHydrationModule\Hydrator\DoctrineHydrator', $hydrator);
        $this->assertInstanceOf('DoctrineModule\Stdlib\Hydrator\DoctrineObject', $hydrator->getExtractService());
        $this->assertInstanceOf('DoctrineModule\Stdlib\Hydrator\DoctrineObject', $hydrator->getHydrateService());
    }

    /**
     * @test
     */
    public function it_should_create_a_custom_ODM_hydrator()
    {
        $this->stubObjectManager('Doctrine\ODM\MongoDb\DocumentManager');

        $factory = new DoctrineHydratorFactory();
        $hydrator = $factory->createServiceWithName($this->hydratorManager, 'customhydrator', 'custom-hydrator');

        $this->assertInstanceOf('Phpro\DoctrineHydrationModule\Hydrator\DoctrineHydrator', $hydrator);
        $this->assertInstanceOf('Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\DoctrineObject', $hydrator->getExtractService());
        $this->assertInstanceOf('Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\DoctrineObject', $hydrator->getHydrateService());
    }

    /**
     * @test
     */
    public function it_should_create_a_custom_ODM_hydrator_which_uses_the_auto_generated_hydrators()
    {
        $this->serviceConfig['doctrine-hydrator']['custom-hydrator']['use_generated_hydrator'] = true;
        $this->serviceManager->setService('Config', $this->serviceConfig);
        $objectManager = $this->stubObjectManager('Doctrine\ODM\MongoDb\DocumentManager');

        $hydratorFactory = $this->getMock('Doctrine\ODM\MongoDB\Hydrator\HydratorFactory', [], [], '', false);
        $generatedHydrator = $this->getMock('Doctrine\ODM\MongoDB\Hydrator\HydratorInterface');

        $objectManager
            ->expects($this->any())
            ->method('getHydratorFactory')
            ->will($this->returnValue($hydratorFactory));

        $hydratorFactory
            ->expects($this->any())
            ->method('getHydratorFor')
            ->with('App\Entity\EntityClass')
            ->will($this->returnValue($generatedHydrator));

        $factory = new DoctrineHydratorFactory();
        $hydrator = $factory->createServiceWithName($this->hydratorManager, 'customhydrator', 'custom-hydrator');

        $this->assertInstanceOf('Phpro\DoctrineHydrationModule\Hydrator\DoctrineHydrator', $hydrator);
        $this->assertInstanceOf('Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\DoctrineObject', $hydrator->getExtractService());
        $this->assertEquals($generatedHydrator, $hydrator->getHydrateService());
    }
}
