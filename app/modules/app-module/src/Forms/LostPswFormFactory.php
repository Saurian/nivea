<?php
/**
 *
 * This file is part of the 2015_02_Q10Plus
 *
 * Copyright (c) 2015
 *
 * @file LostPswFormFactory.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Forms;

use AppModule\Managers\UserManager;
use Nette\Application\UI\Form;
use Nette;
use Nette\Forms\Rendering;


interface ILostPswFormFactory
{
    /** @return LostPswFormFactory */
    function create();
}


class LostPswFormFactory extends AbstractForm implements ILostPswFormFactory
{
    /** @var \AppModule\Managers\UserManager */
    private $userManager;


    public function __construct(UserManager $userManager)
    {
        parent::__construct();
        $this->userManager = $userManager;
    }


    /** @return LostPswFormFactory */
    function create()
    {
        parent::create();
        $this->addGroup();
        $this->addText('email', 'e-mail')
            ->addRule(Form::EMAIL, 'vyplňte_email_v_platném_formátu')
            ->addRule(Form::FILLED, 'vyplňte_prosím_e-mail');

        $this->addGroup()->setOption('container', 'fieldset class=send');
        $this->addSubmit('send', 'zaslat_na_e-mail')->setAttribute('class', 'btn next');

        $this->onSuccess[] = array($this, 'formSubmitted');
        $this->getElementPrototype()->class = 'lost-form';
    }


    public function formSubmitted(Form $form)
    {
        try {
            $email = $form->getValues()['email'];
            if ($user = $this->userManager->findByLogin($email)) {
                $message = $this->userManager->regeneratePassword($email)
                    ? 'email_send'
                    : 'email_fail';
                $this->presenter->flashMessage($this->presenter->translator->translate($message));

            } else {
                $this->presenter->flashMessage($this->presenter->translator->translate('email_nenalezen'));
            }

            $form->getPresenter()->redirect($this->redirect);

        } catch (Nette\Security\AuthenticationException $e) {
            $this->getPresenter()->flashMessage($e->getMessage(), 'warning');
            $form->addError($e->getMessage());
        }

    }


}