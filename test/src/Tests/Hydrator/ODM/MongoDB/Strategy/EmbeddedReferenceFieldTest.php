<?php

namespace PhproTest\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB\Strategy;

use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\EmbeddedReferenceField;
use PhproTest\DoctrineHydrationModule\Fixtures\ODM\MongoDb\HydrationReferenceOne;
use PhproTest\DoctrineHydrationModule\Fixtures\ODM\MongoDb\HydrationUser;
use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * Class EmbeddedReferenceFieldTest.
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

    /**
     * @test
     */
    public function it_should_extract_embedded_fields()
    {
        $user = new HydrationUser();
        $user->setId(1);
        $user->setName('username');

        $referenced = new HydrationReferenceOne();
        $referenced->setId(1);
        $referenced->setName('name');
        $user->setReferenceOne($referenced);

        $strategy = $this->getStrategy($this->dm, $user, 'referenceOne');
        $result = $strategy->extract($user->getReferenceOne());
        $this->assertEquals('name', $result['name']);
    }

    /**
     * @test
     */
    public function it_should_hydrate_referenced_fields()
    {
        $user = new HydrationUser();
        $user->setId(1);
        $user->setName('username');

        $id = $this->createReference('name');
        $data = $id;

        $strategy = $this->getStrategy($this->dm, $user, 'referenceOne');
        $result = $strategy->hydrate($data);
        $this->assertEquals('name', $result->getName());
    }

    /**
     * Create a reference in the database:.
     *
     * @param $name
     *
     * @return string
     */
    protected function createReference($name)
    {
        $embedded = new HydrationReferenceOne();
        $embedded->setName($name);

        $this->dm->persist($embedded);
        $this->dm->flush();

        return $embedded->getId();
    }
}
