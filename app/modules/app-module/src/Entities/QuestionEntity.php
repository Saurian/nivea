<?php
/**
 * This file is part of the 2015_02_Q10Plus
 * Copyright (c) 2015
 *
 * @file    QuestionEntity.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Translation\Translator;
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
     * @var string
     * @ORM\Column(type="string", length=2)
     */
    protected $lang;

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
     */
    public function setUser(UserEntity $user)
    {
        $this->user = $user;
    }

    /**
     * @return \AppModule\Entities\UserEntity
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param string $lang
     *
     * @return $this
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->created = new DateTime();
        $this->updated = $this->created;
    }


    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updated = new DateTime();
    }


    public function getQuizesSet()
    {
        return intval($this->quizOne == true) +
            intval($this->quizTwo == true) +
            intval($this->quizThree == true) +
            intval($this->quizFour == true);
    }


    public function getNextQuiz()
    {
        if (($result = intval($this->quizOne == true)) != 1) {
            return $result;
        }
        if (($result += intval($this->quizOne == true && $this->quizTwo == true)) != 2) {
            return $result;
        }
        if (($result += intval($this->quizOne == true && $this->quizTwo == true && $this->quizThree == true)) != 3) {
            return $result;
        }
        if (($result += intval($this->quizOne == true && $this->quizTwo == true && $this->quizThree == true && $this->quizFour == true)) != 4) {
            return $result;
        }
        return 0;
    }

}