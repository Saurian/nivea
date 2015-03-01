<?php
/**
 *
 * This file is part of the 2015_02_Q10Plus
 *
 * Copyright (c) 2015
 *
 * @file QuizOneFormFactory.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Forms;

use AppModule\Managers\TransactionListener;
use AppModule\Managers\UserManager;
use Flame\Application\UI\Form;
use Kdyby\Translation\ITranslator;
use Nette;


interface IQuizFormFactory
{
    /** @return QuizFormFactory */
    function create();
}


class QuizFormFactory extends BasicForm implements IQuizFormFactory
{
    protected $redirect = 'Registration:';

    /** @var \AppModule\Managers\TransactionListener */
    private $transitionListener;

    /** @var UserManager */
    private $userManager;


    public function __construct(TransactionListener $transactionListener, UserManager $userManager, ITranslator $translator)
    {
        parent::__construct();
        $this->transitionListener = $transactionListener;
        $this->userManager = $userManager;
        $this->setTranslator($translator);
    }


    /** @return QuizFormFactory */
    function create()
    {
        $okAnswers = $this->transitionListener->getContest()['okAnswers'];
        $this->addCheckboxList('quizOne', null, array('otazka_1', 'otazka_2', 'otazka_3', 'otazka_4', 'otazka_5', 'otazka_6', 'otazka_7', 'otazka_8', 'otazka_9', 'otazka_10'))
            ->addRule('FormValidators::validateChecked', 'spatna_odpoved', $okAnswers);

        $this->addSubmit('send', 'pokračovat')->setAttribute('class', 'button next')->getControlPrototype()
            ->setName("button")
            ->create('strong', $this->getTranslator()->translate('forms.quizForm.pokračovat'));

        $this->getElementPrototype()->name = 'quizOne';
        $this->getElementPrototype()->class = 'quiz-form';
        $this->onSuccess[] = array($this, 'processFormSuccess');
    }


    /**
     * @param BasicForm $form
     */
    public function processFormSuccess(BasicForm $form)
    {
        $presenter = $this->getPresenter();
        $values = $form->getValues();
        $section = $presenter->getSession($this->section);
        $section->quizOne = $values['quizOne'];
        $form->getPresenter()->redirect($form->getRedirect());
    }

}