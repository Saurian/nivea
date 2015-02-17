<?php
/**
 * This file is part of the 2015_02_Q10Plus
 * Copyright (c) 2015
 *
 * @created 1.21.15
 * @package QuestionPresenter.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Presenters;

use AppModule\Entities\QuestionEntity;
use AppModule\Forms\IQuizOneFormFactory;
use AppModule\Forms\QuizOneFormFactory;
use AppModule\Managers\UserManager;
use CmsModule\Doctrine\EntityFormMapper;

class InShowerPresenter extends BasePresenter
{

    /** @var IQuizOneFormFactory @inject */
    public $quizOneFormFactory;

    /** @var QuestionEntity @inject */
    public $questionEntity;

    /** @var UserManager @inject */
    public $userManager;

    /** @var \CmsModule\Doctrine\EntityFormMapper */
    private $entityFormMapper;


    function __construct(EntityFormMapper $entityFormMapper)
    {
        $this->entityFormMapper = $entityFormMapper;
    }


    protected function startup()
    {
        parent::startup();
        if ($this->getUser()->isLoggedIn()) {
            if ($entity = $this->userManager->getQuestionDao()->findOneBy(array('user' => $this->getUser()->id))) {
                $this->questionEntity = $entity;
            }
        }
    }


    public function renderDefault()
    {
        /** @var $form QuizOneFormFactory */
        $form = $this['quizOneForm'];

        if (!$this->getUser()->isLoggedIn()) {
            $form->setRedirect('Registration:');

        } else {
            $form->setRedirect('Homepage:');
        }

    }

    protected function createComponentQuizOneForm($name)
    {
        $form = $this->quizOneFormFactory->create();
        $form->injectEntityMapper($this->entityFormMapper);
        $form->setTranslator($this->translator->domain('forms.' . $name));
        $form->bindEntity($this->questionEntity);
        return $form;
    }


}
