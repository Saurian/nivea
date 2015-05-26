<?php
/**
 * This file is part of the 2015_05_protect_and_bronze
 * Copyright (c) 2015
 *
 * @file    HomepagePresenter.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Presenters;

use AppModule\Entities\QuestionEntity;
use AppModule\Forms\IQuizFormFactory;
use CmsModule\Doctrine\EntityFormMapper;
use Nette;


/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{
    /** @var IQuizFormFactory @inject */
    public $quizFormFactory;

    /** @var QuestionEntity @inject */
    public $questionEntity;

    /** @var EntityFormMapper @inject */
    public $entityFormMapper;

    protected function createComponentQuizForm($name)
    {
        $form = $this->quizFormFactory->create();
        $form->setTranslator($this->translator->domain('forms.' . $name));
        $form->injectEntityMapper($this->entityFormMapper);
        $form->bindEntity($this->questionEntity);
        return $form;
    }

}
