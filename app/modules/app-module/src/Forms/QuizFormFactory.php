<?php
/**
 *
 * This file is part of the 2015_03_MagicMoments
 *
 * Copyright (c) 2015
 *
 * @file QuizFormFactory.php
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
        $this->addTextArea('quizOne', null, 50, 15)
            ->addRule(Form::FILLED, 'tato_polozka_je_povinna')
            ->addRule(Form::MAX_LENGTH, 'povolena_delka', 2500)
            ->setAttribute('placeholder', 'spatna_odpoved');

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