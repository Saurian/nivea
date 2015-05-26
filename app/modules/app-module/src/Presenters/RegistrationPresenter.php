<?php
/**
 * This file is part of the 2015_05_protect_and_bronze
 * Copyright (c) 2015
 *
 * @file    HomepagePresenter.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Presenters;

use AppModule\Entities\UserEntity;
use AppModule\Forms\IRegistrationFormFactory;
use AppModule\Forms\RegistrationFormFactory;
use Nette;


/**
 * Homepage presenter.
 */
class RegistrationPresenter extends BasePresenter
{

    /** @var IRegistrationFormFactory @inject */
    public $registrationFormFactory;

    /** @var UserEntity @inject */
    public $userEntity;

    public function actionDefault()
    {
        if ($this->getUser()->isLoggedIn()) {
            if ($user = $this->userManager->getUserDao()->find($this->getUser()->id)) {
                $this->userEntity = $user;
            }
        }
    }

    protected function createComponentRegistrationForm($name)
    {
        $mapper = new \CmsModule\Doctrine\EntityFormMapper($this->em);
        $form = $this->registrationFormFactory->create();
        $form->setTranslator($this->translator->domain('forms.' . $name));
        $form->injectEntityMapper($mapper);
        $form->bindEntity($this->userEntity);
        return $form;
    }

}
