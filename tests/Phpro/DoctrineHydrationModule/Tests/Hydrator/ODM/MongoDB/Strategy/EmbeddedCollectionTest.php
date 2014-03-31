<?php

namespace Phpro\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB\Strategy;

use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\EmbeddedCollection;
use Phpro\DoctrineHydrationModule\Fixtures\ODM\MongoDb\HydrationEmbedMany;
use Phpro\DoctrineHydrationModule\Fixtures\ODM\MongoDb\HydrationUser;
use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;


/**
 * Class EmbeddedCollectionTest
 *
 * @package Phpro\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB\Strategy
 */
class EmbeddedCollectionTest extends AbstractMongoStrategyTest
{
    /**
     * @return StrategyInterface
     */
    protected function createStrategy()
    {
        return new EmbeddedCollection();
    }

    /**
     * @test
     */
    public function it_should_extract_embedded_collections()
    {
        $user = new HydrationUser();
        $user->setId(1);
        $user->setName('username');

        $embedded = new HydrationEmbedMany();
        $embedded->setId(1);
        $embedded->setName('name');
        $user->addEmbedMany([$embedded]);

        $strategy = $this->getStrategy($this->dm, $user, 'embedMany');
        $result = $strategy->extract($user->getEmbedMany());
        $this->assertEquals('name', $result[0]['name']);
    }

    /**
     * @test
     */
    public function it_should_hydrate_embedded_collections()
    {
        $user = new HydrationUser();
        $user->setId(1);
        $user->setName('username');

        $data = [
            [
                'id' => 1,
                'name' => 'name'
            ]
        ];

        $strategy = $this->getStrategy($this->dm, $user, 'embedMany');
        $strategy->hydrate($data);
        $this->assertEquals('name', $user->getEmbedMany()[0]->getName());
    }

}
