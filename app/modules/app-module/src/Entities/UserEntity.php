<?php
/**
 * This file is part of the 2015_05_protect_and_bronze
 * Copyright (c) 2015
 *
 * @file    UserEntity.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Entities;

use CmsModule\Doctrine\InvalidArgumentException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\BigIntType;
use Nette\Object;
use Nette\Utils\DateTime;

/**
 * Class UserEntity
 *
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @ORM\HasLifecycleCallbacks
 * @package AppModule\Entities
 */
class UserEntity extends Object
{
    use \CmsModule\Doctrine\Entities\IdentifiedEntityTrait;

    /**
     * @var QuestionEntity
     * @ORM\OneToOne(targetEntity="QuestionEntity", mappedBy="user", cascade={"persist"})
     */
    protected $questions;

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $firstname;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $lastname;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $password;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $gender;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $street = '';

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $strno;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $zip = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $city = '';

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $day;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $month;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $year;

    /**
     * @var DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    protected $birthday = null;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $newsletter;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $accessToken;

    /**
     * @var string
     * @ORM\Column(type="string", length=32)
     */
    protected $role = 'member';

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
     * @param QuestionEntity $questions
     */
    public function setQuestions(QuestionEntity $questions)
    {
        $this->questions = $questions;
        $questions->setUser($this);

    }

    /**
     * @param $quiz
     * @param $answer
     *
     * @throws \CmsModule\Doctrine\InvalidArgumentException
     */
    public function addQuestion($quiz, $answer)
    {
        if (!isset($this->questions->$quiz)) {
            throw new InvalidArgumentException($quiz);
        }
        $this->questions->$quiz = $answer;
    }

    /**
     * @return \AppModule\Entities\QuestionEntity
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $firstname
     *
     * @return $this
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }


    /**
     * @param string $lastname
     *
     * @return $this
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }


    /**
     * @param \Nette\Utils\DateTime $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return \Nette\Utils\DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param int $day
     *
     * @return $this
     */
    public function setDay($day)
    {
        $this->day = $day;
        $this->settingBirthday();
        return $this;
    }

    /**
     * set correct BirthDay
     */
    private function settingBirthday()
    {
        if ($this->day && $this->month && $this->year) {
            if (!$this->birthday) {
                $this->birthday = new DateTime();
            }
            $this->birthday->setDate(intval($this->year), intval($this->month), intval($this->day));
        }
    }


    /**
     * @return int
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param int $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return int
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param int $month
     *
     * @return $this
     */
    public function setMonth($month)
    {
        $this->month = $month;
        $this->settingBirthday();
        return $this;
    }

    /**
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $strno
     */
    public function setStrno($strno)
    {
        $this->strno = $strno;
    }

    /**
     * @return string
     */
    public function getStrno()
    {
        return $this->strno;
    }

    /**
     * @param int $year
     *
     * @return $this
     */
    public function setYear($year)
    {
        $this->year = $year;
        $this->settingBirthday();
        return $this;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param string $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param string $accessToken
     *
     * @return $this
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = sha1($password);
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param boolean $newsletter
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;
    }

    /**
     * @return boolean
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    public function isAllRequiderFill()
    {
        return
            $this->city !== null &&
            $this->firstname !== null &&
            $this->lastname !== null &&
            $this->gender !== null &&
            $this->street !== null &&
            $this->strno !== null &&
            $this->zip !== null &&
            $this->email !== null;
    }


    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->created = new DateTime();
        $this->created = $this->updated = new DateTime();
    }


    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updated = new DateTime();
    }


    /**
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

}