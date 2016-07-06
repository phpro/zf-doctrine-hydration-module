<?php

namespace PhproTest\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB\Strategy;

use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\EmbeddedField;
use PhproTest\DoctrineHydrationModule\Fixtures\ODM\MongoDb\HydrationEmbedOne;
use PhproTest\DoctrineHydrationModule\Fixtures\ODM\MongoDb\HydrationUser;
use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * Class EmbeddedFieldTest.
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
    public function it_should_not_break_when_embed_field_not_set()
    {
        $user = new HydrationUser();
        $user->setId(1);
        $user->setName('username');

        $embedded = new HydrationEmbedOne();
        $embedded->setId(1);
        $embedded->setName('name');
        $strategy = $this->getStrategy($this->dm, $user, 'embedOne');
        $result = $strategy->extract($user->getEmbedOne());
        $this->assertNull($result);
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

        $data = array(
            'id' => 1,
            'name' => 'name',
        );

        $strategy = $this->getStrategy($this->dm, $user, 'embedOne');
        $result = $strategy->hydrate($data);
        $this->assertEquals('name', $result->getName());
    }
}
