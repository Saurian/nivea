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
        $this->addRadioList('quizOne', null, array('A' => 'otazka1', 'B' => 'otazka2', 'C' => 'otazka3'))
            ->addRule(Nette\Application\UI\Form::FILLED, 'tato_polozka_je_povinna');
//            ->addRule(Form::EQUAL, 'spatna_odpoved', 'B'); // jediná správná odpověď je "B"

        $this->addSubmit('send')->setAttribute('class', 'button next')->getControlPrototype()
            ->setName("button")
            ->create('strong', $this->getTranslator()->translate('forms.quizForm.next'));

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