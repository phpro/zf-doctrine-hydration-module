<?php

namespace PhproTest\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB\Strategy;

use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\DateTimeField;
use PHPUnit\Framework\TestCase;

/**
 * Class DateTimeFieldTest.
 */
class DateTimeFieldTest extends TestCase
{
    /**
     * @param bool $isTimestamp
     *
     * @return DateTimeField
     */
    protected function createStrategy($isTimestamp = false)
    {
        return new DateTimeField($isTimestamp);
    }

    /**
     * @test
     */
    public function it_should_be_initializable()
    {
        $strategy = $this->createStrategy();
        $this->assertInstanceOf('Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\DateTimeField', $strategy);
    }

    /**
     * @test
     */
    public function it_should_be_a_strategy_interface()
    {
        $strategy = $this->createStrategy();
        $this->assertInstanceOf('Zend\Hydrator\Strategy\StrategyInterface', $strategy);
    }

    /**
     * @test
     */
    public function it_should_extract_datetime()
    {
        $strategy = $this->createStrategy();
        $date = new \DateTime('1 january 2014');

        $result = $strategy->extract($date);
        $this->assertEquals($date->getTimestamp(), $result);
    }

    /**
     * @test
     */
    public function it_should_hydrate_datetime()
    {
        $date = new \DateTime('1 january 2014');
        $dateMongo = new \MongoDate($date->getTimestamp());
        $dateInt = $date->getTimestamp();
        $dateString = $date->format('Y-m-d');

        $strategy = $this->createStrategy();
        $this->assertEquals($date->getTimestamp(), $strategy->hydrate($date)->getTimestamp());
        $this->assertEquals($date->getTimestamp(), $strategy->hydrate($dateMongo)->getTimestamp());
        $this->assertEquals($date->getTimestamp(), $strategy->hydrate($dateInt)->getTimestamp());
        $this->assertEquals($date->getTimestamp(), $strategy->hydrate($dateString)->getTimestamp());
    }

    public function it_should_hydrate_timestamps()
    {
        $date = new \DateTime('1 january 2014');
        $dateMongo = new \MongoDate($date->getTimestamp());
        $dateInt = $date->getTimestamp();
        $dateString = $date->format('Y-m-d');

        $strategy = $this->createStrategy(true);
        $this->assertEquals($date->getTimestamp(), $strategy->hydrate($date));
        $this->assertEquals($date->getTimestamp(), $strategy->hydrate($dateMongo));
        $this->assertEquals($date->getTimestamp(), $strategy->hydrate($dateInt));
        $this->assertEquals($date->getTimestamp(), $strategy->hydrate($dateString));
    }
}
