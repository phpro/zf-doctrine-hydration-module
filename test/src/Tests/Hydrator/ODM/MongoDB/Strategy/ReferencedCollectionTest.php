<?php

namespace PhproTest\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB\Strategy;

use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\ReferencedCollection;
use PhproTest\DoctrineHydrationModule\Fixtures\ODM\MongoDb\HydrationReferenceMany;
use PhproTest\DoctrineHydrationModule\Fixtures\ODM\MongoDb\HydrationUser;
use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * Class ReferencedCollectionTest.
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

    /**
     * @test
     */
    public function it_should_extract_referenced_collections()
    {
        $user = new HydrationUser();
        $user->setId(1);
        $user->setName('username');

        $referenced = new HydrationReferenceMany();
        $referenced->setId(1);
        $referenced->setName('name');
        $user->addReferenceMany(array($referenced));

        $strategy = $this->getStrategy($this->dm, $user, 'referenceMany');
        $result = $strategy->extract($user->getReferenceMany());
        $this->assertEquals(1, $result[0]);
    }

    /**
     * @test
     */
    public function it_should_hydrate_referenced_collections()
    {
        $user = new HydrationUser();
        $user->setId(1);
        $user->setName('username');

        $id = $this->createReference('name');
        $data = array($id);

        $strategy = $this->getStrategy($this->dm, $user, 'referenceMany');
        $strategy->hydrate($data);
        $referenceMany = $user->getReferenceMany();
        $this->assertEquals('name', $referenceMany[0]->getName());
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
        $embedded = new HydrationReferenceMany();
        $embedded->setName($name);

        $this->dm->persist($embedded);
        $this->dm->flush();

        return $embedded->getId();
    }
}
