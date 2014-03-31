<?php

namespace Phpro\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB\Strategy;
use Doctrine\ODM\MongoDB\DocumentManager;
use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\ReferencedCollection;
use Phpro\DoctrineHydrationModule\Tests\Fixtures\ODM\MongoDb\HydrationReferenceMany;
use Phpro\DoctrineHydrationModule\Tests\Fixtures\ODM\MongoDb\HydrationUser;
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
        $user->addReferenceMany([$referenced]);

        /** @var DocumentManager $objectmanager */
        $objectManager = $this->dm;
        $metadata = $objectManager->getClassMetadata(get_class($user));
        $strategy = $this->getStrategy($objectManager, $user, $metadata, 'referenceMany');

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
        $data = [$id];

        /** @var DocumentManager $objectmanager */
        $objectManager = $this->dm;
        $metadata = $objectManager->getClassMetadata(get_class($user));
        $strategy = $this->getStrategy($objectManager, $user, $metadata, 'referenceMany');

        $strategy->hydrate($data);
        $this->assertEquals('name', $user->getReferenceMany()[0]->getName());
    }

    /**
     * Create a reference in the database:
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
