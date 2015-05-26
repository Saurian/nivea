<?php
/**
 * This file is part of the 2015_05_protect_and_bronze
 * Copyright (c) 2015
 *
 * @file    QuestionEntity.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Entities;

use Doctrine\ORM\Mapping as ORM;
use Nette\DI\Container;
use Nette\Object;
use Nette\Utils\DateTime;

/**
 * Class QuestionEntity
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="questions")
 * @package AppModule\Entities
 */
class QuestionEntity extends Object
{
    use \CmsModule\Doctrine\Entities\IdentifiedEntityTrait;

    /** @var TransactionManager */
    public $transactionManager;


    /**
     * @var UserEntity
     * @ORM\OneToOne(targetEntity="UserEntity", inversedBy="questions")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $quizOne;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $quizTwo;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $quizThree;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $quizFour;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated;


    /**
     * @param string $quizFour
     *
     * @return $this
     */
    public function setQuizFour($quizFour)
    {
        $this->quizFour = $quizFour;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuizFour()
    {
        return $this->quizFour;
    }

    /**
     * @param string $quizOne
     *
     * @return $this
     */
    public function setQuizOne($quizOne)
    {
        $this->quizOne = $quizOne;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuizOne()
    {
        return $this->quizOne;
    }

    /**
     * @param string $quizTree
     *
     * @return $this
     */
    public function setQuizThree($quizTree)
    {
        $this->quizThree = $quizTree;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuizThree()
    {
        return $this->quizThree;
    }

    /**
     * @param string $quizTwo
     *
     * @return $this
     */
    public function setQuizTwo($quizTwo)
    {
        $this->quizTwo = $quizTwo;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuizTwo()
    {
        return $this->quizTwo;
    }

    /**
     * @param \AppModule\Entities\UserEntity $user
     *
     * @return $this
     */
    public function setUser(UserEntity $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return \AppModule\Entities\UserEntity
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->created = $this->updated = new DateTime();
    }


    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updated = new DateTime();
    }


}