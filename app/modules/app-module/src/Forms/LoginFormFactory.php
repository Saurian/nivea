<?php
/**
 *
 * This file is part of the 2015_03_MagicMoments
 *
 * Copyright (c) 2015
 *
 * @file LoginFormFactory.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Forms;

use Nette\Application\UI\Form;
use Nette;
use Nette\Forms\Rendering;


interface ILoginFormFactory
{
    /** @return LoginFormFactory */
    function create();
}


class LoginFormFactory extends AbstractForm implements ILoginFormFactory
{

    public function __construct()
    {
        parent::__construct();
    }


    /** @return LoginFormFactory */
    function create()
    {
        parent::create();
        $this->addGroup();
        $this->addText('email', 'e-mail')
            ->addRule(Form::EMAIL, 'vyplňte_email_v_platném_formátu')
            ->addRule(Form::FILLED, 'vyplňte_prosím_e-mail');

        $this->addText('password', 'heslo')
            ->addRule(Form::FILLED, 'vyplňte_vaše_heslo');

        $this->addGroup()->setOption('container', 'fieldset class=send');
        $this->addSubmit('send', 'přihlásit_se')->setAttribute('class', 'btn next');
        $this->addButton('lostPassword', 'zaslat_zapomenuté_heslo')
            ->setAttribute('class', 'btn next track-btn')
            ->setAttribute('data-name', 'lost-password-panel');

        $this->onSuccess[] = array($this, 'formSubmitted');
        $this->getElementPrototype()->class = 'login-form';
    }


    public function formSubmitted(Form $form)
    {
        $presenter = $this->getPresenter();
        try {
            $user = $presenter->getUser();
            $user->setExpiration('14 days', TRUE);

            $user->login($form['email']->value, $form['password']->value);
            $presenter->redirect($this->redirect);

        } catch (Nette\Security\AuthenticationException $e) {
            $presenter->flashMessage($e->getMessage(), 'warning');
            $form->addError($e->getMessage());
        }

    }


}