<?php

namespace PhproTest\DoctrineHydrationModule\Fixtures\ODM\MongoDb;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class HydrationUser.
 *
 *
 * @ODM\Document
 */
class HydrationUserWithAssocEmbedMany
{
    /** @ODM\Id */
    public $id;

    /** @ODM\Field(type="string") */
    public $name;

    /**
     * @ODM\Field(type="date")
     *
     * @var \DateTime
     */
    public $birthday;

    /**
     * @ODM\Timestamp
     *
     * @var \DateTime
     */
    public $createdAt;

    /**
     * @ODM\ReferenceOne(targetDocument="HydrationReferenceOne")
     */
    public $referenceOne;

    /**
     * @ODM\ReferenceMany(targetDocument="HydrationReferenceMany")
     *
     * @var ArrayCollection
     */
    public $referenceMany = array();

    /**
     * @ODM\EmbedOne(targetDocument="HydrationEmbedOne")
     */
    public $embedOne;

    /**
     * @ODM\EmbedMany(targetDocument="HydrationEmbedMany")
     *
     * @var ArrayCollection
     */
    public $embedMany;

    /**
     * Basic state.
     */
    public function __construct()
    {
        $this->embedMany = new ArrayCollection();
        $this->referenceMany = new ArrayCollection();

        $now = new \DateTime();
        $this->createdAt = $now->getTimestamp();
    }

    /**
     * @param mixed $embedOne
     */
    public function setEmbedOne($embedOne)
    {
        $this->embedOne = $embedOne;
    }

    /**
     * @return mixed
     */
    public function getEmbedOne()
    {
        return $this->embedOne;
    }

    /**
     * @param mixed $embedMany
     */
    public function setEmbedMany($embedMany)
    {
        $this->embedMany = $embedMany;
    }

    /**
     * @return mixed
     */
    public function getEmbedMany()
    {
        return $this->embedMany;
    }

    /**
     * @param $embedMany
     */
    public function addEmbedMany($embedMany)
    {
        foreach ($embedMany as $key => $record) {
            $this->embedMany->set($key, $record);
        }
    }

    /**
     * @param $embedMany
     */
    public function removeEmbedMany($embedMany)
    {
        foreach ($embedMany as $key => $record) {
            $this->embedMany->remove($key);
        }
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $referenceMany
     */
    public function setReferenceMany($referenceMany)
    {
        $this->referenceMany = $referenceMany;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getReferenceMany()
    {
        return $this->referenceMany;
    }

    /**
     * @param $referenceMany
     */
    public function addReferenceMany($referenceMany)
    {
        foreach ($referenceMany as $key => $record) {
            $this->referenceMany->set($key, $record);
        }
    }

    /**
     * @param $referenceMany
     */
    public function removeReferenceMany($referenceMany)
    {
        foreach ($referenceMany as $key => $record) {
            $this->referenceMany->remove($key);
        }
    }

    /**
     * @param mixed $referenceOne
     */
    public function setReferenceOne($referenceOne)
    {
        $this->referenceOne = $referenceOne;
    }

    /**
     * @return mixed
     */
    public function getReferenceOne()
    {
        return $this->referenceOne;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return mixed
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param int $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
