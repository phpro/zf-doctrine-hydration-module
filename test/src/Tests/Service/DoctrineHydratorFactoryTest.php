<?php

namespace PhproTest\DoctrineHydrationModule\Tests\Service;

use PhproTest\DoctrineHydrationModule\Hydrator\CustomBuildHydratorFactory;
use Phpro\DoctrineHydrationModule\Service\DoctrineHydratorFactory;
use PHPUnit\Framework\TestCase;
use Zend\ServiceManager\ServiceManager;
use Zend\Hydrator\HydratorPluginManager;

class DoctrineHydratorFactoryTest extends TestCase
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
     * Setup the service manager.
     */
    protected function setUp()
    {
        $this->serviceConfig = require TEST_BASE_PATH.'/config/module.config.php';

        $this->serviceManager = new ServiceManager();
        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setService('config', $this->serviceConfig);
        $this->serviceManager->setService(
            'custom.strategy',
            $this->getMockBuilder('Zend\Hydrator\Strategy\StrategyInterface')->getMock()
        );
        $this->serviceManager->setService(
            'custom.filter',
            $this->getMockBuilder('Zend\Hydrator\Filter\FilterInterface')->getMock()
        );
        $this->serviceManager->setService(
            'custom.naming_strategy',
            $this->getMockBuilder('Zend\Hydrator\NamingStrategy\NamingStrategyInterface')->getMock()
        );

        $this->hydratorManager = $this->getMockBuilder(HydratorPluginManager::class)
            ->disableOriginalConstructor()
            ->getMock();

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
        $objectManager = $this->getMockBuilder($objectManagerClass)
            ->disableOriginalConstructor()
            ->getMock();
        $this->serviceManager->setService('doctrine.default.object-manager', $objectManager);

        return $objectManager;
    }

    /**
     * @return \Phpro\DoctrineHydrationModule\Hydrator\DoctrineHydrator
     */
    protected function createOrmHydrator()
    {
        $this->stubObjectManager('Doctrine\ORM\EntityManager');

        $factory = new DoctrineHydratorFactory();
        $hydrator = $factory->createServiceWithName($this->hydratorManager, 'customhydrator', 'custom-hydrator');

        return $hydrator;
    }

    /**
     * @return \Phpro\DoctrineHydrationModule\Hydrator\DoctrineHydrator
     */
    protected function createOdmHydrator()
    {
        $this->stubObjectManager('Doctrine\ODM\MongoDb\DocumentManager');

        $factory = new DoctrineHydratorFactory();
        $hydrator = $factory->createServiceWithName($this->hydratorManager, 'customhydrator', 'custom-hydrator');

        return $hydrator;
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
        $hydrator = $this->createOrmHydrator();

        $this->assertInstanceOf('Phpro\DoctrineHydrationModule\Hydrator\DoctrineHydrator', $hydrator);
        $this->assertInstanceOf('DoctrineModule\Stdlib\Hydrator\DoctrineObject', $hydrator->getExtractService());
        $this->assertInstanceOf('DoctrineModule\Stdlib\Hydrator\DoctrineObject', $hydrator->getHydrateService());
    }

    /**
     * @test
     */
    public function it_should_create_a_custom_ODM_hydrator()
    {
        $hydrator = $this->createOdmHydrator();

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
        $this->serviceManager->setService('config', $this->serviceConfig);
        $objectManager = $this->stubObjectManager('Doctrine\ODM\MongoDb\DocumentManager');

        $hydratorFactory = $this->getMockBuilder('Doctrine\ODM\MongoDB\Hydrator\HydratorFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $generatedHydrator = $this->getMockBuilder('Doctrine\ODM\MongoDB\Hydrator\HydratorInterface')->getMock();

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

    /**
     * @test
     */
    public function it_should_be_possible_to_configure_a_custom_hydrator()
    {
        $this->serviceConfig['doctrine-hydrator']['custom-hydrator']['hydrator'] = 'custom.hydrator';
        $this->serviceManager->setService('config', $this->serviceConfig);

        $this->serviceManager->setService(
            'custom.hydrator',
            $this->getMockBuilder('Zend\Hydrator\ArraySerializable')->getMock()
        );

        $hydrator = $this->createOrmHydrator();

        $this->assertInstanceOf('Zend\Hydrator\ArraySerializable', $hydrator->getHydrateService());
        $this->assertInstanceOf('Zend\Hydrator\ArraySerializable', $hydrator->getExtractService());
    }

    /**
     * @test
     */
    public function it_should_be_possible_to_configure_a_custom_hydrator_as_factory()
    {
        $this->serviceConfig['doctrine-hydrator']['custom-hydrator']['hydrator'] = 'custom.build.hydrator';
        $this->serviceManager->setService('config', $this->serviceConfig);

        $this->serviceManager->setFactory(
            'custom.build.hydrator',
            new CustomBuildHydratorFactory()
        );

        $hydrator = $this->createOrmHydrator();

        $this->assertInstanceOf('Zend\Hydrator\ArraySerializable', $hydrator->getHydrateService());
        $this->assertInstanceOf('Zend\Hydrator\ArraySerializable', $hydrator->getExtractService());
    }

    /**
     * @test
     */
    public function it_should_be_possible_to_configure_hydration_stategies()
    {
        $hydrator = $this->createOrmHydrator();
        $realHydrator = $hydrator->getExtractService();

        $this->assertTrue($realHydrator->hasStrategy('fieldname'));
        $this->assertInstanceOf('Zend\Hydrator\Strategy\StrategyInterface', $realHydrator->getStrategy('fieldname'));
    }

    /**
     * @test
     */
    public function it_should_be_possible_to_configure_a_naming_stategy()
    {
        $hydrator = $this->createOrmHydrator();
        $realHydrator = $hydrator->getExtractService();

        $this->assertTrue($realHydrator->hasNamingStrategy());
        $this->assertInstanceOf('Zend\Hydrator\NamingStrategy\NamingStrategyInterface', $realHydrator->getNamingStrategy());
    }

    /**
     * @test
     */
    public function it_should_be_possible_to_configure_hydration_filters()
    {
        $hydrator = $this->createOrmHydrator();
        $realHydrator = $hydrator->getExtractService();

        $this->assertTrue($realHydrator->hasFilter('custom.filter.name'));
    }
}
