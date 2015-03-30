<?php
/**
 * This file is part of the 2015_02_InShower
 * Copyright (c) 2015
 *
 * @file    UserManager.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Managers;

use AppModule\Entities\QuestionEntity;
use AppModule\Entities\UserEntity;
use Doctrine\ORM\Query;
use Kdyby\Doctrine\EntityDao;
use Kdyby\Translation\Translator;
use Nette\Database\Context;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Object;
use Nette\Utils\ArrayHash;
use Nette\Utils\Random;

class UserManager extends Object
{

    const TABLE_NAME = 'user';
    const DEFAULT_PASSWORD_CHARS = 6;

    /** @var EntityDao|UserEntity */
    private $userDao;

    /** @var EntityDao|QuestionEntity */
    private $questionDao;

    /** @var Translator */
    private $translator;

    /** @var array service settings */
    private $contest;

    /** @var Context */
    private $database;

    /** @var array Subsciber */
    public $onUpdate;


    function __construct(
        EntityDao $userDao,
        EntityDao $questionsDao,
        $contest,
        Translator $translator
    )
    {
        $this->contest     = $contest;
        $this->userDao     = $userDao;
        $this->translator  = $translator;
        $this->questionDao = $questionsDao;
    }

    /**
     * @return \Kdyby\Doctrine\EntityDao
     */
    public function getUserDao()
    {
        return $this->userDao;
    }

    /**
     * @return \AppModule\Entities\QuestionEntity|\Kdyby\Doctrine\EntityDao
     */
    public function getQuestionDao()
    {
        return $this->questionDao;
    }

    /**
     * @param ArrayHash $values
     *
     * @return bool|int|\Nette\Database\Table\IRow
     */
    public function insert(ArrayHash $values)
    {
        return $this->database->table(self::TABLE_NAME)->insert($values);
    }


    public function findByLogin($email)
    {
        return $this->userDao->createQueryBuilder('e')
            ->where("e.email = :username")
            ->setParameter('username', $email)
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }


    public function regeneratePassword($login, $sendEmail = true)
    {
        if ($record = $this->userDao->findOneBy(array('email' => $login))) {
            $length = isset($this->contest['email']['passwordChars']) ? $this->contest['email']['passwordChars'] : static::DEFAULT_PASSWORD_CHARS;
            $sender = $this->translator->translate("messages.email.sender");
            $header = $this->translator->translate("messages.email.header");
            $random = Random::generate($length);
            $record->password = $random;
            $this->userDao->save($record);

            if ($sendEmail) {
                $mail = new Message();
                $mail->setFrom($sender)
                    ->addTo($login)
                    ->setSubject($header)
                    ->setBody($this->translator->translate("vaše_nové_heslo", null, array('d' => $random)));
                $mailer = new SendmailMailer();
                $mailer->send($mail);
            }
        }

        return $record;
    }


    public function deleteAccount(UserEntity $user)
    {
        return $user
            ? $this->getUserDao()->delete($user)
            : false;
    }


    /**
     * @return mixed
     */
    public function getContest()
    {
        return $this->contest;
    }


}