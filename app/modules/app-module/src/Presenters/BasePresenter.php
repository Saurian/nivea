<?php

/**
 *
 * This file is part of the 2015_04_makeUP_starter
 *
 * Copyright (c) 2015
 *
 * @file BasePresenter.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Presenters;

use AppModule\Entities\UserEntity;
use AppModule\Forms\ILoginFormFactory;
use AppModule\Forms\ILostPswFormFactory;
use AppModule\Managers\ApplicationManager;
use AppModule\Managers\UserManager;
use Nette\Application\UI\Presenter;
use Nette\DI\Container;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Tracy\Debugger;
use WebLoader\Nette\CssLoader;
use WebLoader\Nette\JavaScriptLoader;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter
//class BasePresenter extends \App\Presenters\BasePresenter
{
    /** @persistent */
    public $locale;

    /** @var \Kdyby\Doctrine\EntityManager @inject */
    public $em;

    /** @var \WebLoader\LoaderFactory @inject */
    public $webLoader;

    /** @var UserManager @inject */
    public $userManager;

    /** @var ApplicationManager @inject */
    public $application;

    /** @var \Kdyby\Translation\Translator */
    protected $translator;

    /** @var ILoginFormFactory @inject */
    public $loginFormFactory;

    /** @var ILostPswFormFactory @inject */
    public $lostPswFormFactory;

    /** @var string */
    protected $description = "";

    /** @var string */
    protected $keywords = "";

    /** @var UserEntity */
    protected $_userData;

    /** @var array[] */
    protected $status;

    /** @var Container */
    protected $container;


    function __construct(Container $container)
    {
        $this->container = $container;
    }


    protected function startup()
    {
        parent::startup();

        $user = $this->getUser();

        if (!$user->isAllowed($this->name, $this->action)) {
            //$this->flashMessage($message, 'warning');
            $this->redirect('Homepage:', array('backlink' => $this->storeRequest()));
        }

        $name    = explode(':', $this->getName());
        $action  = ($this->action != 'default') ? ' ' . $this->action : null;
        $locale  = ' ' . $this->locale;
        $regions = array_search($this->locale, array('cz' => 'cs'));

        $this->template->robots      = "index, follow";
        $this->template->description = $this->description;
        $this->template->keywords    = $this->keywords;
        $this->template->locale      = $regions ? $regions : $this->locale;
        $this->template->webLocale   = $this->locale;

        $this->template->name = Strings::lower($name[1] . $action . $locale);
        $page = $this->link('this');
        $page = str_replace('/15/2014_11_christmas', '', $page);
        $page = str_replace('/26/2014_11_christmas', '', $page);
        $this->template->page = $page;

        $sysParams    = $this->container->getParameters();
        $this->template->registerTo = $registerTo = DateTime::from($sysParams['contest']['registerTo']);
        $this->template->registerFrom = $registerFrom = DateTime::from($sysParams['contest']['registerFrom']);

        $this->template->canRegister = new DateTime() >= $registerFrom && date('Y-m-d') <= $registerTo;
        $this->template->allowLogin = true;
        $this->template->omnitureIframe = isset($sysParams['contest']) && isset($sysParams['contest']['omnitureIframe'])
            ? $sysParams['contest']['omnitureIframe']
            : false;

        $this->template->googleAnalytics = (!Debugger::$productionMode)
            ? false
            : true;
    }


    /**
     * @param \Kdyby\Translation\Translator $translator
     *
     * @return void
     */
    public function injectTranslator(\Kdyby\Translation\Translator $translator)
    {
        $this->translator = $translator;
    }


    /**
     * @return \Kdyby\Translation\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }


    protected function createComponentLoginForm($name)
    {
        $form = $this->loginFormFactory->create();
        $form->setTranslator($this->translator->domain('forms.' . $name));
        return $form;
    }

    protected function createComponentLostPswForm($name)
    {
        $form = $this->lostPswFormFactory->create();
        $form->setTranslator($this->translator->domain('forms.' . $name));
        return $form;
    }


    /** @return CssLoader */
    protected function createComponentCss()
    {
        return $this->webLoader->createCssLoader('default');
    }

    /** @return JavaScriptLoader */
    protected function createComponentJs()
    {
        return $this->webLoader->createJavaScriptLoader('default');
    }


    public function actionLogoff()
    {
        $this->getUser()->logout(true);
        $this->redirect('Registration:');
    }


    public function setUserData()
    {
        if ($this->getUser()->isLoggedIn()) {
            $this->_userData = $this->userManager->getUserDao()->find($this->getUser()->id);
        }
    }

    /**
     * @return \AppModule\Entities\UserEntity
     */
    public function getUserData()
    {

        if ($this->_userData === null) {
            $this->setUserData();
        }
        return $this->_userData;
    }


    public function actionClearCache()
    {
        if ($dir = $this->getContext()->getParameters()['tempDir'] . '/cache') {
            foreach (\Nette\Utils\Finder::findFiles('*.php')->from($dir) as $key => $file) {
                @unlink($key);
            }
            $storage = new \Nette\Caching\Storages\FileStorage($dir);
            $cache = new \Nette\Caching\Cache($storage);
            $cache->clean(array(\Nette\Caching\Cache::ALL=>true));
            $this->redirect('Homepage:');
        }
    }


    public function isStatus($i)
    {
        return (isset($this->status[$i]) && $this->status[$i] == ' burn') == true;
    }


    protected function setStatus($i)
    {
        $result = '';
        if ($user = $this->getUserData()) {
            switch ($i) {
                case 1: $result = $this->getUserData()->getQuestions()->getQuizOne() ? ' burn': '';
                    break;
                case 2: $result = $this->getUserData()->getQuestions()->getQuizTwo() ? ' burn': '';
                    break;
                case 3: $result = $this->getUserData()->getQuestions()->getQuizThree() ? ' burn': '';
                    break;
                case 4: $result = $this->getUserData()->getQuestions()->getQuizFour() ? ' burn': '';
                    break;
                default: $result = '';
            }
        }

        if (!$result) {
            $from   = DateTime::from($this->application->getSettingDateForWeek($i, ApplicationManager::PERIOD_FROM));
            $to     = DateTime::from($this->application->getSettingDateForWeek($i, ApplicationManager::PERIOD_TO));
            $now    = DateTime::from('now');
            $result = ($now > $from) // and $now < $to
                ? ' full'
                : '';
        }

        $this->status[$i] = $result;
    }


    public function getStatus($i)
    {
        if (!isset($this->status[$i])) {
            $this->setStatus($i);
        }
        return $this->status[$i];

    }


}
