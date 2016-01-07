<?php

namespace PhproTest\DoctrineHydrationModule\Tests\Hydrator;

use Phpro\DoctrineHydrationModule\Hydrator\DoctrineHydrator;

/**
 * Class DoctrineHydratorTest.
 */
class DoctrineHydratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param null $hydrateService
     * @param null $extractService
     *
     * @return DoctrineHydrator
     */
    protected function createHydrator($hydrateService = null, $extractService = null)
    {
        $hydrateService = $hydrateService ? $hydrateService : $this->getMock('Zend\Hydrator\HydratorInterface');
        $extractService = $extractService ? $extractService : $this->getMock('Zend\Hydrator\HydratorInterface');

        return new DoctrineHydrator($extractService, $hydrateService);
    }

    /**
     * @test
     */
    public function it_should_be_initializable()
    {
        $hydrator = $this->createHydrator();
        $this->assertInstanceOf('Phpro\DoctrineHydrationModule\Hydrator\DoctrineHydrator', $hydrator);
    }

    /**
     * @test
     */
    public function it_should_have_a_hydrator_service()
    {
        $hydrator = $this->createHydrator();
        $this->assertInstanceOf('Zend\Hydrator\HydratorInterface', $hydrator->getHydrateService());
    }

    /**
     * @test
     */
    public function it_should_have_an_extractor_service()
    {
        $hydrator = $this->createHydrator();
        $this->assertInstanceOf('Zend\Hydrator\HydratorInterface', $hydrator->getExtractService());
    }

    /**
     * @test
     */
    public function it_should_extract_an_object()
    {
        $object = new \stdClass();
        $extracted = array('extracted' => true);
        $extractService = $this->getMock('Zend\Hydrator\HydratorInterface');
        $extractService
            ->expects($this->any())
            ->method('extract')
            ->will($this->returnValue($extracted));

        $hydrator = $this->createHydrator(null, $extractService);
        $result = $hydrator->extract($object);

        $this->assertEquals($extracted, $result);
    }

    /**
     * @test
     */
    public function it_should_hydrate_an_object()
    {
        $object = new \stdClass();
        $data = array('field' => 'value');

        $hydrateService = $this->getMock('Zend\Hydrator\HydratorInterface');
        $hydrateService
            ->expects($this->any())
            ->method('hydrate')
            ->with($data, $object)
            ->will($this->returnValue($object));

        $hydrator = $this->createHydrator($hydrateService, null);
        $result = $hydrator->hydrate($data, $object);

        $this->assertEquals($object, $result);
    }

    /**
     * @test
     */
    public function it_should_use_a_generated_doctrine_hydrator_while_hydrating_an_object()
    {
        $object = new \stdClass();
        $data = array('field' => 'value');

        $hydrateService = $this->getMock('Doctrine\ODM\MongoDB\Hydrator\HydratorInterface');
        $hydrateService
            ->expects($this->any())
            ->method('hydrate')
            ->with($object, $data)
            ->will($this->returnValue($object));

        $hydrator = $this->createHydrator($hydrateService, null);
        $result = $hydrator->hydrate($data, $object);

        $this->assertEquals($object, $result);
    }
}
