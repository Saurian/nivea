<?php

/**
 *
 * This file is part of the 2015_03_MagicMoments
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
use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Tracy\Debugger;
use WebLoader\Nette\CssLoader;
use WebLoader\Nette\JavaScriptLoader;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter
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


    protected function startup()
    {
        parent::startup();

        $user = $this->getUser();

        if (!$user->isAllowed($this->name, $this->action)) {
//             $this->flashMessage($message, 'warning');
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
        $this->template->canRegister =
            new DateTime() >= DateTime::from($this->getContext()->getParameters()['contest']['registerFrom']) &&
            date('Y-m-d') <= DateTime::from($this->getContext()->getParameters()['contest']['registerTo']);

        $this->template->name = Strings::lower($name[1] . $action . $locale);
        $page = $this->link('this');
        $page = str_replace('/15/2014_11_christmas', '', $page);
        $page = str_replace('/26/2014_11_christmas', '', $page);
        $this->template->page = $page;

        $sysParams = $this->getContext()->getParameters();
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


}
