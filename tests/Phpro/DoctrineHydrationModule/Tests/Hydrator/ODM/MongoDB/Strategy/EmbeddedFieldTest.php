<?php

namespace Phpro\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB\Strategy;
use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\EmbeddedField;
use Phpro\DoctrineHydrationModule\Fixtures\ODM\MongoDb\HydrationEmbedOne;
use Phpro\DoctrineHydrationModule\Fixtures\ODM\MongoDb\HydrationUser;
use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;


/**
 * Class EmbeddedFieldTest
 *
 * @package Phpro\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB\Strategy
 */
class EmbeddedFieldTest extends AbstractMongoStrategyTest
{
    /**
     * @return StrategyInterface
     */
    protected function createStrategy()
    {
        return new EmbeddedField();
    }

    /**
     * @test
     */
    public function it_should_extract_embedded_fields()
    {
        $user = new HydrationUser();
        $user->setId(1);
        $user->setName('username');

        $embedded = new HydrationEmbedOne();
        $embedded->setId(1);
        $embedded->setName('name');
        $user->setEmbedOne($embedded);

        $strategy = $this->getStrategy($this->dm, $user, 'embedOne');
        $result = $strategy->extract($user->getEmbedOne());
        $this->assertEquals('name', $result['name']);
    }

    /**
     * @test
     */
    public function it_should_hydrate_embedded_fields()
    {
        $user = new HydrationUser();
        $user->setId(1);
        $user->setName('username');

        $data = [
            'id' => 1,
            'name' => 'name'
        ];

        $strategy = $this->getStrategy($this->dm, $user, 'embedOne');
        $result = $strategy->hydrate($data);
        $this->assertEquals('name', $result->getName());
    }
}
