<?php

namespace Phpro\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB;
use Doctrine\ODM\MongoDB\Tests\BaseTest;
use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\DoctrineObject;
use Phpro\DoctrineHydrationModule\Fixtures\ODM\MongoDb\HydrationEmbedMany;
use Phpro\DoctrineHydrationModule\Fixtures\ODM\MongoDb\HydrationEmbedOne;
use Phpro\DoctrineHydrationModule\Fixtures\ODM\MongoDb\HydrationReferenceMany;
use Phpro\DoctrineHydrationModule\Fixtures\ODM\MongoDb\HydrationReferenceOne;
use Phpro\DoctrineHydrationModule\Fixtures\ODM\MongoDb\HydrationUser;

/**
 * Class DoctrineObjectTest
 *
 * @package Phpro\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB
 */
class DoctrineObjectTest extends BaseTest
{

    /**
     * @param null $objectManager
     *
     * @return DoctrineObject
     */
    protected function createHydrator($objectManager = null)
    {
        $objectManager = $objectManager ? $objectManager : $this->getMock('Doctrine\ODM\MongoDB\DocumentManager', [], [], '', false);
        $hydrator = new DoctrineObject($objectManager);
        return $hydrator;
    }

    /**
     * @test
     */
    public function it_should_be_initializable()
    {
        $hydrator = $this->createHydrator();
        $this->assertInstanceOf('Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\DoctrineObject', $hydrator);
    }

    /**
     * @test
     */
    public function it_should_be_a_doctrine_hydrator()
    {
        $hydrator = $this->createHydrator();
        $this->assertInstanceOf('DoctrineModule\Stdlib\Hydrator\DoctrineObject', $hydrator);
    }

    /**
     * @test
     */
    public function it_should_extract_a_document()
    {
        $creationDate = new \DateTime();
        $birthday = new \DateTime('1 january 2014');

        $user = new HydrationUser();
        $user->setId(1);
        $user->setName('user');
        $user->setCreatedAt($creationDate->getTimestamp());
        $user->setBirthday($birthday);

        $embedOne = new HydrationEmbedOne();
        $embedOne->setId(1);
        $embedOne->setName('name');
        $user->setEmbedOne($embedOne);

        $embedMany = new HydrationEmbedMany();
        $embedMany->setId(1);
        $embedMany->setName('name');
        $user->addEmbedMany([$embedMany]);

        $referenceOne = new HydrationReferenceOne();
        $referenceOne->setId(1);
        $referenceOne->setName('name');
        $user->setReferenceOne($referenceOne);

        $referenceMany = new HydrationEmbedMany();
        $referenceMany->setId(1);
        $referenceMany->setName('name');
        $user->addReferenceMany([$referenceMany]);

        $hydrator = new DoctrineObject($this->dm);
        $result = $hydrator->extract($user);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('user', $result['name']);
        $this->assertEquals($creationDate->getTimestamp(), $result['createdAt']);
        $this->assertEquals($birthday->getTimestamp(), $result['birthday']);
        $this->assertEquals(1, $result['embedOne']['id']);
        $this->assertEquals('name', $result['embedOne']['name']);
        $this->assertEquals(1, $result['embedMany'][0]['id']);
        $this->assertEquals('name', $result['embedMany'][0]['name']);
        $this->assertEquals(1, $result['referenceOne']);
        $this->assertEquals(1, $result['referenceMany'][0]);
    }

    /**
     * @test
     */
    public function it_should_hydrate_a_document()
    {
        $creationDate = new \DateTime();
        $birthday = new \DateTime('1 january 2014');

        $user = new HydrationUser();
        $data = [
            'id' => 1,
            'name' => 'user',
            'creationDate' => $creationDate->getTimestamp(),
            'birthday' => $birthday->getTimestamp(),
            'referenceOne' => $this->createReferenceOne('name'),
            'referenceMany' => [$this->createReferenceMany('name')],
            'embedOne' => [
                'id' => 1,
                'name' => 'name'
            ],
            'embedMany' => [
                [
                    'id' => 1,
                    'name' => 'name'
                ]
            ]
        ];

        $hydrator = new DoctrineObject($this->dm);
        $hydrator->hydrate($data, $user);

        $this->assertEquals(1, $user->getId());
        $this->assertEquals('user', $user->getName());
        $this->assertEquals($creationDate->getTimestamp(), $user->getCreatedAt());
        $this->assertEquals($birthday->getTimestamp(), $user->getBirthday()->getTimestamp());
        $this->assertInstanceOf('Phpro\DoctrineHydrationModule\Fixtures\ODM\MongoDb\HydrationReferenceOne', $user->getReferenceOne());
        $this->assertInstanceOf('Phpro\DoctrineHydrationModule\Fixtures\ODM\MongoDb\HydrationReferenceMany', $user->getReferenceMany()[0]);
        $this->assertInstanceOf('Phpro\DoctrineHydrationModule\Fixtures\ODM\MongoDb\HydrationEmbedOne', $user->getEmbedOne());
        $this->assertInstanceOf('Phpro\DoctrineHydrationModule\Fixtures\ODM\MongoDb\HydrationEmbedMany', $user->getEmbedMany()[0]);
        $this->assertEquals('name', $user->getReferenceOne()->getName());
        $this->assertEquals('name', $user->getReferenceMany()[0]->getName());
        $this->assertEquals('name', $user->getEmbedOne()->getName());
        $this->assertEquals('name', $user->getEmbedMany()[0]->getName());
    }

    /**
     * @param $name
     *
     * @return string
     */
    protected function createReferenceOne($name)
    {
        $embedded = new HydrationReferenceOne();
        $embedded->setName($name);

        $this->dm->persist($embedded);
        $this->dm->flush();

        return $embedded->getId();
    }

    /**
     * @param $name
     *
     * @return string
     */
    protected function createReferenceMany($name)
    {
        $embedded = new HydrationReferenceMany();
        $embedded->setName($name);

        $this->dm->persist($embedded);
        $this->dm->flush();

        return $embedded->getId();
    }

}
